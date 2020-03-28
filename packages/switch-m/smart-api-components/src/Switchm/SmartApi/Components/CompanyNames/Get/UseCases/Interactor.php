<?php

namespace Switchm\SmartApi\Components\CompanyNames\Get\UseCases;

use Switchm\SmartApi\Queries\Dao\Rdb\CompanyNamesDao;

class Interactor implements InputBoundary
{
    private $companyNamesDao;

    private $outputBoundary;

    /**
     * Interactor constructor.
     * @param CompanyNamesDao $companyNamesDao
     * @param OutputBoundary $outputBoundary
     */
    public function __construct(CompanyNamesDao $companyNamesDao, OutputBoundary $outputBoundary)
    {
        $this->companyNamesDao = $companyNamesDao;
        $this->outputBoundary = $outputBoundary;
    }

    /**
     * @param InputData $inputData
     */
    public function __invoke(InputData $inputData): void
    {
        $split = mb_split(' ', mb_convert_kana($inputData->companyName(), 'rnas'));
        $companyNames = [];

        foreach ($split as &$val) {
            $val = trim($val);

            if (!empty($val)) {
                $companyNames[] = '%' . $val . '%';
            }
        }
        $params = [
            'startTime' => $inputData->startTimeShort(),
            'endTime' => $inputData->endTimeShort(),
            'companyNames' => $companyNames,
            'startDate' => $inputData->startDate(),
            'endDate' => $inputData->endDate(),
            'progIds' => $inputData->progIds(),
            'regionId' => $inputData->regionId(),
            'companyId' => $inputData->companyId(),
            'channels' => $inputData->channels(),
            'cmType' => $inputData->cmType(),
            'cmSeconds' => $inputData->cmSeconds(),
            'productIds' => $inputData->productIds(),
        ];

        $data = $this->companyNamesDao->findForCondition($params, $inputData->straddlingFlg());

        $outputData = new OutputData($data);

        ($this->outputBoundary)($outputData);
    }
}
