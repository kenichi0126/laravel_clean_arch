<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class ValidatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Validator::extend('str_len_over', 'Smart2\Application\Validation\ParameterValidator@validateStrLenOver');
        Validator::extend('str_in_comma_or_doublequote', 'Smart2\Application\Validation\ParameterValidator@validateStrInCommaOrDoublequote');
        Validator::extend('reset_password_user', 'Smart2\Application\Validation\ResetPasswordValidator@validateResetPasswordUser');
        Validator::extend('reset_password_token', 'Smart2\Application\Validation\ResetPasswordValidator@validateResetPasswordToken');
    }
}
