<?php

namespace App\Http\UserInterfaces\ProgramList\Get;

use Switchm\SmartApi\Components\Common\OutputCsvTrait;
use Switchm\SmartApi\Components\ProgramList\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\ProgramList\Get\UseCases\OutputData;

class CsvPresenter implements OutputBoundary
{
    use OutputCsvTrait;

    private $presenterOutput;

    public function __construct()
    {
    }

    /**
     * @param OutputData $outputData
     * @return mixed
     */
    public function __invoke(OutputData $outputData): void
    {
        $filename = 'SMI_TVPrglist_' . $outputData->startDateShort() . '-' . $outputData->endDateShort() . '.csv';
        $data = $this->outputCsv(
            $filename,
            $outputData->header(),
            $outputData->list(),
            false
        );
        $data->send();
    }
}
