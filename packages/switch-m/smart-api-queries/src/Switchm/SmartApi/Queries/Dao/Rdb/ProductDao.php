<?php

namespace Switchm\SmartApi\Queries\Dao\Rdb;

class ProductDao extends Dao
{
    public function search(array $params): array
    {
        $bindings = [];

        $select = '';
        $select .= 'p.id, ';
        $select .= 'p.name, ';
        $select .= 'p.company_id ';

        // TODO - sato: 商品名の大文字小文字等の変換処理は方針未定のため、現状実施しない
        $companyKeys = $this->createArrayBindParam('companyIds', $params, $bindings);

        $where = '';

        if (count($companyKeys) > 0) {
            $where .= ' p.company_id IN (' . implode(',', $companyKeys) . ') AND ';
        }

        $tmpArr = [];

        if (!empty($params['productNames'])) {
            $bindArr = $this->createArrayBindParam('productNames', $params, $bindings);

            foreach ($bindArr as $bindKey) {
                $tmpArr[] = "p.name ILIKE ${bindKey} ";
            }
            $where .= '(' . implode(' OR ', $tmpArr) . ' ) AND ';
        }

        if (!empty($params['productIds'])) {
            $bindArr = $this->createArrayBindParam('productIds', $params, $bindings);
            $where .= ' p.id NOT IN (' . implode(',', $bindArr) . ' ) AND ';
        }

        $where .= ' EXISTS ( ';
        $where .= ' SELECT 1 ';
        $where .= ' FROM commercials cm ';
        $where .= ' WHERE ';
        // TODO - sato: 原本から条件を逆にしている。環境が整ったら時間を計って確認する予定
        $bindings[':startDate'] = $params['startDate'];
        $bindings[':endDate'] = $params['endDate'];
        $where .= 'cm.date BETWEEN :startDate AND :endDate ';
        $where .= 'AND cm.product_id = p.id ';

        if (count($companyKeys) > 0) {
            $where .= ' AND cm.company_id IN (' . implode(',', $companyKeys) . ') ';
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

        // 番組指定
        if (is_array($params['progIds']) && count($params['progIds'])) {
            $progIdBindArr = [];

            foreach ($params['progIds'] as $key => $val) {
                $bindKey = ':progIds_' . $key;
                $progIdBindArr[] = $bindKey;
                $bindings[$bindKey] = $val;
            }
            $where .= '              AND cm.program_title IN ( SELECT p.title FROM programs p WHERE p.prog_id IN ( ' . implode(',', $progIdBindArr) . ' ) )';
        }

        // 地域
        if (array_key_exists('regionIds', $params) && !empty($params['regionIds'])) {
            $regionKeys = $this->createArrayBindParam('regionIds', $params, $bindings);
            $where .= 'AND cm.region_id IN (' . implode(',', $regionKeys) . ') ';
        }

        // 局指定
        if (is_array($params['channels']) && count($params['channels'])) {
            $bindChannels = $this->createArrayBindParam('channels', $params, $bindings);
            $where .= '              AND cm.channel_id IN ( ' . implode(',', $bindChannels) . ' )';
        }

        // 時間（R&Fの場合は設定しない）
        if (array_key_exists('startTimeHour', $params) && array_key_exists('startTimeMin', $params) && array_key_exists('endTimeHour', $params) && array_key_exists('endTimeMin', $params)) {
            $i_start_time_hour = (int) $params['startTimeHour'];
            $i_start_time_min = (int) $params['startTimeMin'];
            $i_end_time_hour = (int) $params['endTimeHour'];
            $i_end_time_min = (int) $params['endTimeMin'];

            // TODO - sato: 時間系処理は共通化すべきか？方針確定後に要対処。
            // 時刻計算(5:00～28:59ならば時刻指定なし)
            if ($i_start_time_hour != 5 || $i_start_time_min != 0 || $i_end_time_hour != 28 || $i_end_time_min != 59) {
                // 24～28時ならば0～4に変更
                $i_start_time_hour = $i_start_time_hour >= 24 ? $i_start_time_hour - 24 : $i_start_time_hour;
                $i_end_time_hour = $i_end_time_hour >= 24 ? $i_end_time_hour - 24 : $i_end_time_hour;

                // 0埋めして連結
                $pattern = '%02d';
                $start_time = sprintf($pattern, $i_start_time_hour) . sprintf($pattern, $i_start_time_min);
                $end_time = sprintf($pattern, $i_end_time_hour) . sprintf($pattern, $i_end_time_min);

                $bindings[':start_time'] = $start_time;
                $bindings[':end_time'] = $end_time;

                // 0時またぎ判定
                if ($i_start_time_hour > $i_end_time_hour) {
                    $where .= "AND NOT(to_char(cm.started_at,'HH24MI') >= :end_time AND to_char(cm.ended_at,'HH24MI') < :start_time )";
                } else {
                    $where .= "AND to_char(cm.started_at,'HH24MI') < :end_time AND to_char(cm.ended_at,'HH24MI') >= :start_time ";
                }
            }
        }

        $where .= ')';

        $limit = '30;';

        $order = 'p.id';

        $query = sprintf('SELECT %s FROM products p WHERE %s ORDER BY %s LIMIT %s', $select, $where, $order, $limit);

        return $this->select($query, $bindings);
    }

    public function findCompanyIds($productIds): array
    {
        $bindings = [];
        $bindProductIds = $this->createArrayBindParam('productIds', [
            'productIds' => $productIds,
        ], $bindings);

        $query = '';
        $query .= ' SELECT ';
        $query .= '   company_id ';
        $query .= ' FROM ';
        $query .= '   products ';
        $query .= ' WHERE ';
        $query .= '   id IN (' . implode(',', $bindProductIds) . ')';
        $query .= ' GROUP BY ';
        $query .= '   company_id ';

        return $this->select($query, $bindings);
    }

    public function find(array $productIds): array
    {
        $bindings = [];
        $bindProductIds = $this->createArrayBindParam('productIds', ['productIds' => $productIds], $bindings);
        $query = 'SELECT company_id, id, name FROM products WHERE id IN (' . implode(',', $bindProductIds) . ')';
        return $this->select($query, $bindings);
    }
}
