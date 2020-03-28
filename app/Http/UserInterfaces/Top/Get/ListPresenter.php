<?php

namespace App\Http\UserInterfaces\Top\Get;

use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\Top\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\Top\Get\UseCases\OutputData;

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
        if (empty($output->date())) {
            abort(404);
        }

        $this->presenterOutput->set(json_encode([
            'date' => $output->date(),
            'programs' => $output->programs(),
            'charts' => $output->charts(),
            'categories' => $output->categories(),
            'phNumbers' => $output->phNumbers(),
        ], JSON_NUMERIC_CHECK));
    }
}
