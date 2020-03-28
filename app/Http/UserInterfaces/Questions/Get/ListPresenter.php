<?php

namespace App\Http\UserInterfaces\Questions\Get;

use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\Questions\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\Questions\Get\UseCases\OutputData;

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
            $output->data()
        );
    }
}
