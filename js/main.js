document.addEventListener("DOMContentLoaded", () => {

    const contenedor = document.getElementById("productos-destacados");

    if (!contenedor) return;

    const productosDestacados = productos.filter(
        producto => producto.masVendido === true
    );

    productosDestacados.forEach(producto => {

        const tarjeta = document.createElement("article");

        tarjeta.classList.add("producto-card");

        tarjeta.innerHTML = `
            <div class="producto-imagen">
                <img src="${producto.imagen.replace("../", "")}" alt="${producto.nombre}">
            </div>

            <div class="producto-info">

                <span class="producto-destacado">
                    ⭐ Más vendido
                </span>

                <p class="producto-marca">
                    ${producto.marca}
                </p>

                <h3 class="producto-nombre">
                    ${producto.nombre}
                </h3>

                <p class="producto-precio">
                    $${producto.precio.toLocaleString("es-AR")}
                </p>

                <a
                    href="pages/detalle-producto.html?id=${producto.id}"
                    class="btn-producto">
                    Ver producto
                </a>

            </div>
        `;

        contenedor.appendChild(tarjeta);
    });

});
// ==========================================
// BUSCADOR DESDE INICIO
// ==========================================

document.addEventListener("DOMContentLoaded", () => {

    const buscador = document.getElementById("buscador-productos");
    const botonBuscar = document.getElementById("btn-buscar");

    if (!buscador || !botonBuscar) return;

    function realizarBusqueda() {

        const texto = buscador.value.trim();

        if (texto === "") {
            window.location.href = "pages/productos.html";
            return;
        }

        window.location.href =
            "pages/productos.html?busqueda=" +
            encodeURIComponent(texto);
    }

    botonBuscar.addEventListener("click", realizarBusqueda);

    buscador.addEventListener("keydown", (evento) => {

        if (evento.key === "Enter") {
            realizarBusqueda();
        }

    });

});

// ==========================================
// SECCIÓN NOSOTRAS
// ==========================================

document.addEventListener("DOMContentLoaded", () => {

    const botonNosotras = document.getElementById("btn-nosotras");
    const contenidoNosotras = document.getElementById("contenido-nosotras");

    if (!botonNosotras || !contenidoNosotras) return;

    botonNosotras.addEventListener("click", () => {

        contenidoNosotras.classList.toggle("mostrar");

    });

});
// ==========================================
// OFERTAS DESTACADAS
// ==========================================

document.addEventListener("DOMContentLoaded", () => {

    const contenedor = document.getElementById("productos-ofertas");

    if (!contenedor) return;

    const productosEnOferta = productos.filter(
        producto => producto.oferta === true
    );

    productosEnOferta.forEach(producto => {

        const tarjeta = document.createElement("article");

        tarjeta.classList.add("producto-card");

        const descuento = Math.round(
            ((producto.precio - producto.precioOferta) / producto.precio) * 100
        );

        tarjeta.innerHTML = `
            <div class="producto-imagen">

                <img
                    src="${producto.imagen.replace("../", "")}"
                    alt="${producto.nombre}"
                >

            </div>

            <div class="producto-info">

                <span class="producto-descuento">
                    🔥 -${descuento}%
                </span>

                <p class="producto-marca">
                    ${producto.marca}
                </p>

                <h3 class="producto-nombre">
                    ${producto.nombre}
                </h3>

                <p class="producto-precio-anterior">
                    $${producto.precio.toLocaleString("es-AR")}
                </p>

                <p class="producto-precio-oferta">
                    $${producto.precioOferta.toLocaleString("es-AR")}
                </p>

                <a
                    href="pages/detalle-producto.html?id=${producto.id}"
                    class="btn-producto">
                    Ver producto
                </a>

            </div>
        `;

        contenedor.appendChild(tarjeta);

    });

});
