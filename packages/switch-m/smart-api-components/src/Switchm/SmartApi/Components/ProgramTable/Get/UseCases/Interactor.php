<?php

namespace Switchm\SmartApi\Components\ProgramTable\Get\UseCases;

use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Components\Common\Exceptions\SampleCountException;
use Switchm\SmartApi\Queries\Dao\Dwh\ProgramDao;
use Switchm\SmartApi\Queries\Services\DivisionService;
use Switchm\SmartApi\Queries\Services\HolidayService;
use Switchm\SmartApi\Queries\Services\SampleService;

class Interactor implements InputBoundary
{
    private $programDao;

    private $rdbProgramDao;

    private $holidayService;

    private $sampleService;

    private $divisionService;

    private $searchConditionTextAppService;

    private $outputBoundary;

    /**
     * @param ProgramDao $programDao
     * @param \Switchm\SmartApi\Queries\Dao\Rdb\ProgramDao $rdbProgramDao
     * @param HolidayService $holidayService
     * @param SampleService $sampleService
     * @param DivisionService $divisionService
     * @param SearchConditionTextAppService $searchConditionTextAppService
     * @param OutputBoundary $outputBoundary
     */
    public function __construct(
        ProgramDao $programDao,
        \Switchm\SmartApi\Queries\Dao\Rdb\ProgramDao $rdbProgramDao,
        HolidayService $holidayService,
        SampleService $sampleService,
        DivisionService $divisionService,
        SearchConditionTextAppService $searchConditionTextAppService,
        OutputBoundary $outputBoundary
    ) {
        $this->programDao = $programDao;
        $this->rdbProgramDao = $rdbProgramDao;
        $this->holidayService = $holidayService;
        $this->sampleService = $sampleService;
        $this->divisionService = $divisionService;
        $this->searchConditionTextAppService = $searchConditionTextAppService;
        $this->outputBoundary = $outputBoundary;
    }

    /**
     * @param InputData $inputData
     * @throws SampleCountException
     */
    public function __invoke(InputData $inputData): void
    {
        // 放送
        $channels = null;
        $bsFlg = false;

        if ($inputData->digitalAndBs() === 'digital') {
            $channels = $inputData->digitalKanto();
        } elseif ($inputData->digitalAndBs() === 'bs1') {
            $channels = $inputData->bs1();
            $bsFlg = true;
        } elseif ($inputData->digitalAndBs() === 'bs2') {
            $channels = $inputData->bs2();
            $bsFlg = true;
        }

        $params = [
            $inputData->startDateTime(),
            $inputData->endDateTime(),
            $inputData->minusFiveStartTimeShort(),
            $inputData->minusFiveEndTimeShort(),
            $channels,
            $inputData->division(),
            $inputData->conditionCross(),
            $inputData->codes(),
            $bsFlg,
            $inputData->regionId(),
        ];

        $list = $this->getData($inputData->period(), $inputData->division(), $inputData->baseDivision(), $inputData->codes(), $params);

        $dateList = $this->holidayService->getDateList($inputData->carbonMinusFiveStartDateTime(), $inputData->carbonMinusFiveEndDateTime());

        $cnt = 0;

        if ($inputData->division() === 'condition_cross') {
            $cnt = $this->sampleService->getConditionCrossCount($inputData->conditionCross(), $inputData->startDate(), $inputData->endDate(), $inputData->regionId());
        }

        $codeList = $this->divisionService->getCodeList($inputData->division(), $inputData->regionId(), $inputData->userId(), $inputData->baseDivision());

        $paramsForHeader = [
            $inputData->startDate(),
            $inputData->endDate(),
            $inputData->minusFiveStartTimeShort(),
            $inputData->minusFiveEndTimeShort(),
            $inputData->division(),
            $inputData->conditionCross(),
            $inputData->codes(),
            $inputData->regionId(),
            $cnt,
            $codeList,
            $inputData->searchHours(),
        ];

        $header = $this->searchConditionTextAppService->getProgramTableHeader(...$paramsForHeader);

        $dt = array_map(function ($v) {
            unset($v['carbon']);
            return $v;
        }, $dateList);

        $output = new OutputData($list['list'], $inputData->draw(), $dt, $header);
        ($this->outputBoundary)($output);
    }

    private function getData(array $period, string $division, array $baseDivision, ?array $codes, array $params): array
    {
        if ($period['isRdb']) {
            $this->programDao = $this->rdbProgramDao;

            if (in_array($division, $baseDivision) || (!empty($codes) && in_array($codes[0], ['personal', 'household']))) {
                // 基本五属性 またはサンプルが個人か世帯の場合
                return $this->rdbProgramDao->table(...$params);
            }

            // 掛け合わせ・拡張属性・オリジナル
            return $this->rdbProgramDao->tableOriginal(...$params);
        }

        if ($period['isDwh']) {
            if (in_array($division, $baseDivision) || (!empty($codes) && in_array($codes[0], ['personal', 'household']))) {
                // 基本五属性 またはサンプルが個人か世帯の場合
                return $this->programDao->table(...$params);
            }

            // 掛け合わせ・拡張属性・オリジナル
            return $this->programDao->tableOriginal(...$params);
        }

        return ['list' => []];
    }
}
