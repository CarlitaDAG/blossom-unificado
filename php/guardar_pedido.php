
<?php
header("Content-Type: application/json; charset=utf-8");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
require_once __DIR__ . "/conexion.php";

$conexion->set_charset("utf8mb4");

try {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(405);
        throw new Exception("Método no permitido.");
    }

    $datos = json_decode(file_get_contents("php://input"), true);

    if (!is_array($datos)) {
        throw new Exception("Los datos recibidos no son válidos.");
    }

    $cliente = $datos["cliente"] ?? [];
    $carrito = $datos["carrito"] ?? [];
    $envio = (string)($datos["envio"] ?? "");
    $pago = (string)($datos["pago"] ?? "");

    foreach (["nombre", "apellido", "email", "telefono", "direccion"] as $campo) {
        if (empty(trim((string)($cliente[$campo] ?? "")))) {
            throw new Exception("Completá todos los datos del cliente.");
        }
    }

    if (!filter_var($cliente["email"], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("El correo electrónico no es válido.");
    }

    if (!in_array($envio, ["0", "5000", "8000"], true)) {
        throw new Exception("El método de envío no es válido.");
    }

    if (!in_array($pago, ["tarjeta", "transferencia", "efectivo"], true)) {
        throw new Exception("El método de pago no es válido.");
    }

   
if (!is_array($carrito) || count($carrito) === 0) {
    throw new Exception("El carrito está vacío.");
}

// Agrupar productos repetidos y sumar sus cantidades.
$carritoAgrupado = [];

foreach ($carrito as $item) {
    if (!is_array($item)) {
        throw new Exception("Hay un producto inválido en el carrito.");
    }

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

    if ($productoId === false || $productoId === null ||
        $cantidad === false || $cantidad === null) {
        throw new Exception("Hay un producto o una cantidad inválidos.");
    }

    if (!isset($carritoAgrupado[$productoId])) {
        $carritoAgrupado[$productoId] = [
            "id" => $productoId,
            "cantidad" => 0
        ];
    }

    $carritoAgrupado[$productoId]["cantidad"] += $cantidad;
}

$carrito = array_values($carritoAgrupado);


    $costoEnvio = (float)$envio;
    $metodoEnvio = [
        "0" => "Retiro en tienda",
        "5000" => "Envío estándar",
        "8000" => "Envío express"
    ][$envio];

    $conexion->begin_transaction();

    // Buscar al cliente por correo.
    $stmt = $conexion->prepare(
        "SELECT id FROM clientes WHERE email = ? LIMIT 1 FOR UPDATE"
    );
    $stmt->bind_param("s", $cliente["email"]);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $filaCliente = $resultado->fetch_assoc();
    $stmt->close();

    if ($filaCliente) {
        $clienteId = (int)$filaCliente["id"];

        $stmt = $conexion->prepare(
            "UPDATE clientes
             SET nombre = ?, apellido = ?, telefono = ?, direccion = ?
             WHERE id = ?"
        );
        $stmt->bind_param(
            "ssssi",
            $cliente["nombre"],
            $cliente["apellido"],
            $cliente["telefono"],
            $cliente["direccion"],
            $clienteId
        );
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $conexion->prepare(
            "INSERT INTO clientes (nombre, apellido, email, telefono, direccion)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "sssss",
            $cliente["nombre"],
            $cliente["apellido"],
            $cliente["email"],
            $cliente["telefono"],
            $cliente["direccion"]
        );
        $stmt->execute();
        $clienteId = (int)$conexion->insert_id;
        $stmt->close();
    }

    // Consultar precios y stock reales, y calcular el subtotal.
    $productosPedido = [];
    $subtotal = 0;

    foreach ($carrito as $item) {
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

        if (!$productoId || !$cantidad) {
            throw new Exception("Hay un producto o una cantidad inválidos.");
        }

        $stmt = $conexion->prepare(
            "SELECT id, nombre, precio, precio_oferta, stock, activo
             FROM productos
             WHERE id = ?
             FOR UPDATE"
        );
        $stmt->bind_param("i", $productoId);
        $stmt->execute();
        $producto = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$producto || (int)$producto["activo"] !== 1) {
            throw new Exception("Uno de los productos ya no está disponible.");
        }

        if ($cantidad > (int)$producto["stock"]) {
            throw new Exception(
                "No hay stock suficiente de " . $producto["nombre"] . "."
            );
        }

        $precioNormal = (float)$producto["precio"];
        $precioOferta = $producto["precio_oferta"] !== null
            ? (float)$producto["precio_oferta"]
            : 0;

        $precioUnitario = (
            $precioOferta > 0 && $precioOferta < $precioNormal
        ) ? $precioOferta : $precioNormal;

        $subtotalLinea = round($precioUnitario * $cantidad, 2);
        $subtotal += $subtotalLinea;

        $productosPedido[] = [
            "id" => (int)$producto["id"],
            "nombre" => $producto["nombre"],
            "precio" => $precioUnitario,
            "cantidad" => $cantidad,
            "subtotal" => $subtotalLinea
        ];
    }

    $subtotal = round($subtotal, 2);
    $total = round($subtotal + $costoEnvio, 2);

    // Crear el pedido.
    $estado = "Pendiente";

    $stmt = $conexion->prepare(
        "INSERT INTO pedidos
         (cliente_id, subtotal, costo_envio, total, metodo_envio, metodo_pago, estado)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "idddsss",
        $clienteId,
        $subtotal,
        $costoEnvio,
        $total,
        $metodoEnvio,
        $pago,
        $estado
    );

    $stmt->execute();
    $pedidoId = (int)$conexion->insert_id;
    $stmt->close();

    // Guardar cada producto y actualizar el stock.
    $stmtDetalle = $conexion->prepare(
        "INSERT INTO detalle_pedido
         (pedido_id, producto_id, nombre_producto, precio_unitario, cantidad, subtotal)
         VALUES (?, ?, ?, ?, ?, ?)"
    );

    $stmtStock = $conexion->prepare(
        "UPDATE productos SET stock = stock - ? WHERE id = ? AND stock >= ?"
    );

    foreach ($productosPedido as $producto) {
        $id = $producto["id"];
        $nombre = $producto["nombre"];
        $precio = $producto["precio"];
        $cantidad = $producto["cantidad"];
        $subtotalProducto = $producto["subtotal"];

        $stmtDetalle->bind_param(
            "iisdid",
            $pedidoId,
            $id,
            $nombre,
            $precio,
            $cantidad,
            $subtotalProducto
        );
        $stmtDetalle->execute();

        $stmtStock->bind_param("iii", $cantidad, $id, $cantidad);
        $stmtStock->execute();

        if ($stmtStock->affected_rows !== 1) {
            throw new Exception(
                "El stock cambió mientras se procesaba la compra. Intentá nuevamente."
            );
        }
    }

    $stmtDetalle->close();
    $stmtStock->close();

    $conexion->commit();

    echo json_encode([
        "ok" => true,
        "pedido_id" => $pedidoId,
        "subtotal" => $subtotal,
        "costo_envio" => $costoEnvio,
        "total" => $total,
        "cliente" => [
            "nombre" => $cliente["nombre"],
            "apellido" => $cliente["apellido"],
            "email" => $cliente["email"],
            "telefono" => $cliente["telefono"],
            "direccion" => $cliente["direccion"]
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {

    if (isset($conexion) && $conexion instanceof mysqli) {
        try {
            $conexion->rollback();
        } catch (Throwable $ignorado) {
        }
    }

    error_log(
        "Error al guardar pedido Blossom: " .
        $e->getMessage()
    );

    http_response_code(400);

    echo json_encode([
        "ok" => false,
        "mensaje" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}