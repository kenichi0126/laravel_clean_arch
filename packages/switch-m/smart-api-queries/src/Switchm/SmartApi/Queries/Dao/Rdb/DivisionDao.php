<?php

namespace Switchm\SmartApi\Queries\Dao\Rdb;

class DivisionDao extends Dao
{
    /**
     * @param array $divisions
     * @return array
     */
    public function find(array $divisions): array
    {
        $bindings = [];

        $query = '';
        $query .= ' WITH divisions AS( ';
        $query .= ' SELECT ';
        $query .= '   LOWER(division) division ';
        $query .= '   , code ';
        $query .= '   , name ';
        $query .= '   , display_order ';
        $query .= ' FROM ';
        $query .= '   attr_divs  ';
        $query .= ' WHERE ';

        $keyArr = [];

        foreach ($divisions as $key => $val) {
            $keyName = ':division' . $key;
            $bindings[$keyName] = strtolower($val);
            $keyArr[] = $keyName;
        }
        $query .= '   LOWER(division) IN ( ' . implode(',', $keyArr) . ')  ';
        $query .= '   AND display_order >= 100 ';

        $query .= "   AND code = '_def'  ";
        $query .= ' ) ';
        $query .= ' SELECT ';
        $query .= '  * ';
        $query .= ' FROM ';
        $query .= '   (SELECT DISTINCT';
        $query .= '     ad.division, ';
        $query .= '     ad.code AS code, ';
        $query .= '     d.name AS division_name, ';
        $query .= '     ad.name, ';
        $query .= '     d.display_order division_order, ';
        $query .= '     ad.display_order ';
        $query .= '   FROM ';
        $query .= '     divisions d ';
        $query .= '   INNER JOIN ';
        $query .= '     attr_divs ad ';
        $query .= '   ON ';
        $query .= "     LOWER(d.division) = LOWER(ad.division) AND ad.code <> '_def' ) odr";
        $query .= ' ORDER BY ';
        $query .= '   odr.division_order, odr.division_name, odr.display_order; ';

        return $this->select($query, $bindings);
    }

    /**
     * @param array $divisions
     * @param int $memberId
     * @param int $regionId
     * @return array
     */
    public function findOriginalDiv(array $divisions, int $memberId, int $regionId): array
    {
        $bindings = [];

        $bindings['memberId'] = $memberId;
        $bindings['regionId'] = $regionId;

        $query = '';
        $query .= ' WITH divisions AS( ';
        $query .= ' SELECT ';
        $query .= '   LOWER(ad.division) division';
        $query .= '   , ad.code ';
        $query .= '   , ad.name ';
        $query .= '   , mod.display_order ';
        $query .= ' FROM ';
        $query .= '   attr_divs ad ';
        $query .= ' INNER JOIN ';
        $query .= '   member_original_divs mod ';
        $query .= ' ON ';
        $query .= '   ad.division = mod.division ';
        $query .= '   AND NOW() BEtweeN mod.target_date_from AND mod.target_date_to ';
        $query .= '   AND mod.region_id = :regionId ';
        $query .= ' WHERE ';

        $keyArr = [];

        foreach ($divisions as $key => $val) {
            $keyName = ':division' . $key;
            $bindings[$keyName] = strtolower($val);
            $keyArr[] = $keyName;
        }
        $query .= '   LOWER(ad.division) IN ( ' . implode(',', $keyArr) . ')  ';
        $query .= "   AND ad.code = '_def'  ";
        $query .= '   AND mod.member_id = :memberId  ';
        $query .= ' ) ';
        $query .= ' SELECT ';
        $query .= '  * ';
        $query .= ' FROM ';
        $query .= '   (SELECT DISTINCT';
        $query .= '     LOWER(ad.division) division, ';
        $query .= '     ad.code AS code, ';
        $query .= '     d.name AS division_name, ';
        $query .= '     ad.name, ';
        $query .= '     d.display_order division_order, ';
        $query .= '     ad.display_order, ';
        $query .= '     ad.definition ';
        $query .= '   FROM ';
        $query .= '     divisions d ';
        $query .= '   INNER JOIN ';
        $query .= '     attr_divs ad ';
        $query .= '   ON ';
        $query .= "     LOWER(d.division) = LOWER(ad.division) AND ad.code <> '_def' ) odr";
        $query .= ' ORDER BY ';
        $query .= '   odr.division_order, odr.division_name, odr.display_order; ';

        return $this->select($query, $bindings);
    }

    /**
     * 個人と世帯だけ取得する.
     *
     * @return array
     */
    public function getPersonalHouseHold(): array
    {
        $query = "SELECT division, division as code, name as division_name, name FROM attr_divs WHERE division IN ('personal','household') ORDER BY division DESC";

        return $this->select($query);
    }
}
