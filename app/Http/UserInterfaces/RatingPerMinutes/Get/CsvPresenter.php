<?php

namespace App\Http\UserInterfaces\RatingPerMinutes\Get;

use Switchm\SmartApi\Components\Common\OutputCsvTrait;
use Switchm\SmartApi\Components\RatingPerMinutes\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\RatingPerMinutes\Get\UseCases\OutputData;

class CsvPresenter implements OutputBoundary
{
    use OutputCsvTrait;

    /**
     * @param OutputData $output
     */
    public function __invoke(OutputData $output): void
    {
        $filename = $this->getFileName(
            $output->channelType(),
            $output->displayType(),
            $output->aggregateType(),
            $output->startDateShort(),
            $output->endDateShort()
        );

        $data = $this->outputCsv($filename, $output->header(), $output->data());
        $data->send();
    }

    private function getFileName(string $channelType, string $displayType, string $aggregateType, string $startDateShort, string $endDateShort)
    {
        $preFix = 'SMI_TIME';

        if ($channelType === 'summary') {
            $preFix .= 'sum_';
        } elseif (in_array($channelType, [
                'dt1',
                'dt2',
                'bs1',
                'bs2',
                'bs3',
            ]) && $displayType == 'channelBy') {
            $preFix .= 'ch_';
        } else {
            $preFix .= 'day_';
        }

        $postFix = '';

        if ($aggregateType == 'hourly') {
            $postFix = '';
        } else {
            $postFix = '-m' . $aggregateType;
        }

        $filename = $preFix . $startDateShort . '-' . $endDateShort . $postFix . '.csv';

        return $filename;
    }
}
