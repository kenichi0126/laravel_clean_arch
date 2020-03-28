<?php

namespace Switchm\SmartApi\Components\CommercialAdvertising\Get\UseCases;

use Carbon\Carbon;
use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Components\Common\CreateTableData;
use Switchm\SmartApi\Components\Common\Exceptions\SampleCountException;
use Switchm\SmartApi\Components\Common\RatingPoint;
use Switchm\SmartApi\Queries\Dao\Dwh\CommercialDao;
use Switchm\SmartApi\Queries\Services\DivisionService;
use Switchm\SmartApi\Queries\Services\ProductService;
use Switchm\SmartApi\Queries\Services\SampleService;

class Interactor implements InputBoundary
{
    private $productService;

    private $commercialDao;

    private $divisionService;

    private $sampleService;

    private $ratingPoint;

    private $createTableData;

    private $outputBoundary;

    private $searchConditionTextAppService;

    public function __construct(
        ProductService $productService,
        CommercialDao $commercialDao,
        DivisionService $divisionService,
        SampleService $sampleService,
        RatingPoint $ratingPoint,
        CreateTableData $createTableData,
        OutputBoundary $outputBoundary,
        SearchConditionTextAppService $searchConditionTextAppService
    ) {
        $this->productService = $productService;
        $this->commercialDao = $commercialDao;
        $this->divisionService = $divisionService;
        $this->sampleService = $sampleService;
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
        $companyIds = $this->productService->getCompanyIds($inputData->productIds(), $inputData->companyIds());

        $params = [
            $inputData->startDate(),
            $inputData->endDate(),
            $inputData->startTimeShort(),
            $inputData->endTimeShort(),
            $inputData->cmType(),
            $inputData->cmSeconds(),
            $inputData->progIds(),
            $inputData->regionId(),
            $companyIds,
            $inputData->productIds(),
            $inputData->cmIds(),
            $inputData->channels(),
            $inputData->straddlingFlg(),
        ];

        $data = $this->commercialDao->searchAdvertising(...$params);

        $rp = $this->getRatingPoints(
            $inputData->startDateTime(),
            $inputData->endDateTime(),
            $inputData->division(),
            $inputData->code(),
            $inputData->conditionCross(),
            $inputData->regionId(),
            $inputData->channels(),
            $inputData->rdbDwhSearchPeriod(),
            $inputData->sampleCountMaxNumber(),
            ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            $inputData->userID(),
            $inputData->heatMapRating(),
            $inputData->heatMapTciPersonal(),
            $inputData->heatMapTciHousehold(),
            $inputData->baseDivision(),
            $inputData->intervalHourly(),
            $inputData->intervalMinutes(),
            $inputData->sampleCodePrefix(),
            $inputData->sampleCodeNumberPrefix(),
            $inputData->selectedPersonalName()
        );

        $header = $this->getHeader($inputData->csvFlag(), array_merge($params, [$inputData->heatMapRating(), $inputData->heatMapTciPersonal(), $inputData->heatMapTciHousehold()]));

        $outputData = new OutputData($data, $inputData->channels(), $inputData->csvFlag(), $inputData->draw(), $rp, $inputData->startDateTimeShort(), $inputData->endDateTimeShort(), $header);

        ($this->outputBoundary)($outputData);
    }

    /**
     * @param $csvFlg
     * @param array $params
     * @return array
     */
    protected function getHeader($csvFlg, array $params): array
    {
        if ($csvFlg === '1') {
            return $this->searchConditionTextAppService->getAdvertisingCsv(...$params);
        }
        return $this->searchConditionTextAppService->getAdvertisingHeader(...$params);
    }

    /**
     * @param $startDateTime
     * @param $endDateTime
     * @param $division
     * @param $code
     * @param $conditionCross
     * @param $regionId
     * @param $channels
     * @param $rdbDwhSearchPeriod
     * @param $sampleCountMaxNumber
     * @param $dataTypeFlags
     * @param $userId
     * @param $heatMapRating
     * @param $heatMapTciPersonal
     * @param $heatMapTciHousehold
     * @param $baseDivision
     * @param mixed $intervalHourly
     * @param mixed $intervalMinutes
     * @param mixed $sampleCodePrefix
     * @param mixed $sampleCodeNumberPrefix
     * @param mixed $selectedPersonalName
     * @throws SampleCountException
     * @return array
     */
    protected function getRatingPoints(
        $startDateTime,
        $endDateTime,
        $division,
        $code,
        $conditionCross,
        $regionId,
        $channels,
        $rdbDwhSearchPeriod,
        $sampleCountMaxNumber,
        $dataTypeFlags,
        $userId,
        $heatMapRating,
        $heatMapTciPersonal,
        $heatMapTciHousehold,
        $baseDivision,
        $intervalHourly,
        $intervalMinutes,
        $sampleCodePrefix,
        $sampleCodeNumberPrefix,
        $selectedPersonalName
    ): array {
        $result = [];

        $startDate = new Carbon($startDateTime);
        $endDate = new Carbon($endDateTime);

        $baseParams = [
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d'),
            $regionId,
            $channels,
            'advertising',
            $division,
            $conditionCross,
            '1',
            $code,
            [0],
            'channelBy',
            'hourly',
            'hourly',
            $rdbDwhSearchPeriod,
            $sampleCountMaxNumber,
            $dataTypeFlags,
            $userId,
            $baseDivision,
        ];

        if ($heatMapRating == 'true') {
            $dataDivision = 'viewing_rate';
            $params = array_merge($baseParams, [$dataDivision, $intervalHourly, $intervalMinutes, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName]);

            list($header, $body) = $this->getRatingPointResult(...$params);
            $result = array_merge($result, [[]], $header, $body);
        }

        if ($heatMapTciPersonal == 'true') {
            $dataDivision = 'target_content_personal';
            $params = array_merge($baseParams, [$dataDivision, $intervalHourly, $intervalMinutes, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName]);

            list($header, $body) = $this->getRatingPointResult(...$params);
            $result = array_merge($result, [[]], $header, $body);
        } elseif ($heatMapTciHousehold == 'true') {
            $dataDivision = 'target_content_household';
            $params = array_merge($baseParams, [$dataDivision, $intervalHourly, $intervalMinutes, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName]);

            list($header, $body) = $this->getRatingPointResult(...$params);
            $result = array_merge($result, [[]], $header, $body);
        }

        return $result;
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param $regionId
     * @param $channels
     * @param $channelType
     * @param $divisionOrig
     * @param $conditionCross
     * @param $csvFlag
     * @param $codeOrig
     * @param $dataType
     * @param $displayType
     * @param $aggregateType
     * @param $hour
     * @param $rdbDwhSearchPeriod
     * @param $sampleCountMaxNumber
     * @param $dataTypeFlags
     * @param $userId
     * @param $baseDivision
     * @param $dataDivision
     * @param mixed $intervalHourly
     * @param mixed $intervalMinutes
     * @param mixed $sampleCodePrefix
     * @param mixed $sampleCodeNumberPrefix
     * @param mixed $selectedPersonalName
     * @throws SampleCountException
     * @return array
     */
    private function getRatingPointResult(
        $startDate,
        $endDate,
        $regionId,
        $channels,
        $channelType,
        $divisionOrig,
        $conditionCross,
        $csvFlag,
        $codeOrig,
        $dataType,
        $displayType,
        $aggregateType,
        $hour,
        $rdbDwhSearchPeriod,
        $sampleCountMaxNumber,
        $dataTypeFlags,
        $userId,
        $baseDivision,
        $dataDivision,
        $intervalHourly,
        $intervalMinutes,
        $sampleCodePrefix,
        $sampleCodeNumberPrefix,
        $selectedPersonalName
    ): array {
        $startDateTimeString = (new Carbon($startDate))->format('Y-m-d H:i:s');
        $endDateTimeString = (new Carbon($endDate))->format('Y-m-d H:i:s');

        if ($codeOrig === 'personal' || $codeOrig === 'household') {
            $division = $codeOrig;
            $code = 1;
            $isBasic = true;
        } else {
            $division = $divisionOrig;
            $code = $codeOrig;
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

        list($startDateTime, $endDateTime, $weekStartDateTime, $weekEndDateTime) = $this->ratingPoint->initDate($startDateTimeString, $endDateTimeString, $hour);

        $dateList = $this->ratingPoint->getDateList($weekStartDateTime, $weekEndDateTime, $csvFlag);

        // 共通処理に合わせるため配列か
        if ($isOriginal) {
            $code = [
                $code,
            ];
        }

        $channelIds = $this->ratingPoint->getChannelIds($channelType, $regionId, $channels);

        $params = [
            $startDateTime,
            $endDateTime,
            $rdbDwhSearchPeriod,
            $channelType,
            $channelIds,
            $division,
            $code,
            $dataDivision,
            $conditionCross,
            $isOriginal,
            $regionId,
            $dataType,
            $dataTypeFlags,
            $intervalHourly,
            $intervalMinutes,
            $sampleCodePrefix,
            $sampleCodeNumberPrefix,
            $selectedPersonalName,
        ];

        list($cnt, $tsCnt) = $this->ratingPoint->getConditionCrossCount(
            $division,
            $conditionCross,
            $startDate,
            $endDate,
            $regionId,
            $sampleCountMaxNumber,
            $dataTypeFlags
        );

        list($result, $alias) = $this->ratingPoint->getDataAndAlias($dataDivision, $isCrossCondition, $isOriginal, $params);

        if ($channelType === 'dt2') {
            if ($regionId === '1') {
                $channelIds = [
                    2,
                    9,
                    999,
                ]; // 地デジ2の場合は、 2, 9 とその他で集約する。
            } elseif ($regionId === '2') {
                $channelIds = [
                    45,
                    999,
                ];
            }
        }

        $convertData = ($this->createTableData)($result, $channelIds, $alias, $dataDivision, $csvFlag, $channelType);

        $codeList = $this->divisionService->getCodeList($divisionOrig, $regionId, $userId, $baseDivision);
        $headerParams = [
            $startDateTimeString,
            $endDateTimeString,
            $channelType,
            $channelIds,
            $divisionOrig,
            $codeOrig,
            $dataDivision,
            $displayType,
            $aggregateType,
            $conditionCross,
            $cnt,
            $tsCnt,
            $regionId,
            $codeList,
            $dateList,
            $dataType,
            $csvFlag,
        ];

        $header = $this->searchConditionTextAppService->getRatingCsv(...$headerParams);

        $body = $this->ratingPoint->convertCsvData($convertData, $displayType, $channelIds, $dateList);

        return [
            $header,
            $body,
        ];
    }
}
