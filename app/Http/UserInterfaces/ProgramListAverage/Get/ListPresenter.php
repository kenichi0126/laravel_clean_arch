<?php

namespace App\Http\UserInterfaces\ProgramListAverage\Get;

use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\ProgramListAverage\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\ProgramListAverage\Get\UseCases\OutputData;

class ListPresenter implements OutputBoundary
{
    private $presenterOutput;

    /**
     * ListPresenter constructor.
     * @param PresenterOutput $presenterOutput
     */
    public function __construct(PresenterOutput $presenterOutput)
    {
        $this->presenterOutput = $presenterOutput;
    }

    /**
     * @param OutputData $output
     */
    public function __invoke(OutputData $output): void
    {
        $this->presenterOutput->set($output->data());
    }
}
