<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        parent::boot();

        Route::prefix('api')
            ->middleware('api')
            ->namespace(\App\Http\UserInterfaces::class)
            ->group(base_path('routes/next-api.php'));
    }

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        $this->mapApiRoutes();
        $this->mapBrowseRoutes();
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapApiRoutes(): void
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace(\Smart2\Application\Controllers::class)
            ->group(base_path('routes/api.php'));
    }

    /**
     * Define the "browse" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapBrowseRoutes(): void
    {
        Route::middleware('browse')
            ->namespace(\App\Http\Controllers\Browse::class)
            ->group(base_path('routes/browse.php'));
    }
}
