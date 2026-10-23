
<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

$nombre = htmlspecialchars(
    $_SESSION["admin_nombre"],
    ENT_QUOTES,
    "UTF-8"
);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel administrativo | Blossom</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f7f7fc;
            color: #27304a;
        }

        header {
            padding: 22px 6%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            background: white;
            box-shadow: 0 3px 15px #30305010;
        }

        .logo {
    margin: 0;
    display: flex;
    align-items: center;
}

.logo img {
    display: block;
    width: 150px;
    max-width: 100%;
    height: auto;
    object-fit: contain;
}

        .contenedor {
            width: min(1100px, 90%);
            margin: 42px auto;
        }

        .bienvenida {
            padding: 28px;
            border-radius: 20px;
            background: linear-gradient(120deg, #eaf0ff, #fff0f5);
        }

        .tarjetas {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 22px;
            margin-top: 28px;
        }

        .tarjeta {
            padding: 26px;
            background: white;
            border: 1px solid #eeeaf7;
            border-radius: 18px;
            box-shadow: 0 8px 24px #3030500c;
        }

        .tarjeta h2 { margin-top: 0; }

        .boton {
            display: inline-block;
            margin-top: 10px;
            padding: 12px 18px;
            border-radius: 9px;
            background: #305cde;
            color: white;
            text-decoration: none;
        }

        .salir { color: #b04477; text-decoration: none; }

        
        /* GRÁFICO DE PRODUCTOS MÁS VENDIDOS */
        .grafico-panel {
            margin-top: 30px;
            padding: 28px;
            background: #ffffff;
            border: 1px solid #eeeaf7;
            border-radius: 18px;
            box-shadow: 0 8px 24px #3030500c;
        }

        .grafico-encabezado {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 24px;
        }

        .grafico-encabezado h2 {
            margin: 0 0 8px;
            color: #305cde;
        }

        .etiqueta-demo {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 20px;
            background: #fff0f5;
            color: #a33e75;
            font-size: 12px;
            font-weight: bold;
        }

        .lista-ventas {
            display: grid;
            gap: 20px;
        }

        .venta-producto {
            display: grid;
            gap: 8px;
        }

        .venta-detalle {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .venta-nombre {
            font-weight: bold;
            font-size: 14px;
        }

        .venta-cantidad {
            color: #305cde;
            font-weight: bold;
            white-space: nowrap;
        }

        .barra-fondo {
            width: 100%;
            height: 14px;
            overflow: hidden;
            background: #edf0fa;
            border-radius: 20px;
        }

        .barra-valor {
            height: 100%;
            width: 0;
            border-radius: 20px;
            background: #305cde;
            transition: width 0.3s ease;
        }

        .venta-producto:nth-child(even) .barra-valor {
            background: #ff6f61;
        }

        .controles-ventas {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .control-venta {
            width: 30px;
            height: 30px;
            border: none;
            border-radius: 8px;
            background: #eef1ff;
            color: #305cde;
            font-size: 18px;
            cursor: pointer;
        }

        .control-venta:hover {
            background: #dce4ff;
        }

        .nota-demo {
            margin-top: 22px;
            color: #77758c;
            font-size: 12px;
        }

        @media (max-width: 600px) {
            .grafico-panel {
                padding: 18px;
            }

            .venta-nombre {
                font-size: 13px;
            }
        }


        /* DOS PANELES INFERIORES */
        .paneles-inferiores {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1.35fr);
            gap: 22px;
            margin-top: 24px;
            align-items: stretch;
        }

        .panel-demo {
            min-width: 0;
            padding: 24px;
            background: #fff;
            border: 1px solid #eeeaf7;
            border-radius: 18px;
            box-shadow: 0 8px 24px #3030500c;
        }

        .panel-demo h2 {
            margin: 0 0 8px;
            color: #305cde;
            font-size: 21px;
        }

        .panel-subtitulo {
            margin: 0 0 22px;
            color: #77758c;
            font-size: 13px;
            line-height: 1.5;
        }

        /* GRÁFICO CIRCULAR */
        .grafico-circular-contenedor {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 24px;
            flex-wrap: wrap;
        }

        .grafico-circular {
            width: 175px;
            height: 175px;
            flex-shrink: 0;
            border-radius: 50%;
            background: conic-gradient(
                #305cde 0% 28%,
                #b78bea 28% 50%,
                #ff6f61 50% 68%,
                #f3a18f 68% 83%,
                #65a9f8 83% 93%,
                #42bdeB 93% 100%
            );
            display: grid;
            place-items: center;
        }

        .grafico-circular-centro {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            gap: 5px;
        }

        .grafico-circular-centro span {
            font-size: 12px;
            color: #77758c;
        }

        .grafico-circular-centro strong {
            font-size: 18px;
            color: #27304a;
        }

        .leyenda-categorias {
            flex: 1;
            min-width: 150px;
            display: grid;
            gap: 13px;
        }

        .categoria-demo {
            display: grid;
            grid-template-columns: 10px minmax(0, 1fr) auto;
            align-items: center;
            gap: 8px;
            font-size: 12px;
        }

        .punto-categoria {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .porcentaje-categoria {
            font-weight: bold;
            color: #305cde;
        }

        /* TABLA DE STOCK */
        .tabla-stock-contenedor {
            overflow-x: auto;
        }

        .tabla-stock {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .tabla-stock th {
            background: #f3f5ff;
            color: #27304a;
            text-align: left;
            padding: 11px 8px;
        }

        .tabla-stock td {
            padding: 12px 8px;
            border-bottom: 1px solid #eeeaf7;
            vertical-align: middle;
        }

        .tabla-stock tbody tr:last-child td {
            border-bottom: none;
        }

        .stock-cantidad {
            display: inline-block;
            min-width: 28px;
            padding: 6px;
            text-align: center;
            border-radius: 8px;
            background: #fff0f5;
            color: #d9485f;
            font-weight: bold;
        }

        .stock-cantidad.stock-medio {
            background: #fff1d6;
            color: #a56800;
        }

        .stock-cantidad.stock-normal {
            background: #e8f7ee;
            color: #21804b;
        }

        .stock-controles {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .stock-boton {
            width: 27px;
            height: 27px;
            border: none;
            border-radius: 7px;
            background: #eef1ff;
            color: #305cde;
            font-size: 17px;
            cursor: pointer;
        }

        .stock-boton:hover {
            background: #dce4ff;
        }

        .stock-alerta {
            display: block;
            margin-top: 5px;
            color: #d9485f;
            font-size: 10px;
            font-weight: bold;
        }

        .nota-stock {
            margin: 16px 0 0;
            color: #77758c;
            font-size: 11px;
            line-height: 1.5;
        }

        @media (max-width: 900px) {
            .paneles-inferiores {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .panel-demo {
                padding: 16px;
            }

            .grafico-circular {
                width: 150px;
                height: 150px;
            }

            .grafico-circular-centro {
                width: 88px;
                height: 88px;
            }

            .tabla-stock {
                min-width: 410px;
            }
        }


    </style>
</head>
<body>
    <header>
        <h1 class="logo">
    <img src="../img/logo/logo-blossom.png" alt="Blossom - Tu hogar, en un solo clic">
</h1>
        <a class="salir" href="logout.php">Cerrar sesión</a>
    </header>

    <main class="contenedor">
        <section class="bienvenida">
            <h2>¡Bienvenido/a, <?= $nombre ?>! 🌸</h2>
            <p>Desde aquí podrás administrar tu tienda Blossom.</p>
        </section>

        <section class="tarjetas">
            <article class="tarjeta">
                <h2>📦 Productos</h2>
                <p>Consultar y administrar los productos de la tienda.</p>
                <a class="boton" href="productos.php">Gestionar productos</a>
            </article>

            <article class="tarjeta">
                <h2>🛍️ Pedidos</h2>
                <p>Consultar las compras registradas y su estado.</p>
                <a class="boton" href="pedidos.php">Ver pedidos</a>
            </article>
        </section>

        
    

        <!-- GRÁFICO DE DEMOSTRACIÓN -->
        <section class="grafico-panel">
            <div class="grafico-encabezado">
                <div>
                    <h2>⭐ Productos más vendidos</h2>
                    <p>Resumen mensual de ventas de Blossom.</p>
                </div>
                <span class="etiqueta-demo">
                    DATOS DE DEMOSTRACIÓN
                </span>
            </div>

            <div class="lista-ventas" id="listaVentas">

                <div class="venta-producto" data-venta="24">
                    <div class="venta-detalle">
                        <span class="venta-nombre">
                            Cocina Nova 4 Hornallas
                        </span>
                        <div class="controles-ventas">
                            <span class="venta-cantidad">24 unidades</span>
                            <button class="control-venta" type="button"
                                aria-label="Aumentar ventas de Cocina Nova"
                                data-cambio="1">+</button>
                            <button class="control-venta" type="button"
                                aria-label="Disminuir ventas de Cocina Nova"
                                data-cambio="-1">−</button>
                        </div>
                    </div>
                    <div class="barra-fondo">
                        <div class="barra-valor" style="width: 80%"></div>
                    </div>
                </div>

                <div class="venta-producto" data-venta="18">
                    <div class="venta-detalle">
                        <span class="venta-nombre">
                            Microondas Stars 20L
                        </span>
                        <div class="controles-ventas">
                            <span class="venta-cantidad">18 unidades</span>
                            <button class="control-venta" type="button"
                                aria-label="Aumentar ventas de Microondas Stars"
                                data-cambio="1">+</button>
                            <button class="control-venta" type="button"
                                aria-label="Disminuir ventas de Microondas Stars"
                                data-cambio="-1">−</button>
                        </div>
                    </div>
                    <div class="barra-fondo">
                        <div class="barra-valor" style="width: 60%"></div>
                    </div>
                </div>

                <div class="venta-producto" data-venta="15">
                    <div class="venta-detalle">
                        <span class="venta-nombre">
                            Cocina Silma 5 Hornallas
                        </span>
                        <div class="controles-ventas">
                            <span class="venta-cantidad">15 unidades</span>
                            <button class="control-venta" type="button"
                                aria-label="Aumentar ventas de Cocina Silma"
                                data-cambio="1">+</button>
                            <button class="control-venta" type="button"
                                aria-label="Disminuir ventas de Cocina Silma"
                                data-cambio="-1">−</button>
                        </div>
                    </div>
                    <div class="barra-fondo">
                        <div class="barra-valor" style="width: 50%"></div>
                    </div>
                </div>

                <div class="venta-producto" data-venta="12">
                    <div class="venta-detalle">
                        <span class="venta-nombre">
                            Heladera Nova Fresh 320L
                        </span>
                        <div class="controles-ventas">
                            <span class="venta-cantidad">12 unidades</span>
                            <button class="control-venta" type="button"
                                aria-label="Aumentar ventas de Heladera Nova"
                                data-cambio="1">+</button>
                            <button class="control-venta" type="button"
                                aria-label="Disminuir ventas de Heladera Nova"
                                data-cambio="-1">−</button>
                        </div>
                    </div>
                    <div class="barra-fondo">
                        <div class="barra-valor" style="width: 40%"></div>
                    </div>
                </div>

                <div class="venta-producto" data-venta="9">
                    <div class="venta-detalle">
                        <span class="venta-nombre">
                            Lavarropas Nova Wash
                        </span>
                        <div class="controles-ventas">
                            <span class="venta-cantidad">9 unidades</span>
                            <button class="control-venta" type="button"
                                aria-label="Aumentar ventas de Lavarropas Nova"
                                data-cambio="1">+</button>
                            <button class="control-venta" type="button"
                                aria-label="Disminuir ventas de Lavarropas Nova"
                                data-cambio="-1">−</button>
                        </div>
                    </div>
                    <div class="barra-fondo">
                        <div class="barra-valor" style="width: 30%"></div>
                    </div>
                </div>

            </div>

            <p class="nota-demo">
                Las cantidades son ficticias y sirven únicamente para
                demostrar el funcionamiento del panel. Los cambios no se
                guardan en la base de datos.
            </p>
        
        </section>

        <!-- PANELES INFERIORES -->
        <section class="paneles-inferiores">

            <!-- VENTAS POR CATEGORÍA -->
            <article class="panel-demo">
                <h2>📊 Ventas por categoría</h2>
                <p class="panel-subtitulo">
                    Distribución porcentual de ejemplo.
                </p>

                <div class="grafico-circular-contenedor">
                    <div class="grafico-circular"
                         role="img"
                         aria-label="Gráfico circular con porcentajes de demostración por categoría">
                        <div class="grafico-circular-centro">
                            <span>Total de ejemplo</span>
                            <strong>100%</strong>
                        </div>
                    </div>

                    <div class="leyenda-categorias">
                        <div class="categoria-demo">
                            <span class="punto-categoria" style="background:#305cde"></span>
                            <span>Refrigeración</span>
                            <span class="porcentaje-categoria">28%</span>
                        </div>

                        <div class="categoria-demo">
                            <span class="punto-categoria" style="background:#b78bea"></span>
                            <span>Cocina</span>
                            <span class="porcentaje-categoria">22%</span>
                        </div>

                        <div class="categoria-demo">
                            <span class="punto-categoria" style="background:#ff6f61"></span>
                            <span>Lavado</span>
                            <span class="porcentaje-categoria">18%</span>
                        </div>

                        <div class="categoria-demo">
                            <span class="punto-categoria" style="background:#f3a18f"></span>
                            <span>Pequeños electrodomésticos</span>
                            <span class="porcentaje-categoria">15%</span>
                        </div>

                        <div class="categoria-demo">
                            <span class="punto-categoria" style="background:#65a9f8"></span>
                            <span>Limpieza</span>
                            <span class="porcentaje-categoria">10%</span>
                        </div>

                        <div class="categoria-demo">
                            <span class="punto-categoria" style="background:#42bdeb"></span>
                            <span>Climatización</span>
                            <span class="porcentaje-categoria">7%</span>
                        </div>
                    </div>
                </div>

                <p class="nota-stock">
                    Datos ficticios para la demostración. No representan
                    ventas reales de Blossom.
                </p>
            </article>

            <!-- PRODUCTOS CON POCO STOCK -->
            <article class="panel-demo">
                <h2>📦 Productos con poco stock</h2>
                <p class="panel-subtitulo">
                    Control manual del inventario de demostración.
                </p>

                <div class="tabla-stock-contenedor">
                    <table class="tabla-stock">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Stock</th>
                                <th>Modificar</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr data-stock="3">
                                <td>
                                    Aire Acondicionado Powerfull 4500F
                                    <span class="stock-alerta"></span>
                                </td>
                                <td>
                                    <span class="stock-cantidad">3</span>
                                </td>
                                <td>
                                    <div class="stock-controles">
                                        <button class="stock-boton"
                                            type="button"
                                            data-stock-cambio="1"
                                            aria-label="Aumentar stock">+</button>
                                        <button class="stock-boton"
                                            type="button"
                                            data-stock-cambio="-1"
                                            aria-label="Disminuir stock">−</button>
                                    </div>
                                </td>
                            </tr>

                            <tr data-stock="4">
                                <td>
                                    Aire Acondicionado Aura 3000F
                                    <span class="stock-alerta"></span>
                                </td>
                                <td>
                                    <span class="stock-cantidad">4</span>
                                </td>
                                <td>
                                    <div class="stock-controles">
                                        <button class="stock-boton" type="button" data-stock-cambio="1" aria-label="Aumentar stock">+</button>
                                        <button class="stock-boton" type="button" data-stock-cambio="-1" aria-label="Disminuir stock">−</button>
                                    </div>
                                </td>
                            </tr>

                            <tr data-stock="4">
                                <td>
                                    Lavarropas Powerfull 10 kg
                                    <span class="stock-alerta"></span>
                                </td>
                                <td>
                                    <span class="stock-cantidad">4</span>
                                </td>
                                <td>
                                    <div class="stock-controles">
                                        <button class="stock-boton" type="button" data-stock-cambio="1" aria-label="Aumentar stock">+</button>
                                        <button class="stock-boton" type="button" data-stock-cambio="-1" aria-label="Disminuir stock">−</button>
                                    </div>
                                </td>
                            </tr>

                            <tr data-stock="1">
                                <td>
                                    Freezer SuperW 300L
                                    <span class="stock-alerta"></span>
                                </td>
                                <td>
                                    <span class="stock-cantidad">1</span>
                                </td>
                                <td>
                                    <div class="stock-controles">
                                        <button class="stock-boton" type="button" data-stock-cambio="1" aria-label="Aumentar stock">+</button>
                                        <button class="stock-boton" type="button" data-stock-cambio="-1" aria-label="Disminuir stock">−</button>
                                    </div>
                                </td>
                            </tr>

                            <tr data-stock="4">
                                <td>
                                    Heladera Powerfull No Frost 400L
                                    <span class="stock-alerta"></span>
                                </td>
                                <td>
                                    <span class="stock-cantidad">4</span>
                                </td>
                                <td>
                                    <div class="stock-controles">
                                        <button class="stock-boton" type="button" data-stock-cambio="1" aria-label="Aumentar stock">+</button>
                                        <button class="stock-boton" type="button" data-stock-cambio="-1" aria-label="Disminuir stock">−</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p class="nota-stock">
                    Datos de demostración. Los cambios son temporales
                    y no modifican el inventario real de MySQL.
                </p>
            </article>

        </section>

    </main>


    <script>
        document.querySelectorAll(".venta-producto").forEach(producto => {
            producto.querySelectorAll(".control-venta").forEach(boton => {
                boton.addEventListener("click", () => {
                    let cantidad = Number(producto.dataset.venta);
                    const cambio = Number(boton.dataset.cambio);

                    cantidad = Math.max(0, Math.min(30, cantidad + cambio));
                    producto.dataset.venta = cantidad;

                    producto.querySelector(".venta-cantidad").textContent =
                        cantidad + (cantidad === 1 ? " unidad" : " unidades");

                    producto.querySelector(".barra-valor").style.width =
                        (cantidad / 30 * 100) + "%";
                });
            });
        });
    </script>

</body>
</html>

   
