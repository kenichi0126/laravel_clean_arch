<?php

namespace Switchm\SmartApi\Components\HourlyReport\Get\UseCases;

use Carbon\Carbon;
use Switchm\SmartApi\Queries\Dao\Rdb\HourlyReportDao;

class Interactor implements InputBoundary
{
    private $hourlyReportDao;

    private $outputBoundary;

    /**
     * @param HourlyReportDao $hourlyReportDao
     * @param OutputBoundary $outputBoundary
     */
    public function __construct(HourlyReportDao $hourlyReportDao, OutputBoundary $outputBoundary)
    {
        $this->hourlyReportDao = $hourlyReportDao;
        $this->outputBoundary = $outputBoundary;
    }

    /**
     * @param InputData $inputData
     */
    public function __invoke(InputData $inputData): void
    {
        $params = [
            $inputData->regionId(),
        ];

        $data = $this->hourlyReportDao->latest(...$params);

        if ($data->datetime === null) {
            $data = new \stdClass;
            $data->datetime = Carbon::now()->format('Y-m-d');
        }
        $date = new Carbon($data->datetime);

        if ($inputData->trialSettings() === null
            || $inputData->trialSettings()['search_range'] === null
            || $inputData->trialSettings()['search_range']['start'] === null
            || $inputData->trialSettings()['search_range']['end'] === null
        ) {
            $output = new OutputData((array) $data);
            ($this->outputBoundary)($output);
            return;
        }

        $endedAt = new Carbon($inputData->trialSettings()['search_range']['end']);

        if ($endedAt->lessThan($date)) {
            $endedAt->hour($date->hour);
            $endedAt->minute($date->minute);
            $endedAt->second($date->second);
            $data->datetime = $endedAt->format('Y-m-d H:i:s');
        }

        $output = new OutputData((array) $data);
        ($this->outputBoundary)($output);
    }
}
