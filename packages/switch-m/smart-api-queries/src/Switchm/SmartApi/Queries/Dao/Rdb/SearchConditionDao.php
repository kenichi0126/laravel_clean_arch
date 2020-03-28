<?php

namespace Switchm\SmartApi\Queries\Dao\Rdb;

/**
 * Class SearchConditionDao.
 */
class SearchConditionDao extends Dao
{
    /**
     * @param int $regionId
     * @param int $memberId
     * @param string $orderColumn
     * @param string $orderDirection
     * @return array
     */
    public function findByMemberId(int $regionId, int $memberId, string $orderColumn, string $orderDirection): array
    {
        $query = '';
        $query .= 'SELECT ';
        $query .= '  id ';
        $query .= '  , member_id ';
        $query .= '  , name ';
        $query .= '  , route_name ';
        $query .= '  , condition ';
        $query .= '  , created_at ';
        $query .= '  , updated_at ';
        $query .= '  , deleted_at ';
        $query .= 'FROM ';
        $query .= '  search_conditions ';
        $query .= 'WHERE ';
        $query .= '  region_id = :regionId ';
        $query .= '  and member_id = :memberId ';
        $query .= '  and deleted_at IS NULL ';
        $query .= 'ORDER BY ';
        $query .= "  ${orderColumn} ${orderDirection} ";
        $query .= ';';

        $bindings = [
            ':regionId' => $regionId,
            ':memberId' => $memberId,
        ];

        return $this->select($query, $bindings);
    }

    /**
     * @param int $regionId
     * @param int $memberId
     * @return int
     */
    public function countByMemberId(int $regionId, int $memberId): int
    {
        $query = '';
        $query .= 'SELECT ';
        $query .= '  COUNT(*) as count ';
        $query .= 'FROM ';
        $query .= '  search_conditions ';
        $query .= 'WHERE ';
        $query .= '  region_id = :regionId ';
        $query .= '  and member_id = :memberId ';
        $query .= '  and deleted_at IS NULL ';
        $query .= ';';

        $bindings = [
            ':regionId' => $regionId,
            ':memberId' => $memberId,
        ];

        return $this->selectOne($query, $bindings)->count;
    }
}
