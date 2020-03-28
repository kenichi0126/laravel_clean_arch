<?php

namespace App\Http\UserInterfaces\SampleCount\Get;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\SampleCount\Get\UseCases\InputBoundary;
use Switchm\SmartApi\Components\SampleCount\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\SampleCount\Get\UseCases\OutputBoundary;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(InputBoundary::class, Interactor::class);

        $this->app->bind(OutputBoundary::class, ListPresenter::class);

        $this->app->singleton(PresenterOutput::class);
    }
}