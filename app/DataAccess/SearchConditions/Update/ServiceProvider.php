<?php

namespace App\DataAccess\SearchConditions\Update;

use App\DataProxy\SearchConditions;
use App\DataProxy\SearchConditionsInterface;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class ServiceProvider.
 */
class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SearchConditionsInterface::class, SearchConditions::class);
    }
}
