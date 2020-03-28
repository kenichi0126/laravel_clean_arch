<?php

namespace App\Http\UserInterfaces\CommercialAdvertising\Get;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\CommercialAdvertising\Get\UseCases\InputBoundary;
use Switchm\SmartApi\Components\CommercialAdvertising\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\CommercialAdvertising\Get\UseCases\OutputBoundary;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(InputBoundary::class, Interactor::class);

        if (\Request::path() === 'api/commercials/advertising') {
            $this->app->bind(OutputBoundary::class, ListPresenter::class);
        }

        if (\Request::path() === 'api/commercials/advertisingCsv') {
            $this->app->bind(OutputBoundary::class, CsvPresenter::class);
        }

        $this->app->singleton(PresenterOutput::class);
    }
}
