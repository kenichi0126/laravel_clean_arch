<?php

namespace Switchm\SmartApi\Components\RatingPerMinutes\Get\UseCases;

use Carbon\Carbon;
use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Components\Common\OutputCsvTrait;
use Switchm\SmartApi\Components\Common\RatingPoint;
use Switchm\SmartApi\Queries\Dao\Dwh\PerMinutesDao;
use Switchm\SmartApi\Queries\Dao\Rdb\PerMinutesDao as RdbPerMinutesDao;
use Switchm\SmartApi\Queries\Services\DivisionService;
use Switchm\SmartApi\Queries\Services\HolidayService;
use Switchm\SmartApi\Queries\Services\SampleService;

class Interactor implements InputBoundary
{
    use OutputCsvTrait;

    private $perMinutesDao;

    private $rdbPerMinutesDao;

    private $divisionService;

    private $sampleService;

    private $holidayService;

    private $ratingPoint;

    private $searchConditionTextAppService;

    private $outputBoundary;

    public function __construct(
        PerMinutesDao $perMinutesDao,
        RdbPerMinutesDao $rdbPerMinutesDao,
        DivisionService $divisionService,
        SampleService $sampleService,
        HolidayService $holidayService,
        RatingPoint $ratingPoint,
        SearchConditionTextAppService $searchConditionTextAppService,
        OutputBoundary $outputBoundary
    ) {
        $this->perMinutesDao = $perMinutesDao;
        $this->rdbPerMinutesDao = $rdbPerMinutesDao;
        $this->divisionService = $divisionService;
        $this->sampleService = $sampleService;
        $this->holidayService = $holidayService;
        $this->ratingPoint = $ratingPoint;
        $this->searchConditionTextAppService = $searchConditionTextAppService;
        $this->outputBoundary = $outputBoundary;
    }

    /**
     * @param InputData $inputData
     * @return mixed
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

        $dateList = $this->holidayService->getDateList($weekStartDateTime, $weekEndDateTime);

        $channelIds = $this->ratingPoint->getChannelIds($inputData->channelType(), $inputData->regionId(), $inputData->channels());

        $params = [
            $startDateTime,
            $endDateTime,
            $inputData->rdbDwhSearchPeriod(),
            $inputData->channelType(),
            $channelIds,
            $division,
            $code,
            $inputData->hour(),
            $inputData->dataDivision(),
            $inputData->conditionCross(),
            $isOriginal,
            $inputData->regionId(),
            $inputData->intervalHourly(),
            $inputData->intervalMinutes(),
        ];

        $cnt = 0;

        // データ区分
        // Rating
        if ($inputData->dataDivision() === 'viewing_rate') {
            $result = $this->getRatingData(...$params);
            $alias = 'viewing_rate';
        }
        // Share
        if ($inputData->dataDivision() === 'viewing_rate_share') {
            $result = $this->getShareData(...$params);
            $alias = 'share';
        }
        // ターゲット含有率
        if ($inputData->dataDivision() === 'target_content_personal' || $inputData->dataDivision() === 'target_content_household') {
            $result = $this->getTargetData(...$params);
            $alias = 'target_viewing_rate';
        }

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

        $convertData = $this->createTableData($result, $channelIds, $alias, $inputData->dataDivision(), $inputData->csvFlag(), $inputData->hour(), $inputData->channelType());

        $dateList = array_filter($dateList, function ($value, $index) {
            return $index < 7;
        }, ARRAY_FILTER_USE_BOTH);
        $codeList = $this->divisionService->getCodeList($inputData->division(), $inputData->regionId(), $inputData->userId(), $inputData->baseDivision());

        $headerParams = [
            $startDateTime,
            $endDateTime,
            $inputData->channelType(),
            $channelIds,
            $inputData->division(),
            $inputData->code(),
            $inputData->dataDivision(),
            $inputData->displayType(),
            $inputData->aggregateType(),
            $inputData->conditionCross(),
            $cnt,
            0,
            $inputData->regionId(),
            $codeList,
            $dateList,
            $inputData->dataType(),
            $inputData->csvFlag(),
        ];

        $header = $this->getHeader($inputData->csvFlag(), $headerParams);

        if ($inputData->csvFlag() == '1') {
            $data = $this->convertCsvData($convertData, $inputData->displayType(), $channelIds, $dateList);
        } else {
            $data = $convertData;
        }

        $dt = array_map(function ($v) {
            unset($v['carbon']);
            return $v;
        }, $dateList);

        $outputData = new OutputData(
            $data,
            $inputData->draw(),
            count($convertData),
            count($convertData),
            $dt,
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

    private function convertCsvData(array $convertData, string $displayType, array $channelIds, array $dateList): array
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
                $tmpRow[] = $row['minute'];

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

    private function createTableData($data, $channelIds, string $alias, string $dataDivision, string $csvFlag, string $hour, string $channelType)
    {
        // 時間帯別視聴データを作成する
        $list = json_decode(json_encode($data), true);
        $hash = [];

        // 時間、曜日、チャンネルでハッシュマップ化する
        foreach ($list as $row) {
            $channelId = $row['channel_id'];
            $dow = $row['dow'];
            $minute = $row['mm'];

            if (empty($hash[$channelId])) {
                $hash[$channelId] = [];
            }

            if (empty($hash[$channelId][$dow])) {
                $hash[$channelId][$dow] = [];
            }

            if (empty($hash[$channelId][$dow][$minute])) {
                $hash[$channelId][$dow][$minute] = $row[$alias];
            }
        }

        $result = [];

        $channels = $channelIds;

        $dows = [
            1,
            2,
            3,
            4,
            5,
            6,
            0,
        ]; // 曜日

        $avgHash = [];

        // 時間
        $minutes = array_map(function ($el) {
            return str_pad($el, 2, 0, STR_PAD_LEFT);
        }, range(0, 59));

        // Rating は 第一
        if ($dataDivision === 'viewing_rate') {
            $digit = 1;
        } elseif ($csvFlag === '0') {
            // その他はcsv でなければ 0
            $digit = 0;
        } else {
            $digit = 1;
        }

        if (strpos($channelType, 'bs') !== false) {
            // BSの場合は必ず2桁
            $digit = 2;
        }

        $minuteSetCountList = [];

        // 時間ループ
        foreach ($minutes as $minute) {
            $rowArray = [];
            // 曜日ループ
            $rowArray['minute'] = $minute;

            foreach ($dows as $dow) {
                // チャンネルループ
                foreach ($channels as $channel) {
                    if (!$this->avgHashExists($hash, $channel, $dow, $minute)) {
                        $rowArray[$channel . $dow] = '';
                        continue;
                    }

                    $rate = $this->avgHashExists($hash, $channel, $dow, $minute) ? $hash[$channel][$dow][$minute] : 0;

                    if (empty($avgHash[$channel])) {
                        $avgHash[$channel] = [];
                        $minuteSetCountList[$channel] = [];
                    }

                    if (empty($avgHash[$channel][$dow])) {
                        $avgHash[$channel][$dow] = [
                            'avg' => 0,
                        ];
                        $minuteSetCountList[$channel][$dow] = 0;
                    }

                    $avgHash[$channel][$dow]['avg'] += (float) $rate;
                    $rowArray[$channel . $dow] = round($rate, $digit);
                    $minuteSetCountList[$channel][$dow]++;
                }
            }

            if ($csvFlag === '1') {
                $rowArray['minute'] = $hour . ':' . $rowArray['minute'];
            }
            array_push($result, $rowArray);
        }
        // $avgHashをループさせキー値を取得する
        $avgRowArray = [];

        foreach ($dows as $dow) {
            $avgRowArray['minute'] = 'Av';

            foreach ($channels as $channel) {
                $avgRate = $this->avgHashExists($avgHash, $channel, $dow, 'avg') ? round(($avgHash[$channel][$dow]['avg'] / $minuteSetCountList[$channel][$dow]), $digit) : '';
                // キー値を設定する
                $avgRowArray[$channel . $dow] = $avgRate;
            }
        }

        if ($csvFlag === '1') {
            $avgRowArray['minute'] = 'AVG';
        }
        array_push($result, $avgRowArray);

        return $result;
    }

    private function avgHashExists(array $avgHash, String $channel, String $dow, String $key): bool
    {
        if (empty($avgHash[$channel])) {
            return false;
        }

        if (empty($avgHash[$channel][$dow])) {
            return false;
        }

        if (!isset($avgHash[$channel][$dow][$key])) {
            return false;
        }

        return true;
    }

    private function getRatingData(Carbon $startDateTime, Carbon $endDateTime, array $period, string $channelType, array $channelIds, string $division, string $code, string $hour, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, string $intervalHourly, string $intervalMinutes): array
    {
        $params = [
            $startDateTime,
            $endDateTime,
            $channelType,
            $channelIds,
            $division,
            $code,
            $hour,
            $dataDivision,
            $conditionCross,
            $isOriginal,
            $regionId,
            $intervalHourly,
            $intervalMinutes,
        ];

        $result = [];

        if ($period['isRdb']) {
            $result = $this->rdbPerMinutesDao->getRatingData(...$params);
        } elseif ($period['isDwh']) {
            $result = $this->perMinutesDao->getRatingData(...$params);
        }

        return $result;
    }

    private function getShareData(Carbon $startDateTime, Carbon $endDateTime, array $period, string $channelType, array $channelIds, string $division, string $code, string $hour, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, string $intervalHourly, string $intervalMinutes): array
    {
        $params = [
            $startDateTime,
            $endDateTime,
            $channelType,
            $channelIds,
            $division,
            $code,
            $hour,
            $dataDivision,
            $conditionCross,
            $isOriginal,
            $regionId,
            $intervalHourly,
            $intervalMinutes,
        ];

        $result = [];

        if ($period['isRdb']) {
            $result = $this->rdbPerMinutesDao->getShareData(...$params);
        } elseif ($period['isDwh']) {
            $result = $this->perMinutesDao->getShareData(...$params);
        }

        return $result;
    }

    private function getTargetData(Carbon $startDateTime, Carbon $endDateTime, array $period, string $channelType, array $channelIds, string $division, string $code, string $hour, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, string $intervalHourly, string $intervalMinutes): array
    {
        $params = [
            $startDateTime,
            $endDateTime,
            $channelType,
            $channelIds,
            $division,
            $code,
            $hour,
            $dataDivision,
            $conditionCross,
            $isOriginal,
            $regionId,
            $intervalHourly,
            $intervalMinutes,
        ];

        $result = [];

        if ($period['isRdb']) {
            $result = $this->rdbPerMinutesDao->getTargetData(...$params);
        } elseif ($period['isDwh']) {
            $result = $this->perMinutesDao->getTargetData(...$params);
        }

        return $result;
    }
}
