<?php

namespace App\DataAccess\Setting\Save;

use App\DataProxy\MemberSystemSettings;
use App\DataProxy\MemberSystemSettingsInterface;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class ServiceProvider.
 */
class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(MemberSystemSettingsInterface::class, MemberSystemSettings::class);
    }
}
