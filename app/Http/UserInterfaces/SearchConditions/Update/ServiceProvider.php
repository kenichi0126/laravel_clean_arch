<?php

namespace App\Http\UserInterfaces\SearchConditions\Update;

use App\DataAccess\SearchConditions\Update\DataAccess;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\SearchConditions\Update\UseCases\DataAccessInterface;
use Switchm\SmartApi\Components\SearchConditions\Update\UseCases\InputBoundary;
use Switchm\SmartApi\Components\SearchConditions\Update\UseCases\Interactor;
use Switchm\SmartApi\Components\SearchConditions\Update\UseCases\OutputBoundary;

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
