<?php

namespace Switchm\SmartApi\Queries\Dao\Rdb;

class MdataSceneDao extends Dao
{
    /**
     * Mデータシーン情報取得.
     *
     * prog_idに一致するMデータシーン情報を取得する。
     * @param string $progId
     */
    public function findMdataScenes(String $progId)
    {
        $bindings = [];
        $bindings[':prog_id'] = $progId;

        $select = '';
        $select .= 'ms.tm_start, ';
        $select .= 'ms.tm_end, ';
        $select .= 'mpc.name, ';
        $select .= 'ms.headline, ';
        $select .= 'ms.tm_active ';

        $from = '';
        $from .= 'mdata_scenes ms ';
        $from .= ' LEFT JOIN ';
        $from .= '  mdata_prog_classes mpc ';
        $from .= '   ON ms.class_id = mpc.class_id ';

        $where = '';
        $where .= 'ms.prog_id = :prog_id ';

        $orderBy = '';
        $orderBy .= 'ms.tm_start ASC ';

        $query = sprintf(
            'SELECT %s FROM %s WHERE %s ORDER BY %s;',
            $select,
            $from,
            $where,
            $orderBy
        );

        return $this->select($query, $bindings);
    }
}
