
<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../php/conexion.php";

$error = "";
$nombre = "";
$marca = "";
$categoriaId = "";
$precio = "";
$precioOferta = "";
$descripcion = "";
$caracteristicas = "";
$imagen = "";
$stock = "0";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"] ?? "");
    $marca = trim($_POST["marca"] ?? "");
    $categoriaId = $_POST["categoria_id"] ?? "";
    $precio = $_POST["precio"] ?? "";
    $precioOferta = $_POST["precio_oferta"] ?? "";
    $descripcion = trim($_POST["descripcion"] ?? "");
    $caracteristicas = trim($_POST["caracteristicas"] ?? "");
    $imagen = trim($_POST["imagen"] ?? "");
    $stock = $_POST["stock"] ?? "0";

    if (
        $nombre === "" ||
        !is_numeric($precio) ||
        (float)$precio < 0 ||
        filter_var($stock, FILTER_VALIDATE_INT) === false ||
        (int)$stock < 0
    ) {
        $error = "Completá el nombre y los valores de precio y stock correctamente.";
    } elseif (
        $precioOferta !== "" &&
        (!is_numeric($precioOferta) || (float)$precioOferta < 0)
    ) {
        $error = "El precio de oferta debe ser un número válido.";
    } else {
        $categoriaId = $categoriaId === "" ? null : (int)$categoriaId;
        $precio = (float)$precio;
        $precioOferta = $precioOferta === "" ? null : (float)$precioOferta;
        $stock = (int)$stock;

        if ($precioOferta !== null && $precioOferta > $precio) {
            $error = "El precio de oferta no puede superar el precio normal.";
        } else {
            try {
                $stmt = $conexion->prepare(
                    "INSERT INTO productos
                    (nombre, marca, categoria_id, precio, precio_oferta,
                     descripcion, caracteristicas, imagen, stock)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );

                $stmt->bind_param(
                    "ssiddsssi",
                    $nombre,
                    $marca,
                    $categoriaId,
                    $precio,
                    $precioOferta,
                    $descripcion,
                    $caracteristicas,
                    $imagen,
                    $stock
                );

                $stmt->execute();

                header("Location: productos.php?guardado=1");
                exit;

            } catch (mysqli_sql_exception $e) {
                error_log("Error al guardar producto: " . $e->getMessage());
                $error = "No se pudo guardar el producto. Revisá los datos e intentá nuevamente.";
            }
        }
    }
}

$categorias = $conexion->query(
    "SELECT id, nombre FROM categorias ORDER BY nombre"
)->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo producto | Blossom</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 28px 16px;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #f5f6ff, #fff0f5);
            color: #27304a;
        }

        .formulario {
            max-width: 760px;
            margin: auto;
            padding: 30px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 12px 35px #30305012;
        }

        h1 { color: #305cde; }

        .campos {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .campo { min-width: 0; }
        .completo { grid-column: 1 / -1; }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #dcdced;
            border-radius: 9px;
            font: inherit;
        }

        textarea { min-height: 90px; resize: vertical; }

        .botones {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 24px;
        }

        .boton {
            display: inline-block;
            padding: 12px 18px;
            border: 0;
            border-radius: 9px;
            background: #305cde;
            color: white;
            text-decoration: none;
            cursor: pointer;
            font: inherit;
        }

        .secundario { background: #eeeaf7; color: #453c66; }

        .error {
            padding: 12px;
            background: #fff0f0;
            color: #a32335;
            border-radius: 9px;
        }

        @media (max-width: 560px) {
            .campos { grid-template-columns: 1fr; }
            .completo { grid-column: auto; }
            .formulario { padding: 22px; }
        }
    </style>
</head>
<body>
<main class="formulario">
    <h1>🌸 Nuevo producto</h1>
    <p>Completá los datos para agregar un producto a Blossom.</p>

    <?php if ($error !== ""): ?>
        <p class="error"><?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?></p>
    <?php endif; ?>

    <form method="POST">
        <div class="campos">
            <div class="campo completo">
                <label for="nombre">Nombre del producto *</label>
                <input id="nombre" name="nombre" required maxlength="180"
                       value="<?= htmlspecialchars($nombre, ENT_QUOTES, "UTF-8") ?>">
            </div>

            <div class="campo">
                <label for="marca">Marca</label>
                <input id="marca" name="marca" maxlength="100"
                       value="<?= htmlspecialchars($marca, ENT_QUOTES, "UTF-8") ?>">
            </div>

            <div class="campo">
                <label for="categoria_id">Categoría</label>
                <select id="categoria_id" name="categoria_id">
                    <option value="">Sin categoría</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= (int)$categoria["id"] ?>"
                            <?= (string)$categoriaId === (string)$categoria["id"] ? "selected" : "" ?>>
                            <?= htmlspecialchars($categoria["nombre"], ENT_QUOTES, "UTF-8") ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="precio">Precio normal *</label>
                <input type="number" id="precio" name="precio"
                       min="0" step="0.01" required
                       value="<?= htmlspecialchars((string)$precio, ENT_QUOTES, "UTF-8") ?>">
            </div>

            <div class="campo">
                <label for="precio_oferta">Precio de oferta (opcional)</label>
                <input type="number" id="precio_oferta" name="precio_oferta"
                       min="0" step="0.01"
                       value="<?= htmlspecialchars((string)($precioOferta ?? ""), ENT_QUOTES, "UTF-8") ?>">
            </div>

            <div class="campo">
                <label for="stock">Stock *</label>
                <input type="number" id="stock" name="stock"
                       min="0" step="1" required
                       value="<?= htmlspecialchars((string)$stock, ENT_QUOTES, "UTF-8") ?>">
            </div>

            <div class="campo completo">
                <label for="imagen">Ruta de la imagen</label>
                <input id="imagen" name="imagen" maxlength="255"
                       placeholder="../img/productos/ejemplo.jpg"
                       value="<?= htmlspecialchars($imagen, ENT_QUOTES, "UTF-8") ?>">
            </div>

            <div class="campo completo">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion"><?= htmlspecialchars($descripcion, ENT_QUOTES, "UTF-8") ?></textarea>
            </div>

            <div class="campo completo">
                <label for="caracteristicas">Características</label>
                <textarea id="caracteristicas" name="caracteristicas"><?= htmlspecialchars($caracteristicas, ENT_QUOTES, "UTF-8") ?></textarea>
            </div>
        </div>

        <div class="botones">
            <button class="boton" type="submit">Guardar producto</button>
            <a class="boton secundario" href="productos.php">Cancelar</a>
        </div>
    </form>
</main>
</body>
</html>
