<?php

namespace App\Http\UserInterfaces\CommercialGrp\Get;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\CommercialGrp\Get\UseCases\InputBoundary;
use Switchm\SmartApi\Components\CommercialGrp\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\CommercialGrp\Get\UseCases\OutputBoundary;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(InputBoundary::class, Interactor::class);

        if (\Request::path() === 'api/commercials/grp') {
            $this->app->bind(OutputBoundary::class, ListPresenter::class);
        }

        if (\Request::path() === 'api/commercials/grpCsv') {
            $this->app->bind(OutputBoundary::class, CsvPresenter::class);
        }

        $this->app->singleton(PresenterOutput::class);
    }
}
