<?php

namespace App\Http\UserInterfaces\ProgramTable\Get;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\ProgramTable\Get\UseCases\InputBoundary;
use Switchm\SmartApi\Components\ProgramTable\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\ProgramTable\Get\UseCases\OutputBoundary;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(InputBoundary::class, Interactor::class);

        $this->app->bind(OutputBoundary::class, ListPresenter::class);

        $this->app->singleton(PresenterOutput::class);
    }
}
