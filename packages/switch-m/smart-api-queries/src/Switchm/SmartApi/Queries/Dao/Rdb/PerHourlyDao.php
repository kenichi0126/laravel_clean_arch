<?php

namespace Switchm\SmartApi\Queries\Dao\Rdb;

use Carbon\Carbon;

// 毎時
class PerHourlyDao extends Dao
{
    /**
     * @param Carbon $startDateTime
     * @param Carbon $endDateTime
     * @param string $channelType
     * @param array $channelIds
     * @param string $division
     * @param string $code
     * @param string $dataDivision
     * @param array $conditionCross
     * @param bool $isOriginal
     * @param int $regionId
     * @param array $dataType
     * @param array $dataTypeFlags
     * @param string $intervalHourly
     * @param string $intervalMinutes
     * @return array
     */
    public function getRatingData(Carbon $startDateTime, Carbon $endDateTime, string $channelType, array $channelIds, string $division, string $code, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, array $dataType, array $dataTypeFlags, string $intervalHourly, string $intervalMinutes): array
    {
        $bindings = [];

        $bindings[':division'] = $division;
        $bindings[':code'] = $code;
        // 日付処理
        $bindings[':startDate'] = $startDateTime;
        $st = new Carbon($startDateTime);
        $et = new Carbon($endDateTime);
        $bindings[':startTimestamp'] = $st->toDateTimeString();
        $bindings[':endTimestamp'] = $et->toDateTimeString();

        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = array_values($dataTypeFlags);

        $latestDateTime = $this->getPerHourlyLatestDateTime($regionId, $intervalHourly, $intervalMinutes);
        $bindings[':latestDateTime'] = $latestDateTime;

        $bindChannelIds = $this->createArrayBindParam('channelId', [
            'channelId' => $channelIds,
        ], $bindings);

        $bindings[':regionId'] = $regionId;

        $with = '';
        $with .= ' WITH channels AS ( ';

        if ($channelType === 'summary') {
            $tmpArr = [];

            foreach ($bindChannelIds as $key => $val) {
                $tmpArr[] = " SELECT ${val}::numeric id ";
            }
            $with .= implode(' UNION ALL ', $tmpArr);
        } else {
            $with .= '   SELECT ';
            $with .= '     id ';
            $with .= '   FROM ';
            $with .= '     channels ';
            $with .= '   WHERE ';
            $with .= '     id IN (' . implode(',', $bindChannelIds) . ') ';
        }
        $with .= ' ), day_nums AS ( ';
        $tmpArr = [];

        foreach (range(0, $endDateTime->diffInDays($startDateTime)) as $val) {
            $tmpArr[] = "   SELECT {$val} day_num  ";
        }
        $with .= implode(' UNION ALL ', $tmpArr);
        $with .= ' ), hour_nums AS ( ';
        $tmpArr = [];

        foreach (range(5, 28) as $val) {
            $tmpArr[] = "   SELECT {$val} hour_num ";
        }
        $with .= implode(' UNION ALL ', $tmpArr);
        $with .= ' ), datetime_channels AS ( ';
        $with .= ' SELECT  ';
        $with .= "   start_date.datetime + (day_nums.day_num||' days')::interval  + (hour_nums.hour_num||' hours')::interval as datetime, ";
        $with .= '   channels.id channel_id ';
        $with .= ' FROM  ';
        $with .= "   (SELECT TO_DATE(:startDate, 'YYYY-MM-DD') as datetime ) start_date ";
        $with .= ' CROSS JOIN ';
        $with .= '   channels ';
        $with .= ' CROSS JOIN ';
        $with .= '   day_nums ';
        $with .= ' CROSS JOIN ';
        $with .= '   hour_nums ';
        $with .= ' ), datetime_time_box AS ( ';
        $with .= ' SELECT ';
        $with .= '   datetime as datetime, ';
        $with .= '   channel_id, ';
        $with .= '   id time_box_id ';
        $with .= ' FROM ';
        $with .= '   datetime_channels dc ';
        $with .= ' INNER JOIN ';
        $with .= '   time_boxes tb ';
        $with .= ' ON ';
        $with .= '   dc.datetime >= tb.started_at AND ';
        $with .= '   dc.datetime < tb.ended_at AND ';
        $with .= '   tb.region_id = :regionId ';
        $with .= ' ) ';
        $with .= ' , joined_hourly_reports AS (  ';
        // 地デジ2を選んだ場合
        if ($channelType == 'dt2') {
            $with .= '  SELECT ';
            $with .= "    TO_CHAR(wk.datetime, 'HH24') hhmm ";
            $with .= "    , EXTRACT(DOW FROM (datetime + interval '- 5 hours')) dow ";
            $with .= '    , channel_group ';
            $with .= '    , viewing_rate ';
            $with .= '    , time_box_id ';
            $with .= '  FROM ';
            $with .= '    ( ';
            $with .= '    SELECT ';
            $with .= '      dc.datetime ';
            $with .= '      , SUM(viewing_rate) viewing_rate ';
            $with .= '      , dc.time_box_id ';
            $with .= '      , CASE  ';

            if ($regionId === 1) {
                $with .= '      WHEN dc.channel_id = 2  ';
                $with .= '        THEN 2  ';
                $with .= '      WHEN dc.channel_id = 9  ';
                $with .= '        THEN 9  ';
            } elseif ($regionId === 2) {
                $with .= '      WHEN dc.channel_id = 45  ';
                $with .= '        THEN 45  ';
            }
            $with .= '      ELSE 999  ';
            $with .= '      END channel_group  ';
            $with .= '    FROM ';
            $with .= '      datetime_time_box dc  ';
            $with .= '    LEFT JOIN hourly_reports hr  ';
            $with .= '      ON dc.datetime = hr.datetime  ';
            $with .= '      AND dc.channel_id = hr.channel_id  ';
            $with .= '      AND dc.time_box_id = hr.time_box_id  ';
            $with .= '      AND hr.division = :division ';
            $with .= '      AND hr.code = :code ';
            $with .= '      AND hr.datetime BETWEEN :startTimestamp AND :endTimestamp ';
            $with .= '      AND hr.channel_id IN (' . implode(',', $bindChannelIds) . ') ';
            $with .= '    WHERE ';
            $with .= '      dc.datetime BETWEEN :startTimestamp AND :endTimestamp ';
            $with .= '      AND dc.datetime <= :latestDateTime ';
            $with .= '    GROUP BY ';
            $with .= '      dc.datetime, ';
            $with .= '      dc.time_box_id, ';
            $with .= '      channel_group ';
            $with .= '    ) wk ';
        } else {
            $with .= '   SELECT ';
            $with .= "     to_char(dc.datetime, 'HH24') hhmm ";
            $with .= "     , EXTRACT(DOW FROM (dc.datetime + interval '- 5 hours')) dow ";
            $with .= '     , dc.channel_id ';
            $with .= '     , hr.viewing_rate ';
            $with .= '     , dc.time_box_id ';
            $with .= '     , thr.viewing_rate ts_viewing_rate ';
            $with .= '     , thr.total_viewing_rate ';
            $with .= '     , COALESCE(thr.gross_viewing_rate, hr.ts_samples_viewing_rate) gross_viewing_rate ';
            $with .= '     , COALESCE(thr.rt_total_viewing_rate, hr.ts_samples_viewing_rate) rt_total_viewing_rate ';
            $with .= '   FROM ';
            $with .= '     datetime_time_box dc  ';
            $with .= '     LEFT JOIN hourly_reports hr  ';
            $with .= '       ON dc.datetime = hr.datetime  ';
            $with .= '       AND dc.channel_id = hr.channel_id  ';
            $with .= '       AND dc.time_box_id = hr.time_box_id  ';
            $with .= '       AND hr.division = :division  ';
            $with .= '       AND hr.code = :code  ';
            $with .= '       AND hr.datetime BETWEEN :startTimestamp AND :endTimestamp ';
            $with .= '       AND hr.channel_id IN (' . implode(',', $bindChannelIds) . ') ';
            $with .= '     LEFT JOIN ts_hourly_reports thr  ';
            $with .= '       ON dc.datetime = thr.datetime  ';
            $with .= '       AND dc.channel_id = thr.channel_id  ';
            $with .= '       AND dc.time_box_id = thr.time_box_id  ';
            $with .= '       AND thr.division = :division  ';
            $with .= '       AND thr.code = :code  ';
            $with .= '       AND thr.datetime BETWEEN :startTimestamp AND :endTimestamp ';
            $with .= '       AND thr.channel_id IN (' . implode(',', $bindChannelIds) . ') ';
            $with .= '   WHERE ';
            $with .= '     dc.datetime BETWEEN :startTimestamp AND :endTimestamp ';
            $with .= '     AND dc.datetime <= :latestDateTime ';
        }
        $with .= ') ';

        $select = '';
        $select .= 'SELECT ';
        $select .= ' hhmm ';
        $select .= ' ,dow ';
        // 地デジ2を選んだ場合
        if ($channelType == 'dt2') {
            $select .= ' , channel_group as channel_id';
        } else {
            // 地デジ2以外
            $select .= ' ,channel_id ';
        }

        if ($isRt) {
            $select .= ' ,AVG(COALESCE(viewing_rate,0))::real viewing_rate ';
        }

        if ($isTs) {
            $select .= ' ,AVG(COALESCE(ts_viewing_rate,0))::real viewing_rate ';
        }

        if ($isTotal) {
            $select .= ' ,AVG(COALESCE(total_viewing_rate,0))::real viewing_rate ';
        }

        if ($isGross) {
            $select .= ' ,AVG(COALESCE(gross_viewing_rate,0))::real viewing_rate ';
        }

        if ($isRtTotal) {
            $select .= ' ,AVG(COALESCE(rt_total_viewing_rate,0))::real viewing_rate ';
        }

        $from = '';
        $from .= 'FROM ';
        $from .= ' joined_hourly_reports ';
        $group = '';
        $group .= 'GROUP BY ';
        $group .= '  hhmm ';
        $group .= '  ,dow ';
        // 地デジ2を選んだ場合
        if ($channelType == 'dt2') {
            $group .= ' , channel_group ';
        } else {
            // 地デジ2以外
            $group .= ' ,channel_id ';
        }
        $order = '';
        $order .= 'ORDER BY ';
        // 地デジ2を選んだ場合
        if ($channelType == 'dt2') {
            $order .= ' channel_group ';
        } else {
            // 地デジ2以外
            $order .= ' channel_id ';
        }
        $order .= '  ,dow ';
        $order .= '  ,hhmm ';

        $query = $with . $select . $from . $group . $order;

        $result = $this->select($query, $bindings);

        return $result;
    }

    /**
     * @param Carbon $startDateTime
     * @param Carbon $endDateTime
     * @param string $channelType
     * @param array $channelIds
     * @param string $division
     * @param string $code
     * @param string $dataDivision
     * @param array $conditionCross
     * @param bool $isOriginal
     * @param int $regionId
     * @param array $dataType
     * @param array $dataTypeFlags
     * @param string $intervalHourly
     * @param string $intervalMinutes
     *
     * @return array
     */
    public function getShareData(Carbon $startDateTime, Carbon $endDateTime, string $channelType, array $channelIds, string $division, string $code, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, array $dataType, array $dataTypeFlags, string $intervalHourly, string $intervalMinutes): array
    {
        $bindings = [];
        $bindings[':division'] = $division;
        $bindings[':code'] = $code;
        // 日付処理
        $bindings[':startDate'] = $startDateTime;
        $st = new Carbon($startDateTime);
        $et = new Carbon($endDateTime);
        $bindings[':startTimestamp'] = $st->toDateTimeString();
        $bindings[':endTimestamp'] = $et->toDateTimeString();

        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = array_values($dataTypeFlags);

        $latestDateTime = $this->getPerHourlyLatestDateTime($regionId, $intervalHourly, $intervalMinutes);
        $bindings[':latestDateTime'] = $latestDateTime;

        $bindChannelIds = $this->createArrayBindParam('channelId', [
            'channelId' => $channelIds,
        ], $bindings);

        $bindings[':regionId'] = $regionId;

        $with = '';
        $with .= ' WITH channels AS ( ';
        $with .= '   SELECT ';
        $with .= '     id ';
        $with .= '   FROM ';
        $with .= '     channels ';
        $with .= '   WHERE ';
        $with .= '     id IN (' . implode(',', $bindChannelIds) . ') ';
        $with .= ' ), day_nums AS ( ';
        $tmpArr = [];

        foreach (range(0, $endDateTime->diffInDays($startDateTime)) as $val) {
            $tmpArr[] = "   SELECT {$val} day_num  ";
        }
        $with .= implode(' UNION ALL ', $tmpArr);
        $with .= ' ), hour_nums AS ( ';
        $tmpArr = [];

        foreach (range(5, 28) as $val) {
            $tmpArr[] = "   SELECT {$val} hour_num ";
        }
        $with .= implode(' UNION ALL ', $tmpArr);
        $with .= ' ), datetime_channels AS ( ';
        $with .= ' SELECT  ';
        $with .= "   start_date.datetime + (day_nums.day_num||' days')::interval  + (hour_nums.hour_num||' hours')::interval as datetime, ";
        $with .= '   channels.id channel_id ';
        $with .= ' FROM  ';
        $with .= "   (SELECT TO_DATE(:startDate, 'YYYY-MM-DD') as datetime ) start_date ";
        $with .= ' CROSS JOIN ';
        $with .= '   channels ';
        $with .= ' CROSS JOIN ';
        $with .= '   day_nums ';
        $with .= ' CROSS JOIN ';
        $with .= '   hour_nums ';
        $with .= ' ), datetime_time_box AS ( ';
        $with .= ' SELECT ';
        $with .= '   datetime as datetime, ';
        $with .= '   channel_id, ';
        $with .= '   id time_box_id ';
        $with .= ' FROM ';
        $with .= '   datetime_channels dc ';
        $with .= ' INNER JOIN ';
        $with .= '   time_boxes tb ';
        $with .= ' ON ';
        $with .= '   dc.datetime >= tb.started_at AND ';
        $with .= '   dc.datetime < tb.ended_at AND ';
        $with .= '   tb.region_id = :regionId ';
        $with .= ' ) ';
        $with .= ', joined_hourly_reports AS (  ';
        $with .= '  SELECT ';
        $with .= "    to_char(dc.datetime, 'HH24') hhmm ";
        $with .= "    , EXTRACT(DOW FROM (dc.datetime + interval '- 5 hours')) dow ";
        $with .= '    , dc.channel_id                                ';
        $with .= '    , hr.viewing_seconds / SUM(hr.viewing_seconds) OVER (PARTITION BY dc.datetime) ::numeric * 100 AS share ';
        $with .= '    , SUM(hr.viewing_seconds) OVER (PARTITION BY dc.datetime) AS total_time ';
        $with .= '    , dc.time_box_id       ';
        $with .= '    , thr.viewing_seconds / CASE WHEN SUM(thr.viewing_seconds) OVER (PARTITION BY dc.datetime) = 0 THEN 1 ELSE SUM(thr.viewing_seconds) OVER (PARTITION BY dc.datetime) END ::numeric * 100 AS ts_share ';
        $with .= '    , SUM(thr.viewing_seconds) OVER (PARTITION BY dc.datetime) AS ts_total_time  ';
        $with .= '    , thr.total_viewing_seconds / CASE WHEN SUM(thr.total_viewing_seconds) OVER (PARTITION BY dc.datetime) = 0 THEN 1 ELSE SUM(thr.total_viewing_seconds) OVER (PARTITION BY dc.datetime) END ::numeric * 100 AS total_share ';
        $with .= '    , SUM(thr.total_viewing_seconds) OVER (PARTITION BY dc.datetime) AS total_total_time  ';
        $with .= '    , COALESCE(thr.gross_viewing_seconds, hr.ts_samples_viewing_seconds) / CASE WHEN SUM(COALESCE(thr.gross_viewing_seconds, hr.ts_samples_viewing_seconds)) OVER (PARTITION BY dc.datetime) = 0 THEN 1 ELSE SUM(COALESCE(thr.gross_viewing_seconds, hr.ts_samples_viewing_seconds)) OVER (PARTITION BY dc.datetime) END ::numeric * 100 AS gross_share ';
        $with .= '    , SUM(COALESCE(thr.gross_viewing_seconds, hr.ts_samples_viewing_seconds)) OVER (PARTITION BY dc.datetime) AS gross_total_time  ';
        $with .= '    , COALESCE(thr.rt_total_viewing_seconds, hr.ts_samples_viewing_seconds) / CASE WHEN SUM(COALESCE(thr.rt_total_viewing_seconds, hr.ts_samples_viewing_seconds)) OVER (PARTITION BY dc.datetime) = 0 THEN 1 ELSE SUM(COALESCE(thr.rt_total_viewing_seconds, hr.ts_samples_viewing_seconds)) OVER (PARTITION BY dc.datetime) END ::numeric * 100 AS rt_total_share ';
        $with .= '    , SUM(COALESCE(thr.rt_total_viewing_seconds, hr.ts_samples_viewing_seconds)) OVER (PARTITION BY dc.datetime) AS rt_total_total_time  ';
        $with .= 'FROM ';
        $with .= '  datetime_time_box dc  ';
        $with .= '  LEFT JOIN hourly_reports hr  ';
        $with .= '    ON dc.datetime = hr.datetime  ';
        $with .= '    AND dc.channel_id = hr.channel_id  ';
        $with .= '    AND dc.time_box_id = hr.time_box_id  ';
        $with .= '    AND hr.division = :division ';
        $with .= '    AND hr.code = :code ';
        $with .= '    AND hr.datetime BETWEEN :startTimestamp AND :endTimestamp  ';
        $with .= '    AND hr.channel_id IN (' . implode(',', $bindChannelIds) . ') ';
        $with .= '  LEFT JOIN ts_hourly_reports thr  ';
        $with .= '    ON dc.datetime = thr.datetime  ';
        $with .= '    AND dc.channel_id = thr.channel_id  ';
        $with .= '    AND dc.time_box_id = thr.time_box_id  ';
        $with .= '    AND thr.division = :division ';
        $with .= '    AND thr.code = :code ';
        $with .= '    AND thr.datetime BETWEEN :startTimestamp AND :endTimestamp  ';
        $with .= '    AND thr.channel_id IN (' . implode(',', $bindChannelIds) . ') ';
        $with .= ' WHERE ';
        $with .= '   dc.datetime <= :latestDateTime ';
        $with .= ') ';

        $select = 'SELECT ';
        $select .= '  hhmm ';
        $select .= '  , dow ';
        $select .= '  , channel_id                                     ';

        if ($isRt) {
            $select .= '  , AVG(COALESCE(share, 0)) AS share ';
        }

        if ($isTs) {
            $select .= '  , AVG(COALESCE(ts_share, 0)) AS share   ';
        }

        if ($isTotal) {
            $select .= '  , AVG(COALESCE(total_share, 0)) AS share   ';
        }

        if ($isGross) {
            $select .= '  , AVG(COALESCE(gross_share, 0)) AS share   ';
        }

        if ($isRtTotal) {
            $select .= '  , AVG(COALESCE(rt_total_share, 0)) AS share   ';
        }
        $from = 'FROM ';
        $from .= '  joined_hourly_reports  ';
        $group = 'GROUP BY ';
        $group .= '  hhmm ';
        $group .= '  , dow ';
        $group .= '  , channel_id  ';
        $order = 'ORDER BY ';
        $order .= '  dow ';
        $order .= '  , hhmm ';
        $order .= '  , channel_id;  ';

        $query = $with . $select . $from . $group . $order;
        $result = $this->select($query, $bindings);

        return $result;
    }

    /**
     * @param Carbon $startDateTime
     * @param Carbon $endDateTime
     * @param string $channelType
     * @param array $channelIds
     * @param string $division
     * @param string $code
     * @param string $dataDivision
     * @param array $conditionCross
     * @param bool $isOriginal
     * @param int $regionId
     * @param array $dataType
     * @param array $dataTypeFlags
     * @param string $intervalHourly
     * @param string $intervalMinutes
     * @return array
     */
    public function getTargetData(Carbon $startDateTime, Carbon $endDateTime, string $channelType, array $channelIds, string $division, string $code, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, array $dataType, array $dataTypeFlags, string $intervalHourly, string $intervalMinutes): array
    {
        $bindings = [];
        $bindings[':division'] = $division;
        $bindings[':code'] = $code;

        // 日付処理
        $bindings[':startDate'] = $startDateTime;
        $st = new Carbon($startDateTime);
        $et = new Carbon($endDateTime);
        $bindings[':startTimestamp'] = $st->subDay()->toDateTimeString();
        $bindings[':endTimestamp'] = $et->addDay()->toDateTimeString();

        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = array_values($dataTypeFlags);

        $latestDateTime = $this->getPerHourlyLatestDateTime($regionId, $intervalHourly, $intervalMinutes);
        $bindings[':latestDateTime'] = $latestDateTime;

        $bindChannelIds = $this->createArrayBindParam('channelId', [
            'channelId' => $channelIds,
        ], $bindings);

        $bindings[':regionId'] = $regionId;

        $with = '';
        $with .= ' WITH channels AS ( ';
        $with .= '   SELECT ';
        $with .= '     id ';
        $with .= '   FROM ';
        $with .= '     channels ';
        $with .= '   WHERE ';
        $with .= '     id IN (' . implode(',', $bindChannelIds) . ') ';
        $with .= ' ), day_nums AS ( ';
        $tmpArr = [];

        foreach (range(0, $endDateTime->diffInDays($startDateTime)) as $val) {
            $tmpArr[] = "   SELECT {$val} day_num  ";
        }
        $with .= implode(' UNION ALL ', $tmpArr);
        $with .= ' ), hour_nums AS ( ';
        $tmpArr = [];

        foreach (range(5, 28) as $val) {
            $tmpArr[] = "   SELECT {$val} hour_num ";
        }
        $with .= implode(' UNION ALL ', $tmpArr);
        $with .= ' ), datetime_channels AS ( ';
        $with .= ' SELECT  ';
        $with .= "   start_date.datetime + (day_nums.day_num||' days')::interval  + (hour_nums.hour_num||' hours')::interval as datetime, ";
        $with .= '   channels.id channel_id ';
        $with .= ' FROM  ';
        $with .= "   (SELECT TO_DATE(:startDate, 'YYYY-MM-DD') as datetime ) start_date ";
        $with .= ' CROSS JOIN ';
        $with .= '   channels ';
        $with .= ' CROSS JOIN ';
        $with .= '   day_nums ';
        $with .= ' CROSS JOIN ';
        $with .= '   hour_nums ';
        $with .= ' ), datetime_time_box AS ( ';
        $with .= ' SELECT ';
        $with .= '   datetime as datetime, ';
        $with .= '   channel_id, ';
        $with .= '   id time_box_id ';
        $with .= ' FROM ';
        $with .= '   datetime_channels dc ';
        $with .= ' INNER JOIN ';
        $with .= '   time_boxes tb ';
        $with .= ' ON ';
        $with .= '   dc.datetime >= tb.started_at AND ';
        $with .= '   dc.datetime < tb.ended_at AND ';
        $with .= '   tb.region_id = :regionId ';
        $with .= ' ) ';

        $with .= ' ,union_hourly_reports AS ( ';
        $with .= '   SELECT ';
        $with .= '     time_box_id ';
        $with .= '     , datetime ';
        $with .= '     , date ';
        $with .= '     , hour ';
        $with .= '     , channel_id ';
        $with .= '     , division ';
        $with .= '     , code ';
        $with .= '     , MAX(viewing_rate) viewing_rate ';
        $with .= '     , MAX(ts_samples_viewing_rate) ts_samples_viewing_rate ';
        $with .= '     , MAX(ts_viewing_rate) ts_viewing_rate ';
        $with .= '     , MAX(total_viewing_rate) total_viewing_rate ';
        $with .= '     , COALESCE(MAX(gross_viewing_rate), MAX(ts_samples_viewing_rate)) gross_viewing_rate ';
        $with .= '     , COALESCE(MAX(rt_total_viewing_rate), MAX(ts_samples_viewing_rate)) rt_total_viewing_rate ';
        $with .= '   FROM ( ';
        $with .= '     SELECT ';
        $with .= '       time_box_id ';
        $with .= '       , datetime ';
        $with .= '       , date ';
        $with .= '       , hour ';
        $with .= '       , channel_id ';
        $with .= '	     , division ';
        $with .= '	     , code ';
        $with .= '       , viewing_rate ';
        $with .= '       , ts_samples_viewing_rate ';
        $with .= '       , NULL ts_viewing_rate ';
        $with .= '       , NULL total_viewing_rate ';
        $with .= '       , NULL gross_viewing_rate  ';
        $with .= '       , NULL rt_total_viewing_rate  ';
        $with .= '     FROM ';
        $with .= '       hourly_reports hr ';
        $with .= '     WHERE ';
        $with .= '       hr.datetime BETWEEN :startTimestamp AND :endTimestamp ';
        $with .= "	     AND (hr.division, hr.code) IN (('household', '1'), ('personal', '1'), (:division, :code)) ";
        $with .= '       AND hr.channel_id IN (' . implode(',', $bindChannelIds) . ') ';
        $with .= '       AND EXISTS(SELECT 1 FROM datetime_time_box dc WHERE hr.datetime = dc.datetime AND hr.channel_id = dc.channel_id AND hr.time_box_id = dc.time_box_id) ';
        $with .= '     UNION ALL ';
        $with .= '     SELECT ';
        $with .= '       time_box_id ';
        $with .= '       , datetime ';
        $with .= '       , date ';
        $with .= '       , hour ';
        $with .= '       , channel_id ';
        $with .= '	     , division ';
        $with .= '	     , code ';
        $with .= '       , NULL viewing_rate ';
        $with .= '       , NULL ts_samples_viewing_rate ';
        $with .= '       , viewing_rate ts_viewing_rate ';
        $with .= '       , total_viewing_rate ';
        $with .= '       , gross_viewing_rate  ';
        $with .= '       , rt_total_viewing_rate  ';
        $with .= '     FROM ';
        $with .= '       ts_hourly_reports hr ';
        $with .= '     WHERE ';
        $with .= '       hr.datetime BETWEEN :startTimestamp AND :endTimestamp  ';
        $with .= "	     AND (hr.division, hr.code) IN (('household', '1'), ('personal', '1'), (:division, :code)) ";
        $with .= '       AND hr.channel_id IN (' . implode(',', $bindChannelIds) . ') ';
        $with .= '       AND EXISTS(SELECT 1 FROM datetime_time_box dc WHERE hr.datetime = dc.datetime AND hr.channel_id = dc.channel_id AND hr.time_box_id = dc.time_box_id) ';
        $with .= '   ) u ';
        $with .= '   GROUP BY ';
        $with .= '     time_box_id ';
        $with .= '     , datetime ';
        $with .= '     , date ';
        $with .= '     , hour ';
        $with .= '     , channel_id ';
        $with .= '     , division ';
        $with .= '     , code ';
        $with .= ' ) ';

        $with .= ', joined_hourly_reports AS (  ';
        $with .= '  SELECT ';
        $with .= '    dc.time_box_id ';
        $with .= '    , dc.datetime ';
        $with .= "    , TO_CHAR(dc.datetime, 'HH24') hhmm ";
        $with .= "    , EXTRACT(DOW FROM dc.datetime - interval '5 hours') dow ";
        $with .= '    , dc.channel_id ';
        $with .= '    , hr.viewing_rate ';
        $with .= '    , hr.ts_viewing_rate ';
        $with .= '    , hr.total_viewing_rate ';
        $with .= '    , hr.gross_viewing_rate  ';
        $with .= '    , hr.rt_total_viewing_rate  ';

        if ($dataDivision === 'target_content_personal') {
            $with .= '    , phr.viewing_rate denominator_viewing_rate ';
            $with .= '    , phr.ts_viewing_rate ts_denominator_viewing_rate ';
            $with .= '    , phr.total_viewing_rate total_denominator_viewing_rate ';
            $with .= '    , phr.gross_viewing_rate gross_denominator_viewing_rate ';
        } elseif ($dataDivision === 'target_content_household') {
            $with .= '    , hhr.viewing_rate denominator_viewing_rate';
            $with .= '    , hhr.ts_viewing_rate ts_denominator_viewing_rate ';
            $with .= '    , hhr.total_viewing_rate total_denominator_viewing_rate ';
            $with .= '    , hhr.gross_viewing_rate gross_denominator_viewing_rate ';
        }
        $with .= '    , phr.rt_total_viewing_rate rt_total_personal_viewing_rate ';
        $with .= '  FROM ';
        $with .= '    datetime_time_box dc  ';
        $with .= '    LEFT JOIN union_hourly_reports hhr  ';
        $with .= '      ON dc.datetime = hhr.datetime  ';
        $with .= '      AND dc.channel_id = hhr.channel_id  ';
        $with .= '      AND dc.time_box_id = hhr.time_box_id  ';
        $with .= "      AND hhr.division = 'household'  ";
        $with .= "      AND hhr.code = '1'  ";
        $with .= '    LEFT JOIN union_hourly_reports phr  ';
        $with .= '      ON dc.datetime = phr.datetime  ';
        $with .= '      AND dc.channel_id = phr.channel_id  ';
        $with .= '      AND dc.time_box_id = phr.time_box_id  ';
        $with .= "      AND phr.division = 'personal'  ";
        $with .= "      AND phr.code = '1'  ";
        $with .= '    LEFT JOIN union_hourly_reports hr  ';
        $with .= '      ON dc.datetime = hr.datetime  ';
        $with .= '      AND dc.channel_id = hr.channel_id  ';
        $with .= '      AND dc.time_box_id = hr.time_box_id  ';
        $with .= '      AND hr.division = :division  ';
        $with .= '      AND hr.code = :code  ';
        $with .= '), hourly_target AS (';
        $with .= '  SELECT ';
        $with .= '    time_box_id ';
        $with .= '    , hhmm ';
        $with .= '    , dow ';
        $with .= '    , channel_id ';

        $with .= '    , CASE WHEN SUM(denominator_viewing_rate) = 0 THEN 0 ELSE SUM(viewing_rate) / SUM(denominator_viewing_rate) * 100 END AS target_viewing_rate  ';
        $with .= '    , CASE WHEN SUM(ts_denominator_viewing_rate) = 0 THEN 0 ELSE SUM(ts_viewing_rate) / SUM(ts_denominator_viewing_rate) * 100 END AS ts_target_viewing_rate  ';
        $with .= '    , CASE WHEN SUM(gross_denominator_viewing_rate) = 0 THEN 0 ELSE SUM(gross_viewing_rate) / SUM(gross_denominator_viewing_rate) * 100 END AS gross_target_viewing_rate  ';
        $with .= '    , CASE WHEN SUM(rt_total_personal_viewing_rate) = 0 THEN 0 ELSE SUM(rt_total_viewing_rate) / SUM(rt_total_personal_viewing_rate) * 100 END AS rt_total_target_viewing_rate  ';
        $with .= '  FROM ';
        $with .= '    joined_hourly_reports  ';
        $with .= '  WHERE ';
        $with .= '    datetime <= :latestDateTime ';
        $with .= '  GROUP BY ';
        $with .= '    time_box_id ';
        $with .= '    , hhmm ';
        $with .= '    , dow ';
        $with .= '    , channel_id ';
        $with .= ')  ';
        $select = 'SELECT ';
        $select .= '  hhmm ';
        $select .= '  , dow ';
        $select .= '  , channel_id ';

        if ($isRt) {
            $select .= '  , AVG(COALESCE(target_viewing_rate, 0)) AS target_viewing_rate  ';
        }

        if ($isTs) {
            $select .= '  , AVG(COALESCE(ts_target_viewing_rate, 0)) AS target_viewing_rate  ';
        }

        if ($isGross) {
            $select .= '  , AVG(COALESCE(gross_target_viewing_rate, 0)) AS target_viewing_rate  ';
        }

        if ($isRtTotal) {
            $select .= '  , AVG(COALESCE(rt_total_target_viewing_rate, 0)) AS target_viewing_rate  ';
        }

        $from = 'FROM ';
        $from .= '  hourly_target  ';
        $group = 'GROUP BY ';
        $group .= '  channel_id ';
        $group .= '  , dow ';
        $group .= '  , hhmm  ';
        $order = 'ORDER BY ';
        $order .= '  channel_id ';
        $order .= '  , dow ';
        $order .= '  , hhmm ';

        $query = $with . $select . $from . $group . $order;
        $result = $this->select($query, $bindings);

        return $result;
    }

    /**
     * @param Carbon $startDateTime
     * @param Carbon $endDateTime
     * @param string $channelType
     * @param array $channelIds
     * @param string $division
     * @param array $code
     * @param string $dataDivision
     * @param array $conditionCross
     * @param bool $isOriginal
     * @param int $regionId
     * @param array $dataType
     * @param array $dataTypeFlags
     * @param string $intervalHourly
     * @param string $intervalMinutes
     * @return array
     */
    public function getConditionCrossRatingData(Carbon $startDateTime, Carbon $endDateTime, string $channelType, array $channelIds, string $division, ?array $code, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, array $dataType, array $dataTypeFlags, string $intervalHourly, string $intervalMinutes): array
    {
        $bindings = [];
        $divisionKey = $division . '_';

        // 日付処理
        $bindings[':startDate'] = $startDateTime;
        $st = new Carbon($startDateTime);
        $et = new Carbon($endDateTime);
        $bindings[':startTimestamp'] = $st->subDay()->toDateTimeString();
        $bindings[':endTimestamp'] = $et->addDay()->toDateTimeString();

        $bindChannelIds = $this->createArrayBindParam('channelId', [
            'channelId' => $channelIds,
        ], $bindings);

        $bindings[':regionId'] = $regionId;

        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = array_values($dataTypeFlags);

        $latestDateTime = $this->getPerHourlyLatestDateTime($regionId, $intervalHourly, $intervalMinutes);
        $bindings[':latestDateTime'] = $latestDateTime;

        $with = '';
        $with .= ' WITH channels AS ( ';

        if ($channelType === 'summary') {
            $tmpArr = [];

            foreach ($bindChannelIds as $key => $val) {
                $tmpArr[] = " SELECT ${val}::numeric id ";
            }
            $with .= implode(' UNION ALL ', $tmpArr);
        } else {
            $with .= '   SELECT ';
            $with .= '     id ';
            $with .= '   FROM ';
            $with .= '     channels ';
            $with .= '   WHERE ';
            $with .= '     id IN (' . implode(',', $bindChannelIds) . ') ';
        }
        $with .= ' ), day_nums AS ( ';
        $tmpArr = [];

        foreach (range(0, $endDateTime->diffInDays($startDateTime)) as $val) {
            $tmpArr[] = "   SELECT {$val} day_num  ";
        }
        $with .= implode(' UNION ALL ', $tmpArr);
        $with .= ' ), hour_nums AS ( ';
        $tmpArr = [];

        foreach (range(5, 28) as $val) {
            $tmpArr[] = "   SELECT {$val} hour_num ";
        }
        $with .= implode(' UNION ALL ', $tmpArr);
        $with .= ' ), datetime_channels AS ( ';
        $with .= ' SELECT  ';
        $with .= "   start_date.datetime + (day_nums.day_num||' days')::interval  + (hour_nums.hour_num||' hours')::interval as datetime, ";
        $with .= '   channels.id channel_id ';
        $with .= ' FROM  ';
        $with .= "   (SELECT TO_DATE(:startDate, 'YYYY-MM-DD') as datetime ) start_date ";
        $with .= ' CROSS JOIN ';
        $with .= '   channels ';
        $with .= ' CROSS JOIN ';
        $with .= '   day_nums ';
        $with .= ' CROSS JOIN ';
        $with .= '   hour_nums ';
        $with .= ' ), datetime_time_box AS ( ';
        $with .= ' SELECT ';
        $with .= '   datetime as datetime, ';
        $with .= '   channel_id, ';
        $with .= '   id time_box_id ';
        $with .= ' FROM ';
        $with .= '   datetime_channels dc ';
        $with .= ' INNER JOIN ';
        $with .= '   time_boxes tb ';
        $with .= ' ON ';
        $with .= '   dc.datetime >= tb.started_at AND ';
        $with .= '   dc.datetime < tb.ended_at AND ';
        $with .= '   tb.region_id = :regionId ';
        $with .= ') ';

        $with .= ', timebox as (  ';
        $with .= '  SELECT ';
        $with .= '    id ';
        $with .= '    , FIRST_VALUE(id) OVER (  ';
        $with .= '      ORDER BY ';
        $with .= '        id DESC ROWS BETWEEN 1 PRECEDING AND CURRENT ROW ';
        $with .= '    ) next_id ';
        $with .= '    , region_id  ';
        $with .= '	, started_at ';
        $with .= '	, ended_at ';
        $with .= '  FROM ';
        $with .= '    time_boxes  ';
        $with .= '  WHERE ';
        $with .= '    region_id = :regionId ';
        $with .= ')  ';

        $with .= ', ts_samples AS (  ';
        $with .= '  SELECT ';
        $with .= '    tbp.paneler_id ';
        $with .= '    , tbp.time_box_id ';
        $with .= '    , codes.code ';
        $with .= ' 	  , t.started_at ';
        $with .= '	  , t.ended_at  ';
        $with .= '  FROM ';
        $with .= '    ts_time_box_panelers tbp ';
        $with .= '	INNER JOIN time_boxes t ';
        $with .= '	ON tbp.time_box_id = t.id  ';
        $with .= '	    AND t.region_id = :regionId  ';
        $with .= '      AND t.started_at <= :endTimestamp  ';
        $with .= '      AND t.ended_at > :startTimestamp  ';
        $with .= '  CROSS JOIN ';

        if ($isOriginal) {
            $tmpArr = [];
            // 拡張、オリジナル属性
            foreach ($code as $cd) {
                $key = ':union_' . $divisionKey . $cd;
                $bindings[$key] = $cd;
                $tmpSql = ' ( ';
                $tmpSql .= ' SELECT ' . $key . '::varchar(255) as code ';
                $tmpSql .= ' ) ';
                array_push($tmpArr, $tmpSql);
            }
            $with .= ' ( ' . implode(' UNION ', $tmpArr) . ' ) AS codes ';
        } else {
            // かけ合わせ条件
            $bindings[':condition_cross_code'] = 'condition_cross';
            $with .= ' ( ';
            $with .= ' (SELECT :condition_cross_code ::varchar(255) as code) ';
            $with .= ' ) codes ';
        }
        $with .= ' WHERE   ';
        $with .= '   ( ';

        if ($isOriginal) {
            $with .= $this->createCrossJoinWhereClause($division, $code, $bindings);
        } else {
            $with .= " codes.code = 'condition_cross' ";
            $with .= $this->createConditionCrossSql($conditionCross, $bindings);
        }
        $with .= ' ) ';

        $with .= ')  ';
        $with .= ', samples AS (  ';
        $with .= '  SELECT ';
        $with .= '    tbp.paneler_id ';
        $with .= '    , tbp.time_box_id ';
        $with .= '    , codes.code ';
        $with .= ' 	  , t.started_at ';
        $with .= '	  , t.ended_at  ';
        $with .= '  FROM ';
        $with .= '    time_box_panelers tbp ';
        $with .= '	INNER JOIN time_boxes t ';
        $with .= '	ON tbp.time_box_id = t.id  ';
        $with .= '	    AND t.region_id = :regionId  ';
        $with .= '      AND t.started_at <= :endTimestamp  ';
        $with .= '      AND t.ended_at > :startTimestamp  ';

        $with .= '  CROSS JOIN ';

        if ($isOriginal) {
            $tmpArr = [];
            // 拡張、オリジナル属性
            foreach ($code as $cd) {
                $key = ':union_' . $divisionKey . $cd;
                $bindings[$key] = $cd;
                $tmpSql = ' ( ';
                $tmpSql .= ' SELECT ' . $key . '::varchar(255) as code ';
                $tmpSql .= ' ) ';
                array_push($tmpArr, $tmpSql);
            }
            $with .= ' ( ' . implode(' UNION ', $tmpArr) . ' ) AS codes ';
        } else {
            // かけ合わせ条件
            $bindings[':condition_cross_code'] = 'condition_cross';
            $with .= ' ( ';
            $with .= ' (SELECT :condition_cross_code ::varchar(255) as code) ';
            $with .= ' ) codes ';
        }
        $with .= ' WHERE   ';
        $with .= '   ( ';

        if ($isOriginal) {
            $with .= $this->createCrossJoinWhereClause($division, $code, $bindings);
        } else {
            $with .= " codes.code = 'condition_cross' ";
            $with .= $this->createConditionCrossSql($conditionCross, $bindings);
        }
        $with .= ' ) ';

        $with .= ')  ';
        $with .= ', rt_numbers AS (  ';
        $with .= '  SELECT ';
        $with .= '    COUNT(paneler_id) number ';
        $with .= '    , SUM(COUNT(paneler_id)) OVER (PARTITION BY time_box_id) all_number ';
        $with .= '    , code ';
        $with .= '    , time_box_id  ';
        $with .= '  FROM ';
        $with .= '    samples  ';
        $with .= '  GROUP BY ';
        $with .= '    code ';
        $with .= '    , time_box_id ';
        $with .= ')  ';
        $with .= ', ts_numbers AS (  ';
        $with .= '  SELECT ';
        $with .= '    COUNT(paneler_id) number ';
        $with .= '    , SUM(COUNT(paneler_id)) OVER (PARTITION BY time_box_id) all_number ';
        $with .= '    , code ';
        $with .= '    , time_box_id  ';
        $with .= '  FROM ';
        $with .= '    ts_samples  ';
        $with .= '  GROUP BY ';
        $with .= '    code ';
        $with .= '    , time_box_id ';
        $with .= ')  ';
        $with .= ', union_viewers AS (  ';
        $with .= '  SELECT ';
        $with .= '      "datetime" ';
        $with .= '    , channel_id ';
        $with .= '    , paneler_id ';
        $with .= '	  , time_box_id ';
        $with .= '    , code ';
        $with .= '    , SUM(viewing_seconds) viewing_seconds ';
        $with .= '    , SUM(ts_viewing_seconds) ts_viewing_seconds ';
        $with .= '    , SUM(total_viewing_seconds) total_viewing_seconds ';
        $with .= '    , COALESCE(MAX(gross_viewing_seconds), MAX(viewing_seconds)) gross_viewing_seconds ';
        $with .= '    , SUM(rt_total_viewing_seconds) rt_total_viewing_seconds  ';
        $with .= '  FROM ';
        $with .= '    (  ';
        $with .= '      SELECT ';
        $with .= '        hv.datetime ';
        $with .= '        , hv.channel_id ';
        $with .= '        , hv.paneler_id ';
        $with .= '		  , s.time_box_id ';
        $with .= '        , s.code ';
        $with .= '        , hv.viewing_seconds ';
        $with .= '        , 0 ts_viewing_seconds ';
        $with .= '        , 0 total_viewing_seconds ';
        $with .= '        , NULL gross_viewing_seconds ';
        $with .= '        , viewing_seconds rt_total_viewing_seconds  ';
        $with .= '      FROM ';
        $with .= '        hourly_viewers hv  ';
        $with .= '        INNER JOIN samples s  ';
        $with .= '          ON hv.paneler_id = s.paneler_id  ';
        $with .= '          AND hv.datetime >= s.started_at AND hv.datetime < s.ended_at  ';
        $with .= '          AND hv.datetime >= :startTimestamp AND hv.datetime <= :endTimestamp  ';
        $with .= '      UNION ALL  ';
        $with .= '      SELECT ';
        $with .= '        hv.datetime ';
        $with .= '        , hv.channel_id ';
        $with .= '        , hv.paneler_id ';
        $with .= '		  , s.time_box_id ';
        $with .= '        , s.code ';
        $with .= '        , 0 viewing_seconds ';
        $with .= '        , viewing_seconds ts_viewing_seconds ';
        $with .= '        , total_viewing_seconds ';
        $with .= '        , gross_viewing_seconds ';
        $with .= '        , total_viewing_seconds rt_total_viewing_seconds  ';
        $with .= '      FROM ';
        $with .= '        ts_hourly_viewers hv  ';
        $with .= '        INNER JOIN ts_samples s  ';
        $with .= '          ON hv.paneler_id = s.paneler_id  ';
        $with .= '          AND hv.datetime >= s.started_at AND hv.datetime < s.ended_at  ';
        $with .= '          AND hv.datetime >= :startTimestamp AND hv.datetime <= :endTimestamp  ';
        $with .= '    ) unioned  ';
        $with .= '  GROUP BY ';
        $with .= '    "datetime" ';
        $with .= '    , channel_id ';
        $with .= '	, time_box_id ';
        $with .= '    , code ';
        $with .= '    , paneler_id ';
        $with .= ')  ';
        $with .= ', hourly_viewers_grouped AS ( ';
        $with .= ' SELECT ';
        $with .= "    TO_CHAR(dc.datetime, 'HH24') hhmm ";
        $with .= "    , EXTRACT(DOW FROM dc.datetime - interval ' 5 hours') dow ";
        // 放送で地デジ2を選んだ場合はU局計の視聴率を集計する
        if ($channelType == 'dt2') {
            $with .= '      , CASE  ';

            if ($regionId === 1) {
                $with .= '      WHEN dc.channel_id = 2  ';
                $with .= '        THEN 2  ';
                $with .= '      WHEN dc.channel_id = 9  ';
                $with .= '        THEN 9  ';
            } elseif ($regionId === 2) {
                $with .= '      WHEN dc.channel_id = 45  ';
                $with .= '        THEN 45  ';
            }
            $with .= '      ELSE 999 END channel_group ';
        } else {
            $with .= '     , dc.channel_id ';
        }
        $with .= '    , dc.time_box_id ';
        $with .= '    , SUM(viewers.viewing_seconds) ::numeric / (rtn.number * 60 * 60) * 100 viewing_rate  ';
        $with .= '    , SUM(viewers.ts_viewing_seconds) ::numeric / (tsn.number * 60 * 60) * 100 ts_viewing_rate  ';
        $with .= '    , SUM(viewers.total_viewing_seconds) ::numeric / (tsn.number * 60 * 60) * 100 total_viewing_rate  ';
        $with .= '    , SUM(viewers.gross_viewing_seconds) ::numeric / (tsn.number * 60 * 60) * 100 gross_viewing_rate  ';
        $with .= '    , SUM(viewers.rt_total_viewing_seconds) ::numeric / (tsn.number * 60 * 60) * 100 rt_total_viewing_rate  ';
        $with .= '   FROM ';
        $with .= '     datetime_time_box dc ';
        $with .= '    LEFT JOIN (  ';
        $with .= '      SELECT ';
        $with .= '        hv.datetime ';
        $with .= '        , hv.channel_id ';
        $with .= '        , hv.viewing_seconds ';
        $with .= '        , hv.ts_viewing_seconds ';
        $with .= '        , hv.total_viewing_seconds ';
        $with .= '        , hv.gross_viewing_seconds ';
        $with .= '        , hv.rt_total_viewing_seconds ';
        $with .= '        , hv.time_box_id ';
        $with .= '        , hv.paneler_id ';
        $with .= '      FROM ';
        $with .= '        union_viewers hv  ';

        if ($isRt) {
            $with .= ' WHERE EXISTS(SELECT 1 FROM samples s WHERE hv.paneler_id = s.paneler_id AND hv.time_box_id = s.time_box_id AND hv.code = s.code) ';
        } else {
            $with .= ' WHERE EXISTS(SELECT 1 FROM ts_samples s WHERE hv.paneler_id = s.paneler_id AND hv.time_box_id = s.time_box_id AND hv.code = s.code) ';
        }
        $with .= '    ) viewers  ';
        $with .= '      ON dc.datetime = viewers.datetime  ';
        $with .= '      AND dc.channel_id = viewers.channel_id ';
        $with .= '    LEFT JOIN rt_numbers rtn  ';
        $with .= '      ON viewers.time_box_id = rtn.time_box_id  ';
        $with .= '    LEFT JOIN ts_numbers tsn  ';
        $with .= '      ON viewers.time_box_id = tsn.time_box_id  ';
        $with .= '   WHERE ';
        $with .= '     dc.datetime <= :latestDateTime ';
        $with .= '   GROUP BY ';
        $with .= '     dc.time_box_id ';
        $with .= '     , hhmm ';
        $with .= '     , dow ';
        $with .= '     , rtn.number ';
        $with .= '     , tsn.number ';
        // 地デジ2を選んだ場合
        if ($channelType == 'dt2') {
            $with .= ' , channel_group ';
        } else {
            // 地デジ2以外
            $with .= ' ,dc.channel_id ';
        }
        $with .= ' ) ';
        $select = '';
        $select .= 'SELECT ';
        $select .= ' hhmm ';
        $select .= ' ,dow ';
        // 地デジ2を選んだ場合
        if ($channelType == 'dt2') {
            $select .= ' , channel_group channel_id ';
        } else {
            // 地デジ2以外
            $select .= ' ,channel_id ';
        }

        if ($isRt) {
            $select .= '  , AVG(COALESCE(viewing_rate, 0)) AS viewing_rate  ';
        }

        if ($isTs) {
            $select .= '  , AVG(COALESCE(ts_viewing_rate, 0)) AS viewing_rate  ';
        }

        if ($isTotal) {
            $select .= '  , AVG(COALESCE(total_viewing_rate, 0)) AS viewing_rate  ';
        }

        if ($isGross) {
            $select .= '  , AVG(COALESCE(gross_viewing_rate, 0)) AS viewing_rate  ';
        }

        if ($isRtTotal) {
            $select .= '  , AVG(COALESCE(rt_total_viewing_rate, 0)) AS viewing_rate  ';
        }
        $from = '';
        $from .= 'FROM ';
        $from .= ' hourly_viewers_grouped ';
        $group = '';
        $group .= 'GROUP BY ';
        $group .= '  hhmm ';
        $group .= '  ,dow ';
        // 地デジ2を選んだ場合
        if ($channelType == 'dt2') {
            $group .= ' , channel_group ';
        } else {
            // 地デジ2以外
            $group .= ' ,channel_id ';
        }
        $order = '';
        $order .= 'ORDER BY ';
        // 地デジ2を選んだ場合
        if ($channelType == 'dt2') {
            $order .= ' channel_group ';
        } else {
            // 地デジ2以外
            $order .= ' channel_id ';
        }
        $order .= '  ,dow ';
        $order .= '  ,hhmm ';

        $query = $with . $select . $from . $group . $order;

        $result = $this->select($query, $bindings);

        return $result;
    }

    /**
     * @param Carbon $startDateTime
     * @param Carbon $endDateTime
     * @param string $channelType
     * @param array $channelIds
     * @param string $division
     * @param array $code
     * @param string $dataDivision
     * @param array $conditionCross
     * @param bool $isOriginal
     * @param int $regionId
     * @param array $dataType
     * @param array $dataTypeFlags
     * @param string $intervalHourly
     * @param string $intervalMinutes
     * @return array
     */
    public function getConditionCrossShareData(Carbon $startDateTime, Carbon $endDateTime, string $channelType, array $channelIds, string $division, ?array $code, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, array $dataType, array $dataTypeFlags, string $intervalHourly, string $intervalMinutes): array
    {
        $bindings = [];

        $divisionKey = $division . '_';

        // 日付処理
        $bindings[':startDate'] = $startDateTime;
        $st = new Carbon($startDateTime);
        $et = new Carbon($endDateTime);
        $bindings[':startTimestamp'] = $st->subDay()->toDateTimeString();
        $bindings[':endTimestamp'] = $et->addDay()->toDateTimeString();

        $bindChannelIds = $this->createArrayBindParam('channelId', [
            'channelId' => $channelIds,
        ], $bindings);

        $bindings[':regionId'] = $regionId;

        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = array_values($dataTypeFlags);

        $latestDateTime = $this->getPerHourlyLatestDateTime($regionId, $intervalHourly, $intervalMinutes);
        $bindings[':latestDateTime'] = $latestDateTime;

        $with = '';
        $with .= ' WITH channels AS ( ';
        $with .= '   SELECT ';
        $with .= '     id ';
        $with .= '   FROM ';
        $with .= '     channels ';
        $with .= '   WHERE ';
        $with .= '     id IN (' . implode(',', $bindChannelIds) . ') ';
        $with .= ' ), day_nums AS ( ';
        $tmpArr = [];

        foreach (range(0, $endDateTime->diffInDays($startDateTime)) as $val) {
            $tmpArr[] = "   SELECT {$val} day_num  ";
        }
        $with .= implode(' UNION ALL ', $tmpArr);
        $with .= ' ), hour_nums AS ( ';
        $tmpArr = [];

        foreach (range(5, 28) as $val) {
            $tmpArr[] = "   SELECT {$val} hour_num ";
        }
        $with .= implode(' UNION ALL ', $tmpArr);
        $with .= ' ), datetime_channels AS ( ';
        $with .= ' SELECT  ';
        $with .= "   start_date.datetime + (day_nums.day_num||' days')::interval  + (hour_nums.hour_num||' hours')::interval as datetime, ";
        $with .= '   channels.id channel_id ';
        $with .= ' FROM  ';
        $with .= "   (SELECT TO_DATE(:startDate, 'YYYY-MM-DD') as datetime ) start_date ";
        $with .= ' CROSS JOIN ';
        $with .= '   channels ';
        $with .= ' CROSS JOIN ';
        $with .= '   day_nums ';
        $with .= ' CROSS JOIN ';
        $with .= '   hour_nums ';
        $with .= ' ), datetime_time_box AS ( ';
        $with .= ' SELECT ';
        $with .= '   datetime as datetime, ';
        $with .= '   channel_id, ';
        $with .= '   id time_box_id ';
        $with .= ' FROM ';
        $with .= '   datetime_channels dc ';
        $with .= ' INNER JOIN ';
        $with .= '   time_boxes tb ';
        $with .= ' ON ';
        $with .= '   dc.datetime >= tb.started_at AND ';
        $with .= '   dc.datetime < tb.ended_at AND ';
        $with .= '   tb.region_id = :regionId ';
        $with .= ') ';

        $with .= ', ts_samples AS (  ';
        $with .= '  SELECT ';
        $with .= '    tbp.paneler_id ';
        $with .= '    , tbp.time_box_id ';
        $with .= '    , codes.code ';
        $with .= '	  , t.started_at ';
        $with .= '	  , t.ended_at  ';
        $with .= '  FROM ';
        $with .= '    ts_time_box_panelers tbp ';
        $with .= '	INNER JOIN time_boxes t ';
        $with .= '	ON tbp.time_box_id = t.id  ';
        $with .= '	    AND t.region_id = :regionId  ';
        $with .= '      AND t.started_at <= :endTimestamp  ';
        $with .= '      AND t.ended_at > :startTimestamp  ';
        $with .= '  CROSS JOIN ';

        if ($isOriginal) {
            $tmpArr = [];
            // 拡張、オリジナル属性
            foreach ($code as $cd) {
                $key = ':union_' . $divisionKey . $cd;
                $bindings[$key] = $cd;
                $tmpSql = ' ( ';
                $tmpSql .= ' SELECT ' . $key . '::varchar(255) as code ';
                $tmpSql .= ' ) ';
                array_push($tmpArr, $tmpSql);
            }
            $with .= ' ( ' . implode(' UNION ', $tmpArr) . ' ) AS codes ';
        } else {
            // かけ合わせ条件
            $bindings[':condition_cross_code'] = 'condition_cross';
            $with .= ' ( ';
            $with .= ' (SELECT :condition_cross_code ::varchar(255) as code) ';
            $with .= ' ) codes ';
        }
        $with .= ' WHERE   ';
        $with .= '   ( ';

        if ($isOriginal) {
            $with .= $this->createCrossJoinWhereClause($division, $code, $bindings);
        } else {
            $with .= " codes.code = 'condition_cross' ";
            $with .= $this->createConditionCrossSql($conditionCross, $bindings);
        }
        $with .= ' ) ';

        $with .= ')  ';
        $with .= ', samples AS (  ';
        $with .= '  SELECT ';
        $with .= '    tbp.paneler_id ';
        $with .= '    , tbp.time_box_id ';
        $with .= '    , codes.code ';
        $with .= '	  , t.started_at ';
        $with .= '	  , t.ended_at  ';
        $with .= '  FROM ';
        $with .= '    time_box_panelers tbp ';
        $with .= '	INNER JOIN time_boxes t ';
        $with .= '	ON tbp.time_box_id = t.id  ';
        $with .= '	    AND t.region_id = :regionId  ';
        $with .= '      AND t.started_at <= :endTimestamp  ';
        $with .= '      AND t.ended_at > :startTimestamp  ';
        $with .= '  CROSS JOIN ';

        if ($isOriginal) {
            $tmpArr = [];
            // 拡張、オリジナル属性
            foreach ($code as $cd) {
                $key = ':union_' . $divisionKey . $cd;
                $bindings[$key] = $cd;
                $tmpSql = ' ( ';
                $tmpSql .= ' SELECT ' . $key . '::varchar(255) as code ';
                $tmpSql .= ' ) ';
                array_push($tmpArr, $tmpSql);
            }
            $with .= ' ( ' . implode(' UNION ', $tmpArr) . ' ) AS codes ';
        } else {
            // かけ合わせ条件
            $bindings[':condition_cross_code'] = 'condition_cross';
            $with .= ' ( ';
            $with .= ' (SELECT :condition_cross_code ::varchar(255) as code) ';
            $with .= ' ) codes ';
        }

        $with .= ' WHERE   ';
        $with .= '   ( ';

        if ($isOriginal) {
            $with .= $this->createCrossJoinWhereClause($division, $code, $bindings);
        } else {
            $with .= " codes.code = 'condition_cross' ";
            $with .= $this->createConditionCrossSql($conditionCross, $bindings);
        }
        $with .= ' ) ';

        $with .= ')  ';
        $with .= ', rt_numbers AS (  ';
        $with .= '  SELECT ';
        $with .= '    COUNT(paneler_id) number ';
        $with .= '    , SUM(COUNT(paneler_id)) OVER (PARTITION BY time_box_id) all_number ';
        $with .= '    , code ';
        $with .= '    , time_box_id  ';
        $with .= '  FROM ';
        $with .= '    samples  ';
        $with .= '  GROUP BY ';
        $with .= '    code ';
        $with .= '    , time_box_id ';
        $with .= ')  ';
        $with .= ', ts_numbers AS (  ';
        $with .= '  SELECT ';
        $with .= '    COUNT(paneler_id) number ';
        $with .= '    , SUM(COUNT(paneler_id)) OVER (PARTITION BY time_box_id) all_number ';
        $with .= '    , code ';
        $with .= '    , time_box_id  ';
        $with .= '  FROM ';
        $with .= '    ts_samples  ';
        $with .= '  GROUP BY ';
        $with .= '    code ';
        $with .= '    , time_box_id ';
        $with .= ')  ';
        $with .= ', union_viewers AS (  ';
        $with .= '  SELECT ';
        $with .= '      "datetime" ';
        $with .= '    , channel_id ';
        $with .= '    , paneler_id ';
        $with .= '	  , time_box_id ';
        $with .= '    , code ';
        $with .= '    , SUM(viewing_seconds) viewing_seconds ';
        $with .= '    , SUM(ts_viewing_seconds) ts_viewing_seconds ';
        $with .= '    , SUM(total_viewing_seconds) total_viewing_seconds ';
        $with .= '    , COALESCE(MAX(gross_viewing_seconds), MAX(viewing_seconds)) gross_viewing_seconds ';
        $with .= '    , SUM(rt_total_viewing_seconds) rt_total_viewing_seconds  ';
        $with .= '  FROM ';
        $with .= '    (  ';
        $with .= '      SELECT ';
        $with .= '        hv.datetime ';
        $with .= '        , hv.channel_id ';
        $with .= '        , hv.paneler_id ';
        $with .= '		  , s.time_box_id ';
        $with .= '        , s.code ';
        $with .= '        , hv.viewing_seconds ';
        $with .= '        , 0 ts_viewing_seconds ';
        $with .= '        , 0 total_viewing_seconds ';
        $with .= '        , NULL gross_viewing_seconds ';
        $with .= '        , viewing_seconds rt_total_viewing_seconds  ';
        $with .= '      FROM ';
        $with .= '        hourly_viewers hv  ';
        $with .= '        INNER JOIN samples s  ';
        $with .= '          ON hv.paneler_id = s.paneler_id  ';
        $with .= '          AND hv.datetime >= s.started_at AND hv.datetime < s.ended_at  ';
        $with .= '          AND hv.datetime >= :startTimestamp AND hv.datetime <= :endTimestamp  ';
        $with .= '      UNION ALL  ';
        $with .= '      SELECT ';
        $with .= '        hv.datetime ';
        $with .= '        , hv.channel_id ';
        $with .= '        , hv.paneler_id ';
        $with .= '		  , s.time_box_id ';
        $with .= '        , s.code ';
        $with .= '        , 0 viewing_seconds ';
        $with .= '        , viewing_seconds ts_viewing_seconds ';
        $with .= '        , total_viewing_seconds ';
        $with .= '        , gross_viewing_seconds ';
        $with .= '        , total_viewing_seconds rt_total_viewing_seconds  ';
        $with .= '      FROM ';
        $with .= '        ts_hourly_viewers hv  ';
        $with .= '        INNER JOIN ts_samples s  ';
        $with .= '          ON hv.paneler_id = s.paneler_id  ';
        $with .= '          AND hv.datetime >= s.started_at AND hv.datetime < s.ended_at  ';
        $with .= '          AND hv.datetime >= :startTimestamp AND hv.datetime <= :endTimestamp  ';
        $with .= '    ) unioned  ';
        $with .= '  GROUP BY ';
        $with .= '    "datetime" ';
        $with .= '    , channel_id ';
        $with .= '	, time_box_id ';
        $with .= '    , code ';
        $with .= '    , paneler_id ';
        $with .= ')  ';

        $with .= ' , hourly_viewers_grouped AS ( ';
        $with .= ' SELECT ';
        $with .= "     TO_CHAR(dc.datetime, 'HH24') hhmm ";
        $with .= "     , EXTRACT(DOW FROM dc.datetime - interval ' 5 hours') dow ";
        $with .= '     , dc.channel_id ';
        $with .= '     , dc.time_box_id ';
        $with .= '     , SUM(viewers.viewing_seconds) ::numeric viewing_seconds  ';
        $with .= '     , SUM(viewers.ts_viewing_seconds) ::numeric ts_viewing_seconds  ';
        $with .= '     , SUM(viewers.total_viewing_seconds) ::numeric total_viewing_seconds  ';
        $with .= '     , SUM(viewers.gross_viewing_seconds) ::numeric gross_viewing_seconds  ';
        $with .= '     , SUM(viewers.rt_total_viewing_seconds) ::numeric rt_total_viewing_seconds  ';
        $with .= '   FROM ';
        $with .= '     datetime_time_box dc ';
        $with .= '     LEFT JOIN (  ';
        $with .= '       SELECT ';
        $with .= '         hv.datetime ';
        $with .= '         , hv.channel_id ';
        $with .= '         , hv.viewing_seconds ';
        $with .= '         , hv.ts_viewing_seconds ';
        $with .= '         , hv.total_viewing_seconds ';
        $with .= '         , hv.gross_viewing_seconds ';
        $with .= '         , hv.rt_total_viewing_seconds ';
        $with .= '         , hv.time_box_id ';
        $with .= '         , hv.paneler_id ';
        $with .= '       FROM ';
        $with .= '         union_viewers hv  ';

        if ($isRt) {
            $with .= ' WHERE EXISTS(SELECT 1 FROM samples s WHERE hv.paneler_id = s.paneler_id AND hv.time_box_id = s.time_box_id AND hv.code = s.code) ';
        } else {
            $with .= ' WHERE EXISTS(SELECT 1 FROM ts_samples s WHERE hv.paneler_id = s.paneler_id AND hv.time_box_id = s.time_box_id AND hv.code = s.code) ';
        }
        $with .= '     ) viewers  ';
        $with .= '       ON dc.datetime = viewers.datetime  ';
        $with .= '       AND dc.channel_id = viewers.channel_id ';
        $with .= '     LEFT JOIN rt_numbers rtn  ';
        $with .= '       ON viewers.time_box_id = rtn.time_box_id  ';
        $with .= '     LEFT JOIN ts_numbers tsn  ';
        $with .= '       ON viewers.time_box_id = tsn.time_box_id  ';
        $with .= '   WHERE dc.datetime <= :latestDateTime ';
        $with .= '   GROUP BY ';
        $with .= '     dc.time_box_id ';
        $with .= '     , hhmm ';
        $with .= '     , dow ';
        $with .= '     , rtn.number ';
        $with .= ' 	   , tsn.number ';
        $with .= '     , dc.channel_id ';
        $with .= ' ), share AS ( ';
        $with .= ' SELECT ';
        $with .= '     hhmm ';
        $with .= '     , dow ';
        $with .= '     , channel_id ';
        $with .= '     , viewing_seconds AS viewing_seconds ';
        $with .= '     , SUM(viewing_seconds) OVER (PARTITION BY time_box_id, hhmm, dow) ::real AS total_time  ';
        $with .= '     , ts_viewing_seconds AS ts_viewing_seconds  ';
        $with .= '     , SUM(ts_viewing_seconds) OVER (PARTITION BY time_box_id, hhmm, dow) ::real AS ts_total_time  ';
        $with .= '     , total_viewing_seconds AS total_viewing_seconds ';
        $with .= '     , SUM(total_viewing_seconds) OVER (PARTITION BY time_box_id, hhmm, dow) ::real AS total_total_time  ';
        $with .= '     , gross_viewing_seconds AS gross_viewing_seconds ';
        $with .= '     , SUM(gross_viewing_seconds) OVER (PARTITION BY time_box_id, hhmm, dow) ::real AS gross_total_time  ';
        $with .= '     , rt_total_viewing_seconds AS rt_total_viewing_seconds ';
        $with .= '     , SUM(rt_total_viewing_seconds) OVER (PARTITION BY time_box_id, hhmm, dow) ::real AS rt_total_total_time  ';
        $with .= ' FROM ';
        $with .= '   hourly_viewers_grouped hv ';
        $with .= ' ) ';

        $select = '';
        $select .= 'SELECT ';
        $select .= ' hhmm ';
        $select .= ' ,dow ';
        $select .= ' ,channel_id ';

        if ($isRt) {
            $select .= '   , AVG(COALESCE(viewing_seconds / total_time * 100, 0)) AS share  ';
        }

        if ($isTs) {
            $select .= '   , AVG(COALESCE(ts_viewing_seconds / ts_total_time * 100, 0)) AS share  ';
        }

        if ($isTotal) {
            $select .= '   , AVG(COALESCE(total_viewing_seconds / total_total_time * 100, 0)) AS share  ';
        }

        if ($isGross) {
            $select .= '   , AVG(COALESCE(gross_viewing_seconds / gross_total_time * 100, 0)) AS share  ';
        }

        if ($isRtTotal) {
            $select .= '   , AVG(COALESCE(rt_total_viewing_seconds / rt_total_total_time * 100, 0)) AS share  ';
        }

        $from = '';
        $from .= 'FROM ';
        $from .= ' share ';
        $group = '';
        $group .= 'GROUP BY ';
        $group .= '  hhmm ';
        $group .= '  ,dow ';
        $group .= '  ,channel_id ';
        $order = '';
        $order .= 'ORDER BY ';
        $order .= '   channel_id ';
        $order .= '  ,dow ';
        $order .= '  ,hhmm ';

        $query = $with . $select . $from . $group . $order;
        $result = $this->select($query, $bindings);

        return $result;
    }

    /**
     * @param Carbon $startDateTime
     * @param Carbon $endDateTime
     * @param string $channelType
     * @param array $channelIds
     * @param string $division
     * @param array $code
     * @param string $dataDivision
     * @param array $conditionCross
     * @param bool $isOriginal
     * @param int $regionId
     * @param array $dataType
     * @param array $dataTypeFlags
     * @param string $intervalHourly
     * @param string $intervalMinutes
     * @return array
     */
    public function getConditionCrossTargetData(Carbon $startDateTime, Carbon $endDateTime, string $channelType, array $channelIds, string $division, ?array $code, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, array $dataType, array $dataTypeFlags, string $intervalHourly, string $intervalMinutes): array
    {
        $bindings = [];

        $divisionKey = $division . '_';

        if ($dataDivision === 'target_content_personal') {
            $bindings[':division'] = 'personal';
            $bindings[':code'] = '1';
        } elseif ($dataDivision === 'target_content_household') {
            $bindings[':division'] = 'household';
            $bindings[':code'] = '1';
        }

        // 日付処理
        $bindings[':startDate'] = $startDateTime;
        $st = new Carbon($startDateTime);
        $et = new Carbon($endDateTime);
        $bindings[':startTimestamp'] = $st->subDay()->toDateTimeString();
        $bindings[':endTimestamp'] = $et->addDay()->toDateTimeString();

        $bindChannelIds = $this->createArrayBindParam('channelId', [
            'channelId' => $channelIds,
        ], $bindings);

        $bindings[':regionId'] = $regionId;

        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = array_values($dataTypeFlags);

        $latestDateTime = $this->getPerHourlyLatestDateTime($regionId, $intervalHourly, $intervalMinutes);
        $bindings[':latestDateTime'] = $latestDateTime;

        $with = '';
        $with .= ' WITH channels AS ( ';
        $with .= '   SELECT ';
        $with .= '     id ';
        $with .= '   FROM ';
        $with .= '     channels ';
        $with .= '   WHERE ';
        $with .= '     id IN (' . implode(',', $bindChannelIds) . ') ';
        $with .= ' ), day_nums AS ( ';
        $tmpArr = [];

        foreach (range(0, $endDateTime->diffInDays($startDateTime)) as $val) {
            $tmpArr[] = "   SELECT {$val} day_num  ";
        }
        $with .= implode(' UNION ALL ', $tmpArr);
        $with .= ' ), hour_nums AS ( ';
        $tmpArr = [];

        foreach (range(5, 28) as $val) {
            $tmpArr[] = "   SELECT {$val} hour_num ";
        }
        $with .= implode(' UNION ALL ', $tmpArr);
        $with .= ' ), datetime_channels AS ( ';
        $with .= ' SELECT  ';
        $with .= "   start_date.datetime + (day_nums.day_num||' days')::interval  + (hour_nums.hour_num||' hours')::interval as datetime, ";
        $with .= '   channels.id channel_id ';
        $with .= ' FROM  ';
        $with .= "   (SELECT TO_DATE(:startDate, 'YYYY-MM-DD') as datetime ) start_date ";
        $with .= ' CROSS JOIN ';
        $with .= '   channels ';
        $with .= ' CROSS JOIN ';
        $with .= '   day_nums ';
        $with .= ' CROSS JOIN ';
        $with .= '   hour_nums ';
        $with .= ' ), datetime_time_box AS ( ';
        $with .= ' SELECT ';
        $with .= '   datetime as datetime, ';
        $with .= '   channel_id, ';
        $with .= '   id time_box_id ';
        $with .= ' FROM ';
        $with .= '   datetime_channels dc ';
        $with .= ' INNER JOIN ';
        $with .= '   time_boxes tb ';
        $with .= ' ON ';
        $with .= '   dc.datetime >= tb.started_at AND ';
        $with .= '   dc.datetime < tb.ended_at AND ';
        $with .= '   tb.region_id = :regionId ';
        $with .= ') ';

        $with .= ', ts_samples AS (  ';
        $with .= '  SELECT ';
        $with .= '    tbp.paneler_id ';
        $with .= '    , tbp.time_box_id ';
        $with .= '    , codes.code ';
        $with .= '	  , t.started_at ';
        $with .= '	  , t.ended_at  ';
        $with .= '  FROM ';
        $with .= '    ts_time_box_panelers tbp ';
        $with .= '	INNER JOIN time_boxes t ';
        $with .= '	ON tbp.time_box_id = t.id  ';
        $with .= '	    AND t.region_id = :regionId  ';
        $with .= '      AND t.started_at <= :endTimestamp  ';
        $with .= '      AND t.ended_at > :startTimestamp  ';
        $with .= '  CROSS JOIN ';

        if ($isOriginal) {
            $tmpArr = [];
            // 拡張、オリジナル属性
            foreach ($code as $cd) {
                $key = ':union_' . $divisionKey . $cd;
                $bindings[$key] = $cd;
                $tmpSql = ' ( ';
                $tmpSql .= ' SELECT ' . $key . '::varchar(255) as code ';
                $tmpSql .= ' ) ';
                array_push($tmpArr, $tmpSql);
            }
            $with .= ' ( ' . implode(' UNION ', $tmpArr) . ' ) AS codes ';
        } else {
            // かけ合わせ条件
            $bindings[':condition_cross_code'] = 'condition_cross';
            $with .= ' ( ';
            $with .= ' (SELECT :condition_cross_code ::varchar(255) as code) ';
            $with .= ' ) codes ';
        }
        $with .= ' WHERE   ';
        $with .= '   ( ';

        if ($isOriginal) {
            $with .= $this->createCrossJoinWhereClause($division, $code, $bindings);
        } else {
            $with .= " codes.code = 'condition_cross' ";
            $with .= $this->createConditionCrossSql($conditionCross, $bindings);
        }
        $with .= ' ) ';

        $with .= ')  ';
        $with .= ', samples AS (  ';
        $with .= '  SELECT ';
        $with .= '    tbp.paneler_id ';
        $with .= '    , tbp.time_box_id ';
        $with .= '    , codes.code ';
        $with .= '	  , t.started_at ';
        $with .= '	  , t.ended_at  ';
        $with .= '  FROM ';
        $with .= '    time_box_panelers tbp ';
        $with .= '	INNER JOIN time_boxes t ';
        $with .= '	ON tbp.time_box_id = t.id  ';
        $with .= '	    AND t.region_id = :regionId  ';
        $with .= '      AND t.started_at <= :endTimestamp  ';
        $with .= '      AND t.ended_at > :startTimestamp  ';

        $with .= '  CROSS JOIN ';

        if ($isOriginal) {
            $tmpArr = [];
            // 拡張、オリジナル属性
            foreach ($code as $cd) {
                $key = ':union_' . $divisionKey . $cd;
                $bindings[$key] = $cd;
                $tmpSql = ' ( ';
                $tmpSql .= ' SELECT ' . $key . '::varchar(255) as code ';
                $tmpSql .= ' ) ';
                array_push($tmpArr, $tmpSql);
            }
            $with .= ' ( ' . implode(' UNION ', $tmpArr) . ' ) AS codes ';
        } else {
            // かけ合わせ条件
            $bindings[':condition_cross_code'] = 'condition_cross';
            $with .= ' ( ';
            $with .= ' (SELECT :condition_cross_code ::varchar(255) as code) ';
            $with .= ' ) codes ';
        }
        $with .= ' WHERE   ';
        $with .= '   ( ';

        if ($isOriginal) {
            $with .= $this->createCrossJoinWhereClause($division, $code, $bindings);
        } else {
            $with .= " codes.code = 'condition_cross' ";
            $with .= $this->createConditionCrossSql($conditionCross, $bindings);
        }
        $with .= ' ) ';

        $with .= ')  ';
        $with .= ', rt_numbers AS (  ';
        $with .= '  SELECT ';
        $with .= '    COUNT(paneler_id) number ';
        $with .= '    , SUM(COUNT(paneler_id)) OVER (PARTITION BY time_box_id) all_number ';
        $with .= '    , code ';
        $with .= '    , time_box_id  ';
        $with .= '  FROM ';
        $with .= '    samples  ';
        $with .= '  GROUP BY ';
        $with .= '    code ';
        $with .= '    , time_box_id ';
        $with .= ')  ';
        $with .= ', ts_numbers AS (  ';
        $with .= '  SELECT ';
        $with .= '    COUNT(paneler_id) number ';
        $with .= '    , SUM(COUNT(paneler_id)) OVER (PARTITION BY time_box_id) all_number ';
        $with .= '    , code ';
        $with .= '    , time_box_id  ';
        $with .= '  FROM ';
        $with .= '    ts_samples  ';
        $with .= '  GROUP BY ';
        $with .= '    code ';
        $with .= '    , time_box_id ';
        $with .= ')  ';
        $with .= ', union_viewers AS (  ';
        $with .= '  SELECT ';
        $with .= '      "datetime" ';
        $with .= '    , channel_id ';
        $with .= '    , paneler_id ';
        $with .= '	  , time_box_id ';
        $with .= '    , code ';
        $with .= '    , SUM(viewing_seconds) viewing_seconds ';
        $with .= '    , SUM(ts_viewing_seconds) ts_viewing_seconds ';
        $with .= '    , SUM(total_viewing_seconds) total_viewing_seconds ';
        $with .= '    , COALESCE(MAX(gross_viewing_seconds), MAX(viewing_seconds)) gross_viewing_seconds ';
        $with .= '    , SUM(rt_total_viewing_seconds) rt_total_viewing_seconds  ';
        $with .= '  FROM ';
        $with .= '    (  ';
        $with .= '      SELECT ';
        $with .= '        hv.datetime ';
        $with .= '        , hv.channel_id ';
        $with .= '        , hv.paneler_id ';
        $with .= '		  , s.time_box_id ';
        $with .= '        , s.code ';
        $with .= '        , hv.viewing_seconds ';
        $with .= '        , 0 ts_viewing_seconds ';
        $with .= '        , 0 total_viewing_seconds ';
        $with .= '        , NULL gross_viewing_seconds ';
        $with .= '        , viewing_seconds rt_total_viewing_seconds  ';
        $with .= '      FROM ';
        $with .= '        hourly_viewers hv  ';
        $with .= '        INNER JOIN samples s  ';
        $with .= '          ON hv.paneler_id = s.paneler_id  ';
        $with .= '          AND hv.datetime >= s.started_at AND hv.datetime < s.ended_at  ';
        $with .= '          AND hv.datetime >= :startTimestamp AND hv.datetime <= :endTimestamp  ';
        $with .= '      UNION ALL  ';
        $with .= '      SELECT ';
        $with .= '        hv.datetime ';
        $with .= '        , hv.channel_id ';
        $with .= '        , hv.paneler_id ';
        $with .= '		  , s.time_box_id ';
        $with .= '        , s.code ';
        $with .= '        , 0 viewing_seconds ';
        $with .= '        , viewing_seconds ts_viewing_seconds ';
        $with .= '        , total_viewing_seconds ';
        $with .= '        , gross_viewing_seconds ';
        $with .= '        , total_viewing_seconds rt_total_viewing_seconds  ';
        $with .= '      FROM ';
        $with .= '        ts_hourly_viewers hv  ';
        $with .= '        INNER JOIN ts_samples s  ';
        $with .= '          ON hv.paneler_id = s.paneler_id  ';
        $with .= '          AND hv.datetime >= s.started_at AND hv.datetime < s.ended_at  ';
        $with .= '          AND hv.datetime >= :startTimestamp AND hv.datetime <= :endTimestamp  ';
        $with .= '    ) unioned  ';
        $with .= '  GROUP BY ';
        $with .= '    "datetime" ';
        $with .= '    , channel_id ';
        $with .= '	, time_box_id ';
        $with .= '    , code ';
        $with .= '    , paneler_id ';
        $with .= ')  ';
        $with .= ' , joined_hourly_reports AS (  ';
        $with .= '   SELECT ';
        $with .= "     to_char(dc.datetime, 'HH24') hhmm ";
        $with .= "     , EXTRACT(DOW FROM (dc.datetime + interval '- 5 hours')) dow ";
        $with .= '     , dc.channel_id ';
        $with .= '     , hr.viewing_rate ';
        $with .= '     , dc.time_box_id ';
        $with .= '     , thr.viewing_rate ts_viewing_rate ';
        $with .= '     , thr.total_viewing_rate ';
        $with .= '     , COALESCE(thr.gross_viewing_rate, hr.ts_samples_viewing_rate) gross_viewing_rate ';
        $with .= '     , COALESCE(thr.rt_total_viewing_rate, hr.ts_samples_viewing_rate) rt_total_viewing_rate ';
        $with .= '   FROM ';
        $with .= '     datetime_time_box dc  ';
        $with .= '     LEFT JOIN hourly_reports hr  ';
        $with .= '       ON dc.datetime = hr.datetime  ';
        $with .= '       AND dc.channel_id = hr.channel_id  ';
        $with .= '       AND dc.time_box_id = hr.time_box_id  ';
        $with .= '       AND hr.division = :division  ';
        $with .= '       AND hr.code = :code  ';
        $with .= '       AND hr.datetime BETWEEN :startTimestamp AND :endTimestamp ';
        $with .= '       AND hr.channel_id IN (' . implode(',', $bindChannelIds) . ') ';
        $with .= '     LEFT JOIN ts_hourly_reports thr  ';
        $with .= '       ON dc.datetime = thr.datetime  ';
        $with .= '       AND dc.channel_id = thr.channel_id  ';
        $with .= '       AND dc.time_box_id = thr.time_box_id  ';
        $with .= '       AND thr.division = :division  ';
        $with .= '       AND thr.code = :code  ';
        $with .= '       AND thr.datetime BETWEEN :startTimestamp AND :endTimestamp ';
        $with .= '       AND thr.channel_id IN (' . implode(',', $bindChannelIds) . ') ';
        $with .= ') ';

        $with .= ' , hourly_viewers_grouped AS (  ';
        $with .= '   SELECT ';
        $with .= "     TO_CHAR(dc.datetime, 'HH24') hhmm ";
        $with .= "     , EXTRACT(DOW FROM dc.datetime - interval ' 5 hours') dow ";
        $with .= '     , dc.channel_id ';
        $with .= '     , dc.time_box_id ';
        $with .= '     , SUM(viewers.viewing_seconds) ::numeric / (rtn.number * 60 * 60) * 100 viewing_rate  ';
        $with .= '     , SUM(viewers.ts_viewing_seconds) ::numeric / (tsn.number * 60 * 60) * 100 ts_viewing_rate  ';
        $with .= '     , SUM(viewers.total_viewing_seconds) ::numeric / (tsn.number * 60 * 60) * 100 total_viewing_rate  ';
        $with .= '     , SUM(viewers.gross_viewing_seconds) ::numeric / (tsn.number * 60 * 60) * 100 gross_viewing_rate  ';
        $with .= '     , SUM(viewers.rt_total_viewing_seconds) ::numeric / (tsn.number * 60 * 60) * 100 rt_total_viewing_rate  ';
        $with .= '   FROM ';
        $with .= '     datetime_time_box dc  ';
        $with .= '     LEFT JOIN (  ';
        $with .= '       SELECT ';
        $with .= '         hv.datetime ';
        $with .= '         , hv.channel_id ';
        $with .= '         , hv.viewing_seconds ';
        $with .= '         , hv.ts_viewing_seconds ';
        $with .= '         , hv.total_viewing_seconds ';
        $with .= '         , hv.gross_viewing_seconds ';
        $with .= '         , hv.rt_total_viewing_seconds ';
        $with .= '         , hv.time_box_id ';
        $with .= '         , hv.paneler_id ';
        $with .= '       FROM ';
        $with .= '         union_viewers hv  ';

        if ($isRt) {
            $with .= ' WHERE EXISTS(SELECT 1 FROM samples s WHERE hv.paneler_id = s.paneler_id AND hv.time_box_id = s.time_box_id AND hv.code = s.code) ';
        } else {
            $with .= ' WHERE EXISTS(SELECT 1 FROM ts_samples s WHERE hv.paneler_id = s.paneler_id AND hv.time_box_id = s.time_box_id AND hv.code = s.code) ';
        }
        $with .= '     ) viewers  ';
        $with .= '       ON dc.datetime = viewers.datetime  ';
        $with .= '       AND dc.channel_id = viewers.channel_id ';
        $with .= '     LEFT JOIN rt_numbers rtn  ';
        $with .= '       ON viewers.time_box_id = rtn.time_box_id  ';
        $with .= '     LEFT JOIN ts_numbers tsn  ';
        $with .= '       ON viewers.time_box_id = tsn.time_box_id  ';
        $with .= '   WHERE dc.datetime <= :latestDateTime ';
        $with .= '   GROUP BY ';
        $with .= '     dc.time_box_id ';
        $with .= '     , hhmm ';
        $with .= '     , dow ';
        $with .= ' 	   , rtn.number ';
        $with .= ' 	   , tsn.number ';
        $with .= '     , dc.channel_id ';
        $with .= ' ) ';

        $with .= ', target_content AS ( ';
        $with .= '   SELECT ';
        $with .= '     CASE WHEN hr.viewing_rate = 0 THEN 0 ELSE hv.viewing_rate / hr.viewing_rate * 100 END as target_viewing_rate ';
        $with .= '     , CASE WHEN hr.ts_viewing_rate = 0 THEN 0 ELSE hv.ts_viewing_rate / hr.ts_viewing_rate * 100 END as ts_target_viewing_rate ';
        //         $with .= " --    , hv.total_viewing_rate / hr.total_viewing_rate * 100 as total_target_viewing_rate ";
        $with .= '     , CASE WHEN hr.gross_viewing_rate = 0 THEN 0 ELSE hv.gross_viewing_rate / hr.gross_viewing_rate * 100 END as gross_target_viewing_rate ';
        $with .= '     , CASE WHEN hr.rt_total_viewing_rate = 0 THEN 0 ELSE hv.rt_total_viewing_rate / hr.rt_total_viewing_rate * 100 END as rt_total_target_viewing_rate ';
        $with .= '     , hr.hhmm ';
        $with .= '     , hr.dow ';
        $with .= '     , hr.channel_id  ';
        $with .= '   FROM ';
        $with .= '     joined_hourly_reports hr  ';
        $with .= '     INNER JOIN hourly_viewers_grouped hv  ';
        $with .= '       ON hr.hhmm = hv.hhmm  ';
        $with .= '       AND hr.dow = hv.dow  ';
        $with .= '       AND hr.channel_id = hv.channel_id  ';
        $with .= '       AND hr.time_box_id = hv.time_box_id ';

        if ($isRt) {
            $with .= ' WHERE hr.viewing_rate IS NOT NULL ';
        }

        if ($isTs) {
            $with .= ' WHERE hr.ts_viewing_rate IS NOT NULL ';
        }

        if ($isTotal) {
            $with .= ' WHERE hr.total_viewing_rate IS NOT NULL ';
        }

        if ($isGross) {
            $with .= ' WHERE hr.gross_viewing_rate IS NOT NULL ';
        }

        if ($isRtTotal) {
            $with .= ' WHERE hr.rt_total_viewing_rate IS NOT NULL ';
        }
        $with .= ' ) ';

        $select = '';
        $select .= 'SELECT ';
        $select .= '  hhmm ';
        $select .= ' ,dow ';
        $select .= ' ,hv.channel_id ';

        if ($isRt) {
            $select .= '   ,AVG(COALESCE(target_viewing_rate,0)) AS target_viewing_rate ';
        }

        if ($isTs) {
            $select .= '   ,AVG(COALESCE(ts_target_viewing_rate,0)) AS target_viewing_rate ';
        }
        //         if (in_array(\Config::get('const.DATA_TYPE_NUMBER.TOTAL'), $dataType)) {
        //             $select .= '   ,AVG(COALESCE(total_target_viewing_rate,0)) AS target_viewing_rate ';
        //         }
        if ($isGross) {
            $select .= '   ,AVG(COALESCE(gross_target_viewing_rate,0)) AS target_viewing_rate ';
        }

        if ($isRtTotal) {
            $select .= '   ,AVG(COALESCE(rt_total_target_viewing_rate,0)) AS target_viewing_rate ';
        }
        $from = '';
        $from .= 'FROM ';
        $from .= ' target_content hv ';
        $group = '';
        $group .= 'GROUP BY ';
        $group .= '  hhmm ';
        $group .= '  ,dow ';
        $group .= '  ,hv.channel_id ';
        $order = '';
        $order .= 'ORDER BY ';
        $order .= '   channel_id ';
        $order .= '  ,dow ';
        $order .= '  ,hhmm ';

        $query = $with . $select . $from . $group . $order;
        $result = $this->select($query, $bindings);

        return $result;
    }
}
