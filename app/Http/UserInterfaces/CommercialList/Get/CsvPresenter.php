<?php

namespace App\Http\UserInterfaces\CommercialList\Get;

use Switchm\SmartApi\Components\CommercialList\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\CommercialList\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Common\OutputCsvTrait;

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
        $filename = 'SMI_CMlist_' . $outputData->startDateShort() . '-' . $outputData->endDateShort() . '.csv';
        $data = $this->outputCsv(
            $filename,
            $outputData->header(),
            $outputData->list()
        );
        $data->send();
    }
}
