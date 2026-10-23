
 // ========================================
 // BLOSSOM - DETALLE DEL PRODUCTO
 // Conectado con MySQL mediante PHP
 // ========================================

document.addEventListener("DOMContentLoaded", async function () {

    const parametros = new URLSearchParams(window.location.search);
    const idProducto = Number(parametros.get("id"));

    const imagen = document.getElementById("imagen-producto");
    const nombre = document.getElementById("nombre-producto");
    const marca = document.getElementById("marca-producto");
    const categoria = document.getElementById("categoria-producto");
    const precio = document.getElementById("precio-producto");
    const descripcion = document.getElementById("descripcion-producto");
    const caracteristicas = document.getElementById("caracteristicas-producto");
    const stock = document.getElementById("stock-producto");
    const cantidad = document.getElementById("cantidad");
    const botonAgregar = document.getElementById("btn-agregar");
    const mensaje = document.getElementById("mensaje-producto");

    function formatearPrecio(valor) {
        return Number(valor).toLocaleString("es-AR", {
            style: "currency",
            currency: "ARS",
            maximumFractionDigits: 0
        });
    }

    if (!Number.isInteger(idProducto) || idProducto < 1) {
        mostrarError("El identificador del producto no es válido.");
        return;
    }

    try {
        const respuesta = await fetch("../php/listar_productos.php");

        if (!respuesta.ok) {
            throw new Error("No se pudo consultar el catálogo.");
        }

        const productos = await respuesta.json();

        if (!Array.isArray(productos)) {
            throw new Error("La respuesta del servidor no es válida.");
        }

        const productoSeleccionado = productos.find(
            producto => Number(producto.id) === idProducto
        );

        if (!productoSeleccionado) {
            mostrarError("No pudimos encontrar el producto solicitado.");
            return;
        }

        imagen.src = productoSeleccionado.imagen || "";
        imagen.alt = productoSeleccionado.nombre || "Producto Blossom";

        nombre.textContent = productoSeleccionado.nombre;
        marca.textContent = productoSeleccionado.marca || "";
        categoria.textContent = productoSeleccionado.categoria || "";

        const tieneOferta =
            productoSeleccionado.oferta &&
            Number(productoSeleccionado.precioOferta) > 0;

        const precioFinal = tieneOferta
            ? Number(productoSeleccionado.precioOferta)
            : Number(productoSeleccionado.precio);

        precio.textContent = formatearPrecio(precioFinal);
        descripcion.textContent = productoSeleccionado.descripcion || "";
        caracteristicas.textContent =
            productoSeleccionado.caracteristicas || "";

        const stockDisponible = Number(productoSeleccionado.stock);
        stock.textContent = stockDisponible;

        cantidad.min = 1;
        cantidad.max = stockDisponible;
        cantidad.value = 1;

        if (stockDisponible <= 0) {
            botonAgregar.disabled = true;
            botonAgregar.textContent = "Sin stock";
            cantidad.disabled = true;
            mensaje.textContent = "Este producto no tiene stock disponible.";
            return;
        }

        botonAgregar.addEventListener("click", function () {
            const cantidadElegida = Number(cantidad.value);

            if (
                !Number.isInteger(cantidadElegida) ||
                cantidadElegida < 1
            ) {
                mensaje.textContent = "Ingresá una cantidad válida.";
                return;
            }

            if (cantidadElegida > stockDisponible) {
                mensaje.textContent =
                    "La cantidad supera el stock disponible.";
                return;
            }

            if (typeof window.agregarAlCarrito !== "function") {
                mensaje.textContent =
                    "No se pudo cargar el carrito. Revisá carrito.js.";
                return;
            }

            const resultado = window.agregarAlCarrito(
                productoSeleccionado,
                cantidadElegida
            );

            if (resultado && resultado.ok) {
                mensaje.textContent =
                    "¡Producto agregado al carrito correctamente!";
            } else {
                mensaje.textContent =
                    resultado?.mensaje ||
                    "No se pudo agregar el producto.";
            }
        });

    } catch (error) {
        console.error("Error al cargar el detalle:", error);
        mostrarError(
            "No pudimos cargar el producto. Revisá la conexión con el servidor."
        );
    }

    function mostrarError(texto) {
        const main = document.querySelector("main");

        if (main) {
            main.innerHTML = `
                <section class="detalle-contenido">
                    <h1>Producto no disponible</h1>
                    <p>${texto}</p>
                    <a href="productos.html">Volver al catálogo</a>
                </section>
            `;
        }
    }
});
