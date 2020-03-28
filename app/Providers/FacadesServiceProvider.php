<?php

namespace App\Providers;

use App\Services\UserInfo;
use Illuminate\Support\ServiceProvider;

class FacadesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->app->bind('UserInfo', UserInfo::class);
    }
}
