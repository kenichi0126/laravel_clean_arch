<?php

namespace Switchm\SmartApi\Components\Common;

use Carbon\Carbon;
use Switchm\SmartApi\Components\Common\Exceptions\SampleCountException;
use Switchm\SmartApi\Queries\Dao\Dwh\PerHourlyDao;
use Switchm\SmartApi\Queries\Dao\Rdb\HourlyReportDao;
use Switchm\SmartApi\Queries\Services\HolidayService;
use Switchm\SmartApi\Queries\Services\SampleService;

class RatingPoint
{
    private $perHourlyDao;

    private $rdbHourlyDao;

    private $hourlyReportDao;

    private $sampleService;

    private $holidayService;

    private $createTableData;

    public function __construct(
        PerHourlyDao $perHourlyDao,
        \Switchm\SmartApi\Queries\Dao\Rdb\PerHourlyDao $rdbHourlyDao,
        HourlyReportDao $hourlyReportDao,
        SampleService $sampleService,
        HolidayService $holidayService,
        CreateTableData $createTableData
    ) {
        $this->perHourlyDao = $perHourlyDao;
        $this->rdbHourlyDao = $rdbHourlyDao;
        $this->hourlyReportDao = $hourlyReportDao;
        $this->sampleService = $sampleService;
        $this->holidayService = $holidayService;
        $this->createTableData = $createTableData;
    }

    /**
     * @param $weekStartDateTime
     * @param $weekEndDateTime
     * @param $csvFlag
     * @return array
     */
    public function getDateList($weekStartDateTime, $weekEndDateTime, $csvFlag): array
    {
        $dateList = $this->holidayService->getDateList($weekStartDateTime, $weekEndDateTime);

        if ($csvFlag === '1') {
            $dateList = array_filter($dateList, function ($value, $index) {
                return $index < 7;
            }, ARRAY_FILTER_USE_BOTH);
        }

        return $dateList;
    }

    public function initDate(string $startDateTime, string $endDateTime, string $hour): array
    {
        $startDateTime = new Carbon($startDateTime);
        $endDateTime = new Carbon($endDateTime);
        $weekStartDateTime = new Carbon($startDateTime);
        $weekEndDateTime = new Carbon($endDateTime);

        if ($hour === 'hourly') {
            $startDateTime->setDateTime($startDateTime->year, $startDateTime->month, $startDateTime->day, 5, 0);
            $endDateTime->addDay(1);
            $endDateTime->setDateTime($endDateTime->year, $endDateTime->month, $endDateTime->day, 4, 0);

            $weekStartDateTime->startOfWeek()->hour(5)->minute(0)->second(0);
            $weekEndDateTime->endOfWeek()->addDay(1)->hour(4)->minute(0)->second(0);
        } else {
            $weekStartDateTime->startOfWeek();
            $weekEndDateTime->endOfWeek();

            $hour = (int) $hour;

            if ($hour < 5) {
                $startDateTime->addDay(1);
                $endDateTime->addDay(1);
            }

            $startDateTime->setDateTime($startDateTime->year, $startDateTime->month, $startDateTime->day, $hour, 0);
            $endDateTime->setDateTime($endDateTime->year, $endDateTime->month, $endDateTime->day, $hour, 59);
        }

        return [
            $startDateTime,
            $endDateTime,
            $weekStartDateTime,
            $weekEndDateTime,
        ];
    }

    public function getChannelIds(string $channelType, int $regionId, ?array $channels)
    {
        if ($channelType === 'advertising') {
            return $channels;
        }

        $channelHash = [
            'dt1' => [
                1,
                3,
                4,
                5,
                6,
                7,
            ],
            'dt2' => [
                2,
                8,
                9,
                10,
                11,
                12,
                13,
                14,
            ],
            'bs1' => [
                15,
                16,
                17,
                18,
            ],
            'bs2' => [
                19,
                20,
                21,
                22,
            ],
            'bs3' => [
                23,
                24,
                25,
            ],
            'summary' => [
                -10,
                -11,
                -12,
            ],
        ];

        if ($regionId === 2) {
            $channelHash['dt1'] = [
                44,
                46,
                47,
                48,
                49,
                50,
            ];
            $channelHash['dt2'] = [
                45,
                51,
                52,
                53,
                54,
                55,
            ];
            $channelHash['summary'] = [
                -10,
                -11,
                -12,
            ];
        }

        return $channelHash[$channelType];
    }

    /**
     * @param string $division
     * @param array $conditionCross
     * @param string $startDateTime
     * @param string $endDateTime
     * @param int $regionId
     * @param int $sampleCountMaxNumber
     * @param array $dataTypeFlags
     * @throws SampleCountException
     * @return array
     */
    public function getConditionCrossCount(
        string $division,
        array $conditionCross,
        string $startDateTime,
        string $endDateTime,
        int $regionId,
        int $sampleCountMaxNumber,
        array $dataTypeFlags
    ): array {
        $cnt = 0;
        $tsCnt = 0;

        if ($division === 'condition_cross') {
            if ($dataTypeFlags['isRt']) {
                $cnt = $this->sampleService->getConditionCrossCount($conditionCross, $startDateTime, $endDateTime, $regionId, true);

                if ($cnt < $sampleCountMaxNumber) {
                    throw new SampleCountException($sampleCountMaxNumber);
                }
            }

            if ($dataTypeFlags['isTs'] || $dataTypeFlags['isGross'] || $dataTypeFlags['isRtTotal']) {
                $tsCnt = $this->sampleService->getConditionCrossCount($conditionCross, $startDateTime, $endDateTime, $regionId, false);

                if ($tsCnt < $sampleCountMaxNumber) {
                    throw new SampleCountException($sampleCountMaxNumber);
                }
            }
        }
        return [$cnt, $tsCnt];
    }

    public function getDataAndAlias($dataDivision, $isCrossCondition, $isOriginal, $params): array
    {
        $result = [];
        $alias = '';

        // データ区分
        // Rating
        if ($dataDivision === 'viewing_rate') {
            // かけ合わせ条件の場合
            if ($isCrossCondition || $isOriginal) {
                $result = $this->getConditionCrossRatingData(...$params);
            } else {
                $result = $this->getRatingData(...$params);
            }
            $alias = 'viewing_rate';
        }
        // Shareの場合
        if ($dataDivision === 'viewing_rate_share') {
            // かけ合わせ条件の場合
            if ($isCrossCondition || $isOriginal) {
                $result = $this->getConditionCrossShareData(...$params);
            } else {
                $result = $this->getShareData(...$params);
            }
            $alias = 'share';
        }
        // ターゲット含有率の場合
        if ($dataDivision === 'target_content_personal' || $dataDivision === 'target_content_household') {
            // かけ合わせ条件の場合
            if ($isCrossCondition || $isOriginal) {
                $result = $this->getConditionCrossTargetData(...$params);
            } else {
                $result = $this->getTargetData(...$params);
            }
            $alias = 'target_viewing_rate';
        }
        return [$result, $alias];
    }

    public function getConditionCrossRatingData(Carbon $startDateTime, Carbon $endDateTime, $period, string $channelType, array $channelIds, string $division, ?array $code, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, array $dataType, array $dataTypeFlags, string $intervalHourly, string $intervalMinutes, string $sampleCodePrefix, string $sampleCodeNumberPrefix, string $selectedPersonalName): array
    {
        $params = [
            $startDateTime,
            $endDateTime,
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
        ];

        $result = [];

        if ($period['isRdb']) {
            $result = $this->rdbHourlyDao->getConditionCrossRatingData(...$params);
        } elseif ($period['isDwh']) {
            $result = $this->perHourlyDao->getConditionCrossRatingData(...array_merge($params, [$sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName]));
        }

        return $result;
    }

    public function getRatingData(Carbon $startDateTime, Carbon $endDateTime, $period, string $channelType, array $channelIds, string $division, string $code, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, array $dataType, array $dataTypeFlags, string $intervalHourly, string $intervalMinutes, string $sampleCodePrefix, string $sampleCodeNumberPrefix, string $selectedPersonalName): array
    {
        $params = [
            $startDateTime,
            $endDateTime,
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
        ];

        $result = [];

        if ($period['isRdb']) {
            $result = $this->rdbHourlyDao->getRatingData(...$params);
        } elseif ($period['isDwh']) {
            $result = $this->perHourlyDao->getRatingData(...$params);
        }

        return $result;
    }

    public function getConditionCrossShareData(Carbon $startDateTime, Carbon $endDateTime, $period, string $channelType, array $channelIds, string $division, ?array $code, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, array $dataType, array $dataTypeFlags, string $intervalHourly, string $intervalMinutes, string $sampleCodePrefix, string $sampleCodeNumberPrefix, string $selectedPersonalName): array
    {
        $params = [
            $startDateTime,
            $endDateTime,
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
        ];

        $result = [];

        if ($period['isRdb']) {
            $result = $this->rdbHourlyDao->getConditionCrossShareData(...$params);
        } elseif ($period['isDwh']) {
            $result = $this->perHourlyDao->getConditionCrossShareData(...array_merge($params, [$sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName]));
        }

        return $result;
    }

    public function getShareData(Carbon $startDateTime, Carbon $endDateTime, $period, string $channelType, array $channelIds, string $division, string $code, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, array $dataType, array $dataTypeFlags, string $intervalHourly, string $intervalMinutes, string $sampleCodePrefix, string $sampleCodeNumberPrefix, string $selectedPersonalName): array
    {
        $params = [
            $startDateTime,
            $endDateTime,
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
        ];

        $result = [];

        if ($period['isRdb']) {
            $result = $this->rdbHourlyDao->getShareData(...$params);
        } elseif ($period['isDwh']) {
            $result = $this->perHourlyDao->getShareData(...$params);
        }

        return $result;
    }

    public function getConditionCrossTargetData(Carbon $startDateTime, Carbon $endDateTime, $period, string $channelType, array $channelIds, string $division, ?array $code, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, array $dataType, array $dataTypeFlags, string $intervalHourly, string $intervalMinutes, string $sampleCodePrefix, string $sampleCodeNumberPrefix, string $selectedPersonalName): array
    {
        $params = [
            $startDateTime,
            $endDateTime,
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
        ];

        $result = [];

        if ($period['isRdb']) {
            $result = $this->rdbHourlyDao->getConditionCrossTargetData(...$params);
        } elseif ($period['isDwh']) {
            $result = $this->perHourlyDao->getConditionCrossTargetData(...array_merge($params, [$sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName]));
        }

        return $result;
    }

    public function getTargetData(
        Carbon $startDateTime,
        Carbon $endDateTime,
        $period,
        string $channelType,
        array $channelIds,
        string $division,
        string $code,
        string $dataDivision,
        array $conditionCross,
        bool $isOriginal,
        int $regionId,
        array $dataType,
        array $dataTypeFlags,
        string $intervalHourly,
        string $intervalMinutes,
        string $sampleCodePrefix,
        string $sampleCodeNumberPrefix,
        string $selectedPersonalName
    ): array {
        $params = [
            $startDateTime,
            $endDateTime,
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
        ];

        $result = [];

        if ($period['isRdb']) {
            $result = $this->rdbHourlyDao->getTargetData(...$params);
        } elseif ($period['isDwh']) {
            $result = $this->perHourlyDao->getTargetData(...$params);
        }

        return $result;
    }

    /**
     * @param int $regionId
     * @param string $startDate
     * @param string $endDate
     * @return null|Carbon
     */
    public function getLimit(int $regionId, string $startDate, string $endDate): ?Carbon
    {
        $result = null;

        $sd = new Carbon($startDate);
        $ed = new Carbon($endDate);
        $diff = $ed->diffInWeeks($sd);

        if ($diff === 0) {
            $latest = $this->hourlyReportDao->latest($regionId);

            if ($latest !== null) {
                $ld = new Carbon($latest->datetime);

                if ($ed->greaterThanOrEqualTo($ld)) {
                    $result = $ld;
                }
            }
        }

        return $result;
    }

    /**
     * @param array $convertData
     * @param string $displayType
     * @param array $channelIds
     * @param array $dateList
     * @return array
     */
    public function convertCsvData(array $convertData, string $displayType, array $channelIds, array $dateList): array
    {
        // 日付
        $result = [];

        if ($displayType == 'dateBy') {
            foreach ($convertData as $row) {
                foreach ($dateList as $index => $date) {
                    $offset = count($channelIds) * count($dateList) + 1 - (count($channelIds) * $index);

                    if ($offset > 1) {
                        array_splice($row, $offset, 0, '');
                    }
                }
                $result[] = $row;
            }
        } else {
            foreach ($convertData as $row) {
                $tmpRow = [];
                $tmpRow[] = $row['hour'];

                foreach ($channelIds as $channelId) {
                    foreach ($dateList as $index => $date) {
                        $tmpRow[] = $row[$channelId . $date['carbon']->dayOfWeek];
                    }
                    $tmpRow[] = '';
                }
                $result[] = $tmpRow;
            }
        }

        return $result;
    }
}
