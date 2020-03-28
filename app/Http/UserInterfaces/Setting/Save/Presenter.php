<?php

namespace App\Http\UserInterfaces\Setting\Save;

use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\Setting\Save\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\Setting\Save\UseCases\OutputData;

class Presenter implements OutputBoundary
{
    private $presenterOutput;

    public function __construct(PresenterOutput $presenterOutput)
    {
        $this->presenterOutput = $presenterOutput;
    }

    /**
     * @param OutputData $output
     */
    public function __invoke(OutputData $output)
    {
        return $this->presenterOutput->set(response(null, 204));
    }
}
