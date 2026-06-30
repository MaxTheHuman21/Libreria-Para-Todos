<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Almacén</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">🟢 Sistema Librería</a>
        <div class="ms-auto text-white small d-flex align-items-center">
            <span class="me-3">Usuario: <b>{{ Auth::user()->nombre ?? Auth::user()->name }}</b> (<span class="badge bg-info">{{ ucfirst(Auth::user()->role) }}</span>)</span>
            
            <a href="/ventas" class="btn btn-sm btn-warning fw-bold text-dark me-2">🛒 Punto de Venta</a>
            
            <button onclick="cerrarSesion()" class="btn btn-sm btn-danger fw-bold">Salir</button>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row">
        
        @if(Auth::user()->role === 'admin')
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 id="formTitle" class="fw-bold text-primary mb-3">Registrar Libro</h5>
                    <form id="libroForm">
                        <input type="hidden" id="libroId">
                        <input type="text" id="nombre" class="form-control form-control-sm mb-2" required placeholder="Nombre">
                        <input type="text" id="clave" class="form-control form-control-sm mb-2" required placeholder="Clave (Ej. LIB-001)">
                        <input type="number" step="0.01" id="precio" class="form-control form-control-sm mb-2" required placeholder="Precio $">
                        <input type="number" id="stock" class="form-control form-control-sm mb-3" required placeholder="Stock">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-success w-100 fw-bold">Guardar</button>
                            <button type="button" class="btn btn-sm btn-secondary w-100 d-none" id="btnCancelar" onclick="resetearFormulario()">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <div class="{{ Auth::user()->role === 'admin' ? 'col-md-8' : 'col-md-12' }}">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold m-0">Inventario</h5>
                        <input type="text" id="buscarInput" class="form-control form-control-sm w-50" placeholder="🔍 Buscar..." oninput="fetchLibros(1)">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle small">
                            <thead class="table-secondary">
                                <tr>
                                    <th>ID</th><th>Nombre</th><th>Clave</th><th>Precio</th><th>Stock</th>
                                    @if(Auth::user()->role === 'admin') <th class="text-center">Acciones</th> @endif
                                </tr>
                            </thead>
                            <tbody id="tablaLibrosBody"></tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-2">
                        <small id="infoPaginacion" class="text-muted"></small>
                        <div>
                            <button id="btnAnterior" class="btn btn-sm btn-outline-primary" onclick="cambiarPagina(-1)">Anterior</button>
                            <button id="btnSiguiente" class="btn btn-sm btn-outline-primary" onclick="cambiarPagina(1)">Siguiente</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const USER_ROLE = "{{ Auth::user()->role }}";
    let paginaActual = 1, totalPaginas = 1;

    document.addEventListener("DOMContentLoaded", () => fetchLibros(1));

    async function fetchLibros(page = 1) {
        paginaActual = page;
        const search = document.getElementById('buscarInput').value;
        const response = await fetch(`/libros?page=${paginaActual}${search ? '&search='+encodeURIComponent(search) : ''}`);
        const data = await response.json();
        
        totalPaginas = Math.ceil(data.total_registros / 10);
        renderizarTabla(data.libros);
        
        document.getElementById('infoPaginacion').innerHTML = `Página <b>${paginaActual}</b> de <b>${totalPaginas || 1}</b> (Total: ${data.total_registros})`;
        document.getElementById('btnAnterior').disabled = (paginaActual === 1);
        document.getElementById('btnSiguiente').disabled = (paginaActual === totalPaginas || totalPaginas === 0);
    }

    function renderizarTabla(libros) {
        const tbody = document.getElementById('tablaLibrosBody');
        tbody.innerHTML = libros.map(l => `
            <tr>
                <td>${l.id}</td>
                <td class="fw-bold">${l.nombre}</td>
                <td><span class="badge bg-secondary">${l.clave}</span></td>
                <td>$${parseFloat(l.precio).toFixed(2)}</td>
                <td>${l.stock}</td>
                ${USER_ROLE === 'admin' ? `
                <td class="text-center">
                    <button class="btn btn-xs btn-outline-primary btn-sm" onclick="prepararEdicion(${l.id}, '${l.nombre}', '${l.clave}', ${l.precio}, ${l.stock})">Editar</button>
                    <button class="btn btn-xs btn-outline-danger btn-sm" onclick="eliminarLibro(${l.id})">Borrar</button>
                </td>` : ''}
            </tr>
        `).join('');
    }

    function cambiarPagina(dir) { 
        if(paginaActual + dir >= 1 && paginaActual + dir <= totalPaginas) fetchLibros(paginaActual + dir); 
    }

    if (USER_ROLE === 'admin') {
        document.getElementById('libroForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('libroId').value;
            const payload = { 
                nombre: document.getElementById('nombre').value, 
                clave: document.getElementById('clave').value, 
                precio: document.getElementById('precio').value, 
                stock: document.getElementById('stock').value 
            };
            await fetch(id ? `/libros/${id}` : '/libros', { 
                method: id ? 'PUT' : 'POST', 
                headers: { 'Content-Type': 'application/json' }, 
                body: JSON.stringify(payload) 
            });
            resetearFormulario(); 
            fetchLibros(paginaActual);
        });
    }

    function prepararEdicion(id, nombre, clave, precio, stock) {
        document.getElementById('libroId').value = id; 
        document.getElementById('nombre').value = nombre; 
        document.getElementById('clave').value = clave; 
        document.getElementById('precio').value = precio; 
        document.getElementById('stock').value = stock;
        document.getElementById('formTitle').textContent = "Editar Libro"; 
        document.getElementById('btnCancelar').classList.remove('d-none');
    }

    function resetearFormulario() { 
        document.getElementById('libroForm').reset(); 
        document.getElementById('libroId').value = ''; 
        document.getElementById('formTitle').textContent = "Registrar Libro"; 
        document.getElementById('btnCancelar').classList.add('d-none'); 
    }
    
    async function eliminarLibro(id) { 
        if (confirm('¿Eliminar?')) { 
            await fetch(`/libros/${id}`, { method: 'DELETE' }); 
            fetchLibros(paginaActual); 
        } 
    }
    
    async function cerrarSesion() { 
        if ((await fetch('/logout-api')).ok) window.location.href = '/'; 
    }
</script>
</body>
</html>