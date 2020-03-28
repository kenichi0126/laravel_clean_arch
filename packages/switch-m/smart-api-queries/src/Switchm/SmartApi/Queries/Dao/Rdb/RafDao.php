<?php

namespace Switchm\SmartApi\Queries\Dao\Rdb;

class RafDao extends Dao
{
    /**
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param string $cmType
     * @param string $cmSeconds
     * @param int $regionId
     * @param string $division
     * @param null|array $codes
     * @param array $conditionCross
     * @param array $companyIds
     * @param null|array $productIds
     * @param null|array $cmIds
     * @param array $channels
     * @param null|bool $conv15SecFlag
     * @param null|array $progIds
     * @param bool $straddlingFlg
     * @param array $dataType
     * @return \stdClass
     */
    public function getProductNumbers(String $startDate, String $endDate, String $startTime, String $endTime, String $cmType, String $cmSeconds, int $regionId, String $division, ?array $codes, array $conditionCross, array $companyIds, ?array $productIds, ?array $cmIds, array $channels, ?bool $conv15SecFlag, ?array $progIds, bool $straddlingFlg, array $dataType): \stdClass
    {
        $bindings = [];
        list($withWhere, $codeBind, $channelBind, $companyBind, $productIdsBind, $cmIdsBind, $progIdsBind) = $this->createListWhere($bindings, $startDate, $endDate, $startTime, $endTime, $cmType, $cmSeconds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $progIds, $channels, $straddlingFlg);

        $query = '';
        $query .= '   SELECT ';
        $query .= '     COUNT(distinct product_id) number ';
        $query .= '   FROM ';
        $query .= '     commercials c ';
        $query .= $withWhere;
        $query .= '   AND region_id = :regionId ';

        $result = $this->selectOne($query, $bindings);

        return $result;
    }

    private function createListWhere(array &$bindings, String $startDate, String $endDate, String $startTime, String $endTime, String $cmType, String $cmSeconds, int $regionId, String $division, ?array $codes, array $conditionCross, array $companyIds, ?array $productIds, ?array $cmIds, ?array $progIds, array $channels, bool $straddlingFlg)
    {
        $sql = '';

        $codeBind = [];

        // 日付
        $bindings[':startDate'] = $startDate;
        $bindings[':endDate'] = $endDate;

        // 放送局
        $channelBind = $this->createArrayBindParam('channels', [
            'channels' => $channels,
        ], $bindings);

        // 企業ID
        $companyBind = $this->createArrayBindParam('companyIds', [
            'companyIds' => $companyIds,
        ], $bindings);

        // 商品ID
        $productIdsBind = $this->createArrayBindParam('productIds', [
            'productIds' => $productIds,
        ], $bindings);

        // 素材
        $cmIdsBind = $this->createArrayBindParam('cmIds', [
            'cmIds' => $cmIds,
        ], $bindings);

        // 番組ID
        $progIdsBind = $this->createArrayBindParam('progIds', [
            'progIds' => $progIds,
        ], $bindings);

        $sql .= ' WHERE ';
        $sql .= ' 	c.date BETWEEN :startDate AND :endDate ';

        // 時間帯
        if ($startTime === '050000' && $endTime === '045959') {
            // 全選択の場合は検索条件に含めない
        } else {
            $bindings[':startTime'] = $startTime;
            $bindings[':endTime'] = $endTime;

            if ($straddlingFlg) {
                // 0時跨ぎの場合
                $sql .= "              AND NOT(to_char(c.ended_at,'HH24MISS') < :startTime AND to_char(c.started_at,'HH24MISS') > :endTime )";
            } else {
                $sql .= "              AND to_char(c.started_at,'HH24MISS') < :endTime AND to_char(c.ended_at,'HH24MISS') >= :startTime ";
            }
        }

        // 放送局
        if (count($channelBind) > 0) {
            $sql .= ' 	AND c.channel_id IN (' . implode(',', $channelBind) . ') ';
        }

        // 企業ID
        if (count($companyBind)) {
            $sql .= ' AND c.company_id IN (' . implode(',', $companyBind) . ')';
        }

        // 商品ID
        if (count($productIdsBind)) {
            $sql .= ' AND c.product_id IN (' . implode(',', $productIdsBind) . ')';
        }
        // CM素材オプション
        if (count($cmIdsBind)) {
            $sql .= ' AND c.cm_id IN (' . implode(',', $cmIdsBind) . ')';
        }

        // 番組名
        if (count($progIdsBind)) {
            $sql .= '   AND c.program_title IN (SELECT title From programs WHERE prog_id IN (' . implode(',', $progIdsBind) . ')) ';
        }
        // 広告種別
        if (isset($cmType) && $cmType == '1') {
            $sql .= ' AND c.cm_type = 2';
        } elseif (isset($cmType) && $cmType == '2') {
            $sql .= ' AND c.cm_type IN (0, 1)';
        }
        // CM秒数（1は全CMなので、条件に含めない）
        if ($cmSeconds == '2') {
            $sql .= ' AND c.duration = 15';
        } elseif ($cmSeconds == '3') {
            $sql .= ' AND c.duration > 15';
        }

        // 地域コード
        $bindings[':regionId'] = $regionId;

        return [
            $sql,
            $codeBind,
            $channelBind,
            $companyBind,
            $productIdsBind,
            $cmIdsBind,
            $progIdsBind,
        ];
    }
}
