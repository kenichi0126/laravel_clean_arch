<?php

namespace App\Http\UserInterfaces\SampleCount\Get;

use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\SampleCount\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\SampleCount\Get\UseCases\OutputData;

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
        $this->presenterOutput->set(
            $output->cnt()
        );
    }
}
