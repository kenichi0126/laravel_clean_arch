<?php

namespace App\Http\UserInterfaces\SearchConditions\Create;

use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\SearchConditions\Create\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\SearchConditions\Create\UseCases\OutputData;

/**
 * Class Presenter.
 */
final class Presenter implements OutputBoundary
{
    const UPPER_LIMIT_ERROR = 'upper_limit_error';

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
        if ($outputData->result()) {
            $this->presenterOutput->set(response(null, 204));
        } else {
            $this->presenterOutput->set(response(self::UPPER_LIMIT_ERROR, 400));
        }
    }
}
