<?php

namespace App\Http\UserInterfaces\ProgramPeriodAverage\Get;

use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\ProgramPeriodAverage\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\ProgramPeriodAverage\Get\UseCases\OutputData;

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
            'data' => $outputData->data(),
            'draw' => $outputData->draw(),
            'recordsFiltered' => $outputData->recordsFiltered(),
            'recordsTotal' => $outputData->recordsTotal(),
            'header' => $outputData->header(),
        ]);
    }
}
