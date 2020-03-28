<?php

namespace App\Http\UserInterfaces\ProgramList\Get;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\ProgramList\Get\UseCases\InputBoundary;
use Switchm\SmartApi\Components\ProgramList\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\ProgramList\Get\UseCases\OutputBoundary;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(InputBoundary::class, Interactor::class);

        if (\Request::path() === 'api/programs') {
            $this->app->bind(OutputBoundary::class, ListPresenter::class);
        }

        if (\Request::path() === 'api/programs/listCsv') {
            $this->app->bind(OutputBoundary::class, CsvPresenter::class);
        }

        $this->app->singleton(PresenterOutput::class);
    }
}
