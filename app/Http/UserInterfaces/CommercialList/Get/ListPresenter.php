<?php

namespace App\Http\UserInterfaces\CommercialList\Get;

use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\CommercialList\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\CommercialList\Get\UseCases\OutputData;

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
//          TODO: takata/maribelleで対応したらコメント解除する
//            'header' => $outputData->header()
        ]);
    }
}
