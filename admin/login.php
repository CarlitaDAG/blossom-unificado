
<?php
session_start();

if (isset($_SESSION["admin_id"])) {
    header("Location: panel.php");
    exit;
}

$error = $_SESSION["login_error"] ?? "";
unset($_SESSION["login_error"]);
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso administrativo | Blossom</title>
    <link rel="stylesheet" href="../css/style.css">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            padding: 35px 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            color: #18385f;
            background:
                radial-gradient(ellipse at 0% 0%,
                    #ff7668 0%, #ff7668 23%, transparent 23.2%),
                radial-gradient(ellipse at 100% 100%,
                    #2867e8 0%, #2867e8 25%, transparent 25.2%),
                linear-gradient(135deg, #fff0eb, #fff8f6 55%, #fce8e8);
            position: relative;
            overflow-x: hidden;
        }

        .login-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 590px;
            padding: 42px 44px 30px;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.97);
            box-shadow: 0 18px 55px rgba(39, 53, 92, 0.12);
            text-align: center;
        }

        .logo-icono {
            width: 150px;
            height: 130px;
            margin: 0 auto 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ff6f61;
        }

        
     
.logo {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    margin: 0 auto 24px;
}

.logo-imagen {
    display: block;
    width: 300px !important;
    max-width: 100%;
    height: auto;
    object-fit: contain;
}

        .logo span {
            color: #ff6f61;
        }

        .eslogan {
            margin: 5px 0 30px;
            color: #4274b8;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 3px;
        }

        h2 {
            margin: 0 0 9px;
            font-size: clamp(23px, 4vw, 31px);
            color: #18385f;
        }

        .subtitulo {
            margin: 0 0 28px;
            color: #75839a;
            font-size: 16px;
            line-height: 1.5;
        }

        .campo {
            position: relative;
            margin-bottom: 16px;
        }

        .campo .icono {
            position: absolute;
            left: 19px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 21px;
            color: #71829b;
            pointer-events: none;
        }

        .campo input {
            width: 100%;
            min-height: 58px;
            padding: 16px 17px 16px 58px;
            border: 1px solid #e6eaf3;
            border-radius: 17px;
            background: #f5f7fb;
            color: #273b59;
            font-size: 16px;
            transition: 0.2s;
        }

        .campo input::placeholder {
            color: #8190a7;
        }

        .campo input:focus {
            outline: none;
            border-color: #668be9;
            background: white;
            box-shadow: 0 0 0 4px rgba(48, 92, 222, 0.10);
        }

        .boton-ingresar {
            width: 100%;
            min-height: 60px;
            margin-top: 12px;
            padding: 15px 20px;
            border: none;
            border-radius: 17px;
            background: linear-gradient(100deg, #ff7968, #ff5e50);
            color: white;
            font-size: 17px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 7px 18px rgba(255, 111, 97, 0.14);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .boton-ingresar:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 23px rgba(255, 111, 97, 0.28);
        }

        .boton-ingresar span {
            margin-left: 10px;
            font-size: 23px;
            vertical-align: -2px;
        }

        .error {
            padding: 13px;
            margin-bottom: 20px;
            border: 1px solid #ffd5d9;
            border-radius: 12px;
            background: #fff0f2;
            color: #a32335;
            text-align: left;
            font-size: 14px;
        }

        .volver {
            display: inline-block;
            margin-top: 24px;
            color: #3972c8;
            font-size: 14px;
            text-decoration: none;
        }

        .volver:hover {
            color: #ff6f61;
            text-decoration: underline;
        }

        .pie-login {
            margin: 28px 0 0;
            padding-top: 24px;
            border-top: 1px solid #edf0f5;
            color: #78859a;
            font-size: 14px;
        }

        @media (max-width: 480px) {
            body {
                padding: 24px 14px;
            }

            .login-card {
                padding: 30px 22px 25px;
                border-radius: 24px;
            }

            .logo-icono {
                width: 115px;
                height: 100px;
            }

            .eslogan {
                font-size: 9px;
                letter-spacing: 2px;
            }

            .campo input {
                min-height: 54px;
                font-size: 15px;
            }

            .boton-ingresar {
                min-height: 55px;
            }
        }
        
.recuperar {
    display: flex;
    justify-content: flex-end;
    margin: 0 2px 14px;
}

.recuperar a {
    color: #3972c8;
    font-size: 14px;
    text-decoration: none;
    transition: color 0.2s;
}

.recuperar a:hover {
    color: #ff6f61;
    text-decoration: underline;
}

    </style>
</head>

<body>
    <main class="login-card">

       

        <div class="logo">
    <img
        src="../img/logo/logo-blossom.png"
        alt="Blossom - Tu hogar, en un solo clic"
        class="logo-imagen"
    >
</div> 
        

        <h2>Panel de Administrador</h2>
        <p class="subtitulo">
            Iniciá sesión para gestionar tu tienda.
        </p>

        <?php if ($error !== ""): ?>
            <p class="error">
                <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?>
            </p>
        <?php endif; ?>

        <form action="verificar.php" method="POST">
            <div class="campo">
                <span class="icono" aria-hidden="true">✉</span>
                <input
                    type="text"
                    id="usuario"
                    name="usuario"
                    placeholder="Usuario o correo electrónico"
                    autocomplete="username"
                    aria-label="Usuario o correo electrónico"
                    required
                >
            </div>

            <div class="campo">
                <span class="icono" aria-hidden="true">♙</span>
                <input
                    type="password"
                    id="contrasena"
                    name="contrasena"
                    placeholder="Contraseña"
                    autocomplete="current-password"
                    aria-label="Contraseña"
                    required
                >
            </div>

<div class="recuperar">
    <a href="recuperar.php">¿Olvidaste tu contraseña?</a>
</div>


            <button class="boton-ingresar" type="submit">
                Iniciar sesión <span aria-hidden="true">→</span>
            </button>
        </form>

        <a class="volver" href="../index.html">
            ← Volver a la tienda
        </a>

        <p class="pie-login">
            Blossom • Panel de Administración
        </p>
    </main>
</body>
</html>
