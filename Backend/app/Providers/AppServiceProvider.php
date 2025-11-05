<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route; // CRÍTICO: Importar la clase Route

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // --- BLOQUE CRÍTICO PARA CARGAR RUTAS API ---
        Route::prefix('api')
            ->middleware('api') // Este middleware es necesario para la gestión de tokens
            ->group(base_path('routes/api.php'));
        // ------------------------------------------

        // Cargar las rutas web (por si acaso)
        Route::middleware('web')
            ->group(base_path('routes/web.php'));
    }
}