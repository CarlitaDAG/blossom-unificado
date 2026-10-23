
let productos = [];

function mostrarProductos(listaProductos) {
    const contenedor = document.getElementById("lista-productos");
    const mensaje = document.getElementById("sin-resultados");

    if (!contenedor || !mensaje) return;

    contenedor.innerHTML = "";

    if (listaProductos.length === 0) {
        mensaje.style.display = "block";
        return;
    }

    mensaje.style.display = "none";

    listaProductos.forEach(producto => {
        const tarjeta = document.createElement("article");
        tarjeta.classList.add("producto-card");

        const imagen = document.createElement("img");
        imagen.src = producto.imagen || "";
        imagen.alt = producto.nombre;
        imagen.loading = "lazy";

        const zonaImagen = document.createElement("div");
        zonaImagen.className = "producto-imagen";
        zonaImagen.appendChild(imagen);

        const info = document.createElement("div");
        info.className = "producto-info";

        const marca = document.createElement("p");
        marca.className = "producto-marca";
        marca.textContent = producto.marca;

        const nombre = document.createElement("h3");
        nombre.className = "producto-nombre";
        nombre.textContent = producto.nombre;

        const precio = document.createElement("p");
        precio.className = "producto-precio";

        if (producto.oferta) {
            const precioAnterior = document.createElement("del");
            precioAnterior.textContent =
                "$" + Number(producto.precio).toLocaleString("es-AR");

            precio.appendChild(precioAnterior);
            precio.appendChild(document.createTextNode(" "));

            const precioActual = document.createElement("strong");
            precioActual.textContent =
                "$" + Number(producto.precioOferta).toLocaleString("es-AR");

            precio.appendChild(precioActual);
        } else {
            precio.textContent =
                "$" + Number(producto.precio).toLocaleString("es-AR");
        }

        info.append(marca, nombre, precio);

        if (producto.masVendido) {
            const destacado = document.createElement("span");
            destacado.className = "producto-destacado";
            destacado.textContent = "⭐ Más vendido";
            info.appendChild(destacado);
        }

        const enlace = document.createElement("a");
        enlace.href = "detalle-producto.html?id=" +
            encodeURIComponent(producto.id);
        enlace.className = "btn-producto";
        enlace.textContent = "Ver producto";

        info.appendChild(enlace);
        tarjeta.append(zonaImagen, info);
        contenedor.appendChild(tarjeta);
    });
}

function filtrarProductos() {
    const buscador = document.getElementById("buscador-productos");
    const texto = (buscador?.value || "").toLowerCase().trim();

    const parametros = new URLSearchParams(window.location.search);
    const categoria = parametros.get("categoria");
    const busquedaURL = parametros.get("busqueda") || "";

    const termino = texto || busquedaURL.toLowerCase().trim();

    const filtrados = productos.filter(producto => {
        const coincideCategoria =
            !categoria || producto.categoria === categoria;

        const coincideTexto =
            !termino ||
            producto.nombre.toLowerCase().includes(termino) ||
            producto.marca.toLowerCase().includes(termino) ||
            producto.categoria.toLowerCase().includes(termino);

        return coincideCategoria && coincideTexto;
    });

    mostrarProductos(filtrados);

    const etiqueta = document.querySelector(".categoria-seleccionada p");

    if (etiqueta) {
        if (categoria) {
            etiqueta.textContent = "Mostrando productos de: " +
                categoria.replaceAll("-", " ");
        } else if (termino) {
            etiqueta.textContent = `Resultados para: "${termino}"`;
        } else {
            etiqueta.textContent = "Mostrando todos los productos";
        }
    }
}

document.addEventListener("DOMContentLoaded", async () => {
    const contenedor = document.getElementById("lista-productos");
    const mensaje = document.getElementById("sin-resultados");
    const boton = document.getElementById("btn-buscar");
    const buscador = document.getElementById("buscador-productos");

    if (mensaje) {
        mensaje.style.display = "none";
    }

    if (contenedor) {
        contenedor.textContent = "Cargando productos...";
    }

    try {
        const respuesta = await fetch("../php/listar_productos.php");

        if (!respuesta.ok) {
            throw new Error("Error al consultar MySQL");
        }

        const datos = await respuesta.json();

        if (!Array.isArray(datos)) {
            throw new Error(datos.error || "Respuesta inválida");
        }

        productos = datos;
        filtrarProductos();

        boton?.addEventListener("click", filtrarProductos);

        buscador?.addEventListener("keydown", evento => {
            if (evento.key === "Enter") {
                filtrarProductos();
            }
        });

    } catch (error) {
        console.error(error);

        if (contenedor) {
            contenedor.textContent =
                "No pudimos cargar los productos. Revisá la conexión con MySQL.";
        }
    }
});
