<?php

namespace Smart2\Application\Services\LoginFailed;

use Smart2\CommandModel\Eloquent\LoginFailed;

class LoginFailedService
{
    public function __construct()
    {
    }

    public function __invoke(): array
    {
    }

    public function add(?string $login_id): bool
    {
        if (empty(trim($login_id))) {
            return false;
        }

        $loginFailed = new LoginFailed();
        $loginFailed->login_id = $login_id;
        $loginFailed->info = \Request::ip();

        return $loginFailed->save();
    }
}
