<?php

namespace App\Http\UserInterfaces\PanelStructure\Get;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\PanelStructure\Get\UseCases\InputBoundary;
use Switchm\SmartApi\Components\PanelStructure\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\PanelStructure\Get\UseCases\OutputBoundary;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(InputBoundary::class, Interactor::class);

        $this->app->bind(OutputBoundary::class, ListPresenter::class);

        $this->app->singleton(PresenterOutput::class);
    }
}
