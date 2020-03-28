<?php

namespace App\Http\UserInterfaces\SearchConditions\Update;

use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\SearchConditions\Update\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\SearchConditions\Update\UseCases\OutputData;

/**
 * Class Presenter.
 */
final class Presenter implements OutputBoundary
{
    private $presenterOutput;

    /**
     * Presenter constructor.
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
        $this->presenterOutput->set(response(null, 204));
    }
}
