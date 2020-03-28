<?php

namespace Switchm\SmartApi\Queries\Dao\Rdb;

use Carbon\Carbon;

class TopDao extends Dao
{
    public function findHourViewingRate(string $date, int $regionId, array $channelIds): array
    {
        $bindings[':date_to'] = $date;
        $fromDate = new Carbon($date);
        $bindings[':date_from'] = $fromDate->subHour(2)->toDateTimeString();
        $bindings[':regionId'] = $regionId;
        $bindArrNames = $this->createArrayBindParam('channelIds', ['channelIds' => $channelIds], $bindings);

        $crossJoin = [];

        foreach ($channelIds as $id) {
            array_push($crossJoin, "SELECT ${id} as channel_id ");
        }

        $query =
            'SELECT ' .
            '  master.date ' .
            ', master.hour ' .
            ', master.minute ' .
            ", EXTRACT(EPOCH FROM master.datetime::timestamptz) || '000' datetime " .
            ', master.channel_id ' .
            ', COALESCE(round(pmr.viewing_rate::numeric, 1), 0) AS                 viewing_rate ' .
            'FROM ' .
            '  ( ' .
            '    SELECT ' .
            "      datetime - interval'5 hours' as date " .
            "      , DATE_PART('hour', datetime - interval '5hours') + 5 as hour " .
            "      , DATE_PART('minute', datetime) as minute " .
            '      , datetime ' .
            '      , channel_id ' .
            '    FROM ' .
            "      (SELECT datetime FROM generate_series(:date_from,:date_to, interval '1minute') as datetime ) datetime " .
            '      CROSS JOIN ' .
            '      (' . implode(' UNION ALL ', $crossJoin) . ') channels ' .
            '  ) as master ' .
            'LEFT JOIN ' .
            '  per_minute_reports pmr ' .
            'ON ' .
            '  master.datetime = pmr.datetime ' .
            'AND master.channel_id = pmr.channel_id ' .
            'AND pmr.datetime BETWEEN :date_from AND :date_to ' .
            'AND pmr.channel_id IN (' . implode(',', $bindArrNames) . ') ' .
            'AND pmr.time_box_id IN (SELECT id FROM time_boxes tb WHERE tb.region_id = :regionId) ' .
            "AND pmr.division = 'household' " .
            'ORDER BY master.date DESC, master.hour DESC, master.minute DESC, master.channel_id ' .
            'LIMIT 360 ';

        $result = $this->select($query, $bindings);
        return json_decode(json_encode($result), true);
    }

    public function findLatestPrograms($date, $regionId): array
    {
        $bindings[':date'] = $date;
        $bindings[':regionId'] = $regionId;

        $query =
            'SELECT ' .
            '   p.channel_id ' .
            '   , p.code_name ' .
            '   , p.title ' .
            'FROM ' .
            '( ' .
            '   select ' .
            '       pr.channel_id ' .
            '       ,ch.code_name ' .
            '       ,pr.title ' .
            '       ,pr.started_at ' .
            '       ,RANK() OVER (PARTITION BY channel_id ORDER BY started_at DESC) rank ' .
            '   from programs pr left outer join channels ch on ' .
            '       pr.channel_id = ch.id ' .
            '   where :date between pr.started_at and pr.ended_at ' .
            '       and pr.time_box_id IN (SELECT id FROM time_boxes tb WHERE tb.region_id = :regionId) ' .
            '       and pr.channel_id in (1,3,4,5,6,7) ' .
            ') p ' .
            'WHERE' .
            '   p.rank = 1' .
            'order by p.channel_id asc, p.started_at desc ' .
            'limit 60 ';
        $result = $this->select($query, $bindings);

        return json_decode(json_encode($result), true);
    }

    public function getTerms(int $regionId)
    {
        $query = ' SELECT * FROM time_keepers WHERE region_id = :regionId';
        $bindings[':regionId'] = $regionId;
        return $this->select($query, $bindings);
    }
}
