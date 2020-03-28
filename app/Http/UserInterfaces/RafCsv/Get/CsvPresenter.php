<?php

namespace App\Http\UserInterfaces\RafCsv\Get;

use Switchm\SmartApi\Components\Common\OutputCsvTrait;
use Switchm\SmartApi\Components\RafCsv\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\RafCsv\Get\UseCases\OutputData;

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
        $filename = 'SMI_RF_' . $outputData->division() . '_' . $outputData->startDateShort() . '-' . $outputData->endDateShort() . '.csv';
        $data = $this->outputCsvGenerator(
            $filename,
            $outputData->header(),
            $outputData->generator(),
            $outputData->data()
        );
        $data->send();
    }
}
