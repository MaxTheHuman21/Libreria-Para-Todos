<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Librería Para Todos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4 text-primary fw-bold">📚 Iniciar Sesión</h3>
                        <form id="formLogin">
                            @csrf 
                            <div class="mb-3">
                                <label>Correo Electrónico</label>
                                <input type="email" id="email" class="form-control" required placeholder="admin@libreria.com">
                            </div>
                            <div class="mb-3">
                                <label>Contraseña</label>
                                <input type="password" id="password" class="form-control" required placeholder="••••••••">
                            </div>
                            <button type="submit" id="btnSubmit" class="btn btn-primary w-100 fw-bold">Entrar al Sistema</button>
                        </form>
                        <div id="mensajeError" class="alert alert-danger mt-3 d-none"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('formLogin').addEventListener('submit', async function(e) {
            e.preventDefault(); 
            let btn = document.getElementById('btnSubmit');
            let email = document.getElementById('email').value;
            let password = document.getElementById('password').value;
            
            btn.disabled = true;
            btn.innerText = 'Verificando...';

            try {
                const response = await fetch('/login-api', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();

                if (response.ok) {
                    // ¡Login exitoso! Brincamos al Inventario
                    window.location.href = '/dashboard-libros'; 
                } else {
                    let alertBox = document.getElementById('mensajeError');
                    alertBox.innerText = data.error || 'Credenciales incorrectas';
                    alertBox.classList.remove('d-none');
                    btn.disabled = false;
                    btn.innerText = 'Entrar al Sistema';
                }
            } catch (error) {
                document.getElementById('mensajeError').innerText = 'Error de conexión con el servidor.';
                document.getElementById('mensajeError').classList.remove('d-none');
                btn.disabled = false;
                btn.innerText = 'Entrar al Sistema';
            }
        });
    </script>
</body>
</html>