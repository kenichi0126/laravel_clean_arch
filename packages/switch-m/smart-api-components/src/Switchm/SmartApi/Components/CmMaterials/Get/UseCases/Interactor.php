<?php

namespace Switchm\SmartApi\Components\CmMaterials\Get\UseCases;

use Switchm\SmartApi\Queries\Dao\Rdb\CmMaterialDao;

class Interactor implements InputBoundary
{
    private $cmMaterialDao;

    private $outputBoundary;

    /**
     * @param CmMaterialDao $cmMaterialDao
     * @param OutputBoundary $outputBoundary
     */
    public function __construct(CmMaterialDao $cmMaterialDao, OutputBoundary $outputBoundary)
    {
        $this->cmMaterialDao = $cmMaterialDao;
        $this->outputBoundary = $outputBoundary;
    }

    /**
     * @param InputData $inputData
     */
    public function __invoke(InputData $inputData): void
    {
        $data = $this->cmMaterialDao->search([
            'product_ids' => $inputData->productIds(),
            'start_date' => $inputData->startDate(),
            'end_date' => $inputData->endDate(),
            'start_time_hour' => $inputData->startTimeHour(),
            'start_time_min' => $inputData->startTimeMin(),
            'end_time_hour' => $inputData->endTimeHour(),
            'end_time_min' => $inputData->endTimeMin(),
            'regionId' => $inputData->regionId(),
            'channels' => $inputData->channels(),
            'cmType' => $inputData->cmType(),
            'cmSeconds' => $inputData->cmSeconds(),
            'companyIds' => $inputData->companyIds(),
            'progIds' => $inputData->progIds(),
        ]);

        $output = new OutputData($data);

        ($this->outputBoundary)($output);
    }
}
