<?php

namespace Switchm\SmartApi\Queries\Dao\Dwh;

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
     * @throws \Exception
     * @return array
     */
    public function getRatingData(Carbon $startDateTime, Carbon $endDateTime, string $channelType, array $channelIds, string $division, string $code, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, array $dataType, array $dataTypeFlags, string $intervalHourly, string $intervalMinutes): array
    {
        // 日付処理
        $st = new Carbon($startDateTime);
        $et = new Carbon($endDateTime);

        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = array_values($dataTypeFlags);
        $this->createCommonMasterTemporaryTable($startDateTime, $channelIds, $regionId);

        $bindings = [];
        $bindings[':startTimestamp'] = $st->toDateTimeString();
        $bindings[':endTimestamp'] = $et->toDateTimeString();
        $bindings[':regionId'] = $regionId;
        $bindings[':division'] = $division;
        $bindings[':code'] = $code;

        $bindChannelIds = $this->createArrayBindParam('channelId', [
            'channelId' => $channelIds,
        ], $bindings);

        $query = '';
        $query .= ' CREATE TEMPORARY TABLE  union_hourly_reports AS ';
        $query .= '   SELECT ';
        $query .= '     time_box_id ';
        $query .= '     , hr.datetime ';
        $query .= '     , date ';
        $query .= '     , hour ';
        $query .= '     , channel_id ';
        $query .= '     , division ';
        $query .= '     , code ';
        $query .= '     , MAX(viewing_seconds) viewing_seconds ';
        $query .= '     , MAX(viewing_rate) viewing_rate ';
        $query .= '     , MAX(ts_samples_viewing_seconds) ts_samples_viewing_seconds ';
        $query .= '     , MAX(ts_samples_viewing_rate) ts_samples_viewing_rate ';
        $query .= '     , MAX(ts_viewing_seconds) ts_viewing_seconds ';
        $query .= '     , MAX(ts_viewing_rate) ts_viewing_rate ';
        $query .= '     , MAX(total_viewing_seconds) total_viewing_seconds ';
        $query .= '     , MAX(total_viewing_rate) total_viewing_rate ';
        $query .= '     , MAX(gross_viewing_seconds) gross_viewing_seconds ';
        $query .= '     , MAX(gross_viewing_rate) gross_viewing_rate ';
        $query .= '     , MAX(rt_total_viewing_seconds) rt_total_viewing_seconds ';
        $query .= '     , MAX(rt_total_viewing_rate) rt_total_viewing_rate ';
        $query .= '   FROM ( ';
        $query .= '     SELECT ';
        $query .= '       time_box_id ';
        $query .= '       , hr.datetime ';
        $query .= '       , date ';
        $query .= '       , hour ';
        $query .= '       , channel_id ';
        $query .= '       , division ';
        $query .= '       , code ';
        $query .= '       , viewing_seconds ';
        $query .= '       , viewing_rate ';
        $query .= '       , ts_samples_viewing_seconds ';
        $query .= '       , ts_samples_viewing_rate ';
        $query .= '       , 0 ts_viewing_seconds ';
        $query .= '       , 0 ts_viewing_rate ';
        $query .= '       , 0 total_viewing_seconds ';
        $query .= '       , 0 total_viewing_rate ';
        $query .= '       , NULL gross_viewing_seconds ';
        $query .= '       , NULL gross_viewing_rate ';
        $query .= '       , NULL rt_total_viewing_seconds ';
        $query .= '       , NULL rt_total_viewing_rate ';
        $query .= '     FROM ';
        $query .= '       hourly_reports hr ';
        $query .= '     WHERE ';
        $query .= '       hr.division = :division  ';
        $query .= '       AND hr.code = :code  ';
        $query .= '       AND hr.datetime BETWEEN :startTimestamp::timestamp without time zone AND :endTimestamp::timestamp without time zone ';
        $query .= '       AND hr.channel_id IN (' . implode(',', $bindChannelIds) . ') ';
        $query .= '       AND hr.time_box_id IN (SELECT id FROM time_boxes WHERE region_id = :regionId) ';
        $query .= '     UNION ALL ';
        $query .= '     SELECT ';
        $query .= '       time_box_id ';
        $query .= '       , thr.datetime ';
        $query .= '       , date ';
        $query .= '       , hour ';
        $query .= '       , channel_id ';
        $query .= '       , division ';
        $query .= '       , code ';
        $query .= '       , 0 viewing_seconds ';
        $query .= '       , 0 viewing_rate ';
        $query .= '       , 0 ts_samples_viewing_seconds ';
        $query .= '       , 0 ts_samples_viewing_rate ';
        $query .= '       , viewing_seconds ts_viewing_seconds ';
        $query .= '       , viewing_rate ts_viewing_rate ';
        $query .= '       , total_viewing_seconds ';
        $query .= '       , total_viewing_rate ';
        $query .= '       , gross_viewing_seconds ';
        $query .= '       , gross_viewing_rate ';
        $query .= '       , rt_total_viewing_seconds ';
        $query .= '       , rt_total_viewing_rate ';
        $query .= '     FROM ';
        $query .= '       ts_hourly_reports thr ';
        $query .= '     WHERE ';
        $query .= '       thr.division = :division  ';
        $query .= '       AND thr.code = :code  ';
        $query .= '       AND thr.datetime BETWEEN :startTimestamp::timestamp without time zone AND :endTimestamp::timestamp without time zone ';
        $query .= '       AND thr.channel_id IN (' . implode(',', $bindChannelIds) . ') ';
        $query .= '       AND thr.time_box_id IN (SELECT id FROM time_boxes WHERE region_id = :regionId) ';
        $query .= '   ) hr ';
        $query .= '   GROUP BY ';
        $query .= '     time_box_id ';
        $query .= '     , hr.datetime ';
        $query .= '     , date ';
        $query .= '     , hour ';
        $query .= '     , channel_id ';
        $query .= '     , division ';
        $query .= '     , code ';
        $this->select($query, $bindings);

        $bindings = [];
        $bindings[':startTimestamp'] = $st->toDateTimeString();
        $bindings[':endTimestamp'] = $et->toDateTimeString();

        $latestDateTime = $this->getPerHourlyLatestDateTime($regionId, $intervalHourly, $intervalMinutes);
        $bindings[':latestDateTime'] = $latestDateTime;

        $query = '';
        $query .= ' WITH joined_hourly_reports AS ( ';
        $query .= ' SELECT ';
        $query .= "   to_char(m.date_time, 'HH24') hhmm ";
        $query .= "   , EXTRACT(DOW FROM (m.date_time + interval '- 5 hours')) dow ";

        if ($channelType == 'dt2') {
            $query .= '   , CASE ';

            if ($regionId === 1) {
                $query .= '      WHEN m.channel_id = 2  ';
                $query .= '        THEN 2  ';
                $query .= '      WHEN m.channel_id = 9  ';
                $query .= '        THEN 9  ';
            } elseif ($regionId === 2) {
                $query .= '      WHEN m.channel_id = 45  ';
                $query .= '        THEN 45  ';
            }
            $query .= '      ELSE 999 END AS channel_id_grp ';
            $query .= '      , SUM(uhr.viewing_rate) AS viewing_rate ';
        } else {
            $query .= '   , m.channel_id ';
            $query .= '   , uhr.viewing_rate ';
            $query .= '   , uhr.ts_viewing_rate ';
            $query .= '   , uhr.total_viewing_rate ';
            $query .= '   , COALESCE(uhr.gross_viewing_rate, uhr.ts_samples_viewing_rate) gross_viewing_rate  ';
            $query .= '   , COALESCE(uhr.rt_total_viewing_rate, uhr.ts_samples_viewing_rate) rt_total_viewing_rate  ';
        }
        $query .= ' FROM ';
        $query .= '   master m ';
        $query .= ' LEFT JOIN ';
        $query .= '   union_hourly_reports uhr ';
        $query .= ' ON ';
        $query .= '   m.date_time = uhr.datetime ';
        $query .= '   AND m.channel_id = uhr.channel_id ';
        $query .= '   AND m.time_box_id = uhr.time_box_id ';
        $query .= ' WHERE ';
        $query .= '   m.date_time BETWEEN :startTimestamp AND :endTimestamp ';
        $query .= '   AND m.date_time <= :latestDateTime ';

        if ($channelType == 'dt2') {
            $query .= ' GROUP BY ';
            $query .= '  date_time, ';
            $query .= '  hhmm, ';
            $query .= '  dow, ';
            $query .= '  channel_id_grp ';
        }
        $query .= ' ) ';
        $query .= ' SELECT ';
        $query .= '   hhmm ';
        $query .= '   , dow ';

        if ($channelType == 'dt2') {
            $query .= '   , channel_id_grp AS channel_id ';
        } else {
            $query .= '   , channel_id ';
        }

        if ($isRt) {
            $query .= ' ,AVG(COALESCE(viewing_rate,0))::real viewing_rate ';
        }

        if ($isTs) {
            $query .= ' ,AVG(COALESCE(ts_viewing_rate,0))::real viewing_rate ';
        }

        if ($isTotal) {
            $query .= ' ,AVG(COALESCE(total_viewing_rate,0))::real viewing_rate ';
        }

        if ($isGross) {
            $query .= ' ,AVG(COALESCE(gross_viewing_rate,0))::real viewing_rate ';
        }

        if ($isRtTotal) {
            $query .= ' ,AVG(COALESCE(rt_total_viewing_rate,0))::real viewing_rate ';
        }
        $query .= ' FROM ';
        $query .= '   joined_hourly_reports  ';
        $query .= ' GROUP BY ';
        $query .= '   hhmm ';
        $query .= '   , dow ';
        $query .= '   , channel_id  ';
        $query .= ' ORDER BY ';
        $query .= '   channel_id ';
        $query .= '   , dow ';
        $query .= '   , hhmm ';
        $query .= ' ; ';

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
     * @throws \Exception
     * @return array
     */
    public function getShareData(Carbon $startDateTime, Carbon $endDateTime, string $channelType, array $channelIds, string $division, string $code, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, array $dataType, array $dataTypeFlags, string $intervalHourly, string $intervalMinutes): array
    {
        $bindings = [];

        $latestDateTime = $this->getPerHourlyLatestDateTime($regionId, $intervalHourly, $intervalMinutes);
        $bindings[':latestDateTime'] = $latestDateTime;

        $bindings[':division'] = $division;
        $bindings[':code'] = $code;
        // 日付処理
        $st = new Carbon($startDateTime);
        $et = new Carbon($endDateTime);
        $bindings[':startTimestamp'] = $st->toDateTimeString();
        $bindings[':endTimestamp'] = $et->toDateTimeString();

        $bindChannelIds = $this->createArrayBindParam('channelId', [
            'channelId' => $channelIds,
        ], $bindings);

        $bindings[':regionId'] = $regionId;

        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = array_values($dataTypeFlags);
        $this->createCommonMasterTemporaryTable($startDateTime, $channelIds, $regionId);

        $query = '';
        $query .= ' WITH union_hourly_reports AS ( ';
        $query .= '   SELECT ';
        $query .= '     time_box_id ';
        $query .= '     , hr.datetime ';
        $query .= '     , date ';
        $query .= '     , hour ';
        $query .= '     , channel_id ';
        $query .= '     , division ';
        $query .= '     , code ';
        $query .= '     , MAX(viewing_seconds) viewing_seconds ';
        $query .= '     , MAX(viewing_rate) viewing_rate ';
        $query .= '     , MAX(ts_samples_viewing_seconds) ts_samples_viewing_seconds ';
        $query .= '     , MAX(ts_samples_viewing_rate) ts_samples_viewing_rate ';
        $query .= '     , MAX(ts_viewing_seconds) ts_viewing_seconds ';
        $query .= '     , MAX(ts_viewing_rate) ts_viewing_rate ';
        $query .= '     , MAX(total_viewing_seconds) total_viewing_seconds ';
        $query .= '     , MAX(total_viewing_rate) total_viewing_rate ';
        $query .= '     , MAX(gross_viewing_seconds) gross_viewing_seconds ';
        $query .= '     , MAX(gross_viewing_rate) gross_viewing_rate ';
        $query .= '     , MAX(rt_total_viewing_seconds) rt_total_viewing_seconds ';
        $query .= '     , MAX(rt_total_viewing_rate) rt_total_viewing_rate ';
        $query .= '   FROM ( ';
        $query .= '     SELECT ';
        $query .= '       time_box_id ';
        $query .= '       , hr.datetime ';
        $query .= '       , date ';
        $query .= '       , hour ';
        $query .= '       , channel_id ';
        $query .= '       , division ';
        $query .= '       , code ';
        $query .= '       , viewing_seconds ';
        $query .= '       , viewing_rate ';
        $query .= '       , ts_samples_viewing_seconds ';
        $query .= '       , ts_samples_viewing_rate ';
        $query .= '       , 0 ts_viewing_seconds ';
        $query .= '       , 0 ts_viewing_rate ';
        $query .= '       , 0 total_viewing_seconds ';
        $query .= '       , 0 total_viewing_rate ';
        $query .= '       , NULL gross_viewing_seconds ';
        $query .= '       , NULL gross_viewing_rate ';
        $query .= '       , NULL rt_total_viewing_seconds ';
        $query .= '       , NULL rt_total_viewing_rate ';
        $query .= '     FROM ';
        $query .= '       hourly_reports hr ';
        $query .= '     WHERE ';
        $query .= '       hr.division = :division  ';
        $query .= '       AND hr.code = :code  ';
        $query .= '       AND hr.datetime BETWEEN :startTimestamp::timestamp without time zone AND :endTimestamp::timestamp without time zone ';
        $query .= '       AND hr.channel_id IN (' . implode(',', $bindChannelIds) . ') ';
        $query .= '       AND hr.time_box_id IN (SELECT id FROM time_boxes WHERE region_id = :regionId) ';
        $query .= '     UNION ALL ';
        $query .= '     SELECT ';
        $query .= '       time_box_id ';
        $query .= '       , thr.datetime ';
        $query .= '       , date ';
        $query .= '       , hour ';
        $query .= '       , channel_id ';
        $query .= '       , division ';
        $query .= '       , code ';
        $query .= '       , 0 viewing_seconds ';
        $query .= '       , 0 viewing_rate ';
        $query .= '       , 0 ts_samples_viewing_seconds ';
        $query .= '       , 0 ts_samples_viewing_rate ';
        $query .= '       , viewing_seconds ts_viewing_seconds ';
        $query .= '       , viewing_rate ts_viewing_rate ';
        $query .= '       , total_viewing_seconds ';
        $query .= '       , total_viewing_rate ';
        $query .= '       , gross_viewing_seconds ';
        $query .= '       , gross_viewing_rate ';
        $query .= '       , rt_total_viewing_seconds ';
        $query .= '       , rt_total_viewing_rate ';
        $query .= '     FROM ';
        $query .= '       ts_hourly_reports thr ';
        $query .= '     WHERE ';
        $query .= '       thr.division = :division  ';
        $query .= '       AND thr.code = :code  ';
        $query .= '       AND thr.datetime BETWEEN :startTimestamp::timestamp without time zone AND :endTimestamp::timestamp without time zone ';
        $query .= '       AND thr.channel_id IN (' . implode(',', $bindChannelIds) . ') ';
        $query .= '       AND thr.time_box_id IN (SELECT id FROM time_boxes WHERE region_id = :regionId) ';
        $query .= '   ) hr ';
        $query .= '   GROUP BY ';
        $query .= '     time_box_id ';
        $query .= '     , hr.datetime ';
        $query .= '     , date ';
        $query .= '     , hour ';
        $query .= '     , channel_id ';
        $query .= '     , division ';
        $query .= '     , code ';

        $query .= ' ), joined_hourly_reports AS ( ';
        $query .= ' SELECT ';
        $query .= "   to_char(m.date_time, 'HH24') hhmm ";
        $query .= "   , EXTRACT(DOW FROM (m.date_time + interval '- 5 hours')) dow ";
        $query .= '   , m.channel_id ';
        $query .= '   , hr.viewing_seconds::numeric / CASE WHEN SUM(hr.viewing_seconds) OVER (PARTITION BY m.date_time ) = 0 THEN 1 ELSE SUM(hr.viewing_seconds) OVER (PARTITION BY m.date_time )::real END::numeric * 100 AS share ';
        $query .= '   , hr.ts_viewing_seconds::numeric / CASE WHEN SUM(hr.ts_viewing_seconds) OVER (PARTITION BY m.date_time ) = 0 THEN 1 ELSE SUM(hr.ts_viewing_seconds) OVER (PARTITION BY m.date_time )::real END::numeric * 100 AS ts_share ';
        $query .= '   , hr.total_viewing_seconds::numeric / CASE WHEN SUM(hr.total_viewing_seconds) OVER (PARTITION BY m.date_time ) = 0 THEN 1 ELSE SUM(hr.total_viewing_seconds) OVER (PARTITION BY m.date_time )::real END::numeric * 100 AS ts_total_share ';
        $query .= '   , COALESCE(hr.gross_viewing_seconds, hr.ts_samples_viewing_seconds)::numeric / CASE WHEN SUM(COALESCE(hr.gross_viewing_seconds, hr.ts_samples_viewing_seconds)) OVER (PARTITION BY m.date_time ) = 0 THEN 1 ELSE SUM(COALESCE(hr.gross_viewing_seconds, hr.ts_samples_viewing_seconds)) OVER (PARTITION BY m.date_time )::real END::numeric * 100 AS gross_share ';
        $query .= '   , COALESCE(hr.rt_total_viewing_seconds, hr.ts_samples_viewing_seconds)::numeric / CASE WHEN SUM(COALESCE(hr.rt_total_viewing_seconds, hr.ts_samples_viewing_seconds)) OVER (PARTITION BY m.date_time ) = 0 THEN 1 ELSE SUM(COALESCE(hr.rt_total_viewing_seconds, hr.ts_samples_viewing_seconds)) OVER (PARTITION BY m.date_time )::real END::numeric * 100 AS rt_total_share ';

        $query .= '   , SUM(hr.viewing_seconds) OVER (PARTITION BY m.date_time )::real AS total_share ';
        $query .= '   , SUM(hr.ts_viewing_seconds) OVER (PARTITION BY m.date_time )::real AS total_ts_share ';
        $query .= '   , SUM(hr.total_viewing_seconds) OVER (PARTITION BY m.date_time )::real AS total_total_share ';
        $query .= '   , SUM(COALESCE(hr.gross_viewing_seconds, hr.ts_samples_viewing_seconds)) OVER (PARTITION BY m.date_time )::real AS total_gross_share ';
        $query .= '   , SUM(COALESCE(hr.rt_total_viewing_seconds, hr.ts_samples_viewing_seconds)) OVER (PARTITION BY m.date_time )::real AS total_rt_total_share ';

        $query .= ' FROM ';
        $query .= '   master m ';
        $query .= ' LEFT JOIN ';
        $query .= '   union_hourly_reports hr ';
        $query .= ' ON ';
        $query .= '   m.date_time = hr.datetime ';
        $query .= '   AND m.channel_id = hr.channel_id ';
        $query .= '   AND m.time_box_id = hr.time_box_id ';
        $query .= ' WHERE ';
        $query .= '   m.date_time BETWEEN :startTimestamp AND :endTimestamp ';
        $query .= '   AND m.date_time <= :latestDateTime ';
        $query .= ' ) ';
        $query .= ' SELECT ';
        $query .= '   hhmm ';
        $query .= '   , dow ';
        $query .= '   , channel_id ';

        if ($isRt) {
            $query .= ' ,AVG(COALESCE(share,0)) AS share ';
        }

        if ($isTs) {
            $query .= ' ,AVG(COALESCE(ts_share,0)) AS share ';
        }

        if ($isTotal) {
            $query .= ' ,AVG(COALESCE(ts_total_share,0)) AS share ';
        }

        if ($isGross) {
            $query .= ' ,AVG(COALESCE(gross_share,0)) AS share ';
        }

        if ($isRtTotal) {
            $query .= ' ,AVG(COALESCE(rt_total_share,0)) AS share ';
        }
        $query .= ' FROM ';
        $query .= '   joined_hourly_reports  ';
        $query .= ' GROUP BY ';
        $query .= '   hhmm ';
        $query .= '   , dow ';
        $query .= '   , channel_id  ';
        $query .= ' ORDER BY ';
        $query .= '   channel_id ';
        $query .= '   , dow ';
        $query .= '   , hhmm ';
        $query .= ' ; ';
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
     * @throws \Exception
     * @return array
     */
    public function getTargetData(Carbon $startDateTime, Carbon $endDateTime, string $channelType, array $channelIds, string $division, string $code, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, array $dataType, array $dataTypeFlags, string $intervalHourly, string $intervalMinutes): array
    {
        $bindings = [];

        $latestDateTime = $this->getPerHourlyLatestDateTime($regionId, $intervalHourly, $intervalMinutes);
        $bindings[':latestDateTime'] = $latestDateTime;

        $bindings[':division'] = $division;
        $bindings[':code'] = $code;
        // 日付処理
        $st = new Carbon($startDateTime);
        $et = new Carbon($endDateTime);
        $bindings[':startTimestamp'] = $st->toDateTimeString();
        $bindings[':endTimestamp'] = $et->toDateTimeString();

        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = array_values($dataTypeFlags);
        $this->createCommonMasterTemporaryTable($startDateTime, $channelIds, $regionId);

        if ($dataDivision === 'target_content_personal') {
            $bindings[':denominator'] = 'personal';
        } elseif ($dataDivision === 'target_content_household') {
            $bindings[':denominator'] = 'household';
        }

        $bindChannelIds = $this->createArrayBindParam('channelId', [
            'channelId' => $channelIds,
        ], $bindings);

        $bindings[':regionId'] = $regionId;

        $query = '';
        $query .= ' WITH union_hourly_reports AS ( ';
        $query .= '   SELECT ';
        $query .= '     time_box_id ';
        $query .= '     , hr.datetime ';
        $query .= '     , date ';
        $query .= '     , hour ';
        $query .= '     , channel_id ';
        $query .= '     , division ';
        $query .= '     , code ';
        $query .= '     , MAX(viewing_seconds) viewing_seconds ';
        $query .= '     , MAX(viewing_rate) viewing_rate ';
        $query .= '     , MAX(ts_samples_viewing_seconds) ts_samples_viewing_seconds ';
        $query .= '     , MAX(ts_samples_viewing_rate) ts_samples_viewing_rate ';
        $query .= '     , MAX(ts_viewing_seconds) ts_viewing_seconds ';
        $query .= '     , MAX(ts_viewing_rate) ts_viewing_rate ';
        $query .= '     , MAX(total_viewing_seconds) total_viewing_seconds ';
        $query .= '     , MAX(total_viewing_rate) total_viewing_rate ';
        $query .= '     , MAX(gross_viewing_seconds) gross_viewing_seconds ';
        $query .= '     , MAX(gross_viewing_rate) gross_viewing_rate ';
        $query .= '     , MAX(rt_total_viewing_seconds) rt_total_viewing_seconds ';
        $query .= '     , MAX(rt_total_viewing_rate) rt_total_viewing_rate ';
        $query .= '   FROM ( ';
        $query .= '     SELECT ';
        $query .= '       time_box_id ';
        $query .= '       , hr.datetime ';
        $query .= '       , date ';
        $query .= '       , hour ';
        $query .= '       , channel_id ';
        $query .= '       , division ';
        $query .= '       , code ';
        $query .= '       , viewing_seconds ';
        $query .= '       , viewing_rate ';
        $query .= '       , ts_samples_viewing_seconds ';
        $query .= '       , ts_samples_viewing_rate ';
        $query .= '       , 0 ts_viewing_seconds ';
        $query .= '       , 0 ts_viewing_rate ';
        $query .= '       , 0 total_viewing_seconds ';
        $query .= '       , 0 total_viewing_rate ';
        $query .= '       , NULL gross_viewing_seconds ';
        $query .= '       , NULL gross_viewing_rate ';
        $query .= '       , NULL rt_total_viewing_seconds ';
        $query .= '       , NULL rt_total_viewing_rate ';
        $query .= '     FROM ';
        $query .= '       hourly_reports hr ';
        $query .= '     WHERE ';
        $query .= '       hr.datetime BETWEEN :startTimestamp::timestamp without time zone AND :endTimestamp::timestamp without time zone ';
        $query .= '       AND hr.channel_id IN (' . implode(',', $bindChannelIds) . ') ';
        $query .= '       AND hr.time_box_id IN (SELECT id FROM time_boxes WHERE region_id = :regionId) ';
        $query .= '     UNION ALL ';
        $query .= '     SELECT ';
        $query .= '       time_box_id ';
        $query .= '       , thr.datetime ';
        $query .= '       , date ';
        $query .= '       , hour ';
        $query .= '       , channel_id ';
        $query .= '       , division ';
        $query .= '       , code ';
        $query .= '       , 0 viewing_seconds ';
        $query .= '       , 0 viewing_rate ';
        $query .= '       , 0 ts_samples_viewing_seconds ';
        $query .= '       , 0 ts_samples_viewing_rate ';
        $query .= '       , viewing_seconds ts_viewing_seconds ';
        $query .= '       , viewing_rate ts_viewing_rate ';
        $query .= '       , total_viewing_seconds ';
        $query .= '       , total_viewing_rate ';
        $query .= '       , gross_viewing_seconds ';
        $query .= '       , gross_viewing_rate ';
        $query .= '       , rt_total_viewing_seconds ';
        $query .= '       , rt_total_viewing_rate ';
        $query .= '     FROM ';
        $query .= '       ts_hourly_reports thr ';
        $query .= '     WHERE ';
        $query .= '       thr.datetime BETWEEN :startTimestamp::timestamp without time zone AND :endTimestamp::timestamp without time zone ';
        $query .= '       AND thr.channel_id IN (' . implode(',', $bindChannelIds) . ') ';
        $query .= '       AND thr.time_box_id IN (SELECT id FROM time_boxes WHERE region_id = :regionId) ';
        $query .= '   ) hr ';
        $query .= '   GROUP BY ';
        $query .= '     time_box_id ';
        $query .= '     , hr.datetime ';
        $query .= '     , date ';
        $query .= '     , hour ';
        $query .= '     , channel_id ';
        $query .= '     , division ';
        $query .= '     , code ';

        $query .= ' ), joined_hourly_reports AS ( ';
        $query .= ' SELECT ';
        $query .= "   to_char(m.date_time, 'HH24') hhmm ";
        $query .= "   , EXTRACT(DOW FROM (m.date_time + interval '- 5 hours')) dow ";
        $query .= '   , m.channel_id ';
        $query .= '   , CASE WHEN hhr.viewing_rate = 0 THEN 0 ELSE hr.viewing_rate / hhr.viewing_rate * 100 END viewing_rate ';
        $query .= '   , CASE WHEN hhr.ts_viewing_rate = 0 THEN 0 ELSE hr.ts_viewing_rate / hhr.ts_viewing_rate * 100 END ts_viewing_rate ';
        $query .= '   , CASE WHEN hhr.total_viewing_rate = 0 THEN 0 ELSE hr.total_viewing_rate / hhr.total_viewing_rate * 100 END total_viewing_rate  ';
        $query .= '   , CASE WHEN COALESCE(hhr.gross_viewing_rate, hhr.ts_samples_viewing_rate) = 0 THEN 0 ELSE COALESCE(hr.gross_viewing_rate, hr.ts_samples_viewing_rate) / COALESCE(hhr.gross_viewing_rate, hhr.ts_samples_viewing_rate) * 100 END gross_viewing_rate  ';
        $query .= '   , CASE WHEN COALESCE(hhr.rt_total_viewing_rate, hhr.ts_samples_viewing_rate) = 0 THEN 0 ELSE COALESCE(hr.rt_total_viewing_rate, hr.ts_samples_viewing_rate) / COALESCE(hhr.rt_total_viewing_rate, hhr.ts_samples_viewing_rate) * 100 END  rt_total_viewing_rate  ';
        $query .= ' FROM ';
        $query .= '   master m ';
        $query .= ' LEFT JOIN  ';
        $query .= '   union_hourly_reports hr ';
        $query .= ' ON ';
        $query .= '   m.date_time = hr.datetime ';
        $query .= '   AND m.channel_id = hr.channel_id ';
        $query .= '   AND m.time_box_id = hr.time_box_id ';
        $query .= '   AND hr.division = :division  ';
        $query .= '   AND hr.code = :code  ';
        $query .= '   AND hr.datetime BETWEEN :startTimestamp::timestamp without time zone AND :endTimestamp::timestamp without time zone ';
        $query .= '   AND hr.channel_id IN (' . implode(',', $bindChannelIds) . ') ';
        $query .= '   AND hr.time_box_id IN (SELECT id FROM time_boxes WHERE region_id = :regionId) ';
        $query .= ' LEFT JOIN  ';
        $query .= '   union_hourly_reports hhr ';
        $query .= ' ON ';
        $query .= '   m.date_time = hhr.datetime ';
        $query .= '   AND m.channel_id = hhr.channel_id ';
        $query .= '   AND m.time_box_id = hhr.time_box_id ';
        $query .= '   AND hhr.division = :denominator  ';
        $query .= "   AND hhr.code = '1'  ";
        $query .= '   AND hhr.datetime BETWEEN :startTimestamp::timestamp without time zone AND :endTimestamp::timestamp without time zone ';
        $query .= '   AND hhr.channel_id IN (' . implode(',', $bindChannelIds) . ') ';
        $query .= '   AND hhr.time_box_id IN (SELECT id FROM time_boxes WHERE region_id = :regionId) ';
        $query .= ' WHERE ';
        $query .= '   m.date_time BETWEEN :startTimestamp AND :endTimestamp ';
        $query .= '   AND m.date_time <= :latestDateTime ';
        $query .= ' ) ';
        $query .= ' SELECT ';
        $query .= '   hhmm ';
        $query .= '   , dow ';
        $query .= '   , channel_id ';

        if ($isRt) {
            $query .= ' ,AVG(COALESCE(viewing_rate,0)) target_viewing_rate ';
        }

        if ($isTs) {
            $query .= ' ,AVG(COALESCE(ts_viewing_rate,0)) target_viewing_rate ';
        }

        if ($isTotal) {
            $query .= ' ,AVG(COALESCE(total_viewing_rate,0)) target_viewing_rate ';
        }

        if ($isGross) {
            $query .= ' ,AVG(COALESCE(gross_viewing_rate,0)) target_viewing_rate ';
        }

        if ($isRtTotal) {
            $query .= ' ,AVG(COALESCE(rt_total_viewing_rate,0)) target_viewing_rate ';
        }
        $query .= ' FROM ';
        $query .= '   joined_hourly_reports  ';
        $query .= ' GROUP BY ';
        $query .= '   hhmm ';
        $query .= '   , dow ';
        $query .= '   , channel_id  ';
        $query .= ' ORDER BY ';
        $query .= '   channel_id ';
        $query .= '   , dow ';
        $query .= '   , hhmm ';
        $query .= ' ; ';
        $result = $this->select($query, $bindings);

        return $result;
    }

    /**
     * @param Carbon $startDateTime
     * @param Carbon $endDateTime
     * @param string $channelType
     * @param array $channelIds
     * @param string $division
     * @param null|array $code
     * @param string $dataDivision
     * @param array $conditionCross
     * @param bool $isOriginal
     * @param int $regionId
     * @param array $dataType
     * @param array $dataTypeFlags
     * @param string $intervalHourly
     * @param string $intervalMinutes
     * @param string $sampleCodePrefix
     * @param string $sampleCodeNumberPrefix
     * @param string $selectedPersonalName
     * @throws \Exception
     * @return array
     */
    public function getConditionCrossRatingData(Carbon $startDateTime, Carbon $endDateTime, string $channelType, array $channelIds, string $division, ?array $code, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, array $dataType, array $dataTypeFlags, string $intervalHourly, string $intervalMinutes, string $sampleCodePrefix, string $sampleCodeNumberPrefix, string $selectedPersonalName): array
    {
        $st = new Carbon($startDateTime);
        $et = new Carbon($endDateTime);

        $bindings = [];

        $latestDateTime = $this->getPerHourlyLatestDateTime($regionId, $intervalHourly, $intervalMinutes);
        $bindings[':latestDateTime'] = $latestDateTime;

        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = array_values($dataTypeFlags);

        $isConditionCross = in_array($division, ['condition_cross']);

        if ($isConditionCross) {
            $code = ['condition_cross'];
        }
        $timeBoxIds = $this->createTimeBoxListWhere($st->toDateTimeString(), $et->toDateTimeString(), $regionId);
        $hasSelectedPersonal = false;
        $codeNumber = 1;

        if ($isRt) {
            $this->createRtSampleTempTable($isConditionCross, $conditionCross, $division, $code, $timeBoxIds, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName, $hasSelectedPersonal, $codeNumber);
        }

        if ($isTs || $isGross || $isRtTotal) {
            $this->createTsSampleTempTable($isConditionCross, $conditionCross, $division, $code, $timeBoxIds, $regionId, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName, $hasSelectedPersonal, $codeNumber);
        }
        $this->createCommonTemporaryTable($st, $et, $channelIds, $division, $code[0], $conditionCross, $regionId, $dataType, $dataTypeFlags);

        $bindings[':startTimestamp'] = $st->toDateTimeString();
        $bindings[':endTimestamp'] = $et->toDateTimeString();
        $bindChannelIds = $this->createArrayBindParam('channelId', [
            'channelId' => $channelIds,
        ], $bindings);

        $resultQuery = '';
        $resultQuery .= 'WITH list AS ( ';
        $resultQuery .= 'SELECT ';
        $resultQuery .= "  to_char(m.date_time, 'HH24') hhmm ";
        $resultQuery .= "  , EXTRACT(DOW FROM (m.date_time + interval '- 5 hours')) dow ";

        if ($channelType == 'dt2') {
            $resultQuery .= '   , CASE ';

            if ($regionId === 1) {
                $resultQuery .= '      WHEN m.channel_id = 2  ';
                $resultQuery .= '        THEN 2  ';
                $resultQuery .= '      WHEN m.channel_id = 9  ';
                $resultQuery .= '        THEN 9  ';
            } elseif ($regionId === 2) {
                $resultQuery .= '      WHEN m.channel_id = 45  ';
                $resultQuery .= '        THEN 45  ';
            }
            $resultQuery .= '      ELSE 999 END AS channel_id_grp ';
            $resultQuery .= '      , SUM(hr.viewing_rate) AS viewing_rate ';
        } else {
            $resultQuery .= '   , m.channel_id ';
            $resultQuery .= '  , hr.viewing_rate ';
        }
        $resultQuery .= 'FROM ';
        $resultQuery .= '  master m ';
        $resultQuery .= 'LEFT JOIN  ';
        $resultQuery .= '  hv_reports hr ';
        $resultQuery .= 'ON ';
        $resultQuery .= '  m.date_time = hr.datetime ';
        $resultQuery .= '  AND m.channel_id = hr.channel_id ';
        $resultQuery .= '  AND hr.datetime BETWEEN :startTimestamp AND :endTimestamp  ';
        $resultQuery .= '  AND hr.channel_id IN (' . implode(',', $bindChannelIds) . ')  ';
        $resultQuery .= 'WHERE ';
        $resultQuery .= '  m.date_time BETWEEN :startTimestamp AND :endTimestamp  ';
        $resultQuery .= '  AND m.date_time <= :latestDateTime ';

        if ($channelType == 'dt2') {
            $resultQuery .= ' GROUP BY ';
            $resultQuery .= '   date_time, ';
            $resultQuery .= '   hhmm, ';
            $resultQuery .= '   dow, ';
            $resultQuery .= '   channel_id_grp ';
        }
        $resultQuery .= ') ';
        $resultQuery .= 'SELECT ';
        $resultQuery .= '  hhmm ';
        $resultQuery .= '  , dow ';

        if ($channelType == 'dt2') {
            $resultQuery .= '  , channel_id_grp AS channel_id ';
        } else {
            $resultQuery .= '  , channel_id ';
        }
        $resultQuery .= '  , AVG(COALESCE(viewing_rate, 0)) viewing_rate  ';
        $resultQuery .= 'FROM ';
        $resultQuery .= '  list  ';
        $resultQuery .= 'GROUP BY ';
        $resultQuery .= '  hhmm ';
        $resultQuery .= '  , dow ';
        $resultQuery .= '  , channel_id ';
        $resultQuery .= 'ORDER BY ';
        $resultQuery .= '  channel_id ';
        $resultQuery .= '  , dow ';
        $resultQuery .= '  , hhmm ';

        $result = $this->select($resultQuery, $bindings);

        return $result;
    }

    /**
     * @param Carbon $startDateTime
     * @param Carbon $endDateTime
     * @param string $channelType
     * @param array $channelIds
     * @param string $division
     * @param null|array $code
     * @param string $dataDivision
     * @param array $conditionCross
     * @param bool $isOriginal
     * @param int $regionId
     * @param array $dataType
     * @param array $dataTypeFlags
     * @param string $intervalHourly
     * @param string $intervalMinutes
     * @param string $sampleCodePrefix
     * @param string $sampleCodeNumberPrefix
     * @param string $selectedPersonalName
     * @throws \Exception
     * @return array
     */
    public function getConditionCrossShareData(Carbon $startDateTime, Carbon $endDateTime, string $channelType, array $channelIds, string $division, ?array $code, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, array $dataType, array $dataTypeFlags, string $intervalHourly, string $intervalMinutes, string $sampleCodePrefix, string $sampleCodeNumberPrefix, string $selectedPersonalName): array
    {
        $st = new Carbon($startDateTime);
        $et = new Carbon($endDateTime);

        $bindings = [];
        $latestDateTime = $this->getPerHourlyLatestDateTime($regionId, $intervalHourly, $intervalMinutes);
        $bindings[':latestDateTime'] = $latestDateTime;

        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = array_values($dataTypeFlags);

        $isConditionCross = in_array($division, ['condition_cross']);

        if ($isConditionCross) {
            $code = ['condition_cross'];
        }
        $timeBoxIds = $this->createTimeBoxListWhere($st->toDateTimeString(), $et->toDateTimeString(), $regionId);
        $hasSelectedPersonal = false;
        $codeNumber = 1;

        if ($isRt || $isGross || $isRtTotal) {
            $this->createRtSampleTempTable($isConditionCross, $conditionCross, $division, $code, $timeBoxIds, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName, $hasSelectedPersonal, $codeNumber);
        }

        if ($isTs || $isGross || $isRtTotal) {
            $this->createTsSampleTempTable($isConditionCross, $conditionCross, $division, $code, $timeBoxIds, $regionId, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName, $hasSelectedPersonal, $codeNumber);
        }
        $this->createCommonTemporaryTable($st, $et, $channelIds, $division, $code[0], $conditionCross, $regionId, $dataType, $dataTypeFlags);

        $bindings[':startTimestamp'] = $st->toDateTimeString();
        $bindings[':endTimestamp'] = $et->toDateTimeString();

        $query = '';
        $query .= 'WITH total_time_list AS ( ';
        $query .= 'SELECT ';
        $query .= '  hr.datetime AS datetime, ';
        $query .= ' SUM(COALESCE(viewing_seconds, 0))::real AS total_time ';
        $query .= 'FROM ';
        $query .= '  hv_reports hr ';
        $query .= 'GROUP BY ';
        $query .= '  hr.datetime ),';
        $query .= 'list AS ( ';
        $query .= '  SELECT ';
        $query .= "    to_char(m.date_time, 'HH24') hhmm ";
        $query .= "    , EXTRACT(DOW FROM (m.date_time + interval '- 5 hours')) dow ";
        $query .= '    , m.channel_id ';
        $query .= '    , COALESCE(hr.viewing_seconds, 0) AS viewing_seconds ';
        $query .= '    , tl.total_time AS total_time ';
        $query .= '    , hr.viewing_seconds::numeric / CASE WHEN tl.total_time = 0 THEN 1 ELSE tl.total_time::real END::numeric * 100 AS share ';
        $query .= '  FROM ';
        $query .= '    master m ';
        $query .= '  LEFT JOIN hv_reports hr ON ';
        $query .= '    m.date_time = hr.datetime ';
        $query .= '    AND m.channel_id = hr.channel_id ';
        $query .= '  LEFT JOIN total_time_list tl ON ';
        $query .= '     m.date_time = tl.datetime ';
        $query .= ' WHERE ';
        $query .= '   m.date_time BETWEEN :startTimestamp AND :endTimestamp  ';
        $query .= '   AND m.date_time <= :latestDateTime ';
        $query .= ' )  ';
        $query .= 'SELECT ';
        $query .= '  hhmm , ';
        $query .= '  dow , ';
        $query .= '  channel_id, ';
        $query .= '  AVG(COALESCE(share, 0)) as share ';
        $query .= 'FROM ';
        $query .= '  list ';
        $query .= 'GROUP BY ';
        $query .= '  hhmm , ';
        $query .= '  dow , ';
        $query .= '  channel_id ';
        $query .= 'ORDER BY ';
        $query .= '  channel_id , ';
        $query .= '  dow , ';
        $query .= '  hhmm ';
        $query .= ';';
        $result = $this->select($query, $bindings);
        return $result;
    }

    /**
     * @param Carbon $startDateTime
     * @param Carbon $endDateTime
     * @param string $channelType
     * @param array $channelIds
     * @param string $division
     * @param null|array $code
     * @param string $dataDivision
     * @param array $conditionCross
     * @param bool $isOriginal
     * @param int $regionId
     * @param array $dataType
     * @param array $dataTypeFlags
     * @param string $intervalHourly
     * @param string $intervalMinutes
     * @param string $sampleCodePrefix
     * @param string $sampleCodeNumberPrefix
     * @param string $selectedPersonalName
     * @throws \Exception
     * @return array
     */
    public function getConditionCrossTargetData(Carbon $startDateTime, Carbon $endDateTime, string $channelType, array $channelIds, string $division, ?array $code, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, array $dataType, array $dataTypeFlags, string $intervalHourly, string $intervalMinutes, string $sampleCodePrefix, string $sampleCodeNumberPrefix, string $selectedPersonalName): array
    {
        $st = new Carbon($startDateTime);
        $et = new Carbon($endDateTime);

        $bindings = [];
        $latestDateTime = $this->getPerHourlyLatestDateTime($regionId, $intervalHourly, $intervalMinutes);
        $bindings[':latestDateTime'] = $latestDateTime;

        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = array_values($dataTypeFlags);

        $isConditionCross = in_array($division, ['condition_cross']);

        if ($isConditionCross) {
            $code = ['condition_cross'];
        }
        $timeBoxIds = $this->createTimeBoxListWhere($st->toDateTimeString(), $et->toDateTimeString(), $regionId);
        $hasSelectedPersonal = false;
        $codeNumber = 1;

        if ($isRt || $isGross || $isRtTotal) {
            $this->createRtSampleTempTable($isConditionCross, $conditionCross, $division, $code, $timeBoxIds, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName, $hasSelectedPersonal, $codeNumber);
        }

        if ($isTs || $isGross || $isRtTotal) {
            $this->createTsSampleTempTable($isConditionCross, $conditionCross, $division, $code, $timeBoxIds, $regionId, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName, $hasSelectedPersonal, $codeNumber);
        }
        $this->createCommonTemporaryTable($st, $et, $channelIds, $division, $code[0], $conditionCross, $regionId, $dataType, $dataTypeFlags);

        $bindings[':startTimestamp'] = $st->toDateTimeString();
        $bindings[':endTimestamp'] = $et->toDateTimeString();

        $bindChannelIds = $this->createArrayBindParam('channelId', [
            'channelId' => $channelIds,
        ], $bindings);

        $rtHourlyTable = '';
        $tsHourlyTable = '';
        $householdViewingRate = '';

        if ($isRt) {
            $rtHourlyTable = 'hourly_reports';
            $tsHourlyTable = '';
            $householdViewingRate = 'rt.viewing_rate';
        }

        if ($isTs) {
            $tsHourlyTable = 'ts_hourly_reports';
            $rtHourlyTable = '';
            $householdViewingRate = 'ts.viewing_rate';
        }

        if ($isGross) {
            $rtHourlyTable = 'hourly_reports';
            $tsHourlyTable = 'ts_hourly_reports';
            $householdViewingRate = 'COALESCE(ts.gross_viewing_rate, rt.ts_samples_viewing_rate)';
        }

        if ($isRtTotal) {
            $rtHourlyTable = 'hourly_reports';
            $tsHourlyTable = 'ts_hourly_reports';
            $householdViewingRate = 'COALESCE(ts.rt_total_viewing_rate, rt.ts_samples_viewing_rate)';
        }

        $query = '';
        $query .= 'WITH list AS ( ';
        $query .= 'SELECT ';
        $query .= "  to_char(m.date_time, 'HH24') hhmm ";
        $query .= "  , EXTRACT(DOW FROM (m.date_time + interval '- 5 hours')) dow ";
        $query .= '  , m.channel_id ';
        $query .= "  , ${householdViewingRate} AS household_viewing_rate ";
        $query .= "  , hr.viewing_rate / ${householdViewingRate} * 100 AS viewing_rate  ";
        $query .= 'FROM ';
        $query .= '  master m ';
        $query .= 'LEFT JOIN  ';
        $query .= '  hv_reports hr ';
        $query .= 'ON ';
        $query .= '  m.date_time = hr.datetime ';
        $query .= '  AND m.channel_id = hr.channel_id ';
        $query .= '  AND hr.datetime BETWEEN :startTimestamp AND :endTimestamp ';
        $query .= '  AND hr.channel_id IN (' . implode(',', $bindChannelIds) . ') ';

        if (!empty($rtHourlyTable)) {
            $query .= 'LEFT JOIN  ';
            $query .= "  ${rtHourlyTable} rt ";
            $query .= 'ON ';
            $query .= '  m.date_time = rt.datetime ';
            $query .= '  AND m.channel_id = rt.channel_id ';

            if ($dataDivision === 'target_content_personal') {
                $query .= "  AND rt.division = 'personal' ";
            } elseif ($dataDivision === 'target_content_household') {
                $query .= "  AND rt.division = 'household' ";
            }
            $query .= "  AND rt.code = '1'  ";
            $query .= '  AND rt.datetime BETWEEN :startTimestamp AND :endTimestamp  ';
            $query .= '  AND rt.channel_id IN (' . implode(',', $bindChannelIds) . ') ';
        }

        if (!empty($tsHourlyTable)) {
            $query .= 'LEFT JOIN  ';
            $query .= "  ${tsHourlyTable} ts ";
            $query .= 'ON ';
            $query .= '  m.date_time = ts.datetime ';
            $query .= '  AND m.channel_id = ts.channel_id ';

            if ($isRtTotal) {
                $query .= "  AND ts.division = 'personal' ";
            } else {
                $query .= "  AND ts.division = 'household' ";
            }
            $query .= "  AND ts.code = '1'  ";
            $query .= '  AND ts.datetime BETWEEN :startTimestamp AND :endTimestamp  ';
            $query .= '  AND ts.channel_id IN (' . implode(',', $bindChannelIds) . ') ';
        }
        $query .= 'WHERE ';
        $query .= '  m.date_time BETWEEN :startTimestamp AND :endTimestamp ';
        $query .= '  AND m.date_time <= :latestDateTime ';
        $query .= ') ';
        $query .= 'SELECT ';
        $query .= '  hhmm ';
        $query .= '  , dow ';
        $query .= '  , channel_id ';
        $query .= '  , AVG(COALESCE(viewing_rate, 0)) AS target_viewing_rate ';
        $query .= 'FROM ';
        $query .= '  list  ';
        $query .= 'GROUP BY ';
        $query .= '  hhmm ';
        $query .= '  , dow ';
        $query .= '  , channel_id  ';
        $query .= 'ORDER BY ';
        $query .= '  channel_id ';
        $query .= '  , dow ';
        $query .= '  , hhmm ';
        $query .= ';';

        $result = $this->select($query, $bindings);

        return $result;
    }

    /**
     * @param Carbon $startDateTime
     * @param array $channelIds
     * @param int $regionId
     */
    private function createCommonMasterTemporaryTable(Carbon $startDateTime, array $channelIds, int $regionId): void
    {
        $bindings = [];
        $bindings[':startTimestamp'] = $startDateTime->toDateTimeString();
        $bindChannelIds = $this->createArrayBindParam('channelId', [
            'channelId' => $channelIds,
        ], $bindings);

        $sql = '';
        $sql .= ' CREATE TEMPORARY TABLE IF NOT EXISTS master ( ';
        $sql .= '   date_time TIMESTAMP ';
        $sql .= '   , hour NUMERIC ';
        $sql .= '   , channel_id INT ';
        $sql .= '   , time_box_id INT ';
        $sql .= ' ) DISTSTYLE ALL; ';
        $this->select($sql);

        $sql = '';
        $sql .= 'SELECT EXISTS(SELECT * FROM master) as has_record';
        $result = $this->selectOne($sql);

        if (!empty($result->has_record)) {
            return;
        }
        $bindings['regionId'] = $regionId;
        $query = '';
        $query .= ' INSERT INTO master WITH numbers AS ( ';
        $query .= ' SELECT 0 seq UNION SELECT 1 seq UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9  ';
        $query .= ' ), seq AS ( ';
        $query .= ' SELECT  ';
        $query .= '   ROW_NUMBER() OVER() - 1 sq ';
        $query .= ' FROM  ';
        $query .= '  numbers t1 CROSS JOIN numbers t2 CROSS JOIN numbers t3 CROSS JOIN numbers t4 ';
        $query .= ' ), date_list AS ( ';
        $query .= ' SELECT ';
        $query .= "   :startTimestamp::timestamp without time zone  + interval '1hours' * sq date_time ";
        $query .= ' FROM ';
        $query .= '   seq ';
        $query .= ' ) ';
        $query .= ' SELECT ';
        $query .= '   date_time, ';
        $query .= "   TO_CHAR(date_time - interval '5 hours','HH24')::numeric + 5 AS hour, ";
        $query .= '   channel.id channel_id,  ';
        $query .= '   tb.id time_box_id  ';
        $query .= ' FROM  ';
        $query .= '   date_list dl ';
        $query .= ' INNER JOIN ';
        $query .= '   time_boxes tb ';
        $query .= ' ON dl.date_time >= tb.started_at ';
        $query .= ' AND dl.date_time < tb.ended_at ';
        $query .= ' AND region_id = :regionId ';
        $query .= ' CROSS JOIN ';
        $tmpArr = [];

        foreach ($bindChannelIds as $key => $val) {
            $tmpArr[] = " SELECT ${val}::numeric id ";
        }
        $query .= '( ';
        $query .= implode(' UNION ALL ', $tmpArr);
        $query .= ') channel ';

        $this->select($query, $bindings);
    }

    /**
     * @param Carbon $startDateTime
     * @param Carbon $endDateTime
     * @param array $channelIds
     * @param string $division
     * @param string $code
     * @param array $conditionCross
     * @param int $regionId
     * @param array $dataType
     * @param array $dataTypeFlags
     */
    private function createCommonTemporaryTable(Carbon $startDateTime, Carbon $endDateTime, array $channelIds, string $division, string $code, array $conditionCross, int $regionId, array $dataType, array $dataTypeFlags): void
    {
        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = array_values($dataTypeFlags);

        $this->createCommonMasterTemporaryTable($startDateTime, $channelIds, $regionId);

        $bindings = [];
        $bindings[':startTimestamp'] = $startDateTime->toDateTimeString();
        $bindings[':endTimestamp'] = $endDateTime->toDateTimeString();
        $bindChannelIds = $this->createArrayBindParam('channelId', [
            'channelId' => $channelIds,
        ], $bindings);
        $bindings[':regionId'] = $regionId;

        $table = '';
        $samples = '';
        $numbers = '';

        if ($isRt || $isGross || $isRtTotal) {
            $query = '';
            $query .= 'CREATE TEMPORARY TABLE IF NOT EXISTS';
            $query .= '    rt_hv_list  ( ';
            $query .= '      datetime TIMESTAMP , ';
            $query .= '      channel_id INT4 , ';
            $query .= '      paneler_id INT4 , ';
            $query .= '      viewing_seconds INT4 ,';
            $query .= '      gross_viewing_seconds INT4 , ';
            $query .= '      gross_flag INT2 ';
            $query .= '    ) DISTSTYLE ALL SORTKEY ( paneler_id );';
            $this->select($query);

            $query = '';
            $query .= 'SELECT EXISTS(SELECT * FROM rt_hv_list) as has_record';
            $result = $this->selectOne($query);

            if (!empty($result->has_record)) {
                return;
            }

            $query = '';
            $query .= 'INSERT INTO rt_hv_list ';
            $query .= '  SELECT ';
            $query .= '    hv.datetime, ';
            $query .= '    hv.channel_id, ';
            $query .= '    hv.paneler_id, ';
            $query .= '    hv.viewing_seconds, ';
            $query .= '    0 AS gross_viewing_seconds, ';

            if ($isRtTotal) {
                $query .= '  0 AS gross_flag ';
            } else {
                $query .= '  1 AS gross_flag ';
            }
            $query .= '  FROM ';
            $query .= '    hourly_viewers hv ';
            $query .= '  WHERE ';
            $query .= '    datetime BETWEEN :startTimestamp AND :endTimestamp ';
            $query .= '    AND channel_id IN (' . implode(',', $bindChannelIds) . ') ';
            $query .= '    AND hv.region_id = :regionId ';
            $this->insertTemporaryTable($query, $bindings);

            $table = 'rt_hv_list';
            $samples = 'samples';
            $numbers = 'rt_numbers';
        }

        if ($isTs || $isGross || $isRtTotal) {
            $query = '';
            $query .= 'CREATE TEMPORARY TABLE IF NOT EXISTS';
            $query .= '    ts_hv_list  ( ';
            $query .= '      datetime TIMESTAMP , ';
            $query .= '      channel_id INT4 , ';
            $query .= '      paneler_id INT4 , ';
            $query .= '      viewing_seconds INT4 ,';
            $query .= '      total_viewing_seconds INT4 ,';
            $query .= '      gross_viewing_seconds INT4 , ';
            $query .= '      gross_flag INT2 ';
            $query .= '    ) DISTSTYLE ALL SORTKEY ( paneler_id );';
            $this->select($query);

            $query = '';
            $query .= 'SELECT EXISTS(SELECT * FROM ts_hv_list) as has_record';
            $result = $this->selectOne($query);

            if (!empty($result->has_record)) {
                return;
            }

            $query = '';
            $query .= 'INSERT INTO ts_hv_list ';
            $query .= '  SELECT ';
            $query .= '    hv.datetime, ';
            $query .= '    hv.channel_id, ';
            $query .= '    hv.paneler_id, ';
            $query .= '    hv.viewing_seconds, ';
            $query .= '    hv.total_viewing_seconds, ';
            $query .= '    hv.gross_viewing_seconds, ';

            if ($isGross) {
                $query .= '  2 AS gross_flag ';
            } elseif ($isRtTotal) {
                $query .= '  0 AS gross_flag ';
            } else {
                $query .= '  1 AS gross_flag ';
            }
            $query .= '  FROM ';
            $query .= '    ts_hourly_viewers hv ';
            $query .= '  WHERE ';
            $query .= '    datetime BETWEEN :startTimestamp AND :endTimestamp ';
            $query .= '    AND channel_id IN (' . implode(',', $bindChannelIds) . ') ';
            $query .= '    AND hv.region_id = :regionId ';
            $this->insertTemporaryTable($query, $bindings);

            $table = 'ts_hv_list';
            $samples = 'ts_samples';
            $numbers = 'ts_numbers';
        }

        if ($isGross || $isRtTotal) {
            $query = '';
            $query .= 'CREATE ';
            $query .= '  TEMPORARY TABLE ';
            $query .= '    union_hv_list  ( ';
            $query .= '      datetime TIMESTAMP , ';
            $query .= '      channel_id INT4 , ';
            $query .= '      paneler_id INT4 , ';
            $query .= '      viewing_seconds INT4 ,';
            $query .= '      gross_viewing_seconds INT4 , ';
            $query .= '      gross_flag INT2 ';
            $query .= '    ) DISTSTYLE ALL SORTKEY ( paneler_id );';
            $this->select($query);

            $query = '';
            $query .= 'INSERT INTO union_hv_list';
            $query .= '  SELECT';
            $query .= '      hv.datetime,';
            $query .= '      hv.channel_id,';
            $query .= '      hv.paneler_id,';
            $query .= '      hv.viewing_seconds,';
            $query .= '      hv.gross_viewing_seconds,';
            $query .= '      hv.gross_flag';
            $query .= '    FROM';
            $query .= '      rt_hv_list hv';
            $query .= '  UNION ALL SELECT';
            $query .= '      hv.datetime,';
            $query .= '      hv.channel_id,';
            $query .= '      hv.paneler_id,';

            if ($isRtTotal) {
                $query .= '      hv.total_viewing_seconds viewing_seconds,';
            } else {
                $query .= '      hv.viewing_seconds,';
            }
            $query .= '      hv.gross_viewing_seconds,';
            $query .= '      hv.gross_flag';
            $query .= '    FROM';
            $query .= '      ts_hv_list hv ;';
            $this->insertTemporaryTable($query);

            $query = '';
            $query .= 'ANALYZE union_hv_list;';
            $this->select($query);

            $table = 'union_hv_list';
            $samples = 'ts_samples';
            $numbers = 'ts_numbers';
        }

        $bindings = [];
        $bindings[':regionId'] = $regionId;
        $query = '';
        $query .= 'CREATE TEMPORARY TABLE ';
        $query .= '  hv_list AS SELECT ';
        $query .= '    t.datetime, ';
        $query .= '    t.channel_id, ';
        $query .= '    t.paneler_id, ';
        $query .= '    CASE ';
        $query .= '      WHEN SUM(gross_flag) <= 1 THEN SUM(viewing_seconds) ';
        $query .= '      WHEN SUM(gross_flag) > 1 THEN SUM(gross_viewing_seconds) ';
        $query .= '      ELSE 0 ';
        $query .= '    END AS viewing_seconds, ';
        $query .= "    (select number00 from ${numbers} tn WHERE tn.time_box_id = tb.id) number, ";
        $query .= '    tb.id AS time_box_id ';
        $query .= '  FROM  ';
        $query .= "    ${table} t  ";
        $query .= '    INNER JOIN time_boxes tb ON t.datetime >= tb.started_at and t.datetime < tb.ended_at and tb.region_id = :regionId ';
        $query .= '  WHERE  ';
        $query .= '    EXISTS ';
        $query .= "      (select 1 from ${samples} ts  ";
        $query .= '        WHERE ts.time_box_id = tb.id  ';
        $query .= '          AND ts.code00 = 1 ';
        $query .= '          AND ts.paneler_id = t.paneler_id) ';
        $query .= '  GROUP BY  ';
        $query .= '    t.datetime, ';
        $query .= '    t.channel_id, ';
        $query .= '    t.paneler_id, ';
        $query .= '    tb.id; ';
        $this->select($query, $bindings);

        $query = '';
        $query .= ' CREATE TEMPORARY TABLE hv_reports AS SELECT ';
        $query .= '  SUM(hl.viewing_seconds) viewing_seconds , ';
        $query .= '  SUM(hl.viewing_seconds)::real / (hl.number * 60 * 60) * 100 viewing_rate , ';
        $query .= '  hl.datetime , ';
        $query .= '  hl.channel_id , ';
        $query .= '  hl.time_box_id ';
        $query .= 'FROM ';
        $query .= '  hv_list hl ';
        $query .= 'GROUP BY ';
        $query .= '  hl.number, ';
        $query .= '  hl.datetime , ';
        $query .= '  hl.channel_id , ';
        $query .= '  hl.time_box_id ';

        $this->select($query);
    }
}
