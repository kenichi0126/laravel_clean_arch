<?php

namespace App\Http\UserInterfaces\RankingCommercial\Get;

use Switchm\SmartApi\Components\Common\OutputCsvTrait;
use Switchm\SmartApi\Components\RankingCommercial\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\RankingCommercial\Get\UseCases\OutputData;

class CsvPresenter implements OutputBoundary
{
    use OutputCsvTrait;

    public function __construct()
    {
    }

    /**
     * @param OutputData $outputData
     */
    public function __invoke(OutputData $outputData): void
    {
        $filename = 'SMI_Ranking_CM_' . $outputData->startDateShort() . '-' . $outputData->endDateShort() . '.csv';

        $data = $this->outputCsv(
            $filename,
            $outputData->header(),
            $outputData->list()
        );

        $data->send();
    }
}
