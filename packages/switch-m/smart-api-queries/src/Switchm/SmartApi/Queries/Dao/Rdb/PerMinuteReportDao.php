<?php

namespace Switchm\SmartApi\Queries\Dao\Rdb;

use Carbon\Carbon;

class PerMinuteReportDao extends Dao
{
    /**
     * 視聴秒数取得.
     * @param array $sceneList
     * @param string $channelId
     */
    public function getSeconds(
        array $sceneList,
        String $channelId
    ): ?array {
        $bindings = [];
        $bindings[':channel_id'] = $channelId;

        $groupSelectArr = [];

        $startDate = [];
        $endDate = [];

        $with = '';

        foreach ($sceneList as $index => $row) {
            // 検索範囲が同じ場合は不等号を変更する
            $inequality = '<';

            if ($row['startDateTime'] === $row['endDateTime']) {
                $inequality = '<=';
            }

            $keyFrom = ':keyFrom' . $index;
            $keyTo = ':keyTo' . $index;
            $keyIndex = ':keyIndex' . $index;

            $startDate[] = $bindings[$keyFrom] = $row['startDateTime'];
            $endDate[] = $bindings[$keyTo] = $row['endDateTime'];
            $bindings[$keyIndex] = $index;

            array_push($groupSelectArr, " SELECT ${keyIndex}::VARCHAR(255) as key, ${keyFrom}::VARCHAR(255) as from, ${keyTo}::VARCHAR(255) as to ");
        }

        $groupSelect = implode(' UNION ALL ', $groupSelectArr);

        $with = ' WITH dates AS ( ';
        $with .= $groupSelect;
        $with .= ' ) ';
        $select = '';
        $select .= ' d.key time_group ';
        $select .= ',sum(pmr.viewing_seconds) as seconds ';
        $select .= ',division  ';
        $select .= ',code  ';
        $select .= ' FROM ';
        $select .= '    per_minute_reports pmr ';
        $select .= ' INNER JOIN ';
        $select .= '    dates d ';
        $select .= ' ON ';
        $select .= "    (d.from = d.to AND TO_TIMESTAMP(d.from, 'YYYY-MM-DD HH24:MI:SS') <= pmr.datetime AND pmr.datetime <= TO_TIMESTAMP(d.to,'YYYY-MM-DD HH24:MI:SS')) OR ";
        $select .= "    (d.from < d.to AND TO_TIMESTAMP(d.from, 'YYYY-MM-DD HH24:MI:SS') <= pmr.datetime AND pmr.datetime < TO_TIMESTAMP(d.to,'YYYY-MM-DD HH24:MI:SS')) ";

        $bindings[':minDate'] = min($startDate);
        $bindings[':maxDate'] = max($endDate);
        $where = '';
        $whereArr = [];
        $where .= ' pmr.datetime BETWEEN :minDate AND :maxDate ';
        $where .= 'AND pmr.channel_id = :channel_id ';
        $where .= "AND pmr.division in ('personal', 'household') ";
        $where .= "AND pmr.code = '1' ";

        $groupBy = ' d.key, division, code ';

        $query = sprintf(
            '%s SELECT %s WHERE %s GROUP BY %s ;',
            $with,
            $select,
            $where,
            $groupBy
        );

        return $this->select($query, $bindings);
    }

    /**
     * 番組情報詳細用視聴率情報取得.
     * @param array $timeBoxIds
     * @param array $dates
     * @param array $hours
     * @param array $minutes
     * @param string $channelId
     * @param string $division
     */
    public function getTableDetailReport(
        array $timeBoxIds,
        array $dates,
        array $hours,
        array $minutes,
        String $channelId,
        String $division
    ): array {
        $bindings = [];
        $bindings[':channel_id'] = $channelId;
        $bindings[':division'] = $division;

        $timeBoxIdsBind = $this->createArrayBindParam('timeBoxIds', ['timeBoxIds' => $timeBoxIds], $bindings);
        $datesBind = $this->createArrayBindParam('dates', ['dates' => $dates], $bindings);
        $hoursBind = $this->createArrayBindParam('hours', ['hours' => $hours], $bindings);
        $minutesBind = $this->createArrayBindParam('minutes', ['minutes' => $minutes], $bindings);

        $minDate = new Carbon(min($dates));
        $maxDate = new Carbon(max($dates));

        $bindings[':minDate'] = $minDate->subDay(1);
        $bindings[':maxDate'] = $maxDate->addDay(1)->hour(23)->minute(59);

        $select = '';
        $select .= "to_char(date,'YYYY-MM-DD')||' '||to_char(hour,'FM00')||':'||to_char(minute,'FM00') as concatdate,";
        $select .= 'viewing_rate as rate ';
        $select .= ',division, code ';

        $where = '';
        $where .= 'time_box_id IN (' . implode(',', $timeBoxIdsBind) . ') ';
        $where .= 'AND datetime BETWEEN :minDate AND :maxDate ';
        $where .= 'AND date IN (' . implode(',', $datesBind) . ') ';
        $where .= 'AND hour IN (' . implode(',', $hoursBind) . ') ';
        $where .= 'AND minute IN (' . implode(',', $minutesBind) . ') ';
        $where .= 'AND channel_id = :channel_id ';
        $where .= "AND (division = :division OR division = 'personal' OR division = 'household' )";

        $orderBy = '';
        $orderBy .= 'date ASC, ';
        $orderBy .= 'hour ASC, ';
        $orderBy .= 'minute ASC ';

        $query = sprintf(
            'SELECT %s FROM per_minute_reports pmr WHERE %s ORDER BY %s;',
            $select,
            $where,
            $orderBy
        );

        return $this->select($query, $bindings);
    }

    public function latest(int $regionId): ?\stdClass
    {
        $bindings = [];

        // 地域コード
        $bindings[':regionId'] = $regionId;

        $query = '';
        $query .= ' WITH pmr AS (  ';
        $query .= '   SELECT ';
        $query .= '     pmr.datetime datetime ';
        $query .= "     , EXTRACT(DOW FROM pmr.datetime - interval '5 hours') AS dow ";
        $query .= '     , pmr.hour ';
        $query .= '     , time_box_id ';
        $query .= '     , date ';
        $query .= '     , minute  ';
        $query .= '   FROM ';
        $query .= '     per_minute_reports pmr  ';
        $query .= '   WHERE ';
        $query .= '     pmr.time_box_id IN (  ';
        $query .= '       SELECT ';
        $query .= '         MAX(id) id  ';
        $query .= '       FROM ';
        $query .= '         time_boxes  ';
        $query .= '       WHERE ';
        $query .= '         region_id = :regionId ';
        $query .= '     ) ';
        $query .= ' )  ';
        $query .= ' SELECT ';
        $query .= '   *  ';
        $query .= ' FROM ';
        $query .= '   pmr  ';
        $query .= ' ORDER BY ';
        $query .= '   time_box_id DESC ';
        $query .= '   , date DESC ';
        $query .= '   , hour DESC ';
        $query .= '   , minute DESC  ';
        $query .= ' limit ';
        $query .= '   1;  ';

        return $this->selectOne($query, $bindings);
    }
}
