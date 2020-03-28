<?php

namespace App\Http\UserInterfaces\SearchConditions\Get;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\SearchConditions\Get\UseCases\InputBoundary;
use Switchm\SmartApi\Components\SearchConditions\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\SearchConditions\Get\UseCases\OutputBoundary;

/**
 * Class ServiceProvider.
 */
class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(InputBoundary::class, Interactor::class);
        $this->app->bind(OutputBoundary::class, Presenter::class);
        $this->app->singleton(PresenterOutput::class);
    }
}
