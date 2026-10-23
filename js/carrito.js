// =============================================
// BLOSSOM - CARRITO DE COMPRAS
// Módulo de Mar
// =============================================

const CLAVE_CARRITO = "blossomCarrito";

function obtenerCarrito() {
    try {
        const datos = localStorage.getItem(CLAVE_CARRITO);
        const carrito = datos ? JSON.parse(datos) : [];

        return Array.isArray(carrito) ? carrito : [];
    } catch (error) {
        console.error("No se pudo leer el carrito:", error);
        return [];
    }
}

function guardarCarrito(carrito) {
    localStorage.setItem(CLAVE_CARRITO, JSON.stringify(carrito));
}


function buscarProductoCatalogo(id) {
    // El catálogo ahora se consulta desde MySQL.
    // No usamos el array antiguo de productos.js
    // porque sus IDs pueden no coincidir con la base de datos.
    return null;
}


function obtenerPrecioProducto(producto) {
    if (
        producto.oferta &&
        Number(producto.precioOferta) > 0
    ) {
        return Number(producto.precioOferta);
    }

    return Number(producto.precio);
}

function obtenerPrecioOriginal(producto) {
    return Number(producto.precio);
}

function formatearMoneda(valor) {
    return Number(valor).toLocaleString("es-AR", {
        style: "currency",
        currency: "ARS",
        maximumFractionDigits: 0
    });
}

// =============================================
// AGREGAR PRODUCTO
// Compatible con detalle-producto.js
// =============================================

function agregarAlCarrito(producto, cantidad) {
    if (!producto) {
        return {
            ok: false,
            mensaje: "No se encontró el producto."
        };
    }

    cantidad = Number(cantidad);

    if (!Number.isInteger(cantidad) || cantidad < 1) {
        return {
            ok: false,
            mensaje: "Ingresá una cantidad válida."
        };
    }

    const productoActual = producto;
    const stock = Number(productoActual.stock);

    if (cantidad > stock) {
        return {
            ok: false,
            mensaje: "La cantidad supera el stock disponible."
        };
    }

    const carrito = obtenerCarrito();

    const existente = carrito.find(
        item => Number(item.id) === Number(productoActual.id)
    );

    if (existente) {
        if (existente.cantidad + cantidad > stock) {
            return {
                ok: false,
                mensaje: "No hay stock suficiente para esa cantidad."
            };
        }

        existente.cantidad += cantidad;
        existente.stock = stock;
    } else {
        carrito.push({
            id: productoActual.id,
            nombre: productoActual.nombre,
            marca: productoActual.marca,
            categoria: productoActual.categoria,
            imagen: productoActual.imagen,
            precio: obtenerPrecioProducto(productoActual),
            precioOriginal: obtenerPrecioOriginal(productoActual),
            cantidad: cantidad,
            stock: stock
        });
    }

    guardarCarrito(carrito);
    mostrarCarrito();

    return {
        ok: true,
        mensaje: "¡Producto agregado a tu carrito!"
    };
}

// =============================================
// ACTUALIZAR CANTIDAD
// =============================================

function cambiarCantidad(id, cantidad) {
    cantidad = Number(cantidad);

    const carrito = obtenerCarrito();

    const item = carrito.find(
        producto => Number(producto.id) === Number(id)
    );

    if (!item) {
        return;
    }

    if (!Number.isInteger(cantidad) || cantidad < 1) {
        mostrarMensaje("La cantidad debe ser al menos 1.");
        mostrarCarrito();
        return;
    }

    const stockDisponible = Number(item.stock);

    if (cantidad > stockDisponible) {
        mostrarMensaje(
            `Solo hay ${stockDisponible} unidad(es) disponibles de ${item.nombre}.`
        );

        mostrarCarrito();
        return;
    }

    item.cantidad = cantidad;
    item.stock = stockDisponible;

    guardarCarrito(carrito);
    mostrarCarrito();
    mostrarMensaje("Cantidad actualizada.");
}

// =============================================
// ELIMINAR PRODUCTO
// =============================================

function eliminarDelCarrito(id) {
    const carrito = obtenerCarrito().filter(
        item => Number(item.id) !== Number(id)
    );

    guardarCarrito(carrito);
    mostrarCarrito();
    mostrarMensaje("Producto eliminado del carrito.");
}

// =============================================
// VACIAR CARRITO
// =============================================

function vaciarCarrito() {
    const carrito = obtenerCarrito();

    if (carrito.length === 0) {
        return;
    }

    const confirmar = window.confirm(
        "¿Querés eliminar todos los productos de tu carrito?"
    );

    if (!confirmar) {
        return;
    }

    guardarCarrito([]);
    mostrarCarrito();
    mostrarMensaje("Tu carrito está vacío.");
}

// =============================================
// MENSAJES
// =============================================

function mostrarMensaje(texto) {
    const mensaje = document.getElementById("mensaje-carrito");

    if (mensaje) {
        mensaje.textContent = texto;
    }
}

// =============================================
// CONTADOR DE PRODUCTOS
// =============================================

function actualizarContador(carrito) {
    const contador = document.getElementById("contador-carrito");

    if (!contador) {
        return;
    }

    const cantidadTotal = carrito.reduce(
        (total, item) => total + Number(item.cantidad),
        0
    );

    contador.textContent = cantidadTotal;
}

// =============================================
// DIBUJAR TARJETAS DE PRODUCTOS
// =============================================

function crearTarjetaProducto(item) {
    const tarjeta = document.createElement("article");
    tarjeta.className = "tarjeta-carrito";

    const contenedorImagen = document.createElement("div");
    contenedorImagen.className = "imagen-tarjeta-carrito";

    const imagen = document.createElement("img");
    imagen.src = item.imagen || "";
    imagen.alt = item.nombre;
    imagen.loading = "lazy";

    imagen.addEventListener("error", function () {
        this.style.display = "none";
        contenedorImagen.classList.add("imagen-no-disponible");
        contenedorImagen.textContent = "Imagen no disponible";
    });

    contenedorImagen.appendChild(imagen);

    const contenido = document.createElement("div");
    contenido.className = "contenido-tarjeta-carrito";

    const categoria = document.createElement("span");
    categoria.className = "categoria-tarjeta-carrito";
    categoria.textContent = item.categoria || "Hogar";

    const nombre = document.createElement("h3");
    nombre.textContent = item.nombre;

    const marca = document.createElement("p");
    marca.className = "marca-tarjeta-carrito";
    marca.textContent = item.marca || "Blossom";

    const precios = document.createElement("div");
    precios.className = "precios-tarjeta-carrito";

    const precioActual = document.createElement("strong");
    precioActual.textContent = formatearMoneda(item.precio);
    precios.appendChild(precioActual);

    if (
        Number(item.precioOriginal) > Number(item.precio)
    ) {
        const precioAnterior = document.createElement("del");
        precioAnterior.textContent = formatearMoneda(
            item.precioOriginal
        );
        precios.appendChild(precioAnterior);
    }

    const controles = document.createElement("div");
    controles.className = "controles-cantidad";

    const etiqueta = document.createElement("label");
    etiqueta.textContent = "Cantidad";
    etiqueta.htmlFor = `cantidad-${item.id}`;

    const botonMenos = document.createElement("button");
    botonMenos.type = "button";
    botonMenos.textContent = "−";
    botonMenos.setAttribute(
        "aria-label",
        `Disminuir cantidad de ${item.nombre}`
    );
    botonMenos.disabled = Number(item.cantidad) <= 1;

    botonMenos.addEventListener("click", function () {
        cambiarCantidad(item.id, Number(item.cantidad) - 1);
    });

    const entrada = document.createElement("input");
    entrada.type = "number";
    entrada.id = `cantidad-${item.id}`;
    entrada.min = "1";
    entrada.max = String(item.stock);
    entrada.value = String(item.cantidad);
    entrada.setAttribute(
        "aria-label",
        `Cantidad de ${item.nombre}`
    );

    entrada.addEventListener("change", function () {
        cambiarCantidad(item.id, this.value);
    });

    const botonMas = document.createElement("button");
    botonMas.type = "button";
    botonMas.textContent = "+";
    botonMas.setAttribute(
        "aria-label",
        `Aumentar cantidad de ${item.nombre}`
    );
    botonMas.disabled = Number(item.cantidad) >= Number(item.stock);

    botonMas.addEventListener("click", function () {
        cambiarCantidad(item.id, Number(item.cantidad) + 1);
    });

    controles.append(etiqueta, botonMenos, entrada, botonMas);

    const stock = document.createElement("p");
    stock.className = "stock-tarjeta-carrito";
    stock.textContent = `Disponibles: ${item.stock}`;

    contenido.append(
        categoria,
        nombre,
        marca,
        precios,
        controles,
        stock
    );

    const lateral = document.createElement("div");
    lateral.className = "lateral-tarjeta-carrito";

    const subtotalEtiqueta = document.createElement("span");
    subtotalEtiqueta.textContent = "Subtotal";

    const subtotal = document.createElement("strong");
    subtotal.textContent = formatearMoneda(
        Number(item.precio) * Number(item.cantidad)
    );

    const botonEliminar = document.createElement("button");
    botonEliminar.type = "button";
    botonEliminar.className = "boton-eliminar-producto";
    botonEliminar.textContent = "Eliminar";
    botonEliminar.addEventListener("click", function () {
        eliminarDelCarrito(item.id);
    });

    lateral.append(subtotalEtiqueta, subtotal, botonEliminar);
    tarjeta.append(contenedorImagen, contenido, lateral);

    return tarjeta;
}

// =============================================
// ACTUALIZAR RESUMEN Y VISTA DEL CARRITO
// =============================================

function mostrarCarrito() {
    const contenedor = document.getElementById("carrito-contenido");

    const carrito = obtenerCarrito();

    actualizarContador(carrito);

    if (!contenedor) {
        return;
    }

    const carritoVacio = document.getElementById("carrito-vacio");
    const resumen = document.querySelector(".resumen-compra");
    const subtotalElemento = document.getElementById("subtotal-carrito");
    const descuentoElemento = document.getElementById("descuento-carrito");
    const filaDescuento = document.getElementById("fila-descuento");
    const totalElemento = document.getElementById("total-carrito");
    const botonVaciar = document.getElementById("btn-vaciar-carrito");
    const botonFinalizar = document.getElementById("btn-finalizar-compra");
    const cantidadResumen = document.getElementById("cantidad-resumen");

    contenedor.replaceChildren();

    let cantidadTotal = 0;
    let subtotalOriginal = 0;
    let total = 0;

    carrito.forEach(item => {
        const precio = Number(item.precio) || 0;
        const precioOriginal = Number(item.precioOriginal) || precio;
        const cantidad = Number(item.cantidad) || 0;

        cantidadTotal += cantidad;
        subtotalOriginal += precioOriginal * cantidad;
        total += precio * cantidad;

        contenedor.appendChild(crearTarjetaProducto(item));
    });

    const descuento = Math.max(0, subtotalOriginal - total);

    subtotalElemento.textContent = formatearMoneda(subtotalOriginal);
    totalElemento.textContent = formatearMoneda(total);
    descuentoElemento.textContent = `−${formatearMoneda(descuento)}`;
    filaDescuento.hidden = descuento <= 0;

    cantidadResumen.textContent = cantidadTotal === 1
        ? "1 producto en tu carrito"
        : `${cantidadTotal} productos en tu carrito`;

    carritoVacio.hidden = carrito.length > 0;
    contenedor.hidden = carrito.length === 0;

    // Ocultar beneficios y resumen cuando el carrito está vacío.
    document.querySelector(".beneficios-carrito").hidden =
        carrito.length === 0;

    resumen.hidden = carrito.length === 0;

    botonVaciar.disabled = carrito.length === 0;
    botonFinalizar.disabled = carrito.length === 0;

    botonFinalizar.setAttribute(
        "aria-disabled",
        String(carrito.length === 0)
    );
}

// =============================================
// INICIALIZACIÓN
// =============================================

document.addEventListener("DOMContentLoaded", function () {
    const botonVaciar = document.getElementById("btn-vaciar-carrito");
    const botonFinalizar = document.getElementById("btn-finalizar-compra");

    if (botonVaciar) {
        botonVaciar.addEventListener("click", vaciarCarrito);
    }

    if (botonFinalizar) {
        botonFinalizar.addEventListener("click", function () {
            if (obtenerCarrito().length === 0) {
                mostrarMensaje("Tu carrito está vacío.");
                return;
            }

            // Esta ruta debe coincidir con el checkout real de Dari.
            window.location.href = "checkout.html";
        });
    }

    mostrarCarrito();
});

// Funciones utilizadas por detalle-producto.js
window.agregarAlCarrito = agregarAlCarrito;
window.eliminarDelCarrito = eliminarDelCarrito;
window.cambiarCantidad = cambiarCantidad;
window.mostrarCarrito = mostrarCarrito;