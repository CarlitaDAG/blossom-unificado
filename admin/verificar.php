
<?php

session_start();
require_once __DIR__ . "/../php/conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

$usuario = trim($_POST["usuario"] ?? "");
$contrasena = $_POST["contrasena"] ?? "";

$stmt = $conexion->prepare(
    "SELECT id, usuario, nombre, password_hash
     FROM administradores
     WHERE usuario = ? AND activo = 1
     LIMIT 1"
);

$stmt->bind_param("s", $usuario);
$stmt->execute();

$admin = $stmt->get_result()->fetch_assoc();

if (!$admin || !password_verify($contrasena, $admin["password_hash"])) {
    $_SESSION["login_error"] = "Usuario o contraseña incorrectos.";
    header("Location: login.php");
    exit;
}

session_regenerate_id(true);

$_SESSION["admin_id"] = $admin["id"];
$_SESSION["admin_usuario"] = $admin["usuario"];
$_SESSION["admin_nombre"] = $admin["nombre"];

header("Location: panel.php");
exit;
