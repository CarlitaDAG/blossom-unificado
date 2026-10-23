
<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../php/conexion.php";

function escapar($valor) {
    return htmlspecialchars((string)($valor ?? ""), ENT_QUOTES, "UTF-8");
}

$error = "";
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
}

if (!$id || $id < 1) {
    header("Location: productos.php");
    exit;
}

$stmt = $conexion->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$producto = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$producto) {
    http_response_code(404);
    exit("El producto no existe.");
}

$nombre = $producto["nombre"];
$marca = $producto["marca"] ?? "";
$categoriaId = $producto["categoria_id"] ?? "";
$precio = $producto["precio"];
$precioOferta = $producto["precio_oferta"] ?? "";
$descripcion = $producto["descripcion"] ?? "";
$caracteristicas = $producto["caracteristicas"] ?? "";
$imagen = $producto["imagen"] ?? "";
$stock = $producto["stock"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"] ?? "");
    $marca = trim($_POST["marca"] ?? "");
    $categoriaId = $_POST["categoria_id"] ?? "";
    $precio = $_POST["precio"] ?? "";
    $precioOferta = $_POST["precio_oferta"] ?? "";
    $descripcion = trim($_POST["descripcion"] ?? "");
    $caracteristicas = trim($_POST["caracteristicas"] ?? "");
    $imagen = trim($_POST["imagen"] ?? "");
    $stock = $_POST["stock"] ?? "";

    if (
        $nombre === "" ||
        mb_strlen($nombre) > 180 ||
        mb_strlen($marca) > 100 ||
        mb_strlen($imagen) > 255 ||
        !is_numeric($precio) ||
        !is_finite((float)$precio) ||
        (float)$precio < 0 ||
        filter_var($stock, FILTER_VALIDATE_INT) === false ||
        (int)$stock < 0
    ) {
        $error = "Revisá el nombre, la marca, la imagen, el precio y el stock.";
    } elseif (
        $precioOferta !== "" &&
        (!is_numeric($precioOferta) ||
         !is_finite((float)$precioOferta) ||
         (float)$precioOferta < 0)
    ) {
        $error = "El precio de oferta debe ser un número válido.";
    } elseif (
        $precioOferta !== "" &&
        (float)$precioOferta > (float)$precio
    ) {
        $error = "El precio de oferta no puede superar el precio normal.";
    } else {
        $categoriaId = $categoriaId === "" ? null : filter_var(
            $categoriaId,
            FILTER_VALIDATE_INT
        );

        if ($categoriaId === false || ($categoriaId !== null && $categoriaId < 1)) {
            $error = "La categoría seleccionada no es válida.";
        } elseif ($categoriaId !== null) {
            $cat = $conexion->prepare("SELECT id FROM categorias WHERE id = ?");
            $cat->bind_param("i", $categoriaId);
            $cat->execute();
            $categoriaExiste = $cat->get_result()->num_rows > 0;
            $cat->close();

            if (!$categoriaExiste) {
                $error = "La categoría seleccionada no existe.";
            }
        }

        if ($error === "") {
            $precio = (float)$precio;
            $precioOferta = $precioOferta === ""
                ? null
                : (float)$precioOferta;
            $stock = (int)$stock;

            try {
                $stmt = $conexion->prepare(
                    "UPDATE productos
                     SET nombre = ?, marca = ?, categoria_id = ?,
                         precio = ?, precio_oferta = ?, descripcion = ?,
                         caracteristicas = ?, imagen = ?, stock = ?
                     WHERE id = ?"
                );

                $stmt->bind_param(
                    "ssiddsssii",
                    $nombre,
                    $marca,
                    $categoriaId,
                    $precio,
                    $precioOferta,
                    $descripcion,
                    $caracteristicas,
                    $imagen,
                    $stock,
                    $id
                );

                $stmt->execute();
                $stmt->close();

                header("Location: productos.php?editado=1");
                exit;
            } catch (mysqli_sql_exception $e) {
                error_log("Error al editar producto: " . $e->getMessage());
                $error = "No se pudo actualizar el producto.";
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
    <title>Editar producto | Blossom</title>
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
    <h1>🌸 Editar producto</h1>
    <p>Modificá los datos del producto seleccionado.</p>

    <?php if ($error !== ""): ?>
        <p class="error"><?= escapar($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="id" value="<?= (int)$id ?>">

        <div class="campos">
            <div class="campo completo">
                <label for="nombre">Nombre del producto *</label>
                <input id="nombre" name="nombre" required maxlength="180"
                       value="<?= escapar($nombre) ?>">
            </div>

            <div class="campo">
                <label for="marca">Marca</label>
                <input id="marca" name="marca" maxlength="100"
                       value="<?= escapar($marca) ?>">
            </div>

            <div class="campo">
                <label for="categoria_id">Categoría</label>
                <select id="categoria_id" name="categoria_id">
                    <option value="">Sin categoría</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= (int)$categoria["id"] ?>"
                            <?= (string)$categoriaId === (string)$categoria["id"]
                                ? "selected" : "" ?>>
                            <?= escapar($categoria["nombre"]) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="precio">Precio normal *</label>
                <input type="number" id="precio" name="precio"
                       min="0" step="0.01" required value="<?= escapar($precio) ?>">
            </div>

            <div class="campo">
                <label for="precio_oferta">Precio de oferta</label>
                <input type="number" id="precio_oferta" name="precio_oferta"
                       min="0" step="0.01" value="<?= escapar($precioOferta) ?>">
            </div>

            <div class="campo">
                <label for="stock">Stock *</label>
                <input type="number" id="stock" name="stock"
                       min="0" step="1" required value="<?= escapar($stock) ?>">
            </div>

            <div class="campo completo">
                <label for="imagen">Ruta de la imagen</label>
                <input id="imagen" name="imagen" maxlength="255"
                       value="<?= escapar($imagen) ?>">
            </div>

            <div class="campo completo">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion"><?= escapar($descripcion) ?></textarea>
            </div>

            <div class="campo completo">
                <label for="caracteristicas">Características</label>
                <textarea id="caracteristicas" name="caracteristicas"><?= escapar($caracteristicas) ?></textarea>
            </div>
        </div>

        <div class="botones">
            <button class="boton" type="submit">Guardar cambios</button>
            <a class="boton secundario" href="productos.php">Cancelar</a>
        </div>
    </form>
</main>
</body>
</html>
