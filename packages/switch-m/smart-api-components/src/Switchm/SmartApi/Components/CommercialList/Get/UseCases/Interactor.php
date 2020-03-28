<?php

namespace Switchm\SmartApi\Components\CommercialList\Get\UseCases;

use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Components\Common\Exceptions\SampleCountException;
use Switchm\SmartApi\Queries\Dao\Dwh\CommercialDao;
use Switchm\SmartApi\Queries\Services\DivisionService;
use Switchm\SmartApi\Queries\Services\ProductService;
use Switchm\SmartApi\Queries\Services\SampleService;

class Interactor implements InputBoundary
{
    private $commercialDao;

    private $rdbCommercialDao;

    private $divisionService;

    private $productService;

    private $sampleService;

    private $outputBoundary;

    private $searchConditionTextAppService;

    /**
     * BaseInteractor constructor.
     * @param CommercialDao $commercialDao
     * @param \Switchm\SmartApi\Queries\Dao\Rdb\CommercialDao $rdbCommercialDao
     * @param DivisionService $divisionService
     * @param ProductService $productService
     * @param SampleService $sampleService
     * @param OutputBoundary $outputBoundary
     * @param SearchConditionTextAppService $searchConditionTextAppService
     */
    public function __construct(
        CommercialDao $commercialDao,
        \Switchm\SmartApi\Queries\Dao\Rdb\CommercialDao $rdbCommercialDao,
        DivisionService $divisionService,
        ProductService $productService,
        SampleService $sampleService,
        OutputBoundary $outputBoundary,
        SearchConditionTextAppService $searchConditionTextAppService
    ) {
        $this->commercialDao = $commercialDao;
        $this->rdbCommercialDao = $rdbCommercialDao;
        $this->divisionService = $divisionService;
        $this->productService = $productService;
        $this->sampleService = $sampleService;
        $this->outputBoundary = $outputBoundary;
        $this->searchConditionTextAppService = $searchConditionTextAppService;
    }

    /**
     * @param InputData $inputData
     * @throws SampleCountException
     * @return mixed
     */
    public function __invoke(InputData $inputData): void
    {
        list($cnt, $tsCnt) = $this->getConditionCrossCount(
            $inputData->division(),
            $inputData->conditionCross(),
            $inputData->startDate(),
            $inputData->endDate(),
            $inputData->regionId(),
            $inputData->sampleCountMaxNumber(),
            $inputData->dataTypeFlags()
        );

        $listParams = $this->getParams($inputData);

        $list = $this->getList($inputData->division(), $inputData->baseDivision(), ...$listParams);

        $headerParams = array_merge($listParams, [$cnt, $tsCnt]);

        $header = $this->getHeader($inputData->csvFlag(), $headerParams);

        $outputData = $this->produceOutputData($list['list'], $inputData->draw(), $list['cnt'], $inputData->startDateShort(), $inputData->endDateShort(), $header);

        ($this->outputBoundary)($outputData);
    }

    /**
     * @param string $division
     * @param array $conditionCross
     * @param string $startDate
     * @param string $endDate
     * @param int $regionId
     * @param int $sampleCountMaxNumber
     * @param array $dataTypeFlags
     * @throws SampleCountException
     * @return array
     */
    protected function getConditionCrossCount(
        string $division,
        array $conditionCross,
        string $startDate,
        string $endDate,
        int $regionId,
        int $sampleCountMaxNumber,
        array $dataTypeFlags
    ): array {
        $cnt = 0;
        $tsCnt = 0;

        if ($division === 'condition_cross') {
            if ($dataTypeFlags['isRt']) {
                $cnt = $this->sampleService->getConditionCrossCount($conditionCross, $startDate, $endDate, $regionId, true);

                if ($cnt < $sampleCountMaxNumber) {
                    throw new SampleCountException($sampleCountMaxNumber);
                }
            }

            if ($dataTypeFlags['isTs'] || $dataTypeFlags['isGross'] || $dataTypeFlags['isRtTotal']) {
                $tsCnt = $this->sampleService->getConditionCrossCount($conditionCross, $startDate, $endDate, $regionId, false);

                if ($tsCnt < $sampleCountMaxNumber) {
                    throw new SampleCountException($sampleCountMaxNumber);
                }
            }
        }
        return [$cnt, $tsCnt];
    }

    /**
     * @param InputData $input
     * @return array
     */
    protected function getParams(InputData $input): array
    {
        $codeList = $this->divisionService->getCodeList($input->division(), $input->regionId(), $input->userId(), $input->baseDivision());
        $companyIds = $this->productService->getCompanyIds($input->productIds(), $input->companyIds());
        return [
            $input->startDate(),
            $input->endDate(),
            $input->startTimeShort(),
            $input->endTimeShort(),
            $input->cmType(),
            $input->cmSeconds(),
            $input->progIds(),
            $input->regionId(),
            $input->division(),
            $input->codes(),
            $input->conditionCross(),
            $companyIds,
            $input->productIds(),
            $input->cmIds(),
            $input->channels(),
            $input->order(),
            $input->page(),
            $input->dispCount(),
            $input->conv15SecFlag(),
            $input->straddlingFlg(),
            $codeList,
            $input->csvFlag(),
            $input->dataType(),
            $input->cmMaterialFlag(),
            $input->cmTypeFlag(),
            $input->codeNumber(),
            $input->sampleCodePrefix(),
            $input->sampleCodeNumberPrefix(),
            $input->selectedPersonalName(),
            $input->dataTypes(),
            $input->dataTypeFlags(),
        ];
    }

    /**
     * @param $division
     * @param $baseDivision
     * @param array $params
     * @return array
     */
    protected function getList($division, $baseDivision, ...$params): array
    {
        if (in_array($division, $baseDivision)) {
            return $this->rdbCommercialDao->searchList(...$params);
        }
        return $this->commercialDao->searchListOriginalDivs(...$params);
    }

    /**
     * @param int $csvFlg
     * @param array $params
     * @return array
     */
    protected function getHeader($csvFlg, array $params): array
    {
        if ($csvFlg === '1') {
            return $this->searchConditionTextAppService->getListCsv(...$params);
        }
        return $this->searchConditionTextAppService->getListHeader(...$params);
    }

    /**
     * @param $list
     * @param $draw
     * @param $cnt
     * @param $startDateShort
     * @param $endDateShort
     * @param $header
     * @return OutputData
     */
    protected function produceOutputData($list, $draw, $cnt, $startDateShort, $endDateShort, $header): OutputData
    {
        return new OutputData($list, $draw, $cnt, $startDateShort, $endDateShort, $header);
    }
}
