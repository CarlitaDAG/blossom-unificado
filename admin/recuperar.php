
<?php
session_start();

$usuario = "";
$mostrarFormulario = false;
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST["usuario"] ?? "");

    if (isset($_POST["continuar"])) {
        if ($usuario === "adminblossom") {
            $mostrarFormulario = true;
        } else {
            $mensaje = "Ingresá el usuario administrativo correcto para continuar.";
        }
    }

    if (isset($_POST["solicitar"])) {
        if ($usuario === "adminblossom") {
            $mostrarFormulario = true;
            $mensaje = "Esta es una demostración visual. La recuperación todavía no está habilitada.";
        } else {
            $mensaje = "Volvé a ingresar el usuario administrativo.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña | Blossom</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            font-family: Arial, sans-serif;
            color: #18385f;
            background:
                radial-gradient(ellipse at top left, #ff7668 0 18%, transparent 18.2%),
                radial-gradient(ellipse at bottom right, #2867e8 0 20%, transparent 20.2%),
                #fff3f0;
        }

        .tarjeta {
            width: 100%;
            max-width: 560px;
            padding: 36px;
            background: white;
            border-radius: 24px;
            box-shadow: 0 18px 55px #27355c1f;
        }

        .logo {
            margin: 0 0 28px;
            text-align: center;
            color: #2861d8;
            font-size: 35px;
        }

        .logo span {
            color: #ff6f61;
        }

        h2 {
            margin: 0 0 14px;
            text-align: center;
            font-size: 24px;
        }

        p {
            color: #75839a;
            line-height: 1.6;
        }

        label {
            display: block;
            margin: 20px 0 9px;
            font-weight: bold;
            font-size: 14px;
        }

        input {
            display: block;
            width: 100%;
            min-height: 48px;
            padding: 12px 14px;
            border: 1px solid #dce4f2;
            border-radius: 12px;
            background: #fff;
            color: #18385f;
            font-size: 16px;
            outline: none;
        }

        input:focus {
            border-color: #305cde;
            box-shadow: 0 0 0 3px #305cde18;
        }

        button {
            width: 100%;
            min-height: 49px;
            margin-top: 20px;
            padding: 14px;
            border: none;
            border-radius: 12px;
            background: #ff6f61;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #f45f53;
        }

        .mensaje {
            padding: 12px;
            border-radius: 10px;
            background: #fff0f5;
            color: #a33e75;
            font-size: 13px;
        }

        .volver {
            display: block;
            margin-top: 24px;
            text-align: center;
            color: #305cde;
            text-decoration: none;
        }

        .volver:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .tarjeta {
                padding: 28px 20px;
            }
        }
    </style>
</head>
<body>

<main class="tarjeta">

    <h1 class="logo">Blossom<span>.</span></h1>

    <?php if (!$mostrarFormulario): ?>

        <h2>¿Olvidaste tu contraseña?</h2>

        <p>
            Ingresá tu usuario administrativo para continuar
            con la recuperación de acceso.
        </p>

        <?php if ($mensaje !== ""): ?>
            <p class="mensaje">
                <?= htmlspecialchars($mensaje, ENT_QUOTES, "UTF-8") ?>
            </p>
        <?php endif; ?>

        <form method="POST">
            <label for="usuario">Usuario administrativo</label>

            <input
                type="text"
                id="usuario"
                name="usuario"
                value="<?= htmlspecialchars($usuario, ENT_QUOTES, "UTF-8") ?>"
                autocomplete="username"
                maxlength="60"
                required
            >

            <button type="submit" name="continuar">
                Continuar
            </button>
        </form>

    <?php else: ?>

        <h2>Recuperación de contraseña</h2>

        <p>
            Ingresá tu correo electrónico alternativo para
            recuperar el acceso a tu cuenta administrativa.
        </p>

        <?php if ($mensaje !== ""): ?>
            <p class="mensaje">
                <?= htmlspecialchars($mensaje, ENT_QUOTES, "UTF-8") ?>
            </p>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="usuario" value="adminblossom">

            <label for="correo">Correo electrónico alternativo</label>

            <input
                type="email"
                id="correo"
                name="correo"
                placeholder="Ingresá tu correo electrónico"
                autocomplete="off"
            >

            <label for="confirmar_correo">Confirmar correo electrónico</label>

            <input
                type="email"
                id="confirmar_correo"
                name="confirmar_correo"
                placeholder="Volvé a ingresar tu correo"
                autocomplete="off"
            >

            <button type="submit" name="solicitar">
                Solicitar recuperación
            </button>
        </form>

    <?php endif; ?>

    <a class="volver" href="login.php">
        ← Volver al inicio de sesión
    </a>

</main>

</body>
</html>
