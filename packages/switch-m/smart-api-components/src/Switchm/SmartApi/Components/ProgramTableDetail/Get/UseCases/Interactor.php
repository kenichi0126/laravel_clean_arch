<?php

namespace Switchm\SmartApi\Components\ProgramTableDetail\Get\UseCases;

use Carbon\Carbon;
use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Components\Common\SearchPeriod;
use Switchm\SmartApi\Queries\Dao\Dwh\PerMinuteReportDao;
use Switchm\SmartApi\Queries\Dao\Dwh\ProgramDao;
use Switchm\SmartApi\Queries\Dao\Rdb\AttrDivDao;
use Switchm\SmartApi\Queries\Dao\Rdb\MdataSceneDao;
use Switchm\SmartApi\Queries\Dao\Rdb\TimeBoxDao;

class Interactor implements InputBoundary
{
    private $dwhProgramDao;

    private $rdbProgramDao;

    private $dwhPerMinuteReportDao;

    private $rdbPerMinuteReportDao;

    private $mdataSceneDao;

    private $timeBoxDao;

    private $attrDivDao;

    private $searchPeriod;

    private $searchConditionTextAppService;

    private $outputBoundary;

    public function __construct(
        ProgramDao $dwhProgramDao,
        \Switchm\SmartApi\Queries\Dao\Rdb\ProgramDao $rdbProgramDao,
        PerMinuteReportDao $dwhPerMinuteReportDao,
        \Switchm\SmartApi\Queries\Dao\Rdb\PerMinuteReportDao $rdbPerMinuteReportDao,
        MdataSceneDao $mdataSceneDao,
        TimeBoxDao $timeBoxDao,
        AttrDivDao $attrDivDao,
        SearchPeriod $searchPeriod,
        SearchConditionTextAppService $searchConditionTextAppService,
        OutputBoundary $outputBoundary
    ) {
        $this->dwhProgramDao = $dwhProgramDao;
        $this->rdbProgramDao = $rdbProgramDao;
        $this->dwhPerMinuteReportDao = $dwhPerMinuteReportDao;
        $this->rdbPerMinuteReportDao = $rdbPerMinuteReportDao;
        $this->mdataSceneDao = $mdataSceneDao;
        $this->timeBoxDao = $timeBoxDao;
        $this->attrDivDao = $attrDivDao;
        $this->searchPeriod = $searchPeriod;
        $this->searchConditionTextAppService = $searchConditionTextAppService;
        $this->outputBoundary = $outputBoundary;
    }

    // TODO - takata: いろいろなパーツが混在しているので、オブジェクトに抽出したい（etc. headlines）

    public function __invoke(InputData $inputData): void
    {
        // 番組情報取得
        $program = $this->dwhProgramDao->findProgram($inputData->progId(), $inputData->timeBoxId());

        $isRdb = true;

        if (!empty($program)) {
            $period = $this->searchPeriod->getRdbDwhSearchPeriod(
                new Carbon($program->real_ended_at),
                new Carbon($program->real_ended_at),
                $inputData->subDate(),
                $inputData->boundary()
            );
            $isRdb = $period['isRdb'];
        }
        // 番組情報が取得できない場合は空で返却
        if ($isRdb) {
            // query dwh
            $program = $this->rdbProgramDao->findProgram($inputData->progId(), $inputData->timeBoxId());

            if (empty($program)) {
                $output = new OutputData(null, null);
                ($this->outputBoundary)($output);
                return;
            }
        }

        // 日付を表示用に日本語変換
        $convertJpDate = new Carbon($program->real_started_at);
        $convertJpDate = $convertJpDate->isoFormat('YYYY年MM月DD日(ddd)');

        // 放送時間表示対象フラグ（2分未満の場合false）
        $minuteFlg = false;

        if ($program->prepared) {
            // グラフ内の値を取得
            $graph = $this->perMinRpForGraph(
                $program,
                $inputData->regionId(),
                $inputData->division(),
                $inputData->subDate(),
                $inputData->boundary()
            );

            $minuteFlg = $graph['minuteFlg'];

            $data = [
                'channelName' => $program->channel_name,
                'title' => $program->title,
                'date' => $convertJpDate,
                'fromTime' => $this->convert28Time($program->real_started_at),
                'toTime' => $this->convert28Time($program->real_ended_at),
                'personalAvg' => $this->roundOne($program->personal_viewing_rate),
                'personalMax' => $graph['personalMax'],
                'personalMin' => $graph['personalMin'],
                'personalEnd' => $this->roundOne($program->personald_end_viewing_rate),
                'householdAvg' => $this->roundOne($program->household_viewing_rate),
                'householdMax' => $graph['householdMax'],
                'householdMin' => $graph['householdMin'],
                'householdEnd' => $this->roundOne($program->household_end_viewing_rate),
                'prepared' => true,
                'minuteFlg' => $minuteFlg,
                'graph' => $graph['graph'],
                'xAxis' => $graph['xAxis'],
            ];
        } else {
            // 視聴率が計算されていない場合は番組情報以外セットしない
            $data = [
                'channelName' => $program->channel_name,
                'title' => $program->title,
                'date' => $convertJpDate,
                'fromTime' => $this->convert28Time($program->real_started_at),
                'toTime' => $this->convert28Time($program->real_ended_at),
                'personalAvg' => '-',
                'personalMax' => '-',
                'personalMin' => '-',
                'personalEnd' => '-',
                'householdAvg' => '-',
                'householdMax' => '-',
                'householdMin' => '-',
                'householdEnd' => '-',
                'prepared' => false,
                'minuteFlg' => $minuteFlg,
                'graph' => [],
                'xAxis' => [],
            ];
        }

        // ========== ヘッドライン情報作成 ==========
        $headlines = [];

        if ($program->prepared && $minuteFlg) {
            // Mデータシーン情報取得
            $sceneList = $this->mdataSceneDao->findMdataScenes($inputData->progId());
            $result = $this->getOnePerMinuteDataByDivCode($sceneList, $program, $inputData->subDate(), $inputData->boundary());

            foreach ($sceneList as $index => $scene) {
                $row = [];
                $startDateTime = new Carbon($scene->tm_start);
                $row[] = $this->convert28Time($startDateTime);
                $row[] = $scene->name === null ? '' : $scene->name;
                $row[] = ($scene->headline === 'シーン情報取得中') ? 'ヘッドライン情報取得中' : $scene->headline;
                $row[] = $result['household'][$index];
                $row[] = $result['personal'][$index];
                $headlines[] = $row;
            }
        }

        $output = new OutputData($data, $headlines);
        ($this->outputBoundary)($output);
    }

    public function getPerminuteDatabyDivCodeParams(Carbon $start, Carbon $end, string $regionId): array
    {
        $dates = [];
        $hours = [];
        $minutes = [];
        $timeBoxIds = [];
        $timeLabels = [];
        $datetimeLabels = [];

        $iterator = $start->copy();

        $programMinute = $end->diffInMinutes($start);
        $interval = $this->getGraphInterval($programMinute);

        // $interval で割り切れるように開始時間を補正
        $modulo = $end->diffInMinutes($start) % $interval;

        if ($modulo > 0) {
            $iterator->subMinute($interval - $modulo);
        }

        while ($iterator <= $end) {
            $date28h = $iterator->copy()->subHour(5)->format('Y-m-d');
            $hour28h = $iterator->hour < 5 ? $iterator->hour + 24 : $iterator->hour;
            $minute = $iterator->minute;

            if (!in_array($date28h, $dates)) {
                $dates[] = $date28h;
                $timeBoxId = $this->timeBoxDao->getTimeBoxId($date28h, $regionId)->id;

                if (!in_array($timeBoxId, $timeBoxIds)) {
                    $timeBoxIds[] = $timeBoxId;
                }
            }

            if (!in_array($hour28h, $hours)) {
                $hours[] = $hour28h;
            }

            if (!in_array($minute, $minutes)) {
                $minutes[] = $minute;
            }
            $timeLabels[] = sprintf('%d:%02d', $hour28h, $iterator->minute);
            $datetimeLabels[] = sprintf('%s %02d:%02d', $date28h, $hour28h, $iterator->minute);
            $iterator->addMinutes($interval);
        }

        return [$dates, $hours, $minutes, $timeBoxIds, $timeLabels, $datetimeLabels];
    }

    private function getGraphInterval(int $minute)
    {
        if ($minute <= 15) {
            return 1;
        } elseif ($minute <= 30) {
            return 2;
        } elseif ($minute <= 45) {
            return 3;
        } elseif ($minute <= 90) {
            return 5;
        } elseif ($minute <= 120) {
            return 10;
        }
        return (int) ($minute / 12);
    }

    private function perMinRpForGraph(
        $program,
        $regionId,
        $division,
        $subDate,
        $boundary
    ) {
        $realStartedAt = new Carbon($program->real_started_at);      // 開始時間
        $realEndedAt = new Carbon($program->real_ended_at);          // 終了時間
        $programMinutes = $realStartedAt->diffInMinutes($realEndedAt);  // 放送分数

        // 番組時間が2分未満の場合はデータを取得しない
        if ($programMinutes < 2) {
            return [
                'graph' => [],
                'xAxis' => [],
                'personalMax' => '-',
                'personalMin' => '-',
                'householdMax' => '-',
                'householdMin' => '-',
                'minuteFlg' => false,
            ];
        }

        //基本属性区分
        $attr_divs = $this->attrDivDao->getCode($division);
        $list = $attr_divs['list'];
        array_unshift($list, (object) ['name' => '個人', 'code' => 1]);
        array_push($list, (object) ['name' => '世帯', 'code' => 1]);

        list($dates, $hours, $minutes, $timeBoxIds, $timeLabels, $datetimeLabels)
            = $this->getPerminuteDatabyDivCodeParams($realStartedAt, $realEndedAt, $regionId);

        $attrDivData = $this->getPerMinuteDataByDivCode(
            $timeBoxIds,
            $dates,
            $hours,
            $minutes,
            $program->channel_id,
            $division,
            $datetimeLabels,
            $list,
            $subDate,
            $boundary
        );

        $reports = [];
        $personalData = [];
        $householdData = [];

        foreach ($list as $attrDiv) {
            $div = $division;
            $code = $attrDiv->code;

            if ($attrDiv->name === '個人') {
                $div = 'personal';
                $personalData = $attrDivData[$div . $code];
            }

            if ($attrDiv->name === '世帯') {
                $div = 'household';
                $householdData = $attrDivData[$div . $code];
            }
            $reports[] = [
                'name' => $attrDiv->name,
                'data' => $attrDivData[$div . $code],
            ];
        }

        return [
            'graph' => $reports,
            'xAxis' => $timeLabels,
            'personalMax' => max($personalData),
            'personalMin' => min($personalData),
            'householdMax' => max($householdData),
            'householdMin' => min($householdData),
            'minuteFlg' => true,
        ];
    }

    // per_minute_reportsからviewing_rate（四捨五入済）の配列を取得する
    private function getPerMinuteDataByDivCode(
        $time_box_ids,
        $dates,
        $hours,
        $all_minutes,
        $channel_id,
        $division,
        $datetime_label,
        $list,
        $subDate,
        $boundary
    ) {
        // 視聴率情報取得
        $tableData = $this->getTableDetailReport(
            $time_box_ids,
            $dates,
            $hours,
            $all_minutes,
            $channel_id,
            $division,
            $subDate,
            $boundary
        );

        $divCodes = [];

        foreach ($list as $attrDiv) {
            $div = $division;
            $code = $attrDiv->code;

            if ($attrDiv->name === '個人') {
                $div = 'personal';
            }

            if ($attrDiv->name === '世帯') {
                $div = 'household';
            }
            $divCodes[$div . $code] = [];
        }

        $pmr = [];

        foreach ($tableData as $data) {
            $pmr[$data->division . $data->code][$data->concatdate] = ['rate' => $data->rate, 'key' => $data->division . $data->code];
        }

        $res = [];
        // 初期化

        foreach ($datetime_label as $label_index => $label) {
            foreach ($divCodes as $key => $val) {
                if (isset($pmr[$key]) && array_key_exists($label, $pmr[$key])) {
                    //時間ラベルと日付・時分が一致するデータがあった場合のみ値を入れる
                    $res[$key][] = (float) ($this->roundOne($pmr[$key][$label]['rate']));
                } else {
                    // 見つからなかった場合は0をセットする
                    $res[$key][] = 0;
                }
            }
        }
        return $res;
    }

    // （シーン放送期間のper_minuteのviewing_secondsの合計）/(該当タイムボックスのパネラー数×シーン放送分数×60) = シーン視聴率
    private function getOnePerMinuteDataByDivCode(
        $scenes,
        $program,
        $subDate,
        $boundary
    ) {
        $sceneList = [];

        foreach ($scenes as $scene) {
            $startDateTime = new Carbon($scene->tm_start);
            $startDateTime->second(0);
            $startDateTime = $startDateTime->format('Y-m-d H:i:s');
            $endDateTime = new Carbon($scene->tm_end);
            $endDateTime->second(0);
            $endDateTime = $endDateTime->format('Y-m-d H:i:s');
            $sceneList[] = ['startDateTime' => $startDateTime, 'endDateTime' => $endDateTime];
        }

        // 視聴秒数取得
        $pmr = $this->getSeconds($sceneList, $program->channel_id, $subDate, $boundary);

        $pmrHash = [];

        foreach ($pmr as $row) {
            $pmrHash[$row->time_group . $row->division] = $row;
        }

        $result = ['personal' => [], 'household' => []];

        foreach (['personal', 'household'] as $division) {
            foreach ($scenes as $key => $scene) {
                if (!isset($pmrHash[$key . $division])) {
                    // 秒を取得できなかった場合は0にする
                    $rate = 0;
                } else {
                    $target = $pmrHash[$key . $division];
                    $seconds = $target->seconds;

                    //該当タイムボックスのパネラー数or世帯数取得
                    $time_box = $this->timeBoxDao->getNumber($program->time_box_id);

                    $startDateTime = new Carbon($scene->tm_start);
                    $startDateTime->second(0);
                    $startDateTime = $startDateTime->format('Y-m-d H:i:s');
                    $endDateTime = new Carbon($scene->tm_end);
                    $endDateTime->second(0);
                    $endDateTime = $endDateTime->format('Y-m-d H:i:s');

                    $sample_num = ($division === 'personal') ? $time_box->panelers_number : $time_box->households_number;
                    $min = (strtotime($endDateTime) - strtotime($startDateTime)) / 60;

                    if ($min == 0) {
                        $min = 1;
                    }

                    $rate = $seconds / ($sample_num * $min * 60) * 100;
                }
                $result[$division][] = $this->roundOne($rate);
            }
        }

        return $result;
    }

    // 28時間表示変換
    private function convert28Time($dateTime)
    {
        $convertDateTime = new Carbon($dateTime);
        $hour = $convertDateTime->hour;

        if ($hour < 5) {
            $hour = $hour + 24;
        }
        return $hour . $convertDateTime->format(':i:s');
    }

    /*
     * 小数点一桁補正.
     * 0.0でも表示される様に文字列で返却する
     */
    private function roundOne($val): String
    {
        if (!isset($val)) {
            return '0.0';
        }

        return sprintf('%.' . 1 . 'f', round($val, 1));
    }

    private function getSeconds(
        array $sceneList,
        String $channelId,
        int $subDate,
        int $boundary
    ) {
        $fromDate = [];
        $toDate = [];

        foreach ($sceneList as $row) {
            $fromDate[] = $row['startDateTime'];
            $toDate[] = $row['endDateTime'];
        }

        $period = $this->searchPeriod->getRdbDwhSearchPeriod(
            new Carbon(min($fromDate)),
            new Carbon(max($toDate)),
            $subDate,
            $boundary
        );

        $params = [
            $sceneList,
            $channelId, ];

        if ($period['isRdb']) {
            return $this->rdbPerMinuteReportDao->getSeconds(...$params);
        } elseif ($period['isDwh']) {
            return $this->dwhPerMinuteReportDao->getSeconds(...$params);
        }

        return [];
    }

    private function getTableDetailReport(
        array $timeBoxIds,
        array $dates,
        array $hours,
        array $minutes,
        String $channelId,
        String $division,
        int $subDate,
        int $boundary
    ) {
        $params = [
            $timeBoxIds,
            $dates,
            $hours,
            $minutes,
            $channelId,
            $division,
        ];

        $period = $this->searchPeriod->getRdbDwhSearchPeriod(
            new Carbon(min($dates)),
            new Carbon(max($dates)),
            $subDate,
            $boundary
        );

        if ($period['isRdb']) {
            return $this->rdbPerMinuteReportDao->getTableDetailReport(...$params);
        } elseif ($period['isDwh']) {
            return $this->dwhPerMinuteReportDao->getTableDetailReport(...$params);
        }

        return [];
    }
}
