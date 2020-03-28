<?php

namespace App\DataAccess\UserNoticeRead\Create;

use App\DataProxy\UserNoticeRead;
use App\DataProxy\UserNoticeReadInterface;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserNoticeReadInterface::class, UserNoticeRead::class);
    }
}
