<?php

namespace App\Http\UserInterfaces\CommercialAdvertising\Get;

use Switchm\SmartApi\Components\CommercialAdvertising\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\CommercialAdvertising\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Common\OutputCsvTrait;

class CsvPresenter implements OutputBoundary
{
    use OutputCsvTrait;
    use PresenterTrait;

    /**
     * @param OutputData $output
     */
    public function __invoke(OutputData $output): void
    {
        $list = $this->createTableData($output->data(), $output->channels(), $output->csvFlag());

        $ret = array_merge($list['list'], $output->rp());

        $filename = 'SMI_CMad_count_' . $output->startDateTimeShort() . '-' . $output->endDateTimeShort() . '.csv';
        $data = $this->outputCsv($filename, $output->header(), $ret);
        $data->send();
    }
}
