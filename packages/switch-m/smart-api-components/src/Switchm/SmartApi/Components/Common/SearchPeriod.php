<?php

namespace Switchm\SmartApi\Components\Common;

use Carbon\Carbon;

class SearchPeriod
{
    public function getRdbDwhSearchPeriod(Carbon $startDateTime, Carbon $endDateTime, int $subDate, int $boundary): array
    {
        // 検索開始／終了が const.RDS_PERIOD_BOUNDARY 分 引いて 当日だった場合は要RDS
        $boundaryDateTime = Carbon::today();

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
