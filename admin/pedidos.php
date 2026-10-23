
<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../php/conexion.php";

$conexion->set_charset("utf8mb4");

function escapar($valor) {
    return htmlspecialchars(
        (string)($valor ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

function moneda($valor) {
    return "$ " . number_format((float)$valor, 2, ",", ".");
}

$error = "";
$pedidos = [];

try {
    $sql = "SELECT
                p.id,
                p.fecha,
                p.subtotal,
                p.costo_envio,
                p.total,
                p.metodo_envio,
                p.metodo_pago,
                p.estado,
                c.nombre,
                c.apellido,
                c.email,
                (
                    SELECT COUNT(*)
                    FROM detalle_pedido dp
                    WHERE dp.pedido_id = p.id
                ) AS cantidad_productos
            FROM pedidos p
            INNER JOIN clientes c ON c.id = p.cliente_id
            ORDER BY p.fecha DESC, p.id DESC";

    $resultado = $conexion->query($sql);
    $pedidos = $resultado->fetch_all(MYSQLI_ASSOC);

} catch (Throwable $e) {
    error_log("Error al consultar pedidos Blossom: " . $e->getMessage());
    $error = "No se pudieron cargar los pedidos. Revisá la conexión y las tablas.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pedidos | Blossom</title>

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
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
            background: white;
            box-shadow: 0 3px 15px #30305010;
        }

        .logo {
            margin: 0;
            color: #305cde;
            font: bold 32px Georgia, serif;
        }

        .logo span {
            color: #ff6f61;
        }

        a {
            color: #305cde;
        }

        .contenedor {
            width: min(1250px, 92%);
            margin: 38px auto;
        }

        .encabezado {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
        }

        .encabezado h2 {
            margin-bottom: 8px;
        }

        .boton {
            display: inline-block;
            padding: 12px 18px;
            border-radius: 9px;
            background: #305cde;
            color: white;
            text-decoration: none;
            border: none;
            font-size: 13px;
white-space: nowrap;
        }

        .panel {
            background: white;
            padding: 22px;
            border: 1px solid #eeeaf7;
            border-radius: 18px;
            box-shadow: 0 8px 24px #3030500c;
        }

        .tabla-contenedor {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 950px;
        }

        th, td {
            padding: 15px 12px;
            text-align: left;
            border-bottom: 1px solid #eeeaf7;
        }

        th {
            background: #eef1ff;
            color: #27304a;
        }

        tbody tr:hover {
            background: #faf9ff;
        }

        .estado {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            background: #fff0f5;
            color: #a33e75;
            font-size: 13px;
            font-weight: bold;
        }

        .mensaje {
            padding: 16px;
            border-radius: 10px;
            background: #fff0f5;
            margin-bottom: 18px;
        }

        .vacio {
            padding: 35px 10px;
            text-align: center;
            color: #77758c;
        }

        @media (max-width: 600px) {
            .contenedor {
                width: 94%;
                margin: 24px auto;
            }

            .panel {
                padding: 12px;
            }

            .logo {
                font-size: 27px;
            }
        }
    </style>
</head>

<body>

<header>
    <h1 class="logo">Blossom<span>.</span></h1>
    <a href="panel.php">← Volver al panel</a> 
</header>

<main class="contenedor">

    <section class="encabezado">
        <div>
            <h2>🛍️ Pedidos registrados</h2>
            <p>Consultá las compras realizadas en Blossom.</p>
        </div>

        <a href="productos.php" class="boton">
            Gestionar productos
        </a>
    </section>

    <?php if ($error !== ""): ?>

        <div class="mensaje">
            <?= escapar($error) ?>
        </div>

    <?php else: ?>

        <section class="panel">

            <p>
                Total de pedidos:
                <strong><?= count($pedidos) ?></strong>
            </p>

            <?php if (count($pedidos) === 0): ?>

                <div class="vacio">
                    <h3>Todavía no hay pedidos</h3>
                    <p>Cuando se registre una compra, aparecerá aquí.</p>
                </div>

            <?php else: ?>

                <div class="tabla-contenedor">
                    <table>
                        <thead>
                            <tr>
                                <th>N.º</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Productos</th>
                                <th>Envío</th>
                                <th>Pago</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php foreach ($pedidos as $pedido): ?>
                            <tr>
                                <td>
                                    #<?= (int)$pedido["id"] ?>
                                </td>

                                <td>
                                    <strong>
                                        <?= escapar($pedido["nombre"]) ?>
                                        <?= escapar($pedido["apellido"]) ?>
                                    </strong>
                                    <br>
                                    <small>
                                        <?= escapar($pedido["email"]) ?>
                                    </small>
                                </td>

                                <td>
                                    <?= escapar($pedido["fecha"]) ?>
                                </td>

                                <td>
                                    <?= (int)$pedido["cantidad_productos"] ?>
                                </td>

                                <td>
                                    <?= escapar($pedido["metodo_envio"]) ?>
                                    <br>
                                    <?= moneda($pedido["costo_envio"]) ?>
                                </td>

                                <td>
                                    <?= escapar($pedido["metodo_pago"]) ?>
                                </td>

                                <td>
                                    <strong>
                                        <?= moneda($pedido["total"]) ?>
                                    </strong>
                                </td>

                                <td>
    <span class="estado">
        <?= escapar($pedido["estado"]) ?>
    </span>
</td>

<td>
    <a class="boton"
       href="detalle-pedido.php?id=<?= (int)$pedido['id'] ?>">
        Ver detalle
    </a>
</td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php endif; ?>

        </section>

    <?php endif; ?>

</main>

</body>
</html>
