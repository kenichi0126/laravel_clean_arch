<?php

namespace App\Providers;

use App\Auth\EloquentUserProvider;
use App\Auth\SessionGuard;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        \Auth::extend('session', function (Application $app, string $name, array $config) {
            return new SessionGuard($name, \Auth::createUserProvider($config['provider']), $app['session.store']);
        });

        \Auth::provider('eloquent.member', function (Application $app, array $config) {
            return new EloquentUserProvider($app['hash'], $config['model']);
        });
    }
}
