<?php

namespace App\Http\UserInterfaces\RankingCommercial\Get;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\RankingCommercial\Get\UseCases\InputBoundary;
use Switchm\SmartApi\Components\RankingCommercial\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\RankingCommercial\Get\UseCases\OutputBoundary;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(InputBoundary::class, Interactor::class);

        if (\Request::path() === 'api/rankings/commercial') {
            $this->app->bind(OutputBoundary::class, ListPresenter::class);
        }

        if (\Request::path() === 'api/rankings/commercialCsv') {
            $this->app->bind(OutputBoundary::class, CsvPresenter::class);
        }

        $this->app->singleton(PresenterOutput::class);
    }
}
