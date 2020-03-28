<?php

namespace Switchm\SmartApi\Queries\Dao\Rdb;

class CompanyNamesDao extends Dao
{
    /**
     * 検索条件のために使用するクエリー
     *
     * @param array $params
     * @param bool $straddlingFlg
     * @return array
     */
    public function findForCondition(array $params, $straddlingFlg): array
    {
        $bindings = [];
        $bindings[':regionId'] = $params['regionId'];

        $bindings[':startDate'] = $params['startDate'];
        $bindings[':endDate'] = $params['endDate'];
        $bindings[':startTime'] = $params['startTime'];
        $bindings[':endTime'] = $params['endTime'];

        $select = '';
        $select .= '  c.id ';
        $select .= '  ,c.name ';
        $select .= '  ,c.created_at ';
        $select .= '  ,c.updated_at ';

        $from = '';
        $from .= '  companies c ';

        $where = '';
        // 企業名
        $tmpArr = [];

        if (!empty($params['companyNames'])) {
            $bindArr = $this->createArrayBindParam('companyNames', $params, $bindings);

            foreach ($bindArr as $bindKey) {
                $tmpArr[] = "c.name ILIKE ${bindKey} ";
            }
            $where .= '(' . implode(' OR ', $tmpArr) . ' ) AND ';
        }

        if (!empty($params['companyId'])) {
            $bindArr = $this->createArrayBindParam('companyId', $params, $bindings);
            $where .= 'c.id NOT IN (' . implode(',', $bindArr) . ' ) AND ';
        }

        $where .= '  EXISTS (SELECT ';
        $where .= '                  1 ';
        $where .= '              FROM ';
        $where .= '                  commercials cm ';
        $where .= '              WHERE ';
        $where .= '                  cm.region_id = :regionId   ';
        // 期間
        $where .= '                  AND cm.date BETWEEN :startDate AND :endDate ';
        // 時間帯
        if ($straddlingFlg) {
            // 0時跨ぎの場合
            $where .= "              AND NOT(to_char(cm.started_at,'HH24MISS') >= :endTime AND to_char(cm.ended_at,'HH24MISS') < :startTime )";
        } else {
            $where .= "              AND to_char(cm.started_at,'HH24MISS') < :endTime AND to_char(cm.ended_at,'HH24MISS') >= :startTime ";
        }
        // 番組指定
        if (is_array($params['progIds']) && count($params['progIds'])) {
            $progIdBindArr = [];

            foreach ($params['progIds'] as $key => $val) {
                $bindKey = ':progIds_' . $key;
                array_push($progIdBindArr, $bindKey);
                $bindings[$bindKey] = $val;
            }
            $where .= '              AND cm.program_title IN ( SELECT p.title FROM programs p WHERE p.prog_id IN ( ' . implode(',', $progIdBindArr) . ' ) )';
        }

        // 局指定
        if (is_array($params['channels']) && count($params['channels'])) {
            $bindChannels = $this->createArrayBindParam('channels', $params, $bindings);
            $where .= '              AND cm.channel_id IN ( ' . implode(',', $bindChannels) . ' )';
        }

        //広告種別
        if (array_key_exists('cmType', $params)) {
            if ($params['cmType'] == '1') {
                //タイム
                $where .= '              AND cm.cm_type = 2 ';
            } elseif ($params['cmType'] == '2') {
                //スポット
                $where .= '              AND cm.cm_type in(0, 1) ';
            }
        }
        //CM秒数
        if (array_key_exists('cmSeconds', $params)) {
            if ($params['cmSeconds'] == '2') {
                $where .= '              AND cm.duration = 15 ';
            } elseif ($params['cmSeconds'] == '3') {
                $where .= '              AND cm.duration > 15 ';
            }
        }

        // 商品指定
        if (is_array($params['productIds']) && count($params['productIds'])) {
            $bindProducts = $this->createArrayBindParam('productIds', $params, $bindings);
            $where .= '              AND cm.product_id IN ( ' . implode(',', $bindProducts) . ' )';
        }

        $where .= ' AND cm.company_id = c.id ';
        $where .= ' ) ';

        $orderBy = ' c.name ';

        $limit = ' 30 ';
        $query = sprintf('SELECT %s FROM %s WHERE %s ORDER BY %s LIMIT %s', $select, $from, $where, $orderBy, $limit);
        return $this->select($query, $bindings);
    }

    public function find(array $companyIds): array
    {
        $bindings = [];
        $bindCompanyIds = $this->createArrayBindParam('companyIds', ['companyIds' => $companyIds], $bindings);
        $query = 'SELECT id, name FROM companies WHERE id IN (' . implode(',', $bindCompanyIds) . ')';
        return $this->select($query, $bindings);
    }
}
