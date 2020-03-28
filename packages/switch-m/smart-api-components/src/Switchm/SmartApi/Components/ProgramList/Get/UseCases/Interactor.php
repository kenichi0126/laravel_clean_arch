<?php

namespace Switchm\SmartApi\Components\ProgramList\Get\UseCases;

use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Components\Common\Exceptions\SampleCountException;
use Switchm\SmartApi\Queries\Dao\Dwh\ProgramDao;
use Switchm\SmartApi\Queries\Services\DivisionService;
use Switchm\SmartApi\Queries\Services\SampleService;

class Interactor implements InputBoundary
{
    private $programDao;

    private $rdbProgramDao;

    private $divisionService;

    private $sampleService;

    private $searchConditionTextAppService;

    private $outputBoundary;

    /**
     * @param ProgramDao $programDao
     * @param \Switchm\SmartApi\Queries\Dao\Rdb\ProgramDao $rdbProgramDao
     * @param DivisionService $divisionService
     * @param SampleService $sampleService
     * @param SearchConditionTextAppService $searchConditionTextAppService
     * @param OutputBoundary $outputBoundary
     */
    public function __construct(
        ProgramDao $programDao,
        \Switchm\SmartApi\Queries\Dao\Rdb\ProgramDao $rdbProgramDao,
        DivisionService $divisionService,
        SampleService $sampleService,
        SearchConditionTextAppService $searchConditionTextAppService,
        OutputBoundary $outputBoundary
    ) {
        $this->programDao = $programDao;
        $this->rdbProgramDao = $rdbProgramDao;
        $this->divisionService = $divisionService;
        $this->sampleService = $sampleService;
        $this->searchConditionTextAppService = $searchConditionTextAppService;
        $this->outputBoundary = $outputBoundary;
    }

    /**
     * @param InputData $inputData
     * @throws SampleCountException
     */
    public function __invoke(InputData $inputData): void
    {
        ini_set('memory_limit', '1536M');

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

        if ($inputData->csvFlag() === '1') {
            $page = 0;
        } else {
            $page = $inputData->page() ?? 0;
        }

        $isHoliday = ($inputData->holiday() === 'true') ? true : false;

        $cnt = 0;
        $tsCnt = 0;

        if ($inputData->division() === 'condition_cross') {
            $this->checkSampleCount(
                $inputData->dataTypeFlags(),
                $inputData->sampleCountMaxNumber(),
                $inputData->conditionCross(),
                $inputData->startDate(),
                $inputData->endDate(),
                $inputData->regionId()
            );
        }

        $params = [
            $inputData->startDate(),
            $inputData->endDate(),
            $inputData->minusFiveStartTimeShort(),
            $inputData->minusFiveEndTimeShort(),
            ($inputData->wdays() == null) ? [] : $inputData->wdays(),
            $isHoliday,
            $channels,
            $inputData->genres(),
            $inputData->programNames(),
            $inputData->division(),
            $inputData->conditionCross(),
            $inputData->codes(),
            $inputData->order(),
            $inputData->dispCount(),
            $inputData->regionId(),
            $page,
            $inputData->straddlingFlg(),
            $bsFlg,
            $inputData->csvFlag(),
            $inputData->hasPermission() && $inputData->dataTypeFlags()['isRt'],
            $inputData->dataType(),
        ];

        $data = $this->getData(
            $inputData->division(),
            $inputData->baseDivision(),
            $inputData->csvFlag(),
            $bsFlg,
            $inputData->hasPermission(),
            $params,
            $inputData->dataTypeFlags(),
            $inputData->dataTypeConst(),
            $inputData->prefixes(),
            $inputData->selectedPersonalName(),
            $inputData->codeNumber()
        );

        $codeList = $this->divisionService->getCodeList($inputData->division(), $inputData->regionId(), $inputData->userId(), $inputData->baseDivision());

        if ($inputData->csvFlag()) {
            // 表示用の時刻に書き換える。
            $params[2] = $inputData->startTimeShort();
            $params[3] = $inputData->endTimeShort();
        }

        $header = $this->getHeader($inputData->csvFlag(), array_merge($params, [
            $cnt,
            $tsCnt,
            $inputData->digitalAndBs(),
            $codeList,
        ]));

        $outputData = new OutputData($data['list'], $inputData->draw(), $data['cnt'], $inputData->startDateShort(), $inputData->endDateShort(), $header);

        ($this->outputBoundary)($outputData);
    }

    /**
     * @param array $dataTypeFlags
     * @param int $sampleCountMaxNumber
     * @param null|array $conditionCross
     * @param string $startDate
     * @param string $endDate
     * @param int $regionId
     * @throws SampleCountException
     */
    private function checkSampleCount(array $dataTypeFlags, int $sampleCountMaxNumber, ?array $conditionCross, string $startDate, string $endDate, int $regionId): void
    {
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

    private function getData(string $division, array $baseDivision, string $csvFlag, bool $bsFlg, bool $hasPermission, array $params, array $dataTypeFlags, array $dataTypeConst, array $prefixes, string $selectedPersonalName, int $codeNumber): array
    {
        if (!in_array($division, $baseDivision)) {
            return $this->programDao->searchOriginal(...array_merge($params, [$dataTypeFlags, $dataTypeConst, $prefixes, $selectedPersonalName, $codeNumber]));
        }

        if ($csvFlag === '0') {
            return $this->rdbProgramDao->search(...array_merge($params, [$dataTypeConst, $selectedPersonalName, $codeNumber]));
        }

        if (!$bsFlg && $hasPermission) {
            return $this->programDao->search(...array_merge($params, [$dataTypeFlags, $dataTypeConst, $prefixes, $selectedPersonalName, $codeNumber]));
        }

        return $this->rdbProgramDao->search(...array_merge($params, [$dataTypeConst, $selectedPersonalName, $codeNumber]));
    }

    private function getHeader(string $csvFlag, array $params): array
    {
        if ($csvFlag === '1') {
            return $this->searchConditionTextAppService->getProgramListCsv(...$params);
        }
        return $this->searchConditionTextAppService->getProgramListHeader(...$params);
    }
}
