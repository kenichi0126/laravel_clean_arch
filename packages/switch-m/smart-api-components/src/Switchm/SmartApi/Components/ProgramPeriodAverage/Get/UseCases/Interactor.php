<?php

namespace Switchm\SmartApi\Components\ProgramPeriodAverage\Get\UseCases;

use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Components\Common\Exceptions\SampleCountException;
use Switchm\SmartApi\Queries\Dao\Dwh\ProgramDao;
use Switchm\SmartApi\Queries\Services\DivisionService;
use Switchm\SmartApi\Queries\Services\SampleService;

class Interactor implements InputBoundary
{
    private $programDao;

    private $divisionService;

    private $sampleService;

    private $searchConditionTextAppService;

    private $outputBoundary;

    /**
     * @param ProgramDao $programDao
     * @param DivisionService $divisionService
     * @param SampleService $sampleService
     * @param SearchConditionTextAppService $searchConditionTextAppService
     * @param OutputBoundary $outputBoundary
     */
    public function __construct(
        ProgramDao $programDao,
        DivisionService $divisionService,
        SampleService $sampleService,
        SearchConditionTextAppService $searchConditionTextAppService,
        OutputBoundary $outputBoundary
    ) {
        $this->programDao = $programDao;
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
        $page = $inputData->page() ?? 0;

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
            $inputData->division(),
            $inputData->conditionCross(),
            $inputData->codes(),
            $inputData->dispAverage(),
            $inputData->dataType(),
            ($inputData->wdays() == null) ? [] : $inputData->wdays(),
            ($inputData->holiday() == 'true') ? true : false,
            $inputData->channels(),
            $inputData->genres(),
            $inputData->programTypes(),
            $inputData->dispCount(),
            $inputData->regionId(),
            $page,
            $inputData->straddlingFlg(),
            $inputData->csvFlag(),
            $inputData->dataTypeFlags(),
            $inputData->prefixes(),
        ];

        if (in_array($inputData->division(), $inputData->baseDivision())) {
            $data = $this->programDao->periodAverage(...$params);
            $count = $data['cnt']->cnt;
        } else {
            $data = $this->programDao->periodAverageOriginal(...array_merge($params, [$inputData->selectedPersonalName(), $inputData->codeNumber()]));
            $count = $data['cnt']->cnt;
        }

        if ($count == 0) {
            $outputData = new OutputData([], $inputData->draw(), $count, $count, $inputData->startDateShort(), $inputData->endDateShort(), []);
            ($this->outputBoundary)($outputData);
            return;
        }

        $sd = $inputData->carbonStartDateTime();
        $ed = $inputData->carbonEndDateTime();

        $codeList = $this->divisionService->getCodeList($inputData->division(), $inputData->regionId(), $inputData->userId(), $inputData->baseDivision());

        $headerParams = [
            $sd->format('Y-m-d'),
            $ed->format('Y-m-d'),
            $sd->format('Hi00'),
            $ed->format('Hi00'),
            ($inputData->wdays() == null) ? [] : $inputData->wdays(),
            ($inputData->holiday() == 'true') ? true : false,
            $inputData->channels(),
            $inputData->genres(),
            $inputData->programTypes(),
            $inputData->regionId(),
            $inputData->division(),
            $inputData->conditionCross(),
            $inputData->codes(),
            $inputData->dispAverage(),
            $codeList,
            $cnt,
            $tsCnt,
            $inputData->dataType(),
            $inputData->csvFlag(),
        ];

        $header = $this->getHeader($inputData->csvFlag(), $headerParams);

        $outputData = new OutputData($data['list'], $inputData->draw(), $count, $count, $inputData->startDateShort(), $inputData->endDateShort(), $header);
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

    private function getHeader(string $csvFlag, array $params): array
    {
        if ($csvFlag === '1') {
            return $this->searchConditionTextAppService->getPeriodAverageCsv(...$params);
        }
        return $this->searchConditionTextAppService->getPeriodAverageHeader(...$params);
    }
}
