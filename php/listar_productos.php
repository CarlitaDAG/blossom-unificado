
<?php
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/conexion.php";

try {
    $sql = "SELECT
                p.id,
                p.nombre,
                p.marca,
                c.nombre AS categoria_nombre,
                p.precio,
                p.precio_oferta,
                p.descripcion,
                p.caracteristicas,
                p.imagen,
                p.stock,
                p.mas_vendido
            FROM productos p
            LEFT JOIN categorias c ON c.id = p.categoria_id
            WHERE p.activo = 1
            ORDER BY p.id ASC";

    $resultado = $conexion->query($sql);

    $categorias = [
        "Cocina" => "cocina",
        "Refrigeración" => "refrigeracion",
        "Lavado" => "lavado",
        "Limpieza" => "limpieza",
        "Climatización" => "climatizacion",
        "Pequeños electrodomésticos" => "pequenos-electrodomesticos"
    ];

    $productos = [];

    while ($fila = $resultado->fetch_assoc()) {
        $precio = (float)$fila["precio"];
        $precioOferta = $fila["precio_oferta"] !== null
            ? (float)$fila["precio_oferta"]
            : null;

        $categoriaNombre = $fila["categoria_nombre"] ?? "";

        $producto = [
            "id" => (int)$fila["id"],
            "nombre" => $fila["nombre"],
            "marca" => $fila["marca"],
            "categoria" => $categorias[$categoriaNombre] ?? "",
            "precio" => $precio,
            "descripcion" => $fila["descripcion"] ?? "",
            "caracteristicas" => $fila["caracteristicas"] ?? "",
            "imagen" => $fila["imagen"] ?? "",
            "stock" => (int)$fila["stock"],
            "masVendido" => (bool)$fila["mas_vendido"],
            "oferta" => $precioOferta !== null && $precioOferta > 0 && $precioOferta < $precio
        ];

        if ($producto["oferta"]) {
            $producto["precioOferta"] = $precioOferta;
        }

        $productos[] = $producto;
    }

    echo json_encode(
        $productos,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "No se pudieron consultar los productos."
    ], JSON_UNESCAPED_UNICODE);
}
