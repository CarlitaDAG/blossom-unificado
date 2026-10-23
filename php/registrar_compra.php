
<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/conexion.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function responder($exito, $mensaje, $datos = [], $codigo = 200)
{
    http_response_code($codigo);

    echo json_encode(
        array_merge([
            "ok" => $exito,
            "mensaje" => $mensaje
        ], $datos),
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    responder(false, "Método no permitido.", [], 405);
}

$datos = json_decode(file_get_contents("php://input"), true);

if (!is_array($datos)) {
    responder(false, "Los datos recibidos no son válidos.", [], 400);
}

$cliente = $datos["cliente"] ?? [];
$items = $datos["items"] ?? [];
$envio = $datos["envio"] ?? null;
$metodoEnvio = $datos["metodo_envio"] ?? "";
$metodoPago = $datos["metodo_pago"] ?? "";

$nombre = trim($cliente["nombre"] ?? "");
$apellido = trim($cliente["apellido"] ?? "");
$email = trim($cliente["email"] ?? "");
$telefono = trim($cliente["telefono"] ?? "");
$direccion = trim($cliente["direccion"] ?? "");

if (
    $nombre === "" ||
    $apellido === "" ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    $telefono === "" ||
    $direccion === ""
) {
    responder(false, "Revisá los datos del cliente.", [], 422);
}

if (!is_array($items) || count($items) === 0) {
    responder(false, "El carrito está vacío.", [], 422);
}

if (
    !is_numeric($envio) ||
    !in_array((int)$envio, [0, 5000, 8000], true) ||
    (string)(int)$envio !== (string)$envio
) {
    responder(false, "El costo de envío no es válido.", [], 422);
}

if (
    !in_array($metodoEnvio, ["retiro", "estandar", "express"], true) ||
    !in_array($metodoPago, ["tarjeta", "transferencia", "efectivo"], true)
) {
    responder(false, "El envío o medio de pago no es válido.", [], 422);
}

$costosEnvio = [
    "retiro" => 0,
    "estandar" => 5000,
    "express" => 8000
];

if ($costosEnvio[$metodoEnvio] !== (int)$envio) {
    responder(false, "El costo no coincide con el envío elegido.", [], 422);
}

$conexion->set_charset("utf8mb4");

try {
    $conexion->begin_transaction();

    // Buscar al cliente por correo.
    $consulta = $conexion->prepare(
        "SELECT id FROM clientes WHERE email = ? LIMIT 1"
    );
    $consulta->bind_param("s", $email);
    $consulta->execute();
    $resultado = $consulta->get_result();
    $clienteExistente = $resultado->fetch_assoc();
    $consulta->close();

    if ($clienteExistente) {
        $clienteId = (int)$clienteExistente["id"];

        $actualizar = $conexion->prepare(
            "UPDATE clientes
             SET nombre = ?, apellido = ?, telefono = ?, direccion = ?
             WHERE id = ?"
        );
        $actualizar->bind_param(
            "ssssi",
            $nombre,
            $apellido,
            $telefono,
            $direccion,
            $clienteId
        );
        $actualizar->execute();
        $actualizar->close();
    } else {
        $insertar = $conexion->prepare(
            "INSERT INTO clientes
             (nombre, apellido, email, telefono, direccion)
             VALUES (?, ?, ?, ?, ?)"
        );
        $insertar->bind_param(
            "sssss",
            $nombre,
            $apellido,
            $email,
            $telefono,
            $direccion
        );
        $insertar->execute();
        $clienteId = $conexion->insert_id;
        $insertar->close();
    }

    $subtotal = 0;
    $productosPedido = [];

    // Consultar los precios y bloquear el stock durante la compra.
    $consultaProducto = $conexion->prepare(
        "SELECT id, nombre, precio, precio_oferta, stock, activo
         FROM productos
         WHERE id = ?
         FOR UPDATE"
    );

    foreach ($items as $item) {
        $productoId = filter_var(
            $item["id"] ?? null,
            FILTER_VALIDATE_INT,
            ["options" => ["min_range" => 1]]
        );

        $cantidad = filter_var(
            $item["cantidad"] ?? null,
            FILTER_VALIDATE_INT,
            ["options" => ["min_range" => 1]]
        );

        if ($productoId === false || $cantidad === false) {
            throw new RuntimeException("Hay un producto o cantidad inválidos.");
        }

        $consultaProducto->bind_param("i", $productoId);
        $consultaProducto->execute();

        $resultadoProducto = $consultaProducto->get_result();
        $producto = $resultadoProducto->fetch_assoc();

        if (!$producto || (int)$producto["activo"] !== 1) {
            throw new RuntimeException(
                "Uno de los productos ya no está disponible."
            );
        }

        if ((int)$producto["stock"] < $cantidad) {
            throw new RuntimeException(
                "Stock insuficiente para: " . $producto["nombre"]
            );
        }

        $precioNormal = (float)$producto["precio"];
        $precioOferta = $producto["precio_oferta"];

        $precioUnitario = (
            $precioOferta !== null &&
            (float)$precioOferta > 0 &&
            (float)$precioOferta < $precioNormal
        ) ? (float)$precioOferta : $precioNormal;

        $importe = round($precioUnitario * $cantidad, 2);
        $subtotal = round($subtotal + $importe, 2);

        $productosPedido[] = [
            "id" => (int)$producto["id"],
            "nombre" => $producto["nombre"],
            "precio" => $precioUnitario,
            "cantidad" => $cantidad,
            "subtotal" => $importe
        ];
    }

    $consultaProducto->close();

    $costoEnvio = (int)$envio;
    $total = round($subtotal + $costoEnvio, 2);
    $estado = "Pendiente";

    // Registrar el pedido.
    $insertarPedido = $conexion->prepare(
        "INSERT INTO pedidos
         (cliente_id, subtotal, costo_envio, total,
          metodo_envio, metodo_pago, estado)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );

    $insertarPedido->bind_param(
        "idddsss",
        $clienteId,
        $subtotal,
        $costoEnvio,
        $total,
        $metodoEnvio,
        $metodoPago,
        $estado
    );

    $insertarPedido->execute();
    $pedidoId = $conexion->insert_id;
    $insertarPedido->close();

    // Guardar el detalle y descontar el stock.
    $insertarDetalle = $conexion->prepare(
        "INSERT INTO detalle_pedido
         (pedido_id, producto_id, nombre_producto,
          precio_unitario, cantidad, subtotal)
         VALUES (?, ?, ?, ?, ?, ?)"
    );

    $actualizarStock = $conexion->prepare(
        "UPDATE productos
         SET stock = stock - ?, mas_vendido = mas_vendido + 1
         WHERE id = ? AND activo = 1 AND stock >= ?"
    );

    foreach ($productosPedido as $producto) {
        $id = $producto["id"];
        $nombreProducto = $producto["nombre"];
        $precio = $producto["precio"];
        $cantidad = $producto["cantidad"];
        $importe = $producto["subtotal"];

        $insertarDetalle->bind_param(
            "iisdid",
            $pedidoId,
            $id,
            $nombreProducto,
            $precio,
            $cantidad,
            $importe
        );
        $insertarDetalle->execute();

        $actualizarStock->bind_param("iii", $cantidad, $id, $cantidad);
        $actualizarStock->execute();

        if ($actualizarStock->affected_rows !== 1) {
            throw new RuntimeException(
                "No se pudo actualizar el stock de " . $nombreProducto
            );
        }
    }

    $insertarDetalle->close();
    $actualizarStock->close();

    $conexion->commit();

    responder(true, "¡Compra registrada correctamente!", [
        "pedido_id" => $pedidoId,
        "subtotal" => $subtotal,
        "costo_envio" => $costoEnvio,
        "total" => $total
    ]);

} catch (Throwable $error) {
    try {
        $conexion->rollback();
    } catch (Throwable $ignorado) {
        // La transacción podría no haberse iniciado.
    }

    error_log("Blossom registrar_compra: " . $error->getMessage());

    if ($error instanceof RuntimeException) {
        responder(false, $error->getMessage(), [], 422);
    }

    responder(
        false,
        "No se pudo registrar la compra. Revisá la configuración del servidor.",
        [],
        500
    );
}
