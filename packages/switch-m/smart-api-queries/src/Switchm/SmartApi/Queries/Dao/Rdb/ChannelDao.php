<?php

namespace Switchm\SmartApi\Queries\Dao\Rdb;

class ChannelDao extends Dao
{
    // チャンネル放送情報取得
    public function search(array $params): array
    {
        $bindings = [];

        $select = '';
        $select .= 'c.id, ';
        $select .= 'c.display_name ';

        $bindings[':division'] = $params['division'];
        $where = '';
        $where .= 'c.division = :division ';

        // division指定が「bs」始まりの場合は地域指定しない
        if (isset($params['regionId']) && !preg_match('/^bs/', $params['division'])) {
            $bindings[':regionId'] = $params['regionId'];
            $where .= 'AND c.region_id = :regionId ';
        }

        // CMあり判定
        if ($params['withCommercials'] == 1) {
            $where .= 'AND c.with_commercials = 1 ';
        }

        $query = sprintf(
            'SELECT %s FROM channels c WHERE %s ORDER BY c.button_number;',
            $select,
            $where
        );

        return $this->select($query, $bindings);
    }
}
