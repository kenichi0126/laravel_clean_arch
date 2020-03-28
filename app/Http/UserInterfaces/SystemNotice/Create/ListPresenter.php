<?php

namespace App\Http\UserInterfaces\SystemNotice\Create;

use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\SystemNotice\Create\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\SystemNotice\Create\UseCases\OutputData;

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
