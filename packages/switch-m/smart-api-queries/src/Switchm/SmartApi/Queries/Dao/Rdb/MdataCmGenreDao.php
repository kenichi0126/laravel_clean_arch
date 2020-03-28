<?php

namespace Switchm\SmartApi\Queries\Dao\Rdb;

class MdataCmGenreDao extends Dao
{
    /**
     * Cmジャンル情報取得.
     * @return array
     */
    public function getCmLargeGenres(): array
    {
        $query = '';
        $query .= ' SELECT ';
        $query .= '   mcg.cm_large_genre ';
        $query .= '   , c.name ';
        $query .= ' FROM ';
        $query .= '   mdata_cm_genres mcg ';
        $query .= '   INNER JOIN codes c ';
        $query .= "     ON c.division = 'cm_large_genre' ";
        $query .= '     AND c.code = mcg.cm_large_genre ';
        $query .= '   WHERE ';
        $query .= "     mcg.cm_large_genre <> '0' ";
        $query .= ' GROUP BY ';
        $query .= '   mcg.cm_large_genre ';
        $query .= '   , c.name ';
        $query .= '   , c.display_order ';
        $query .= ' ORDER BY ';
        $query .= '   c.display_order ';

        return $this->select($query);
    }
}
