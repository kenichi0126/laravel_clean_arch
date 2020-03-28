<?php

namespace Switchm\SmartApi\Queries\Dao\Rdb;

use stdClass;

class SampleDao extends Dao
{
    /**
     * @param array $conditionCross
     * @param string $startDate
     * @param string $endDate
     * @param int $regionId
     * @param bool $isRt
     * @return stdClass
     */
    public function getCrossConditionCount(array $conditionCross, string $startDate, string $endDate, int $regionId, bool $isRt = true): stdClass
    {
        $bindings = [];

        $bindings[':startDate'] = $startDate;
        $bindings[':endDate'] = $endDate;

        $bindings[':regionId'] = $regionId;

        $query = '';
        $query .= ' SELECT ';
        $query .= '   COUNT(*) cnt';
        $query .= ' FROM ';

        if ($isRt) {
            $query .= '   time_box_panelers tbp ';
        } else {
            $query .= '   ts_time_box_panelers tbp ';
        }
        $query .= ' WHERE ';
        $query .= '   tbp.time_box_id IN( ';
        $query .= '     SELECT ';
        $query .= '       MAX(tb.id) ';
        $query .= '     FROM ';
        $query .= '       time_boxes tb ';
        $query .= '     WHERE ';
        $query .= "     (:startDate <= ended_at - interval '5 hours' AND started_at - interval '5 hours' <= :endDate ) AND region_id = :regionId ";
        $query .= $this->createConditionCrossSql($conditionCross, $bindings);
        $query .= '     ) ';

        return $this->selectOne($query, $bindings);
    }
}
