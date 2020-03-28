<?php

namespace App\Http\UserInterfaces\SearchConditions\Create;

use App\DataAccess\SearchConditions\Create\DataAccess;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\SearchConditions\Create\UseCases\DataAccessInterface;
use Switchm\SmartApi\Components\SearchConditions\Create\UseCases\InputBoundary;
use Switchm\SmartApi\Components\SearchConditions\Create\UseCases\Interactor;
use Switchm\SmartApi\Components\SearchConditions\Create\UseCases\OutputBoundary;

/**
 * Class ServiceProvider.
 */
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
