<?php

namespace Switchm\SmartApi\Queries\Dao\Rdb;

class MdataProgGenreDao extends Dao
{
    // エムデータ番組ジャンル情報取得
    public function search(): array
    {
        $select = '';
        $select .= 'mpg.genre_id, ';
        // 名称に半角スペースがあれば、それより右の文字は取得しない
        $select .= "CASE WHEN STRPOS(mpg.name, ' ') <> 0 ";
        $select .= "THEN SUBSTR(mpg.name, 0, STRPOS(mpg.name, ' ')) ";
        $select .= 'ELSE mpg.name ';
        $select .= 'END as name ';

        $where = '';
        // 「放送休止」は除外
        $where .= "mpg.genre_id <> '20014' ";

        $orderBy = '';
        // 「その他」を一番最後に
        $orderBy .= "mpg.genre_id = '20001' asc, ";
        $orderBy .= 'mpg.genre_id asc ';

        $query = sprintf(
            'SELECT %s FROM mdata_prog_genres mpg WHERE %s ORDER BY %s;',
            $select,
            $where,
            $orderBy
        );

        return $this->select($query);
    }
}
