<?php

namespace App\Http\UserInterfaces\SettingAttrDivs\Delete;

use App\DataAccess\SettingAttrDivs\Delete\DataAccess;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\SettingAttrDivs\Delete\UseCases\DataAccessInterface;
use Switchm\SmartApi\Components\SettingAttrDivs\Delete\UseCases\InputBoundary;
use Switchm\SmartApi\Components\SettingAttrDivs\Delete\UseCases\Interactor;
use Switchm\SmartApi\Components\SettingAttrDivs\Delete\UseCases\OutputBoundary;

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
