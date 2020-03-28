<?php

namespace App\Http\UserInterfaces\Channels\Get;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\Channels\Get\UseCases\InputBoundary;
use Switchm\SmartApi\Components\Channels\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\Channels\Get\UseCases\OutputBoundary;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(InputBoundary::class, Interactor::class);

        $this->app->bind(OutputBoundary::class, ListPresenter::class);

        $this->app->singleton(PresenterOutput::class);
    }
}
