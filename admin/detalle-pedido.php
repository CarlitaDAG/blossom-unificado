
<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../php/conexion.php";
$conexion->set_charset("utf8mb4");

function escapar($valor) {
    return htmlspecialchars((string)($valor ?? ""), ENT_QUOTES, "UTF-8");
}

function moneda($valor) {
    return "$ " . number_format((float)$valor, 2, ",", ".");
}

$pedidoId = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$pedidoId || $pedidoId < 1) {
    http_response_code(400);
    exit("El número de pedido no es válido.");
}

$sql = "SELECT
            p.id, p.fecha, p.subtotal, p.costo_envio, p.total,
            p.metodo_envio, p.metodo_pago, p.estado,
            c.nombre, c.apellido, c.email, c.telefono, c.direccion
        FROM pedidos p
        INNER JOIN clientes c ON c.id = p.cliente_id
        WHERE p.id = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $pedidoId);
$stmt->execute();
$pedido = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$pedido) {
    http_response_code(404);
    exit("No se encontró el pedido solicitado.");
}

$sqlDetalle = "SELECT
                   nombre_producto, precio_unitario, cantidad, subtotal
               FROM detalle_pedido
               WHERE pedido_id = ?
               ORDER BY id ASC";

$stmt = $conexion->prepare($sqlDetalle);
$stmt->bind_param("i", $pedidoId);
$stmt->execute();
$productos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del pedido #<?= (int)$pedido["id"] ?> | Blossom</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f7f7fc;
            color: #27304a;
        }
        header {
            padding: 22px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            background: white;
            box-shadow: 0 3px 15px #30305010;
        }
        .logo {
            margin: 0;
            color: #305cde;
            font: bold 32px Georgia, serif;
        }
        .logo span { color: #ff6f61; }
        a { color: #305cde; }
        .contenedor {
            width: min(1050px, 92%);
            margin: 35px auto;
        }
        .panel {
            background: white;
            padding: 25px;
            border: 1px solid #eeeaf7;
            border-radius: 18px;
            box-shadow: 0 8px 24px #3030500c;
            margin-bottom: 22px;
        }
        .datos {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }
        .dato {
            padding: 14px;
            border-radius: 10px;
            background: #f5f3ff;
            overflow-wrap: anywhere;
        }
        .dato small {
            display: block;
            color: #77758c;
            margin-bottom: 6px;
        }
        .tabla-contenedor { overflow-x: auto; }
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 520px;
        }
        th, td {
            padding: 13px 10px;
            text-align: left;
            border-bottom: 1px solid #eeeaf7;
        }
        th { background: #eef1ff; }
        .resumen {
            max-width: 380px;
            margin-left: auto;
        }
        .linea {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 10px 0;
        }
        .total {
            border-top: 1px solid #eeeaf7;
            margin-top: 8px;
            padding-top: 18px;
            font-size: 21px;
            font-weight: bold;
            color: #ff6f61;
        }
        .boton {
            display: inline-block;
            padding: 12px 18px;
            border-radius: 9px;
            background: #305cde;
            color: white;
            text-decoration: none;
        }
        .estado {
            display: inline-block;
            padding: 6px 11px;
            border-radius: 20px;
            background: #fff0f5;
            color: #a33e75;
            font-weight: bold;
        }
        @media (max-width: 600px) {
            .panel { padding: 15px; }
            .logo { font-size: 27px; }
        }
    </style>
</head>
<body>
<header>
    <h1 class="logo">Blossom<span>.</span></h1>
    <a href="pedidos.php">← Volver a pedidos</a>
</header>


<main class="contenedor">
    
<?php if (isset($_GET["actualizado"]) && $_GET["actualizado"] === "1"): ?>
    <p style="background: #e8f7ec; color: #216e39; padding: 12px;
              border-radius: 8px;">
        ¡El estado del pedido se actualizó correctamente!
    </p>
<?php endif; ?>

    <h2>🛍️ Detalle del pedido #<?= (int)$pedido["id"] ?></h2>
    <p>Fecha: <?= escapar($pedido["fecha"]) ?></p>
    <p>Estado: <span class="estado"><?= escapar($pedido["estado"]) ?></span></p>

    <section class="panel">
        <h3>Datos del cliente</h3>
        <div class="datos">
            <div class="dato">
                <small>Nombre y apellido</small>
                <strong><?= escapar($pedido["nombre"] . " " . $pedido["apellido"]) ?></strong>
            </div>
            <div class="dato">
                <small>Correo electrónico</small>
                <?= escapar($pedido["email"]) ?>
            </div>
            <div class="dato">
                <small>Teléfono</small>
                <?= escapar($pedido["telefono"]) ?>
            </div>
            <div class="dato">
                <small>Dirección</small>
                <?= escapar($pedido["direccion"]) ?>
            </div>
        </div>
    </section>

    <section class="panel">
        <h3>Productos comprados</h3>
        <?php if (count($productos) === 0): ?>
            <p>Este pedido no tiene productos registrados en el detalle.</p>
        <?php else: ?>
            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio unitario</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><?= escapar($producto["nombre_producto"]) ?></td>
                            <td><?= moneda($producto["precio_unitario"]) ?></td>
                            <td><?= (int)$producto["cantidad"] ?></td>
                            <td><?= moneda($producto["subtotal"]) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <section class="panel">
        <h3>Resumen de la compra</h3>
        <div class="resumen">
            <div class="linea">
                <span>Subtotal</span>
                <strong><?= moneda($pedido["subtotal"]) ?></strong>
            </div>
            <div class="linea">
                <span>Envío: <?= escapar($pedido["metodo_envio"]) ?></span>
                <strong><?= moneda($pedido["costo_envio"]) ?></strong>
            </div>
            <div class="linea">
                <span>Forma de pago</span>
                <strong><?= escapar($pedido["metodo_pago"]) ?></strong>
            </div>
            <div class="linea total">
                <span>Total</span>
                <span><?= moneda($pedido["total"]) ?></span>
            </div>
        </div>
    </section>


<section class="panel">
    <h3>Actualizar estado del pedido</h3>

    <form action="actualizar-estado.php" method="POST">
        <input type="hidden" name="pedido_id"
               value="<?= (int)$pedido["id"] ?>">

        <label for="estado">Seleccioná el nuevo estado:</label>

        <select name="estado" id="estado" required>
            <?php
            $estadosPermitidos = [
                "Pendiente",
                "Confirmado",
                "Preparando",
                "Enviado",
                "Entregado",
                "Cancelado"
            ];

            foreach ($estadosPermitidos as $estado) {
                $seleccionado = ($pedido["estado"] === $estado)
                    ? "selected"
                    : "";

                echo '<option value="' . escapar($estado) . '" '
                    . $seleccionado . '>'
                    . escapar($estado)
                    . '</option>';
            }
            ?>
        </select>

        <button class="boton" type="submit">
            Guardar estado
        </button>
    </form>
</section>


    <a class="boton" href="pedidos.php">← Volver a pedidos</a>
</main>
</body>
</html>
