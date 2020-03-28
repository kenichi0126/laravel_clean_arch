<?php

namespace App\Http\UserInterfaces\SettingAttrDivs\Update;

use App\DataAccess\SettingAttrDivs\Update\DataAccess;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\SettingAttrDivs\Update\UseCases\DataAccessInterface;
use Switchm\SmartApi\Components\SettingAttrDivs\Update\UseCases\InputBoundary;
use Switchm\SmartApi\Components\SettingAttrDivs\Update\UseCases\Interactor;
use Switchm\SmartApi\Components\SettingAttrDivs\Update\UseCases\OutputBoundary;

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
