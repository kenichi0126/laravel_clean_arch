<?php

namespace App\Http\UserInterfaces\Setting\Save;

use App\DataAccess\Setting\Save\DataAccess;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\Setting\Save\UseCases\DataAccessInterface;
use Switchm\SmartApi\Components\Setting\Save\UseCases\InputBoundary;
use Switchm\SmartApi\Components\Setting\Save\UseCases\Interactor;
use Switchm\SmartApi\Components\Setting\Save\UseCases\OutputBoundary;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(InputBoundary::class, Interactor::class);

        $this->app->bind(OutputBoundary::class, Presenter::class);

        $this->app->bind(DataAccessInterface::class, DataAccess::class);

        $this->app->singleton(PresenterOutput::class);
    }
}
