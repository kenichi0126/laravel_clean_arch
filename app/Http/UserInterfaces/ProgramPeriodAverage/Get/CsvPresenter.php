<?php

namespace App\Http\UserInterfaces\ProgramPeriodAverage\Get;

use Switchm\SmartApi\Components\Common\OutputCsvTrait;
use Switchm\SmartApi\Components\ProgramPeriodAverage\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\ProgramPeriodAverage\Get\UseCases\OutputData;

class CsvPresenter implements OutputBoundary
{
    use OutputCsvTrait;

    public function __construct()
    {
    }

    /**
     * @param OutputData $outputData
     * @return mixed
     */
    public function __invoke(OutputData $outputData): void
    {
        $filename = 'SMI_BulkAggregate_' . $outputData->startDateShort() . '-' . $outputData->endDateShort() . '.csv';
        $data = $this->outputCsv(
            $filename,
            $outputData->header(),
            $outputData->data()
        );
        $data->send();
    }
}
