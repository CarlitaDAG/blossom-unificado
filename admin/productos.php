
<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

require_once __DIR__ . "/../php/conexion.php";

$resultado = $conexion->query(
    "SELECT p.id, p.nombre, p.marca, p.precio, p.precio_oferta,
            p.stock, p.activo, c.nombre AS categoria
     FROM productos p
     LEFT JOIN categorias c ON p.categoria_id = c.id
     ORDER BY p.id DESC"
);

$productos = $resultado->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos | Blossom</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f7f7fc;
            color: #27304a;
        }

        header {
            padding: 20px 5%;
            background: white;
            box-shadow: 0 3px 15px #30305010;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        header h1 {
            margin: 0;
            color: #305cde;
            font-family: Georgia, serif;
        }

        header h1 span { color: #ff6f61; }

        a { color: #305cde; text-decoration: none; }

        main {
            width: min(1200px, 94%);
            margin: 32px auto;
        }

        .titulo {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .boton {
            display: inline-block;
            padding: 12px 18px;
            border: 0;
            border-radius: 10px;
            background: #305cde;
            color: white;
            cursor: pointer;
            font-size: 15px;
        }

        .tabla-contenedor {
            margin-top: 24px;
            overflow-x: auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 25px #3030500c;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 760px;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eeeaf7;
        }

        th { background: #edf1ff; }

        .estado { font-weight: bold; }
        .activo { color: #16834a; }
        .inactivo { color: #a64a61; }

        .aviso {
            margin-top: 20px;
            padding: 15px;
            border-radius: 10px;
            background: #fff3d9;
        }
    </style>
</head>
<body>
<header>
    <h1>Blossom<span>.</span></h1>
    <a href="panel.php">← Volver al panel</a>
</header>

<main>
    <div class="titulo">
        <div>
            <h2>Gestión de productos 📦</h2>
            <p>Productos registrados en la base de datos.</p>
        </div>
        <a class="boton" href="producto_guardar.php">+ Nuevo producto</a>
    </div>

    <div class="tabla-contenedor">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Marca</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                    <th>Disponibilidad</th>
                </tr>
            </thead>
            <tbody>
            <?php if (count($productos) === 0): ?>
                <tr>
                    <td colspan="8">
                        Todavía no hay productos cargados en MySQL.
                    </td>
                </tr>
            <?php else: ?>
             
<?php foreach ($productos as $producto): ?>
    <tr>
        <td><?= (int)$producto["id"] ?></td>

        <td><?= htmlspecialchars($producto["nombre"], ENT_QUOTES, "UTF-8") ?></td>

        <td><?= htmlspecialchars($producto["marca"] ?? "", ENT_QUOTES, "UTF-8") ?></td>

        <td><?= htmlspecialchars($producto["categoria"] ?? "Sin categoría", ENT_QUOTES, "UTF-8") ?></td>

        <td>
            $<?= number_format((float)$producto["precio"], 2, ",", ".") ?>
        </td>

        <td><?= (int)$producto["stock"] ?></td>

        <td class="estado <?= $producto["activo"] ? "activo" : "inactivo" ?>">
            <?= $producto["activo"] ? "Activo" : "Inactivo" ?>
        </td>

        <td>
            <a class="boton"
               href="producto_editar.php?id=<?= (int)$producto["id"] ?>">
                Editar
            </a>
        </td>

        <td>
            <form method="POST" action="producto_estado.php"
                  onsubmit="return confirm('¿Querés cambiar la disponibilidad de este producto?');">

                <input type="hidden" name="csrf_token"
                       value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

                <input type="hidden" name="producto_id"
                       value="<?= (int)$producto['id'] ?>">

                <button type="submit" class="boton"
                        style="background: <?= $producto['activo'] ? '#a64a61' : '#16834a' ?>;">
                    <?= $producto['activo'] ? 'Desactivar' : 'Activar' ?>
                </button>
            </form>
        </td>
    </tr>
<?php endforeach; ?>

            <?php endif; ?>
            </tbody>
        </table>
    </div>

    
</main>
</body>
</html>
