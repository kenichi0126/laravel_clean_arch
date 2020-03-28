<?php

namespace App\Http\UserInterfaces\ProductNames\Get;

use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\ProductNames\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\ProductNames\Get\UseCases\OutputData;

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
        if (empty($output->data())) {
            abort(404);
        }

        $this->presenterOutput->set(
            $output->data()
        );
    }
}
