<?php

namespace Switchm\SmartApi\Components\TimeBox\Get\UseCases;

use Carbon\Carbon;
use Smart2\CommandModel\Eloquent\Member;
use Switchm\SmartApi\Queries\Dao\Rdb\TimeBoxDao;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Interactor implements InputBoundary
{
    private $timeBoxDao;

    private $outputBoundary;

    /**
     * Interactor constructor.
     * @param TimeBoxDao $timeBoxDao
     * @param OutputBoundary $outputBoundary
     */
    public function __construct(TimeBoxDao $timeBoxDao, OutputBoundary $outputBoundary)
    {
        $this->timeBoxDao = $timeBoxDao;
        $this->outputBoundary = $outputBoundary;
    }

    /**
     * @param InputData $input
     */
    public function __invoke(InputData $input): void
    {
        $data = $this->timeBoxDao->latest($input->regionId());

        if ($data === null) {
            // TODO - konno: $this->timeBoxDao->latestでModelNotFoundException
            throw new NotFoundHttpException();
        }

        $data->started_at = $timeBoxStartedAt = new Carbon($data->started_at);
        $data->ended_at = $timeBoxEndedAt = new Carbon($data->ended_at);

        if ($this->isTrial($input->trialSettings())) {
            $endedAt = new Carbon($input->trialSettings()['search_range']['end']);

            if ($this->isOutOfDate($endedAt, $data->started_at)) {
                $data->started_at = $endedAt;
                $data->ended_at = $endedAt;
            }
        }
        $output = new OutputData(
            $data->id,
            $data->region_id,
            $data->start_date,
            $data->duration,
            $data->version,
            $data->started_at,
            $data->ended_at,
            $data->panelers_number,
            $data->households_number
        );

        ($this->outputBoundary)($output);
    }

    /**
     * @param Member $trialSettings
     * @return bool
     */
    private function isTrial(Member $trialSettings): bool
    {
        if ($trialSettings === null || $trialSettings['search_range'] === null || $trialSettings['search_range']['start'] === null || $trialSettings['search_range']['end'] === null) {
            return false;
        }
        return true;
    }

    /**
     * @param Carbon $trialEndedAt
     * @param Carbon $timeBoxStartedAt
     * @return bool
     */
    private function isOutOfDate(Carbon $trialEndedAt, Carbon $timeBoxStartedAt): bool
    {
        return $trialEndedAt->lessThan($timeBoxStartedAt);
    }
}
