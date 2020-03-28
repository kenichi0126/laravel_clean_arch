<?php

namespace App\Http\UserInterfaces\CommercialGrp\Get;

use Switchm\SmartApi\Components\CommercialGrp\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\CommercialGrp\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Common\OutputCsvTrait;

class CsvPresenter implements OutputBoundary
{
    use OutputCsvTrait;
    use PresenterTrait;

    public function __construct()
    {
    }

    public function __invoke(OutputData $outputData): void
    {
        $list = [];
        $filename = 'SMI_CMgrp_' . $outputData->startDateShort() . '-' . $outputData->endDateShort() . '.csv';

        if (count($outputData->list()) > 0) {
            $list = $this->convertPeriodTableData(
                json_decode(json_encode($outputData->list()), true),
                $outputData->division(),
                $outputData->codes(),
                $outputData->codeList(),
                $outputData->dataType()
            );
        }

        $data = $this->outputCsv(
            $filename,
            $outputData->header(),
            $this->convertCsvData(
                $list,
                $outputData->division(),
                $outputData->codes(),
                $outputData->codeList(),
                $outputData->period(),
                $outputData->dataType()
            )
        );
        $data->send();
    }
}
