<?php

namespace App\Http\UserInterfaces\TopRanking\Get;

use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\TopRanking\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\TopRanking\Get\UseCases\OutputData;

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
        if (empty($output->programDate()) && empty($output->cmDate())) {
            abort(404);
        }

        $this->presenterOutput->set(json_encode([
            'program' => $output->program(),
            'company_cm' => $output->company_cm(),
            'product_cm' => $output->product_cm(),
            'programDate' => $output->programDate(),
            'cmDate' => $output->cmDate(),
            'programPhNumbers' => $output->programPhNumbers(),
            'cmPhNumbers' => $output->cmPhNumbers(),
        ], JSON_NUMERIC_CHECK));
    }
}
