<?php

namespace Switchm\SmartApi\Queries\Dao\Rdb;

class ProgramNamesDao extends Dao
{
    /**
     * 検索条件のために使用するクエリー
     *
     * @param array $params
     * @param mixed $straddlingFlg
     * @return array
     */
    public function findProgramNames(array $params, $straddlingFlg): array
    {
        $bindings = [];
        $bindings[':startDate'] = $params['startDate'];
        $bindings[':endDate'] = $params['endDate'];

        $select = '';
        $select .= '  DISTINCT ON (p.title) p.prog_id ';
        $select .= '  ,p.title ';

        $from = '';

        if ($params['bsFlag'] === true) {
            $from .= '    bs_programs p ';
        } else {
            $from .= '    programs p ';
        }

        $where = '';

        $tmpArr = [];

        if (!empty($params['title'])) {
            $bindArr = $this->createArrayBindParam('title', $params, $bindings);

            foreach ($bindArr as $bindKey) {
                $tmpArr[] = "p.title ILIKE ${bindKey} ";
            }
            $where .= '(' . implode(' OR ', $tmpArr) . ' ) AND ';
        }

        $tmpArr = [];

        if (!empty($params['programIds'])) {
            $bindArr = $this->createArrayBindParam('programIds', $params, $bindings);

            if ($params['bsFlag'] === true) {
                $where .= ' p.title NOT IN ( SELECT title FROM bs_programs WHERE prog_id IN (' . implode(',', $bindArr) . ') ) AND ';
            } else {
                $where .= ' p.title NOT IN ( SELECT title FROM programs WHERE prog_id IN (' . implode(',', $bindArr) . ') ) AND ';
            }
        }

        // 期間
        $where .= '  p.date BETWEEN :startDate AND :endDate ';
        // 時間帯
        if ($params['startTime'] === '050000' && $params['endTime'] === '045959') {
        } else {
            $bindings[':startTime'] = $params['startTime'];
            $bindings[':endTime'] = $params['endTime'];

            if ($straddlingFlg) {
                // 0時跨ぎの場合
                $where .= "  AND NOT(to_char(p.real_started_at,'HH24MISS') >= :endTime AND to_char(p.real_ended_at,'HH24MISS') < :startTime )";
            } else {
                $where .= "  AND to_char(p.real_started_at,'HH24MISS') < :endTime AND to_char(p.real_ended_at,'HH24MISS') >= :startTime ";
            }
        }

        //放送
        if (!empty($params['channel'])) {
            $keyArr = [];

            foreach ($params['channel'] as $key => $val) {
                $keyName = ':channel' . $key;
                $bindings[$keyName] = $val;
                array_push($keyArr, $keyName);
            }
            $where .= '  AND p.channel_id IN ( ' . implode(',', $keyArr) . ') ';
        } else {
            //地デジ 5放送
            $where .= '  AND p.channel_id IN (3,4,5,6,7) ';
        }

        if (!$params['programFlag']) {
            $bindings[':regionId'] = $params['regionId'];

            //CMとの絞り込み
            $where .= '      AND EXISTS (SELECT ';
            $where .= '                      1 ';
            $where .= '                  FROM  ';
            $where .= '                      commercials c ';
            $where .= '                  WHERE ';
            $where .= '                      c.region_id = :regionId ';
            // 期間
            $where .= ' AND c.date BETWEEN :startDate AND :endDate ';

            if (!empty($params['companyIds'])) {
                $bindArr = $this->createArrayBindParam('companyIds', $params, $bindings);
                $where .= '  AND c.company_id IN ( ' . implode(',', $bindArr) . ' ) ';
            }
            //広告種別
            if (array_key_exists('cmType', $params)) {
                if ($params['cmType'] == '1') {
                    //タイム
                    $where .= '              AND c.cm_type = 2 ';
                } elseif ($params['cmType'] == '2') {
                    //スポット
                    $where .= '              AND c.cm_type in(0, 1) ';
                }
            }
            //CM秒数
            if (array_key_exists('cmSeconds', $params)) {
                if ($params['cmSeconds'] == '2') {
                    $where .= '              AND c.duration = 15 ';
                } elseif ($params['cmSeconds'] == '3') {
                    $where .= '              AND c.duration > 15 ';
                }
            }
            // 商品指定
            if (is_array($params['productIds']) && count($params['productIds'])) {
                $bindProducts = $this->createArrayBindParam('productIds', $params, $bindings);
                $where .= '              AND c.product_id IN ( ' . implode(',', $bindProducts) . ' )';
            }

            $where .= '                      AND c.prog_id = p.prog_id ';
            $where .= '     ) ';
        }

        if (is_array($params['wdays']) && count($params['wdays'])) {
            $wdays = implode(',', $this->createArrayBindParam('wdays', $params, $bindings));

            if (array_key_exists('holiday', $params)) {
                if ($params['holiday'] === true) {
                    $where .= '   AND ( ';
                    $where .= "    (EXTRACT(DOW FROM p.date) IN (${wdays})) ";
                    $where .= '    OR p.date IN (SELECT holiday FROM holidays) ';
                    $where .= '   ) ';
                } elseif ($params['holiday'] === false) {
                    $where .= "   AND (EXTRACT(DOW FROM p.date) IN (${wdays})) ";
                    $where .= '   AND p.date NOT IN (SELECT holiday FROM holidays) ';
                }
            }
        } else {
            if (array_key_exists('holiday', $params)) {
                if ($params['holiday'] === true) {
                    $where .= '   AND p.date IN (SELECT holiday FROM holidays) ';
                } elseif ($params['holiday'] === false) {
                    $where .= '   AND p.date NOT IN (SELECT holiday FROM holidays) ';
                }
            }
        }

        $order = ' p.title asc ';

        $limit = ' 30 ';

        $query = sprintf('SELECT %s FROM %s WHERE %s ORDER BY %s LIMIT %s', $select, $from, $where, $order, $limit);
        return $this->select($query, $bindings);
    }

    public function find(array $programIds): array
    {
        $bindings = [];
        $bindProgramIds = $this->createArrayBindParam('programIds', ['programIds' => $programIds], $bindings);
        $query = 'SELECT prog_id, title FROM programs WHERE prog_id IN (' . implode(',', $bindProgramIds) . ')';
        return $this->select($query, $bindings);
    }
}
