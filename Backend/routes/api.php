<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController; // Necesitarás este controlador para el login

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí registras tus rutas API. Laravel las carga con el prefijo '/api'
| y el middleware 'api', ideal para REST y manejo de JSON.
|
*/

// =======================================================
// 1. RUTAS PÚBLICAS (Login / Inscripción)
// =======================================================

// Login (devuelve el token de seguridad)
Route::post('/login', [AuthController::class, 'login']);

// Inscripción (crea un nuevo usuario Padre y Alumno)
Route::post('/inscripcion', [AdminController::class, 'handleInscripcion']); 


// =======================================================
// 2. RUTAS PROTEGIDAS (Requieren Token de Seguridad - Sanctum)
// =======================================================
Route::middleware('auth:sanctum')->group(function () {
    
    // --- Gestión de Admin/Entrenador ---
    Route::post('/agregar_personal', [AdminController::class, 'addPersonal']);
    Route::post('/padres/actualizar-contrasena', [AdminController::class, 'updateParentPassword']);
    
    // --- Lectura de Datos de Paneles (Admin/Entrenador) ---
    Route::get('/alumnos', [AdminController::class, 'getAlumnos']);
    Route::get('/padres', [AdminController::class, 'getPadres']);
    Route::get('/contadores_personal', [AdminController::class, 'getContadores']);
    
    // --- Rutas del Panel del Padre ---
    Route::get('/padre/hijos', [AdminController::class, 'getPadreHijos']);
    Route::get('/padre/comprobantes', [AdminController::class, 'getComprobantes']);
    
    // --- Gestión de Matrículas y Pagos ---
    Route::get('/matriculas', [AdminController::class, 'getMatriculas']);
    Route::post('/matriculas', [AdminController::class, 'addMatricula']);
    Route::post('/comprobante', [AdminController::class, 'uploadComprobante']);

    // Ruta de logout y user info (Base de Sanctum)
    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada correctamente'], 200);
    });
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});