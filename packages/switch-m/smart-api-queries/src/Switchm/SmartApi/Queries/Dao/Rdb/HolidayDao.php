<?php

namespace Switchm\SmartApi\Queries\Dao\Rdb;

use Carbon\Carbon;

class HolidayDao extends Dao
{
    public function findHoliday(Carbon $start, Carbon $end): array
    {
        $bindings = [];

        $bindings[':start'] = $start;
        $bindings[':end'] = $end;

        $query = '';
        $query .= ' SELECT ';
        $query .= '   holiday ';
        $query .= ' FROM ';
        $query .= '   holidays h ';
        $query .= ' WHERE ';
        $query .= '   h.holiday BETWEEN :start AND :end ';

        return $this->select($query, $bindings);
    }
}
