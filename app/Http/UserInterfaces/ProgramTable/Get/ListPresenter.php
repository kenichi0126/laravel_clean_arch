<?php

namespace App\Http\UserInterfaces\ProgramTable\Get;

use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\ProgramTable\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\ProgramTable\Get\UseCases\OutputData;

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
        if (empty($outputData->data())) {
            abort(404);
        }

        $this->presenterOutput->set([
            'data' => $outputData->data(),
            'draw' => $outputData->draw(),
            'dateList' => $outputData->dateList(),
            'header' => [],
            //TODO - takata:UIを修正後に修正
//            'header' => $outputData->header()
        ]);
    }
}
