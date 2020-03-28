<?php

namespace App\Http\UserInterfaces\CommercialGrp\Get;

use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\CommercialGrp\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\CommercialGrp\Get\UseCases\OutputData;

class ListPresenter implements OutputBoundary
{
    use PresenterTrait;

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
     * @return mixed
     */
    public function __invoke(OutputData $outputData): void
    {
        $list = $outputData->list();

        if (count($list) > 0) {
            $count = $outputData->list()[0]->rowcount;
            $list = $this->convertPeriodTableData(
                json_decode(json_encode($outputData->list()), true),
                $outputData->division(),
                $outputData->codes(),
                $outputData->codeList(),
                $outputData->dataType()
            );
        } else {
            $count = 0;
        }

        $this->presenterOutput->set([
            'data' => $list,
            'draw' => $outputData->draw(),
            'recordsFiltered' => $count,
            'recordsTotal' => $count,
            // TODO: takata/maribelleで対応したらコメント解除する
            // 'header' => $outputData->header()
        ]);
    }
}
