<?php

namespace Switchm\SmartApi\Components\RankingCommercial\Get\UseCases;

use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Queries\Dao\Dwh\RankingDao;
use Switchm\SmartApi\Queries\Dao\Rdb\MdataCmGenreDao;
use Switchm\SmartApi\Queries\Services\DivisionService;

class Interactor implements InputBoundary
{
    private $rankingDao;

    private $mdataCmGenreDao;

    private $divisionService;

    private $outputBoundary;

    private $searchConditionTextAppService;

    /**
     * Interactor constructor.
     * @param RankingDao $rankingDao
     * @param MdataCmGenreDao $mdataCmGenreDao
     * @param DivisionService $divisionService
     * @param OutputBoundary $outputBoundary
     * @param SearchConditionTextAppService $searchConditionTextAppService
     */
    public function __construct(
        RankingDao $rankingDao,
        MdataCmGenreDao $mdataCmGenreDao,
        DivisionService $divisionService,
        OutputBoundary $outputBoundary,
        SearchConditionTextAppService $searchConditionTextAppService
    ) {
        $this->rankingDao = $rankingDao;
        $this->mdataCmGenreDao = $mdataCmGenreDao;
        $this->divisionService = $divisionService;
        $this->outputBoundary = $outputBoundary;
        $this->searchConditionTextAppService = $searchConditionTextAppService;
    }

    /**
     * @param InputData $inputData
     */
    public function __invoke(InputData $inputData): void
    {
        $cnt = 0;
        $codeList = $this->divisionService->getCodeList(
            $inputData->division(),
            $inputData->regionId(),
            $inputData->userId(),
            $inputData->baseDivision()
        );

        $listParams = $this->getParams($inputData);

        $list = $this->getList($listParams);

        $headerParams = array_merge($listParams, [$codeList, $cnt]);

        $header = $this->getHeader($inputData->csvFlag(), $headerParams);

        $outputData = $this->produceOutputData($list['list'], $inputData->draw(), $list['cnt'], $inputData->startDateShort(), $inputData->endDateShort(), $header);

        ($this->outputBoundary)($outputData);
    }

    /**
     * @param InputData $input
     * @return array
     */
    protected function getParams(InputData $input): array
    {
        return [
            $input->startDate(),
            $input->endDate(),
            $input->startTimeShort(),
            $input->endTimeShort(),
            $input->wdays(),
            $input->isHoliday(),
            $input->cmType(),
            $input->regionId(),
            $input->division(),
            $input->codes(),
            $input->conditionCross(),
            $input->channels(),
            $input->order(),
            $input->conv15SecFlag(),
            $input->period(),
            $input->straddlingFlg(),
            $input->dispCount(),
            $input->page(),
            $input->csvFlag(),
            $input->dataType(),
            $input->cmLargeGenres(),
            $input->axisType(),
            $input->broadcasterCompanyIds(),
            $input->axisTypeCompany(),
            $input->axisTypeProduct(),
        ];
    }

    /**
     * @param array $params
     * @return array
     */
    protected function getList(array $params): array
    {
        return $this->rankingDao->searchCommercial(...$params);
    }

    /**
     * @param int $csvFlg
     * @param array $params
     * @return array
     */
    protected function getHeader($csvFlg, array $params): array
    {
        if ($csvFlg === '1') {
            return $this->searchConditionTextAppService->getRankingCommercialCsv(...$params);
        }
        return $this->searchConditionTextAppService->getRankingCommercialHeader(...$params);
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
