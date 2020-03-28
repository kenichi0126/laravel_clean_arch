<?php

namespace Switchm\SmartApi\Queries\Dao\Rdb;

class HourlyReportDao extends Dao
{
    public function latest(int $regionId): ?\stdClass
    {
        $bindings = [];

        // 地域コード
        $bindings[':regionId'] = $regionId;

        $query = '';
        $query .= ' SELECT  ';
        $query .= '     MAX(hr.datetime) datetime  ';
        $query .= ' FROM  ';
        $query .= '     hourly_reports hr ';
        $query .= ' WHERE ';
        $query .= '     hr.time_box_id in (SELECT MAX(id) FROM time_boxes WHERE region_id = :regionId) ';
        $query .= ' limit 1; ';

        return $this->selectOne($query, $bindings);
    }
}
