<?php

namespace Switchm\SmartApi\Queries\Dao\Rdb;

use Carbon\Carbon;

// 毎分
class PerMinutesDao extends Dao
{
    /**
     * @param Carbon $startDateTime
     * @param Carbon $endDateTime
     * @param string $channelType
     * @param array $channelIds
     * @param string $division
     * @param string $code
     * @param string $hour
     * @param string $dataDivision
     * @param array $conditionCross
     * @param bool $isOriginal
     * @param int $regionId
     * @param string $intervalHourly
     * @param string $intervalMinutes
     * @return array
     */
    public function getRatingData(Carbon $startDateTime, Carbon $endDateTime, string $channelType, array $channelIds, string $division, string $code, string $hour, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, string $intervalHourly, string $intervalMinutes): array
    {
        $bindings = [];
        $bindings[':division'] = $division;
        $bindings[':code'] = $code;
        // 日付処理
        $bindings[':startDate'] = $startDateTime;

        if ($hour < 5) {
            $bindings[':hour'] = $hour + 24;
        } else {
            $bindings[':hour'] = $hour;
        }

        $latestDateTime = $this->getPerMinutesLatestDateTime($regionId, $intervalHourly, $intervalMinutes);
        $bindings[':latestDateTime'] = $latestDateTime;

        $bindings[':regionId'] = $regionId;

        $bindChannelIds = $this->createArrayBindParam('channelId', [
            'channelId' => $channelIds,
        ], $bindings);

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
        $with .= ' ) ';

        $with .= ', day_nums AS ( ';
        $tmpArr = [];

        foreach (range(0, $endDateTime->diffInDays($startDateTime)) as $val) {
            $tmpArr[] = "   SELECT {$val} day_num  ";
        }
        $with .= implode(' UNION ALL ', $tmpArr);
        $with .= ' ) ';

        $with .= ' , minute_nums AS ( ';
        $tmpArr = [];

        foreach (range(0, 59) as $val) {
            $tmpArr[] = "   SELECT {$val} minute_num ";
        }
        $with .= implode(' UNION ALL ', $tmpArr);
        $with .= ' ) ';

        $with .= ', datetime_channels AS ( ';
        $with .= '  SELECT  ';
        $with .= "    start.datetime + (day_num ||' days')::interval + (minute_num ||' minutes')::interval as datetime, ";
        $with .= '    channels.id channel_id ';
        $with .= '  FROM ';
        $with .= "    (SELECT TO_TIMESTAMP(:startDate, 'YYYY-MM-DD HH24:MI:SS') as datetime ) start ";
        $with .= '  CROSS JOIN ';
        $with .= '    channels ';
        $with .= '  CROSS JOIN ';
        $with .= '    day_nums ';
        $with .= '  CROSS JOIN ';
        $with .= '    minute_nums ';
        $with .= ') ';

        $with .= ', datetime_time_box AS ( ';
        $with .= '  SELECT ';
        $with .= '    datetime as datetime, ';
        $with .= '    channel_id, ';
        $with .= '    id time_box_id ';
        $with .= '  FROM ';
        $with .= '    datetime_channels dc ';
        $with .= '  INNER JOIN ';
        $with .= '    time_boxes tb ';
        $with .= '  ON ';
        $with .= '    dc.datetime >= tb.started_at AND ';
        $with .= '    dc.datetime < tb.ended_at AND ';
        $with .= '    tb.region_id = :regionId ';
        $with .= ') ';

        $with .= ', per_minute_dow AS( ';

        if ($channelType == 'dt2') {
            $with .= '  SELECT ';
            $with .= "     to_char(wk.datetime, 'MI') AS mm  ";
            $with .= "    ,EXTRACT(DOW FROM wk.datetime - interval '5 hours') AS dow ";
            $with .= '    ,wk.channel_group ';
            $with .= '    ,wk.viewing_rate ';
            $with .= '  FROM ';
            $with .= '    ( ';
            $with .= '    SELECT ';
            $with .= '      dtb.datetime ';
            $with .= '      , SUM(viewing_rate) viewing_rate ';
            $with .= '      , CASE ';

            if ($regionId === 1) {
                $with .= '      WHEN dtb.channel_id = 2  ';
                $with .= '        THEN 2  ';
                $with .= '      WHEN dtb.channel_id = 9  ';
                $with .= '        THEN 9  ';
            } elseif ($regionId === 2) {
                $with .= '      WHEN dtb.channel_id = 45  ';
                $with .= '        THEN 45  ';
            }
            $with .= '      ELSE 999  ';
            $with .= '      END channel_group  ';
            $with .= '   FROM ';
            $with .= '     datetime_time_box dtb';
            $with .= '   LEFT JOIN ';
            $with .= '   (';
            $with .= '     SELECT ';
            $with .= '       * ';
            $with .= '     FROM ';
            $with .= '       per_minute_reports ';
            $with .= '     WHERE ';
            $with .= '       datetime BETWEEN (SELECT MIN(datetime) FROM datetime_time_box) ';
            $with .= '       AND (SELECT MAX(datetime) FROM datetime_time_box) ';
            $with .= '       AND time_box_id IN (SELECT DISTINCT time_box_id FROM datetime_time_box) ';
            $with .= '       AND hour = :hour';
            $with .= '       AND division = :division ';
            $with .= '       AND code = :code ';
            $with .= '   ) pmr ';
            $with .= '   ON ';
            $with .= '     dtb.datetime = pmr.datetime ';
            $with .= '     AND dtb.time_box_id = pmr.time_box_id ';
            $with .= '     AND dtb.channel_id = pmr.channel_id ';
            $with .= '   WHERE ';
            $with .= '     dtb.datetime <= :latestDateTime ';
            $with .= '   GROUP BY ';
            $with .= '     dtb.datetime, ';
            $with .= '     channel_group ';
            $with .= '   ) wk ';
        } else {
            $with .= ' SELECT ';
            $with .= "  to_char(dtb.datetime, 'MI') AS mm  ";
            $with .= "  ,EXTRACT(DOW FROM dtb.datetime - interval '5 hours') AS dow ";
            $with .= '  ,dtb.channel_id ';
            $with .= '  ,pmr.viewing_rate ';
            $with .= ' FROM ';
            $with .= '   datetime_time_box dtb';
            $with .= '   LEFT JOIN ';
            $with .= '   (';
            $with .= '     SELECT ';
            $with .= '       * ';
            $with .= '     FROM ';
            $with .= '       per_minute_reports ';
            $with .= '     WHERE ';
            $with .= '       datetime BETWEEN (SELECT MIN(datetime) FROM datetime_time_box) ';
            $with .= '       AND (SELECT MAX(datetime) FROM datetime_time_box) ';
            $with .= '       AND time_box_id IN (SELECT DISTINCT time_box_id FROM datetime_time_box) ';
            $with .= '       AND hour = :hour';
            $with .= '       AND division = :division ';
            $with .= '       AND code = :code ';
            $with .= '   ) pmr ';
            $with .= '   ON ';
            $with .= '     dtb.datetime = pmr.datetime ';
            $with .= '     AND dtb.time_box_id = pmr.time_box_id ';
            $with .= '     AND dtb.channel_id = pmr.channel_id ';
            $with .= '   WHERE ';
            $with .= '     dtb.datetime <= :latestDateTime ';
        }
        $with .= ') ';

        $select = '';
        $select .= 'SELECT ';
        $select .= ' mm ';
        $select .= ' ,dow ';
        // 地デジ2を選んだ場合
        if ($channelType == 'dt2') {
            $select .= ' , channel_group channel_id ';
        } else {
            // 地デジ2以外
            $select .= ' ,channel_id ';
        }
        $dig = 1;

        if (strpos($channelType, 'bs') > -1) {
            $dig = 2;
        }
        $select .= ' ,AVG(COALESCE(viewing_rate, 0))::numeric AS viewing_rate ';
        $from = '';
        $from .= 'FROM ';
        $from .= ' per_minute_dow ';
        $group = '';
        $group .= 'GROUP BY ';
        $group .= '  mm ';
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
        $order .= '   mm ';
        $order .= '  ,dow ';
        // 地デジ2を選んだ場合
        if ($channelType == 'dt2') {
            $order .= ' ,channel_group ';
        } else {
            // 地デジ2以外
            $order .= ' ,channel_id ';
        }
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
     * @param string $hour
     * @param string $dataDivision
     * @param array $conditionCross
     * @param bool $isOriginal
     * @param int $regionId
     * @param string $intervalHourly
     * @param string $intervalMinutes
     * @return array
     */
    public function getShareData(Carbon $startDateTime, Carbon $endDateTime, string $channelType, array $channelIds, string $division, string $code, string $hour, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, string $intervalHourly, string $intervalMinutes): array
    {
        $bindings = [];
        $bindings[':division'] = $division;
        $bindings[':code'] = $code;
        // 日付処理
        $bindings[':startDate'] = $startDateTime;

        if ($hour < 5) {
            $bindings[':hour'] = $hour + 24;
        } else {
            $bindings[':hour'] = $hour;
        }

        $latestDateTime = $this->getPerMinutesLatestDateTime($regionId, $intervalHourly, $intervalMinutes);
        $bindings[':latestDateTime'] = $latestDateTime;

        $bindings[':regionId'] = $regionId;

        $bindChannelIds = $this->createArrayBindParam('channelId', [
            'channelId' => $channelIds,
        ], $bindings);

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
        $with .= ' ) ';

        $with .= ', day_nums AS ( ';
        $tmpArr = [];

        foreach (range(0, $endDateTime->diffInDays($startDateTime)) as $val) {
            $tmpArr[] = "   SELECT {$val} day_num  ";
        }
        $with .= implode(' UNION ALL ', $tmpArr);
        $with .= ' ) ';

        $with .= ' , minute_nums AS ( ';
        $tmpArr = [];

        foreach (range(0, 59) as $val) {
            $tmpArr[] = "   SELECT {$val} minute_num ";
        }
        $with .= implode(' UNION ALL ', $tmpArr);
        $with .= ' ) ';

        $with .= ', datetime_channels AS ( ';
        $with .= '  SELECT  ';
        $with .= "    start.datetime + (day_num ||' days')::interval + (minute_num ||' minutes')::interval as datetime, ";
        $with .= '    channels.id channel_id ';
        $with .= '  FROM ';
        $with .= "    (SELECT TO_TIMESTAMP(:startDate, 'YYYY-MM-DD HH24:MI:SS') as datetime ) start ";
        $with .= '  CROSS JOIN ';
        $with .= '    channels ';
        $with .= '  CROSS JOIN ';
        $with .= '    day_nums ';
        $with .= '  CROSS JOIN ';
        $with .= '    minute_nums ';
        $with .= ') ';

        $with .= ', datetime_time_box AS ( ';
        $with .= '  SELECT ';
        $with .= '    datetime as datetime, ';
        $with .= '    channel_id, ';
        $with .= '    id time_box_id ';
        $with .= '  FROM ';
        $with .= '    datetime_channels dc ';
        $with .= '  INNER JOIN ';
        $with .= '    time_boxes tb ';
        $with .= '  ON ';
        $with .= '    dc.datetime >= tb.started_at AND ';
        $with .= '    dc.datetime < tb.ended_at AND ';
        $with .= '    tb.region_id = :regionId ';
        $with .= ') ';

        $with .= ', per_minute_dow AS( ';
        $with .= ' SELECT ';
        $with .= "  to_char(dtb.datetime, 'MI') AS mm  ";
        $with .= "  ,EXTRACT(DOW FROM dtb.datetime  - interval '5 hours') AS dow ";
        $with .= '  ,dtb.channel_id ';
        $with .= '  ,pmr.viewing_seconds ';
        $with .= '  ,dtb.time_box_id ';
        $with .= ' FROM ';
        $with .= '   datetime_time_box dtb';
        $with .= '   LEFT JOIN ';
        $with .= '   (';
        $with .= '     SELECT ';
        $with .= '       * ';
        $with .= '     FROM ';
        $with .= '       per_minute_reports ';
        $with .= '     WHERE ';
        $with .= '       datetime BETWEEN (SELECT MIN(datetime) FROM datetime_time_box) ';
        $with .= '       AND (SELECT MAX(datetime) FROM datetime_time_box) ';
        $with .= '       AND time_box_id IN (SELECT DISTINCT time_box_id FROM datetime_time_box) ';
        $with .= '       AND hour = :hour';
        $with .= '       AND division = :division ';
        $with .= '       AND code = :code ';
        $with .= '   ) pmr ';
        $with .= '   ON ';
        $with .= '     dtb.datetime = pmr.datetime ';
        $with .= '     AND dtb.time_box_id = pmr.time_box_id ';
        $with .= '     AND dtb.channel_id = pmr.channel_id ';
        $with .= '   WHERE ';
        $with .= '     dtb.datetime <= :latestDateTime ';

        $with .= '), minutes_seconds AS ( ';
        $with .= ' SELECT ';
        $with .= '  time_box_id ';
        $with .= '  ,mm ';
        $with .= '  ,dow ';
        $with .= '  ,channel_id ';
        $with .= '  ,SUM(viewing_seconds) AS viewing_seconds ';
        $with .= ' FROM ';
        $with .= '  per_minute_dow ';
        $with .= ' GROUP BY ';
        $with .= '  time_box_id ';
        $with .= '  ,mm ';
        $with .= '  ,dow ';
        $with .= '  ,channel_id ';
        $with .= '), minutes_shared AS ( ';
        $with .= ' SELECT ';
        $with .= '  mm ';
        $with .= '  ,dow ';
        $with .= '  ,channel_id ';
        $with .= '  ,viewing_seconds / SUM(viewing_seconds) OVER (PARTITION BY time_box_id, mm, dow)::numeric * 100 AS share ';
        $with .= ' FROM ';
        $with .= '  minutes_seconds ';
        $with .= ') ';

        $select = '';
        $select .= ' SELECT ';
        $select .= '   mm ';
        $select .= '   ,dow ';
        $select .= '   ,channel_id ';
        $select .= '   ,AVG(COALESCE(share,0))::numeric AS share ';
        $from = '';
        $from .= ' FROM ';
        $from .= '  minutes_shared ';
        $group = '';
        $group .= ' GROUP BY ';
        $group .= '   dow ';
        $group .= '   ,mm ';
        $group .= '   ,channel_id ';
        $order = ' ';
        $order .= ' ORDER BY ';
        $order .= '   channel_id ';
        $order .= '   ,dow ';
        $order .= '   ,mm ';

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
     * @param string $hour
     * @param string $dataDivision
     * @param array $conditionCross
     * @param bool $isOriginal
     * @param int $regionId
     * @param string $intervalHourly
     * @param string $intervalMinutes
     * @return array
     */
    public function getTargetData(Carbon $startDateTime, Carbon $endDateTime, string $channelType, array $channelIds, string $division, string $code, string $hour, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, string $intervalHourly, string $intervalMinutes): array
    {
        $bindings = [];
        $bindings[':division'] = $division;
        $bindings[':code'] = $code;

        // 日付処理
        $bindings[':startDate'] = $startDateTime;
        $bindings[':endDate'] = $endDateTime;

        if ($hour < 5) {
            $bindings[':hour'] = $hour + 24;
        } else {
            $bindings[':hour'] = $hour;
        }

        if ($dataDivision === 'target_content_personal') {
            $bindings[':denominator'] = 'personal';
        } elseif ($dataDivision === 'target_content_household') {
            $bindings[':denominator'] = 'household';
        }

        $latestDateTime = $this->getPerMinutesLatestDateTime($regionId, $intervalHourly, $intervalMinutes);
        $bindings[':latestDateTime'] = $latestDateTime;

        $bindings[':regionId'] = $regionId;

        $bindChannelIds = $this->createArrayBindParam('channelId', [
            'channelId' => $channelIds,
        ], $bindings);

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
        $with .= ' ) ';

        $with .= ', day_nums AS ( ';
        $tmpArr = [];

        foreach (range(0, $endDateTime->diffInDays($startDateTime)) as $val) {
            $tmpArr[] = "   SELECT {$val} day_num  ";
        }
        $with .= implode(' UNION ALL ', $tmpArr);
        $with .= ' ) ';

        $with .= ' , minute_nums AS ( ';
        $tmpArr = [];

        foreach (range(0, 59) as $val) {
            $tmpArr[] = "   SELECT {$val} minute_num ";
        }
        $with .= implode(' UNION ALL ', $tmpArr);
        $with .= ' ) ';

        $with .= ', datetime_channels AS ( ';
        $with .= '  SELECT  ';
        $with .= "    start.datetime + (day_num ||' days')::interval + (minute_num ||' minutes')::interval as datetime, ";
        $with .= '    channels.id channel_id ';
        $with .= '  FROM ';
        $with .= "    (SELECT TO_TIMESTAMP(:startDate, 'YYYY-MM-DD HH24:MI:SS') as datetime ) start ";
        $with .= '  CROSS JOIN ';
        $with .= '    channels ';
        $with .= '  CROSS JOIN ';
        $with .= '    day_nums ';
        $with .= '  CROSS JOIN ';
        $with .= '    minute_nums ';
        $with .= ') ';

        $with .= ', datetime_time_box AS ( ';
        $with .= '  SELECT ';
        $with .= '    datetime as datetime, ';
        $with .= '    channel_id, ';
        $with .= '    id time_box_id ';
        $with .= '  FROM ';
        $with .= '    datetime_channels dc ';
        $with .= '  INNER JOIN ';
        $with .= '    time_boxes tb ';
        $with .= '  ON ';
        $with .= '    dc.datetime >= tb.started_at AND ';
        $with .= '    dc.datetime < tb.ended_at AND ';
        $with .= '    tb.region_id = :regionId ';
        $with .= ') ';

        $with .= ', minutes_rated AS( ';
        $with .= ' SELECT ';
        $with .= '  hh.time_box_id time_box_id  ';
        $with .= '  ,hh.datetime ';
        $with .= '  ,hh.channel_id ';
        $with .= '  ,hh.viewing_rate denominator_viewing_rate ';
        $with .= '  ,s.viewing_rate sample_viewing_rate ';
        $with .= ' FROM ';
        $with .= '  (SELECT ';
        $with .= '      hpmr.time_box_id ';
        $with .= '      ,hpmr.datetime ';
        $with .= '      ,hpmr.hour ';
        $with .= '      ,hpmr.channel_id ';
        $with .= '      ,hpmr.viewing_seconds ';
        $with .= '      ,hpmr.viewing_rate ';
        $with .= '    FROM ';
        $with .= '      per_minute_reports hpmr ';
        $with .= '    WHERE ';
        $with .= '      hpmr.division = :denominator ';
        $with .= "      AND hpmr.datetime BETWEEN TO_TIMESTAMP(:startDate, 'YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP(:endDate, 'YYYY-MM-DD HH24:MI:SS') ";
        $with .= '      AND hpmr.hour = :hour';
        $with .= '    ) hh ';
        $with .= '  INNER JOIN ';
        $with .= '   (SELECT ';
        $with .= '      spmr.time_box_id ';
        $with .= '      ,spmr.datetime ';
        $with .= '      ,spmr.hour ';
        $with .= '      ,spmr.channel_id ';
        $with .= '      ,spmr.viewing_rate ';
        $with .= '    FROM ';
        $with .= '      per_minute_reports spmr ';
        $with .= '    WHERE ';
        $with .= '      spmr.division = :division ';
        $with .= '      AND spmr.code = :code ';
        $with .= '      AND spmr.time_box_id in (SELECT id FROM time_boxes WHERE region_id = :regionId) ';
        $with .= "      AND spmr.datetime BETWEEN TO_TIMESTAMP(:startDate, 'YYYY-MM-DD HH24:MI:SS') AND TO_TIMESTAMP(:endDate, 'YYYY-MM-DD HH24:MI:SS')  ";
        $with .= '      AND spmr.hour = :hour';
        $with .= '   ) AS s ';
        $with .= '  ON ';
        $with .= '     hh.time_box_id = s.time_box_id AND ';
        $with .= '     hh.datetime = s.datetime AND ';
        $with .= '     hh.hour = s.hour AND ';
        $with .= '     hh.channel_id = s.channel_id ';

        $with .= '), minutes_reports_dow AS ( ';
        $with .= ' SELECT ';
        $with .= '  dtb.time_box_id ';
        $with .= "  ,to_char(dtb.datetime, 'MI') AS mm ";
        $with .= "  , EXTRACT(DOW FROM dtb.datetime  - interval '5 hours') AS dow ";
        $with .= '  , dtb.channel_id ';
        $with .= '  , mr.denominator_viewing_rate ';
        $with .= '  , mr.sample_viewing_rate ';
        $with .= '  FROM ';
        $with .= '   datetime_time_box dtb';
        $with .= '   LEFT JOIN ';
        $with .= '   minutes_rated mr';
        $with .= '   ON ';
        $with .= '     dtb.datetime = mr.datetime ';
        $with .= '     AND dtb.channel_id = mr.channel_id ';
        $with .= '   WHERE ';
        $with .= "     dtb.datetime <= TO_TIMESTAMP(:latestDateTime, 'YYYY-MM-DD HH24:MI:SS') ";

        $with .= '), minutes_target AS (';
        $with .= ' SELECT ';
        $with .= '  time_box_id ';
        $with .= '  ,mm ';
        $with .= '  ,dow ';
        $with .= '  ,channel_id ';
        $with .= '  , SUM(denominator_viewing_rate) AS denominator_viewing_rate ';
        $with .= '  , SUM(sample_viewing_rate) AS sample_viewing_rate ';
        $with .= ' FROM ';
        $with .= '  minutes_reports_dow ';
        $with .= ' GROUP BY ';
        $with .= '  time_box_id ';
        $with .= '  ,mm ';
        $with .= '  ,dow ';
        $with .= '  ,channel_id ';
        $with .= '), target_rated AS ( ';
        $with .= ' SELECT ';
        $with .= '  mm ';
        $with .= '  ,dow ';
        $with .= '  ,channel_id ';
        $with .= '  ,(sample_viewing_rate / denominator_viewing_rate)  * 100 AS target_viewing_rate ';
        $with .= ' FROM ';
        $with .= '  minutes_target ';
        $with .= ') ';

        $select = '';
        $select .= ' SELECT ';
        $select .= '   mm ';
        $select .= '   ,dow ';
        $select .= '   ,channel_id ';
        $select .= '   ,AVG(COALESCE(target_viewing_rate,0))::numeric AS target_viewing_rate ';
        $from = '';
        $from .= ' FROM ';
        $from .= '  target_rated ';
        $group = '';
        $group .= ' GROUP BY ';
        $group .= '   mm ';
        $group .= '   ,dow ';
        $group .= '   ,channel_id ';
        $order = ' ';
        $order .= ' ORDER BY ';
        $order .= '   channel_id ';
        $order .= '   ,dow ';
        $order .= '   ,mm ';

        $query = $with . $select . $from . $group . $order;
        $result = $this->select($query, $bindings);

        return $result;
    }
}
