/* =========================================
   🌸 BLOSSOM — PRODUCTOS
   ========================================= */

// Productos iniciales de prueba

const productos = [
    {
        id: 1,
        nombre: "Cocina Nova 4 Hornallas",
        marca: "Nova",
        categoria: "cocina",
        precio: 489999,
        precioOferta: 439999,
        oferta: true,
        descripcion: "Cocina práctica y moderna para preparar tus comidas todos los días.",
        caracteristicas: "4 hornallas, horno amplio, parrilla superior y luz interior.",
        imagen: "../img/productos/cocina-nova.jpg",
        stock: 8,
        masVendido: true
    },
    {
        id: 2,
        nombre: "Microondas Stars 20L",
        marca: "Stars",
        categoria: "cocina",
        precio: 189999,
        precioOferta: 159999,
        oferta: true,
        descripcion: "Microondas compacto ideal para calentar y cocinar rápidamente.",
        caracteristicas: "20 litros, 5 niveles de potencia, descongelado y temporizador.",
        imagen: "../img/productos/microondas-stars.jpg",
        stock: 12,
        masVendido: true
    },
    {
        id: 3,
        nombre: "Horno Eléctrico Ovez 45L",
        marca: "Ovez",
        categoria: "cocina",
        precio: 249999,
        descripcion: "Horno eléctrico versátil para cocinar, hornear y gratinar.",
        caracteristicas: "45 litros, temperatura regulable, timer y bandeja.",
        imagen: "../img/productos/horno-ovez.jpg",
        stock: 7,
        masVendido: false
    },
    {
        id: 4,
        nombre: "Cocina Silma 5 Hornallas",
        marca: "Silma",
        categoria: "cocina",
        precio: 649999,
        descripcion: "Cocina amplia pensada para preparar varias comidas al mismo tiempo.",
        caracteristicas: "5 hornallas, horno con luz interior y parrilla.",
        imagen: "../img/productos/cocina-silma.jpg",
        stock: 5,
        masVendido: true
    },
    {
        id: 5,
        nombre: "Microondas Dagcy Digital 25L",
        marca: "Dagcy",
        categoria: "cocina",
        precio: 229999,
        descripcion: "Microondas digital con gran capacidad y funcionamiento sencillo.",
        caracteristicas: "25 litros, panel digital, descongelado y 10 niveles de potencia.",
        imagen: "../img/productos/microondas-dagcy.jpg",
        stock: 9,
        masVendido: false
    },
    {
        id: 6,
        nombre: "Horno Eléctrico Lopdar 60L",
        marca: "Lopdar",
        categoria: "cocina",
        precio: 319999,
        descripcion: "Horno de gran capacidad para preparar comidas familiares.",
        caracteristicas: "60 litros, timer, temperatura regulable y doble resistencia.",
        imagen: "../img/productos/horno-lopdar.jpg",
        stock: 6,
        masVendido: false
    },

    {
        id: 7,
        nombre: "Heladera Nova Fresh 320L",
        marca: "Nova",
        categoria: "refrigeracion",
        precio: 899999,
        precioOferta: 799999,
        oferta: true,
        descripcion: "Heladera espaciosa para conservar alimentos y bebidas.",
        caracteristicas: "320 litros, freezer superior, estantes regulables y luz interior.",
        imagen: "../img/productos/heladera-nova.jpg",
        stock: 5,
        masVendido: true
    },
    {
        id: 8,
        nombre: "Heladera Powerfull No Frost 400L",
        marca: "Powerfull",
        categoria: "refrigeracion",
        precio: 1199999,
        descripcion: "Heladera de gran capacidad con sistema No Frost.",
        caracteristicas: "400 litros, No Frost, freezer independiente y control de temperatura.",
        imagen: "../img/productos/heladera-powerfull.jpg",
        stock: 4,
        masVendido: true
    },
    {
        id: 9,
        nombre: "Freezer Epic 200L",
        marca: "Epic",
        categoria: "refrigeracion",
        precio: 579999,
        descripcion: "Freezer compacto para almacenar alimentos congelados.",
        caracteristicas: "200 litros, temperatura regulable y canastos interiores.",
        imagen: "../img/productos/freezer-epic.jpg",
        stock: 6,
        masVendido: false
    },
    {
        id: 10,
        nombre: "Heladera Aura 280L",
        marca: "Aura",
        categoria: "refrigeracion",
        precio: 819999,
        descripcion: "Heladera compacta y funcional para el hogar.",
        caracteristicas: "280 litros, freezer superior, estantes de vidrio y cajón para frutas.",
        imagen: "../img/productos/heladera-aura.jpg",
        stock: 7,
        masVendido: false
    },
    {
        id: 11,
        nombre: "Freezer SuperW 300L",
        marca: "SuperW",
        categoria: "refrigeracion",
        precio: 699999,
        descripcion: "Freezer de gran capacidad para conservar productos durante más tiempo.",
        caracteristicas: "300 litros, control de temperatura y canastos organizadores.",
        imagen: "../img/productos/freezer-superw.jpg",
        stock: 4,
        masVendido: false
    },

    {
        id: 12,
        nombre: "Lavarropas Nova Wash 7kg",
        marca: "Nova",
        categoria: "lavado",
        precio: 629999,
        descripcion: "Lavarropas práctico para el uso cotidiano.",
        caracteristicas: "7 kg, múltiples programas, centrifugado y carga frontal.",
        imagen: "../img/productos/lavarropas-nova.jpg",
        stock: 8,
        masVendido: true
    },
    {
        id: 13,
        nombre: "Lavarropas Pretty 8kg",
        marca: "Pretty",
        categoria: "lavado",
        precio: 729999,
        precioOferta: 649999,
        oferta: true,
        descripcion: "Lavarropas moderno con programas para diferentes tipos de ropa.",
        caracteristicas: "8 kg, 12 programas y centrifugado regulable.",
        imagen: "../img/productos/lavarropas-pretty.jpg",
        stock: 6,
        masVendido: false
    },
    {
        id: 14,
        nombre: "Lavarropas Powerfull 10kg",
        marca: "Powerfull",
        categoria: "lavado",
        precio: 899999,
        descripcion: "Gran capacidad para familias y lavados frecuentes.",
        caracteristicas: "10 kg, programas rápidos, lavado profundo y centrifugado.",
        imagen: "../img/productos/lavarropas-powerfull.jpg",
        stock: 4,
        masVendido: true
    },
    {
        id: 15,
        nombre: "Lavarropas Dagcy Eco 6kg",
        marca: "Dagcy",
        categoria: "lavado",
        precio: 559999,
        descripcion: "Modelo compacto pensado para ahorrar espacio y recursos.",
        caracteristicas: "6 kg, programas Eco, lavado rápido y centrifugado.",
        imagen: "../img/productos/lavarropas-dagcy.jpg",
        stock: 7,
        masVendido: false
    },

    {
        id: 16,
        nombre: "Aspiradora Stars Cyclone",
        marca: "Stars",
        categoria: "limpieza",
        precio: 179999,
        precioOferta: 149999,
        oferta: true,
        descripcion: "Aspiradora potente para limpiar diferentes superficies del hogar.",
        caracteristicas: "Sistema ciclónico, depósito de 2L y filtro lavable.",
        imagen: "../img/productos/aspiradora-stars.jpg",
        stock: 10,
        masVendido: true
    },
    {
        id: 17,
        nombre: "Aspiradora Ovez Compact",
        marca: "Ovez",
        categoria: "limpieza",
        precio: 139999,
        descripcion: "Aspiradora compacta y fácil de guardar.",
        caracteristicas: "Depósito de 1,5L, filtro lavable y accesorios.",
        imagen: "../img/productos/aspiradora-ovez.jpg",
        stock: 8,
        masVendido: false
    },
    {
        id: 18,
        nombre: "Plancha Nova Steam",
        marca: "Nova",
        categoria: "limpieza",
        precio: 99999,
        descripcion: "Plancha a vapor para facilitar el planchado diario.",
        caracteristicas: "Vapor regulable, suela antiadherente y tanque de agua.",
        imagen: "../img/productos/plancha-nova.jpg",
        stock: 12,
        masVendido: true
    },
    {
        id: 19,
        nombre: "Plancha Pretty Pro",
        marca: "Pretty",
        categoria: "limpieza",
        precio: 119999,
        descripcion: "Plancha liviana con sistema de vapor para distintos tipos de prendas.",
        caracteristicas: "Vapor vertical, temperatura regulable y sistema antigoteo.",
        imagen: "../img/productos/plancha-pretty.jpg",
        stock: 9,
        masVendido: false
    },

    {
        id: 20,
        nombre: "Aire Acondicionado Aura 3000F",
        marca: "Aura",
        categoria: "climatizacion",
        precio: 1049999,
        descripcion: "Aire acondicionado para mantener una temperatura agradable durante todo el año.",
        caracteristicas: "3000 frigorías, frío/calor, control remoto y timer.",
        imagen: "../img/productos/aire-aura.jpg",
        stock: 5,
        masVendido: true
    },
    {
        id: 21,
        nombre: "Aire Acondicionado Powerfull 4500F",
        marca: "Powerfull",
        categoria: "climatizacion",
        precio: 1399999,
        descripcion: "Equipo de gran potencia para ambientes amplios.",
        caracteristicas: "4500 frigorías, frío/calor, control remoto y modo sleep.",
        imagen: "../img/productos/aire-powerfull.jpg",
        stock: 3,
        masVendido: true
    },
    {
        id: 22,
        nombre: "Ventilador SuperW Turbo",
        marca: "SuperW",
        categoria: "climatizacion",
        precio: 159999,
        descripcion: "Ventilador potente para refrescar diferentes espacios del hogar.",
        caracteristicas: "3 velocidades, cabezal oscilante y altura regulable.",
        imagen: "../img/productos/ventilador-superw.jpg",
        stock: 10,
        masVendido: false
    },
    {
        id: 23,
        nombre: "Calefactor Epic Ceramic",
        marca: "Epic",
        categoria: "climatizacion",
        precio: 189999,
        descripcion: "Calefactor cerámico compacto para los días fríos.",
        caracteristicas: "2 niveles de potencia, termostato y protección contra sobrecalentamiento.",
        imagen: "../img/productos/calefactor-epic.jpg",
        stock: 8,
        masVendido: false
    },
    {
        id: 24,
        nombre: "Ventilador Lopdar 18",
        marca: "Lopdar",
        categoria: "climatizacion",
        precio: 129999,
        descripcion: "Ventilador de pie para una ventilación práctica y silenciosa.",
        caracteristicas: "3 velocidades, oscilación y altura regulable.",
        imagen: "../img/productos/ventilador-lopdar.jpg",
        stock: 9,
        masVendido: false
    },

    {
        id: 25,
        nombre: "Cafetera Nova Classic",
        marca: "Nova",
        categoria: "pequenos-electrodomesticos",
        precio: 89999,
        descripcion: "Cafetera práctica para preparar café de manera sencilla.",
        caracteristicas: "1,2 litros, filtro reutilizable y sistema antigoteo.",
        imagen: "../img/productos/cafetera-nova.jpg",
        stock: 15,
        masVendido: true
    },
    {
        id: 26,
        nombre: "Licuadora Stars Mix 600",
        marca: "Stars",
        categoria: "pequenos-electrodomesticos",
        precio: 109999,
        descripcion: "Licuadora potente para preparar bebidas y mezclas.",
        caracteristicas: "600 W, jarra de 1,5L y varias velocidades.",
        imagen: "../img/productos/licuadora-stars.jpg",
        stock: 12,
        masVendido: true
    },
    {
        id: 27,
        nombre: "Batidora Ovez Power Mix",
        marca: "Ovez",
        categoria: "pequenos-electrodomesticos",
        precio: 129999,
        descripcion: "Batidora versátil para preparar mezclas y masas.",
        caracteristicas: "5 velocidades, función turbo y accesorios.",
        imagen: "../img/productos/batidora-ovez.jpg",
        stock: 9,
        masVendido: false
    },
    {
        id: 28,
        nombre: "Tostadora Silma 4",
        marca: "Silma",
        categoria: "pequenos-electrodomesticos",
        precio: 79999,
        descripcion: "Tostadora compacta para preparar tostadas rápidamente.",
        caracteristicas: "4 niveles de tostado, función cancelar y bandeja recogemigas.",
        imagen: "../img/productos/tostadora-silma.jpg",
        stock: 11,
        masVendido: false
    },
    {
        id: 29,
        nombre: "Freidora de Aire Dagcy 5L",
        marca: "Dagcy",
        categoria: "pequenos-electrodomesticos",
        precio: 179999,
        precioOferta: 149999,
        oferta: true,
        descripcion: "Freidora de aire para cocinar alimentos de manera práctica.",
        caracteristicas: "5 litros, temperatura regulable, timer y pantalla digital.",
        imagen: "../img/productos/freidora-dagcy.jpg",
        stock: 10,
        masVendido: true
    },
    {
        id: 30,
        nombre: "Cafetera Pretty Latte",
        marca: "Pretty",
        categoria: "pequenos-electrodomesticos",
        precio: 149999,
        descripcion: "Cafetera diseñada para preparar diferentes tipos de café.",
        caracteristicas: "1,5 litros, vaporizador de leche y sistema de presión.",
        imagen: "../img/productos/cafetera-pretty.jpg",
        stock: 6,
        masVendido: false
    }
];


/* =========================================
   🛍️ MOSTRAR PRODUCTOS
   ========================================= */

function mostrarProductos(listaProductos) {

    const contenedor = document.getElementById("lista-productos");

    const mensajeSinResultados =
        document.getElementById("sin-resultados");


    if (!contenedor) {
        return;
    }


    contenedor.innerHTML = "";


    if (listaProductos.length === 0) {

        mensajeSinResultados.style.display = "block";

        return;
    }


    mensajeSinResultados.style.display = "none";


    listaProductos.forEach(producto => {

        const tarjeta = document.createElement("article");

        tarjeta.classList.add("producto-card");


        tarjeta.innerHTML = `

    <div class="producto-imagen">

        <img
            src="${producto.imagen}"
            alt="${producto.nombre}"
        >

    </div>


    <div class="producto-info">

        <p class="producto-marca">
            ${producto.marca}
        </p>

        <h3 class="producto-nombre">
            ${producto.nombre}
        </h3>

        <p class="producto-precio">
            $${producto.precio.toLocaleString("es-AR")}
        </p>


        ${
            producto.masVendido
            ? '<span class="producto-destacado">⭐ Más vendido</span>'
            : ''
        }


        <a
            href="detalle-producto.html?id=${producto.id}"
            class="btn-producto"
        >
            Ver producto
        </a>

    </div>

`;


        contenedor.appendChild(tarjeta);

    });

}




/* =========================================
   🔎 BÚSQUEDA + CATEGORÍA
   ========================================= */

function buscarProductos() {

    const buscador =
        document.getElementById("buscador-productos");

    if (!buscador) {
        return;
    }


    const texto =
        buscador.value.toLowerCase().trim();


    const parametros =
        new URLSearchParams(window.location.search);


    const categoria =
        parametros.get("categoria");


    let productosFiltrados = productos;


    // Primero filtramos por categoría
    if (categoria) {

        productosFiltrados =
            productosFiltrados.filter(producto => {

                return producto.categoria === categoria;

            });

    }


    // Después aplicamos la búsqueda
    if (texto !== "") {

        productosFiltrados =
            productosFiltrados.filter(producto => {

                return (
                    producto.nombre
                        .toLowerCase()
                        .includes(texto) ||

                    producto.marca
                        .toLowerCase()
                        .includes(texto) ||

                    producto.categoria
                        .toLowerCase()
                        .includes(texto)
                );

            });

    }


    mostrarProductos(productosFiltrados);


    // Actualizar mensaje
    const categoriaTexto =
        document.querySelector(
            ".categoria-seleccionada p"
        );


    if (categoriaTexto) {

        if (categoria && texto !== "") {

            categoriaTexto.textContent =
                `Buscando "${texto}" en la categoría seleccionada`;

        }

        else if (categoria) {

            const nombresCategorias = {

                cocina: "Cocina",

                refrigeracion: "Refrigeración",

                lavado: "Lavado",

                limpieza: "Limpieza",

                climatizacion: "Climatización",

                "pequenos-electrodomesticos":
                    "Pequeños electrodomésticos"

            };


            const nombre =
                nombresCategorias[categoria]
                || categoria;


            categoriaTexto.textContent =
                `Mostrando productos de: ${nombre}`;

        }

        else if (texto !== "") {

            categoriaTexto.textContent =
                `Resultados para: "${texto}"`;

        }

        else {

            categoriaTexto.textContent =
                "Mostrando todos los productos";

        }

    }

}
function filtrarPorCategoria() {

    const parametros = new URLSearchParams(window.location.search);

    const categoria = parametros.get("categoria");
    const busqueda = parametros.get("busqueda");

    let productosFiltrados = productos;

    // Filtrar por categoría
    if (categoria) {

        productosFiltrados = productosFiltrados.filter(
            producto => producto.categoria === categoria
        );

    }

    // Filtrar por búsqueda
    if (busqueda) {

        const texto = busqueda.toLowerCase().trim();

        productosFiltrados = productosFiltrados.filter(producto =>
            producto.nombre.toLowerCase().includes(texto) ||
            producto.marca.toLowerCase().includes(texto) ||
            producto.categoria.toLowerCase().includes(texto)
        );

    }

    mostrarProductos(productosFiltrados);

    const categoriaTexto =
        document.querySelector(".categoria-seleccionada p");

    if (categoriaTexto) {

        if (categoria && busqueda) {

            categoriaTexto.textContent =
                `Buscando "${busqueda}" en la categoría seleccionada`;

        } else if (categoria) {

            const nombresCategorias = {
                cocina: "Cocina",
                refrigeracion: "Refrigeración",
                lavado: "Lavado",
                limpieza: "Limpieza",
                climatizacion: "Climatización",
                "pequenos-electrodomesticos":
                    "Pequeños electrodomésticos"
            };

            const nombre =
                nombresCategorias[categoria] || categoria;

            categoriaTexto.textContent =
                `Mostrando productos de: ${nombre}`;

        } else if (busqueda) {

            categoriaTexto.textContent =
                `Resultados para: "${busqueda}"`;

        } else {

            categoriaTexto.textContent =
                "Mostrando todos los productos";
        }
    }
}


/* =========================================
   🚀 INICIAR CATÁLOGO
   ========================================= */

document.addEventListener("DOMContentLoaded", () => {

    const botonBuscar =
        document.getElementById("btn-buscar");

    const buscador =
        document.getElementById("buscador-productos");


    if (botonBuscar) {

        botonBuscar.addEventListener(
            "click",
            buscarProductos
        );

    }


    if (buscador) {

        buscador.addEventListener(
            "keydown",
            (evento) => {

                if (evento.key === "Enter") {

                    buscarProductos();

                }

            }
        );

    }


    filtrarPorCategoria();

});