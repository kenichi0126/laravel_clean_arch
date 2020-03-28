<?php

namespace Switchm\SmartApi\Components\RatingPerHourly\Get\UseCases;

use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Components\Common\CreateTableData;
use Switchm\SmartApi\Components\Common\Exceptions\SampleCountException;
use Switchm\SmartApi\Components\Common\RatingPoint;
use Switchm\SmartApi\Queries\Services\DivisionService;

class Interactor implements InputBoundary
{
    private $divisionService;

    private $ratingPoint;

    private $createTableData;

    private $outputBoundary;

    private $searchConditionTextAppService;

    public function __construct(
        DivisionService $divisionService,
        RatingPoint $ratingPoint,
        CreateTableData $createTableData,
        OutputBoundary $outputBoundary,
        SearchConditionTextAppService $searchConditionTextAppService
    ) {
        $this->divisionService = $divisionService;
        $this->ratingPoint = $ratingPoint;
        $this->createTableData = $createTableData;
        $this->outputBoundary = $outputBoundary;
        $this->searchConditionTextAppService = $searchConditionTextAppService;
    }

    /**
     * @param InputData $inputData
     * @throws SampleCountException
     */
    public function __invoke(InputData $inputData): void
    {
        $code = $inputData->code();

        if ($code === 'personal' || $code === 'household') {
            $division = $code;
            $code = 1;
            $isBasic = true;
        } else {
            $division = $inputData->division();
            $isBasic = in_array($division, [
                'ga8',
                'ga12',
                'ga10s',
                'gm',
                'oc',
            ]);
        }

        $isCrossCondition = $division === 'condition_cross';

        $isOriginal = !($isBasic || $isCrossCondition);

        list($startDateTime, $endDateTime, $weekStartDateTime, $weekEndDateTime) = $this->ratingPoint->initDate($inputData->startDateTime(), $inputData->endDateTime(), $inputData->hour());

        $dateList = $this->ratingPoint->getDateList($weekStartDateTime, $weekEndDateTime, $inputData->csvFlag());

        // 共通処理に合わせるため配列か
        if ($isOriginal) {
            $code = [
                $code,
            ];
        }

        $channelIds = $this->ratingPoint->getChannelIds($inputData->channelType(), $inputData->regionId(), $inputData->channels());

        $params = [
            $startDateTime,
            $endDateTime,
            $inputData->rdbDwhSearchPeriod(),
            $inputData->channelType(),
            $channelIds,
            $division,
            $code,
            $inputData->dataDivision(),
            $inputData->conditionCross(),
            $isOriginal,
            $inputData->regionId(),
            $inputData->dataType(),
            $inputData->dataTypeFlags(),
            $inputData->intervalHourly(),
            $inputData->intervalMinutes(),
            $inputData->sampleCodePrefix(),
            $inputData->sampleCodeNumberPrefix(),
            $inputData->selectedPersonalName(),
        ];

        list($cnt, $tsCnt) = $this->ratingPoint->getConditionCrossCount(
            $inputData->division(),
            $inputData->conditionCross(),
            $inputData->startDate(),
            $inputData->endDate(),
            $inputData->regionId(),
            $inputData->sampleCountMaxNumber(),
            $inputData->dataTypeFlags()
        );

        list($result, $alias) = $this->ratingPoint->getDataAndAlias($inputData->dataDivision(), $isCrossCondition, $isOriginal, $params);

        if ($inputData->channelType() === 'dt2') {
            if ($inputData->regionId() === '1') {
                $channelIds = [
                    2,
                    9,
                    999,
                ]; // 地デジ2の場合は、 2, 9 とその他で集約する。
            } elseif ($inputData->regionId() === '2') {
                $channelIds = [
                    45,
                    999,
                ];
            }
        }

        $convertData = ($this->createTableData)($result, $channelIds, $alias, $inputData->dataDivision(), $inputData->csvFlag(), $inputData->channelType());
        $codeList = $this->divisionService->getCodeList($inputData->division(), $inputData->regionId(), $inputData->userId(), $inputData->baseDivision());
        $headerParams = [
            $inputData->startDateTime(),
            $inputData->endDateTime(),
            $inputData->channelType(),
            $channelIds,
            $inputData->division(),
            $inputData->code(),
            $inputData->dataDivision(),
            $inputData->displayType(),
            $inputData->aggregateType(),
            $inputData->conditionCross(),
            $cnt,
            $tsCnt,
            $inputData->regionId(),
            $codeList,
            $dateList,
            $inputData->dataType(),
            $inputData->csvFlag(),
        ];

        $header = $this->getHeader($inputData->csvFlag(), $headerParams);

        if ($inputData->csvFlag() === '1') {
            $data = $this->ratingPoint->convertCsvData($convertData, $inputData->displayType(), $channelIds, $dateList);
        } else {
            $data = $convertData;
        }

        $outputData = $this->produceOutputData(
            $data,
            $inputData->draw(),
            count($data),
            $dateList,
            $inputData->channelType(),
            $inputData->displayType(),
            $inputData->aggregateType(),
            $inputData->startDateShort(),
            $inputData->endDateShort(),
            $header
        );
        ($this->outputBoundary)($outputData);
    }

    /**
     * @param $isCsvFlag
     * @param $params
     * @return array
     */
    protected function getHeader($isCsvFlag, $params): array
    {
        if ($isCsvFlag === '1') {
            return $this->searchConditionTextAppService->getRatingCsv(...$params);
        }
        return $this->searchConditionTextAppService->getRatingHeader(...$params);
    }

    /**
     * @param $data
     * @param $draw
     * @param $cnt
     * @param $dateList
     * @param $channelType
     * @param $displayType
     * @param $aggregateType
     * @param $startDateShort
     * @param $endDateShort
     * @param $header
     * @return OutputData
     */
    protected function produceOutputData($data, $draw, $cnt, $dateList, $channelType, $displayType, $aggregateType, $startDateShort, $endDateShort, $header): OutputData
    {
        $dt = array_map(function ($v) {
            unset($v['carbon']);
            return $v;
        }, $dateList);

        return new OutputData($data, $draw, $cnt, $dt, $channelType, $displayType, $aggregateType, $startDateShort, $endDateShort, $header);
    }
}
