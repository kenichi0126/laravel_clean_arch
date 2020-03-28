<?php

namespace Switchm\SmartApi\Components\ProductNames\Get\UseCases;

use Switchm\SmartApi\Queries\Dao\Rdb\ProductDao;

class Interactor implements InputBoundary
{
    private $productDao;

    private $outputBoundary;

    /**
     * Interactor constructor.
     * @param ProductDao $productDao
     * @param OutputBoundary $outputBoundary
     */
    public function __construct(ProductDao $productDao, OutputBoundary $outputBoundary)
    {
        $this->productDao = $productDao;
        $this->outputBoundary = $outputBoundary;
    }

    /**
     * @param InputData $inputData
     */
    public function __invoke(InputData $inputData): void
    {
        $split = mb_split(' ', mb_convert_kana($inputData->productName(), 'rnas'));
        $productNames = [];

        foreach ($split as &$val) {
            $val = trim($val);

            if (!empty($val)) {
                $productNames[] = '%' . $val . '%';
            }
        }

        $data = $this->productDao->search([
            'companyIds' => $inputData->companyIds(),
            'productNames' => $productNames,
            'startDate' => $inputData->startDate(),
            'endDate' => $inputData->endDate(),
            'startTimeHour' => $inputData->startHour(),
            'startTimeMin' => $inputData->startMinute(),
            'endTimeHour' => $inputData->endHour(),
            'endTimeMin' => $inputData->endMinute(),
            'regionIds' => $inputData->regionIds(),
            'productIds' => $inputData->productIds(),
            'channels' => $inputData->channels(),
            'cmType' => $inputData->cmType(),
            'cmSeconds' => $inputData->cmSeconds(),
            'progIds' => $inputData->progIds(),
        ]);

        $outputData = new OutputData($data);

        ($this->outputBoundary)($outputData);
    }
}
