<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if(Auth::attempt($credentials)){
            $request -> session()->regenerate();

            return response()->json([
                'mensaje' => 'Autorizacion exitosa! Sesion iniciada correctamente',
                'usuario' => [
                    'id'=> Auth::user()->id,
                    'name' => Auth::user()  -> name,
                    'email'=> Auth::user() -> email,
                    'role' => Auth::user()->role,
                ]
            ], 200);
        }

        return response()->json([
            'error' => 'Credenciales invalidas. Verifica tu correo y contraseña.'
        ], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request -> session()->invalidate();
        $request -> session()->regenerateToken();

        return response()->json([
            'mensaje'=> 'Sesion cerrada con exito.',
        ], 200);
    }
}
