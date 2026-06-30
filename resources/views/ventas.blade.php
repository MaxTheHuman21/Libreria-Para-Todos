<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Punto de Venta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">🛒 Punto de Venta</a>
        <div class="ms-auto">
            <a href="/dashboard-libros" class="btn btn-sm btn-secondary fw-bold">⬅ Volver al Inventario</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-7 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold">Buscar Libro</h5>
                    <input type="text" id="buscarVenta" class="form-control mb-3" placeholder="Buscar por nombre o clave..." oninput="buscarParaVenta()">
                    
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover align-middle small">
                            <thead class="table-light sticky-top">
                                <tr><th>Clave</th><th>Nombre</th><th>Precio</th><th>Stock</th><th></th></tr>
                            </thead>
                            <tbody id="listaLibrosVenta"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-body bg-light">
                    <h5 class="fw-bold text-primary">🛍️ Resumen de Venta</h5>
                    <hr>
                    <ul id="carritoLista" class="list-group mb-3">
                        </ul>
                    <div class="d-flex justify-content-between fs-4 fw-bold mb-3">
                        <span>Total:</span>
                        <span id="totalVenta">$0.00</span>
                    </div>
                    <button id="btnCobrar" class="btn btn-success w-100 fw-bold fs-5" onclick="procesarVenta()" disabled>💵 COBRAR</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let carrito = [];
    let librosDisponibles = [];

    document.addEventListener("DOMContentLoaded", () => buscarParaVenta());

    async function buscarParaVenta() {
        const query = document.getElementById('buscarVenta').value;
        // Reutilizamos el endpoint del Inventario para obtener los datos
        const response = await fetch(`/libros?search=${encodeURIComponent(query)}`);
        const data = await response.json();
        librosDisponibles = data.libros;
        
        const tbody = document.getElementById('listaLibrosVenta');
        tbody.innerHTML = librosDisponibles.map(l => `
            <tr>
                <td>${l.clave}</td>
                <td class="fw-bold">${l.nombre}</td>
                <td>$${parseFloat(l.precio).toFixed(2)}</td>
                <td><span class="badge ${l.stock > 0 ? 'bg-success' : 'bg-danger'}">${l.stock} u.</span></td>
                <td>
                    <button class="btn btn-sm btn-primary fw-bold" ${l.stock == 0 ? 'disabled' : ''} 
                            onclick="agregarAlCarrito(${l.id}, '${l.nombre}', ${l.precio}, ${l.stock})">
                        + Añadir
                    </button>
                </td>
            </tr>
        `).join('');
    }

    function agregarAlCarrito(id, nombre, precio, stock) {
        let item = carrito.find(i => i.id === id);
        if (item) {
            if (item.cantidad < stock) item.cantidad++;
            else alert("¡No hay suficiente stock en el almacén!");
        } else {
            carrito.push({ id, nombre, precio, cantidad: 1 });
        }
        actualizarCarrito();
    }

    function actualizarCarrito() {
        const lista = document.getElementById('carritoLista');
        let total = 0;
        lista.innerHTML = carrito.map((item, index) => {
            total += item.precio * item.cantidad;
            return `
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <b class="d-block">${item.nombre}</b>
                    <small class="text-muted">${item.cantidad} x $${parseFloat(item.precio).toFixed(2)}</small>
                </div>
                <div>
                    <span class="fw-bold me-3">$${(item.precio * item.cantidad).toFixed(2)}</span>
                    <button class="btn btn-sm btn-outline-danger" onclick="quitarDelCarrito(${index})">X</button>
                </div>
            </li>`;
        }).join('');

        document.getElementById('totalVenta').innerText = '$' + total.toFixed(2);
        document.getElementById('btnCobrar').disabled = carrito.length === 0;
    }

    function quitarDelCarrito(index) {
        carrito.splice(index, 1);
        actualizarCarrito();
    }

    async function procesarVenta() {
        const btn = document.getElementById('btnCobrar');
        btn.disabled = true;
        btn.innerText = "Procesando...";

        try {
            // El carrito viaja al servidor
            const response = await fetch('/api/ventas', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ items: carrito })
            });

            const data = await response.json();

            if (response.ok) {
                // EL BRINCO: Laravel devolvió el ID de la venta creada, nos vamos al ticket
                window.location.href = `/ticket/${data.venta_id}`;
            } else {
                alert("Error: " + (data.error || "No se pudo procesar la venta"));
                btn.disabled = false;
                btn.innerText = "💵 COBRAR";
            }
        } catch (error) {
            alert("Error de conexión con el servidor.");
            btn.disabled = false;
            btn.innerText = "💵 COBRAR";
        }
    }
</script>
</body>
</html>