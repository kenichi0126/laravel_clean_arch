<?php

namespace App\DataAccess\SettingAttrDivs\Delete;

use App\DataProxy\AttrDivs;
use App\DataProxy\AttrDivsInterface;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class ServiceProvider.
 */
class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AttrDivsInterface::class, AttrDivs::class);
    }
}
