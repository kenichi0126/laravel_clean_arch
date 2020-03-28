<?php

namespace App\DataAccess\SystemNoticeRead\Create;

use App\DataProxy\SystemNoticeRead;
use App\DataProxy\SystemNoticeReadInterface;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SystemNoticeReadInterface::class, SystemNoticeRead::class);
    }
}
