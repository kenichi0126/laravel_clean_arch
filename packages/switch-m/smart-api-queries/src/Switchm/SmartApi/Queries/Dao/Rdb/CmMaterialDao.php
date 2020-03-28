<?php

namespace Switchm\SmartApi\Queries\Dao\Rdb;

class CmMaterialDao extends Dao
{
    public function search(array $params): array
    {
        $bindings = [];

        $select = '';
        $select .= 'cm_id, ';
        $select .= 'min(duration) as duration, ';
        $select .= "coalesce(min(cm.setting), '') as setting, ";
        $select .= "coalesce(min(cm.talent), '') as talent, ";
        $select .= "coalesce(min(cm.bgm), '') as bgm, ";
        $select .= "coalesce(min(cm.memo), '') as memo ";

        $productKeys = $this->createArrayBindParam('product_ids', $params, $bindings);
        $where = '';
        $where .= 'cm.product_id IN (' . implode(',', $productKeys) . ') ';

        $bindings[':start_date'] = $params['start_date'];
        $bindings[':end_date'] = $params['end_date'];
        $where .= 'AND cm.date BETWEEN :start_date AND :end_date ';

        $bindings[':regionId'] = $params['regionId'];
        $where .= 'AND cm.region_id = :regionId ';

        // 時間（R&Fの場合は設定しない）
        // TODO - sato : 時間条件については商品検索と同一。共通化の方針が固まったら共通化すべきか。
        if (array_key_exists('start_time_hour', $params)
            && array_key_exists('start_time_min', $params)
            && array_key_exists('end_time_hour', $params)
            && array_key_exists('end_time_min', $params)) {
            $i_start_time_hour = (int) $params['start_time_hour'];
            $i_start_time_min = (int) $params['start_time_min'];
            $i_end_time_hour = (int) $params['end_time_hour'];
            $i_end_time_min = (int) $params['end_time_min'];

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
                if ($i_start_time_hour < 23 && $i_end_time_hour <= 4) {
                    $where .= "AND NOT(to_char(cm.started_at,'HH24MI') > :start_time AND to_char(cm.ended_at,'HH24MI') < :end_time )";
                } else {
                    $where .= "AND to_char(cm.started_at,'HH24MI') < :end_time AND to_char(cm.ended_at,'HH24MI') >= :start_time ";
                }
            }
        }

        // 局指定
        if (is_array($params['channels']) && count($params['channels'])) {
            $bindChannels = $this->createArrayBindParam('channels', $params, $bindings);
            $where .= '              AND cm.channel_id IN ( ' . implode(',', $bindChannels) . ' )';
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

        if (!empty($params['companyIds'])) {
            $bindArr = $this->createArrayBindParam('companyIds', $params, $bindings);
            $where .= '  AND cm.company_id IN ( ' . implode(',', $bindArr) . ' ) ';
        }

        $query = sprintf(
            'SELECT %s FROM commercials cm WHERE %s GROUP BY cm.cm_id ORDER BY cm.cm_id;',
            $select,
            $where
        );

        return $this->select($query, $bindings);
    }
}
