<?php

namespace Switchm\SmartApi\Components\CommercialGrp\Get\UseCases;

use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Components\Common\Exceptions\SampleCountException;
use Switchm\SmartApi\Queries\Dao\Dwh\CommercialDao;
use Switchm\SmartApi\Queries\Services\DivisionService;
use Switchm\SmartApi\Queries\Services\ProductService;
use Switchm\SmartApi\Queries\Services\SampleService;

class Interactor implements InputBoundary
{
    protected $divisionService;

    private $commercialDao;

    private $rdbCommercialDao;

    private $productService;

    private $sampleService;

    private $outputBoundary;

    private $searchConditionTextAppService;

    /**
     * Interactor constructor.
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

        list($listParams, $headerParams) = $this->getParams($inputData);

        list($list, $codeList) = $this->getList($inputData->division(), $inputData->regionId(), $inputData->userId(), $inputData->baseDivision(), ...$listParams);

        $headerParams = array_merge($headerParams, [$codeList, $cnt, $tsCnt]);

        $header = $this->getHeader($inputData->csvFlag(), $headerParams);

        $outputData = $this->produceOutputData(
            $list,
            $inputData->draw(),
            $inputData->division(),
            $inputData->codes(),
            $codeList,
            $inputData->period(),
            $inputData->dataType(),
            $inputData->startDateShort(),
            $inputData->endDateShort(),
            $header
        );

        ($this->outputBoundary)($outputData);
    }

    /**
     * @param InputData $inputData
     * @return array
     */
    public function getParams(InputData $inputData): array
    {
        $companyIds = $this->productService->getCompanyIds($inputData->productIds(), $inputData->companyIds());

        return [[
            $inputData->startDate(),
            $inputData->endDate(),
            $inputData->startTimeShort(),
            $inputData->endTimeShort(),
            $inputData->cmType(),
            $inputData->cmSeconds(),
            $inputData->progIds(),
            $inputData->regionId(),
            $inputData->division(),
            $inputData->codes(),
            $inputData->conditionCross(),
            $companyIds,
            $inputData->productIds(),
            $inputData->cmIds(),
            $inputData->channels(),
            $inputData->conv15SecFlag(),
            $inputData->period(),
            $inputData->allChannels(),
            $inputData->straddlingFlg(),
            $inputData->dispCount(),
            $inputData->page(),
            $inputData->csvFlag(),
            $inputData->dataType(),
            $inputData->codeNumber(),
            $inputData->sampleCodePrefix(),
            $inputData->sampleCodeNumberPrefix(),
            $inputData->selectedPersonalName(),
            $inputData->dataTypes(),
            $inputData->dataTypeFlags(),
        ], [
            $inputData->startDate(),
            $inputData->endDate(),
            $inputData->startTimeShort(),
            $inputData->endTimeShort(),
            $inputData->cmType(),
            $inputData->cmSeconds(),
            $inputData->progIds(),
            $inputData->regionId(),
            $inputData->division(),
            $inputData->codes(),
            $inputData->conditionCross(),
            $companyIds,
            $inputData->productIds(),
            $inputData->cmIds(),
            $inputData->channels(),
            $inputData->conv15SecFlag(),
            $inputData->period(),
            $inputData->allChannels(),
            $inputData->straddlingFlg(),
            $inputData->dispCount(),
            $inputData->page(),
            $inputData->csvFlag(),
            $inputData->dataType(),
        ]];
    }

    /**
     * @param $division
     * @param $regionId
     * @param $userId
     * @param $baseDivision
     * @param mixed ...$params
     * @return array
     */
    public function getList($division, $regionId, $userId, $baseDivision, ...$params): array
    {
        $codeList = $this->divisionService->getCodeList($division, $regionId, $userId, $baseDivision);

        if (in_array($division, $baseDivision, true)) {
            return [$this->rdbCommercialDao->searchGrp(...$params), $codeList];
        }
        return [$this->commercialDao->searchGrpOriginalDivs(...array_merge($params, [$codeList])), $codeList];
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
     * @param int $csvFlg
     * @param array $params
     * @return array
     */
    protected function getHeader($csvFlg, array $params): array
    {
        if ($csvFlg === '1') {
            return $this->searchConditionTextAppService->getGrpCsv(...$params);
        }
        return $this->searchConditionTextAppService->getGrpHeader(...$params);
    }

    protected function produceOutputData($list, $draw, $division, $codes, $codeList, $period, $dataType, $startDateShort, $endDateShort, $header): OutputData
    {
        return new OutputData($list, $draw, $division, $codes, $codeList, $period, $dataType, $startDateShort, $endDateShort, $header);
    }
}
