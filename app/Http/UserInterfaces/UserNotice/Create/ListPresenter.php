<?php

namespace App\Http\UserInterfaces\UserNotice\Create;

use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\UserNotice\Create\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\UserNotice\Create\UseCases\OutputData;

class ListPresenter implements OutputBoundary
{
    private $presenterOutput;

    public function __construct(PresenterOutput $presenterOutput)
    {
        $this->presenterOutput = $presenterOutput;
    }

    /**
     * @param OutputData $output
     */
    public function __invoke(OutputData $output): void
    {
        $this->presenterOutput->set(response(null, 204));
    }
}
