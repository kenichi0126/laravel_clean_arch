<?php

namespace Switchm\SmartApi\Queries\Dao\Rdb;

class TimeBoxDao extends Dao
{
    public function latest(int $regionId): ?\stdClass
    {
        $bindings[':region_id'] = $regionId;

        $query = '';
        $query .= ' SELECT  ';
        $query .= '     tb.id, ';
        $query .= '     tb.region_id, ';
        $query .= '     tb.start_date, ';
        $query .= '     tb.duration, ';
        $query .= '     tb.version, ';
        $query .= '     tb.started_at, ';
        $query .= '     tb.ended_at, ';
        $query .= '     tb.panelers_number, ';
        $query .= '     tb.households_number  ';
        $query .= ' FROM  ';
        $query .= '     time_boxes tb ';
        $query .= ' WHERE ';
        $query .= '     tb.region_id = :region_id ';
        $query .= ' ORDER BY ';
        $query .= '     tb.start_date DESC  ';
        $query .= ' limit 1; ';

        return $this->selectOne($query, $bindings);
    }

    /**
     * パネラー数取得.
     * @param string $timeBoxId
     */
    public function getNumber(String $timeBoxId): ?\stdClass
    {
        $bindings = [];
        $bindings[':time_box_id'] = $timeBoxId;

        $select = '';
        $select .= 'tb.panelers_number, ';
        $select .= 'tb.households_number ';

        $where = '';
        $where .= 'tb.id = :time_box_id ';

        $query = sprintf(
            'SELECT %s FROM time_boxes tb WHERE %s;',
            $select,
            $where
        );

        return $this->selectOne($query, $bindings);
    }

    /**
     * タイムボックスID取得.
     * @param string $startDate
     * @param string $regionId
     */
    public function getTimeBoxId(
        String $startDate,
        String $regionId
    ): ?\stdClass {
        $bindings = [];
        $bindings[':start_date'] = $startDate;
        $bindings[':region_id'] = $regionId;

        $select = '';
        $select .= 'tb.id ';

        $where = '';
        $where .= 'tb.start_date <= :start_date ';
        $where .= 'AND tb.region_id = :region_id ';

        $orderBy = '';
        $orderBy .= 'tb.start_date DESC ';

        $query = sprintf(
            'SELECT %s FROM time_boxes tb WHERE %s ORDER BY %s;',
            $select,
            $where,
            $orderBy
        );

        return $this->selectOne($query, $bindings);
    }
}
