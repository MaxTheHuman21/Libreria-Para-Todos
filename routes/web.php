<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BookController;
use App\Models\User;

Route::get('/', function () {
    if(session('error')) {
        return '<h1>Libreria Para Todos</h1><p style="color:red; font-weight:bold;">' . session('error') . '<p1>';
    }

    if(Auth::check()){
        return '<h1>Simulador de Libreria Para Todos</h1><p>🟢 Sesión activa: <b>' . Auth::user()->name . '</b></p><p>Rol actual: <u>' . Auth::user()->role . '</u></p><a href="/logout">Cerrar Sesión Temporal</a>';
    }
    return '<h1>Simulador de Libreria Para Todos </h1><p style="color:gray;">⚪ Estado: Ningún usuario ha iniciado sesión.</p>';
});

Route::get('/forzar-login-admin', function () {
    $user = User::where('role', 'admin')->first();
    if (!$user) return 'Error: No has creado al administrador en Tinker todavía.';
    Auth::login($user); // Inyecta al usuario directamente en la sesión del navegador
    return redirect('/');
});

Route::get('/forzar-login-vendedor', function () {
    $user = User::where('role', 'vendedor')->first();
    if (!$user) return 'Error: No has creado al vendedor en Tinker todavía.';
    Auth::login($user); // Inyecta al usuario directamente en la sesión del navegador
    return redirect('/');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/libros', function() {
        return '<h1>Credenciales verificadas correctamente... Contenido del CRUD de Inventario disponible.</h1>';
    });
    // Nota: Dejamos esta función simple por ahora mientras creamos las vistas del BookController
});

//Endpoints publicos
Route::post('/login-api', [AuthController::class, 'login']);
Route::get('logout-api', [AuthController::class,'logout']);