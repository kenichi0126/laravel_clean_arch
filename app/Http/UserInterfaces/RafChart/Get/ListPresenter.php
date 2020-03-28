<?php

namespace App\Http\UserInterfaces\RafChart\Get;

use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\RafChart\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\RafChart\Get\UseCases\OutputData;

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
            [
                'series' => $output->series(),
                'categories' => $output->categories(),
                'tableData' => [
                    'average' => $output->average(),
                    'overOne' => $output->overOne(),
                    'grp' => $output->grp(),
                ],
                'csvButtonInfo' => $output->csvButtonInfo(),
//          TODO - konno: maribelleで対応したらコメント解除する
//            'header' => $output->header()
            ]
        );
    }
}
