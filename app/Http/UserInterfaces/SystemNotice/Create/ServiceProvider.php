<?php

namespace App\Http\UserInterfaces\SystemNotice\Create;

use App\DataAccess\SystemNoticeRead\Create\DataAccess;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\SystemNotice\Create\UseCases\DataAccessInterface;
use Switchm\SmartApi\Components\SystemNotice\Create\UseCases\InputBoundary;
use Switchm\SmartApi\Components\SystemNotice\Create\UseCases\Interactor;
use Switchm\SmartApi\Components\SystemNotice\Create\UseCases\OutputBoundary;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(InputBoundary::class, Interactor::class);

        $this->app->bind(OutputBoundary::class, ListPresenter::class);

        $this->app->bind(DataAccessInterface::class, DataAccess::class);

        $this->app->singleton(PresenterOutput::class);
    }
}
