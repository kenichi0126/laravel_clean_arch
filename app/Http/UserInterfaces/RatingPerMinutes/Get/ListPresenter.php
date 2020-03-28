<?php

namespace App\Http\UserInterfaces\RatingPerMinutes\Get;

use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\RatingPerMinutes\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\RatingPerMinutes\Get\UseCases\OutputData;

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
     * @return mixed
     */
    public function __invoke(OutputData $output): void
    {
        $this->presenterOutput->set([
            'data' => $output->data(),
            'draw' => $output->draw(),
            'recordsFiltered' => $output->recordsFiltered(),
            'recordsTotal' => $output->recordsTotal(),
            'dateList' => $output->dateList(),
//          TODO: takata/maribelleで対応したらコメント解除する
//            'header' => $outputData->header()
        ]);
    }
}
