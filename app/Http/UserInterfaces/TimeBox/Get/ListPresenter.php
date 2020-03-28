<?php

namespace App\Http\UserInterfaces\TimeBox\Get;

use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\TimeBox\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\TimeBox\Get\UseCases\OutputData;

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
                'id' => $output->id(),
                'region_id' => $output->regionId(),
                'start_date' => $output->startDate(),
                'duration' => $output->duration(),
                'version' => $output->version(),
                'started_at' => $output->startedAt()->format('Y-m-d H:i:s'),
                'ended_at' => $output->endedAt()->format('Y-m-d H:i:s'),
                'panelers_number' => $output->panelersNumber(),
                'households_number' => $output->householdsNumber(),
            ]
        );
    }
}
