<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario; 

class AuthController extends Controller
{
    /**
     * Maneja el intento de login y devuelve un token de Sanctum.
     */
    public function login(Request $request)
    {
        // Validación de credenciales
        $credentials = $request->validate([
            'correo' => 'required|email',
            'password' => 'required' 
        ]);

        // Autenticación: Laravel usa Bcrypt automáticamente
        if (Auth::attempt(['correo' => $request->correo, 'password' => $request->password])) {
            
            // Obtener el usuario autenticado
            $user = Auth::user();

            // 1. Generar el token de Sanctum (la parte segura)
            // El token incluye el rol para que el frontend pueda autorizar vistas
            $token = $user->createToken('authToken', [$user->rol])->plainTextToken;

            // 2. Devolver el token y el rol al frontend
            return response()->json([
                'success' => true,
                'token' => $token,
                'rol' => $user->rol,
                'message' => 'Inicio de sesión exitoso.'
            ], 200);
        }

        // Fallo en la autenticación
        return response()->json([
            'success' => false,
            'message' => 'Credenciales incorrectas.'
        ], 401);
    }
}