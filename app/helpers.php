<?php

use Carbon\Carbon;
use Smart2\Application\Exceptions\DateRangeException;
use Smart2\Application\Exceptions\RealtimeSearchRangeException;
use Switchm\SmartApi\Components\Common\Exceptions\TimeshiftSearchRangeException;

if (!function_exists('getRdbDwhSearchPeriod')) {
    /**
     * date.
     *
     *
     * @param Carbon $startDateTime ,
     *                              $endDatetime
     * @param Carbon $endDateTime
     * @return array
     */
    // TODO - takata/SearchPeriodクラスを作成したため、依存がなくなり次第このメソッドは削除する
    function getRdbDwhSearchPeriod(Carbon $startDateTime, Carbon $endDateTime)
    {
        // 検索開始／終了が const.RDS_PERIOD_BOUNDARY 分 引いて 当日だった場合は要RDS
        $boundaryDateTime = Carbon::today();

        $subDate = \Config::get('const.DWH_PERIOD_DATE');
        $boundary = \Config::get('const.DWH_PERIOD_BOUNDARY');

        // 判定用日付
        $date = $boundaryDateTime->hour($boundary)->subDay($subDate);

        // 検索開始
        $sdt = new Carbon($startDateTime);
        $edt = new Carbon($endDateTime);

        // 検索期間初期化
        $rdbStartDate = new Carbon($startDateTime);
        $rdbEndDate = new Carbon($endDateTime);

        $dwhStartDate = new Carbon($startDateTime);
        $dwhEndDate = new Carbon($endDateTime);
        $isDwh = false;
        $isRdb = false;

        // RDSの必要あり
        if ($sdt->gte($date) || $edt->gte($date)) {
            $isRdb = true;
        } else {
            $isDwh = true;
        }

        return [
            'rdbStartDate' => $rdbStartDate,
            'rdbEndDate' => $rdbEndDate,
            'dwhStartDate' => $dwhStartDate,
            'dwhEndDate' => $dwhEndDate,
            'isDwh' => $isDwh,
            'isRdb' => $isRdb,
        ];
    }
}

if (!function_exists('searchPeriodValidation')) {
    function searchPeriodValidation(string $configName, string $division, int $requestPeriod)
    {
        if (in_array($division, \Config::get('const.BASE_DIVISION'))) {
            $number = \Config::get("const.SEARCH_PERIOD_LIMIT.${configName}.BASIC");
        } else {
            $number = \Config::get("const.SEARCH_PERIOD_LIMIT.${configName}.CUSTOM");
        }

        if ($requestPeriod > $number) {
            throw new DateRangeException($number);
        }

        return true;
    }
}

if (!function_exists('searchRangeValidation')) {
    function searchRangeValidation(string $startDate, string $endDate, array $dataType, string $regionId): bool
    {
        $st = new Carbon($startDate);
        $ed = new Carbon($endDate);

        if (
            in_array(\Config::get('const.DATA_TYPE_NUMBER.TIMESHIFT'), $dataType) ||
            in_array(\Config::get('const.DATA_TYPE_NUMBER.GROSS'), $dataType) ||
            in_array(\Config::get('const.DATA_TYPE_NUMBER.TOTAL'), $dataType) ||
            in_array(\Config::get('const.DATA_TYPE_NUMBER.RT_TOTAL'), $dataType)) {
            $isInvalid = false;
            $maxDate = Carbon::now()->startOfDay()->subDay(\Config::get('const.TS_CALCULATED_DAYS')[$regionId]);

            if ($ed->startOfDay()->greaterThanOrEqualTo($maxDate)) {
                $isInvalid = true;
            }

            $minDate = new Carbon(\Config::get('const.TS_MIN_FROM_DATE')[$regionId]);

            if ($st->startOfDay()->lessThan($minDate)) {
                $isInvalid = true;
            }

            if ($isInvalid) {
                throw new TimeshiftSearchRangeException($minDate->format('Y-m-d'));
            }
        } else {
            $isInvalid = false;
            $minDate = new Carbon(\Config::get('const.RT_MIN_FROM_DATE')[$regionId]);

            if ($st->startOfDay()->lessThan($minDate)) {
                $isInvalid = true;
            }

            if ($isInvalid) {
                throw new RealtimeSearchRangeException($minDate->format('Y-m-d'));
            }
        }

        return true;
    }
}

if (!function_exists('createDataTypeFlags')) {
    // TODO - konno:第二引数をすべて 非null にするまで
    function createDataTypeFlags(array $dataType, ?array $dataTypeConst = null): array
    {
        if ($dataTypeConst === null) {
            $rtType = \Config::get('const.DATA_TYPE_NUMBER.REALTIME');
            $tsType = \Config::get('const.DATA_TYPE_NUMBER.TIMESHIFT');
            $grossType = \Config::get('const.DATA_TYPE_NUMBER.GROSS');
            $totalType = \Config::get('const.DATA_TYPE_NUMBER.TOTAL');
            $rtTotalType = \Config::get('const.DATA_TYPE_NUMBER.RT_TOTAL');
        } else {
            $rtType = $dataTypeConst['rt'];
            $tsType = $dataTypeConst['ts'];
            $grossType = $dataTypeConst['gross'];
            $totalType = $dataTypeConst['total'];
            $rtTotalType = $dataTypeConst['rtTotal'];
        }

        $isRt = $isTs = $isGross = $isTotal = $isRtTotal = false;

        foreach ($dataType as $value) {
            switch ($value) {
                case $rtType:
                    $isRt = true;
                    break;
                case $tsType:
                    $isTs = true;
                    break;
                case $grossType:
                    $isGross = true;
                    break;
                case $totalType:
                    $isTotal = true;
                    break;
                case $rtTotalType:
                    $isRtTotal = true;
                    break;
            }
        }

        return [$isRt, $isTs, $isGross, $isTotal, $isRtTotal];
    }
}
