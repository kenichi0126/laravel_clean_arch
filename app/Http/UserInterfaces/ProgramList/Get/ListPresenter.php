<?php

namespace App\Http\UserInterfaces\ProgramList\Get;

use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\ProgramList\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\ProgramList\Get\UseCases\OutputData;

final class ListPresenter implements OutputBoundary
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
     * @param OutputData $outputData
     */
    public function __invoke(OutputData $outputData): void
    {
        $this->presenterOutput->set([
            'data' => $outputData->list(),
            'draw' => $outputData->draw(),
            'recordsFiltered' => $outputData->cnt(),
            'recordsTotal' => $outputData->cnt(),
            'header' => $outputData->header(),
        ]);
    }
}
