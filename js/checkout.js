document.addEventListener("DOMContentLoaded", function () {
    const formulario = document.getElementById("checkoutForm");
    const subtotalElemento = document.getElementById("subtotal");
    const envioElemento = document.getElementById("costoEnvio");
    const totalElemento = document.getElementById("total");
    const botonConfirmar = document.getElementById("btnConfirmarCompra");
    const productosElemento = document.getElementById("checkout-productos");

    const CLAVE_CARRITO = "blossomCarrito";

    function obtenerCarrito() {
        try {
            const carrito = JSON.parse(localStorage.getItem(CLAVE_CARRITO) || "[]");
            return Array.isArray(carrito) ? carrito : [];
        } catch {
            return [];
        }
    }

    function moneda(valor) {
        return Number(valor).toLocaleString("es-AR", {
            style: "currency",
            currency: "ARS",
            maximumFractionDigits: 2
        });
    }

    function obtenerSubtotal() {
        return obtenerCarrito().reduce((suma, producto) => {
            return suma + Number(producto.precio || 0) * Number(producto.cantidad || 0);
        }, 0);
    }

    function obtenerEnvio() {
        const seleccionado = formulario.querySelector('input[name="envio"]:checked');
        return seleccionado ? Number(seleccionado.value) : 0;
    }

    function actualizarResumen() {
        const carrito = obtenerCarrito();
        const subtotal = obtenerSubtotal();
        const envio = obtenerEnvio();

        subtotalElemento.textContent = moneda(subtotal);
        envioElemento.textContent = moneda(envio);
        totalElemento.textContent = moneda(subtotal + envio);

        productosElemento.innerHTML = "";

        carrito.forEach(producto => {
            const linea = document.createElement("p");
            linea.textContent =
                `${producto.nombre} × ${producto.cantidad} — ` +
                moneda(Number(producto.precio || 0) * Number(producto.cantidad || 0));
            productosElemento.appendChild(linea);
        });

        if (carrito.length === 0) {
            productosElemento.textContent = "Tu carrito está vacío.";
            botonConfirmar.disabled = true;
        } else {
            botonConfirmar.disabled = false;
        }
    }

    formulario.querySelectorAll('input[name="envio"]').forEach(opcion => {
        opcion.addEventListener("change", actualizarResumen);
    });

    actualizarResumen();

    formulario.addEventListener("submit", async function (event) {
        event.preventDefault();

        const carrito = obtenerCarrito();

        if (carrito.length === 0) {
            alert("Tu carrito está vacío.");
            return;
        }

        if (!formulario.reportValidity()) {
            return;
        }

        const cliente = {
            nombre: document.getElementById("nombre").value.trim(),
            apellido: document.getElementById("apellido").value.trim(),
            email: document.getElementById("email").value.trim(),
            telefono: document.getElementById("telefono").value.trim(),
            direccion: document.getElementById("direccion").value.trim()
        };

        const envioSeleccionado = formulario.querySelector('input[name="envio"]:checked');
        const pagoSeleccionado = formulario.querySelector('input[name="pago"]:checked');

        if (!envioSeleccionado || !pagoSeleccionado) {
            alert("Elegí un método de envío y una forma de pago.");
            return;
        }

        botonConfirmar.disabled = true;
        const textoOriginal = botonConfirmar.textContent;
        botonConfirmar.textContent = "Registrando compra...";

        try {
             console.log("Enviando compra a guardar_pedido.php");

            const respuesta = await fetch("../php/guardar_pedido.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    cliente,
                    carrito,
                    envio: envioSeleccionado.value,
                    pago: pagoSeleccionado.value
                })
            });

            const resultado = await respuesta.json();
            console.log("Respuesta de PHP:", resultado);

            if (!respuesta.ok || !resultado.ok) {
                throw new Error(resultado.mensaje || "No se pudo registrar la compra.");
            }

            // Guardar datos para la página de confirmación.
            Object.entries(resultado.cliente).forEach(([campo, valor]) => {
                const clave = {
                    nombre: "nombreCliente",
                    apellido: "apellidoCliente",
                    email: "emailCliente",
                    telefono: "telefonoCliente",
                    direccion: "direccionCliente"
                }[campo];

                if (clave) {
                    localStorage.setItem(clave, valor);
                }
            });

            localStorage.setItem("pedidoId", resultado.pedido_id);
            localStorage.setItem("pedidoSubtotal", resultado.subtotal);
            localStorage.setItem("pedidoEnvio", resultado.costo_envio);
            localStorage.setItem("pedidoTotal", resultado.total);
            localStorage.setItem("pedidoMetodoPago", pagoSeleccionado.value);

            localStorage.removeItem(CLAVE_CARRITO);

            window.location.href = "confirmacion.html";

        } catch (error) {
            alert(error.message || "Ocurrió un error al procesar la compra.");
            botonConfirmar.disabled = false;
            botonConfirmar.textContent = textoOriginal;
        }
    });
});
