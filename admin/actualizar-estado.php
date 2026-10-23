
<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../php/conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: pedidos.php");
    exit;
}

$pedidoId = filter_input(INPUT_POST, "pedido_id", FILTER_VALIDATE_INT);
$estado = $_POST["estado"] ?? "";

$estadosPermitidos = [
    "Pendiente",
    "Confirmado",
    "Preparando",
    "Enviado",
    "Entregado",
    "Cancelado"
];

if (!$pedidoId || !in_array($estado, $estadosPermitidos, true)) {
    http_response_code(400);
    exit("El pedido o el estado seleccionado no son válidos.");
}

try {
    $consulta = $conexion->prepare(
        "UPDATE pedidos SET estado = ? WHERE id = ?"
    );

    $consulta->bind_param("si", $estado, $pedidoId);
    $consulta->execute();
    $consulta->close();

    header("Location: detalle-pedido.php?id=" . $pedidoId . "&actualizado=1");
    exit;

} catch (Throwable $e) {
    error_log("Error al actualizar estado del pedido: " . $e->getMessage());
    http_response_code(500);
    exit("No se pudo actualizar el estado. Revisá el registro de errores.");
}
