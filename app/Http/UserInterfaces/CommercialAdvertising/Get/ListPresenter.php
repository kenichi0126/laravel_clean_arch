<?php

namespace App\Http\UserInterfaces\CommercialAdvertising\Get;

use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\CommercialAdvertising\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\CommercialAdvertising\Get\UseCases\OutputData;

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
     * @param OutputData $output
     */
    public function __invoke(OutputData $output): void
    {
        $list = $this->createTableData($output->data(), $output->channels(), $output->csvFlag());

        $this->presenterOutput->set([
            'data' => $list['list'],
            'aggregate' => $list['aggregate'],
            'draw' => $output->draw(),
            'header' => $output->header(),
        ]);
    }
}
