<?php

namespace App\Http\UserInterfaces\ProgramTableDetail\Get;

use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\ProgramTableDetail\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\ProgramTableDetail\Get\UseCases\OutputData;

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
        $this->presenterOutput->set([
            'data' => $output->data(),
            'headlines' => $output->headlines(),
        ]);
    }
}
