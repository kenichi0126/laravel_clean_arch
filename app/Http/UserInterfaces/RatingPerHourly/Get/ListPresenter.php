<?php

namespace App\Http\UserInterfaces\RatingPerHourly\Get;

use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\RatingPerHourly\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\RatingPerHourly\Get\UseCases\OutputData;

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
            'recordsFiltered' => $output->cnt(),
            'recordsTotal' => $output->cnt(),
            'dateList' => $output->dateList(),
//          TODO: takata/maribelleで対応したらコメント解除する
//            'header' => $outputData->header()
        ]);
    }
}
