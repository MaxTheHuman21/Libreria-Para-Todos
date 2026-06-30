<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BookController;
use App\Http\Controllers\SaleController;
use App\Models\Sale;
use App\Models\User;

Route::get('/', function () {
    if(Auth::check()){
        return redirect('dashboard-libros');
    }
    return view('login');
})->name('login');

//CORRECCIONES, SE AGREGO LA DIAGONAL '/' a /logout-api
//Endpoints publicos
Route::post('/login-api', [AuthController::class, 'login']);
Route::get('/logout-api', [AuthController::class,'logout']);

Route::middleware(['auth'])->group(function(){
    Route::get('/dashboard-libros', function(){
        return view('libros');
    }); 

    Route::get('/ventas', function(){
        return view('ventas');
    });

    Route::get('/libros', [BookController::class, 'index']);
    Route::post('/api/ventas', [SaleController::class, 'store']);

    
    Route::get('/ticket/{id}', function($id){
        $venta = Sale::with('books', 'user')->findOrFail($id);
        return view('ticket', compact('venta'));
    });
});

Route::middleware(['auth', 'role:admin'])->group(function(){
    Route::post('/libros', [BookController::class, 'store']);
    Route::put('/libros/{id}', [BookController::class,'update']);
    Route::delete('/libros/{id}', [BookController::class,'destroy']);
});
