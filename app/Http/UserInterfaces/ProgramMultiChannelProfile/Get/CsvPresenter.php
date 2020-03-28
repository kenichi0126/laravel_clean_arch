<?php

namespace App\Http\UserInterfaces\ProgramMultiChannelProfile\Get;

use Switchm\SmartApi\Components\Common\OutputCsvTrait;
use Switchm\SmartApi\Components\ProgramMultiChannelProfile\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\ProgramMultiChannelProfile\Get\UseCases\OutputData;

class CsvPresenter implements OutputBoundary
{
    use OutputCsvTrait;

    /**
     * CsvPresenter constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param OutputData $output
     */
    public function __invoke(OutputData $output): void
    {
        $filename = 'SMI_ProgramTargetIndex_' . $output->startDateShort() . '-' . $output->endDateShort() . '.csv';

        $data = $this->outputCsv(
            $filename,
            $output->header(),
            $output->data(),
            false
        );

        $data->send();
    }
}
