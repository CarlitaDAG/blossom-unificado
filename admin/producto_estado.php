
<?php
session_start();

// Verificar que el administrador haya iniciado sesión.
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

// Conectar con la base de datos.
require_once __DIR__ . "/../php/conexion.php";

// Solo aceptar solicitudes POST.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: productos.php");
    exit;
}

// Validar el token de seguridad CSRF.
$token = $_POST["csrf_token"] ?? "";

if (
    !is_string($token) ||
    empty($_SESSION["csrf_token"]) ||
    !hash_equals($_SESSION["csrf_token"], $token)
) {
    http_response_code(403);
    exit("Solicitud no válida. Volvé al listado e intentá nuevamente.");
}

// Validar el identificador del producto.
$id = filter_input(INPUT_POST, "producto_id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit("El producto seleccionado no es válido.");
}

try {
    // Iniciar una transacción para cambiar el estado.
    $conexion->begin_transaction();

    // Consultar el estado actual del producto.
    $stmt = $conexion->prepare(
        "SELECT activo FROM productos WHERE id = ? FOR UPDATE"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $producto = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$producto) {
        $conexion->rollback();
        http_response_code(404);
        exit("El producto no existe.");
    }

    // Cambiar de activo a inactivo, o viceversa.
    $nuevoEstado = ((int)$producto["activo"] === 1) ? 0 : 1;

    $stmt = $conexion->prepare(
        "UPDATE productos SET activo = ? WHERE id = ?"
    );

    $stmt->bind_param("ii", $nuevoEstado, $id);
    $stmt->execute();
    $stmt->close();

    // Confirmar el cambio en MySQL.
    $conexion->commit();

    // Regresar al listado de productos.
    header("Location: productos.php?estado_actualizado=1");
    exit;

} catch (Throwable $e) {
    try {
        $conexion->rollback();
    } catch (Throwable $ignorado) {
        // La transacción podría no estar activa.
    }

    error_log("Error al cambiar estado del producto: " . $e->getMessage());

    http_response_code(500);
    exit("No se pudo cambiar el estado del producto.");
}
