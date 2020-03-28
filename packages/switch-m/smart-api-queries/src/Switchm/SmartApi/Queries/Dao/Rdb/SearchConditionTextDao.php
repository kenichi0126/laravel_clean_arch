<?php

namespace Switchm\SmartApi\Queries\Dao\Rdb;

class SearchConditionTextDao extends Dao
{
    public function getCompanyNames(array $companyIds): array
    {
        $bindings = [];

        $bindArr = $this->createArrayBindParam('companyIds', [
            'companyIds' => $companyIds,
        ], $bindings);
        $query = '';
        $query .= ' SELECT ';
        $query .= ' 	name ';
        $query .= ' 	, id ';
        $query .= ' FROM ';
        $query .= ' 	companies ';
        $query .= ' WHERE ';
        $query .= ' 	id in (' . implode(',', $bindArr) . ')  ';
        $query .= ' ORDER BY ';
        $query .= ' 	id; ';

        $result = $this->select($query, $bindings);

        return $result;
    }

    public function getProductNames(array $productIds): array
    {
        $bindings = [];

        $bindArr = $this->createArrayBindParam('productIds', [
            'productIds' => $productIds,
        ], $bindings);

        $query = '';
        $query .= ' SELECT ';
        $query .= ' 	name ';
        $query .= ' FROM ';
        $query .= ' 	products ';
        $query .= ' WHERE ';
        $query .= ' 	id in (' . implode(',', $bindArr) . ')  ';
        $query .= ' ORDER BY ';
        $query .= ' 	id; ';

        $result = $this->select($query, $bindings);

        return $result;
    }

    public function getProgramNames(array $programIds): array
    {
        $bindings = [];

        $bindArr = $this->createArrayBindParam('programIds', [
            'programIds' => $programIds,
        ], $bindings);
        $query = '';
        $query .= ' SELECT DISTINCT';
        $query .= ' 	title ';
        $query .= ' FROM ';
        $query .= ' 	programs ';
        $query .= ' WHERE ';
        $query .= ' 	prog_id in (' . implode(',', $bindArr) . ') ';
        $query .= ' ORDER BY ';
        $query .= ' 	title; ';

        $result = $this->select($query, $bindings);

        return $result;
    }

    public function getCmMaterials(array $cmMaterialIds): array
    {
        $bindings = [];

        $bindArr = $this->createArrayBindParam('cmMaterialIds', [
            'cmMaterialIds' => $cmMaterialIds,
        ], $bindings);

        $query = '';
        $query .= ' SELECT  ';
        $query .= ' 	cm_id, ';
        $query .= " 	coalesce(min(setting), '') as setting  ";
        $query .= ' FROM ';
        $query .= ' 	commercials ';
        $query .= ' WHERE ';
        $query .= ' 	cm_id in (' . implode(',', $bindArr) . ') ';
        $query .= ' GROUP BY ';
        $query .= ' 	cm_id  ';
        $query .= ' ORDER BY ';
        $query .= ' 	cm_id ';

        $result = $this->select($query, $bindings);

        return $result;
    }

    public function getBasicNumbers(string $division, array $codes, string $startDateTime, string $endDateTime, int $regionId, bool $isRt): array
    {
        $bindings = [];

        $bindings[':division'] = $division;
        $bindings[':regionId'] = $regionId;
        $bindings[':startDateTime'] = $startDateTime;
        $bindings[':endDateTime'] = $endDateTime;
        $bindArr = $this->createArrayBindParam('codes', [
            'codes' => $codes,
        ], $bindings);

        $query = '';
        $query .= ' SELECT ';
        $query .= '   ad.division ';
        $query .= '   , ad.code ';
        $query .= '   , ad.name ';
        $query .= '   , tban.number  ';
        $query .= ' FROM ';

        if ($isRt) {
            $query .= '   time_box_attr_numbers tban  ';
        } else {
            $query .= '   ts_time_box_attr_numbers tban  ';
        }
        $query .= '   INNER JOIN attr_divs ad  ';
        $query .= '     ON tban.division = ad.division  ';
        $query .= '     AND tban.code = ad.code  ';
        $query .= ' WHERE ';
        $query .= '   tban.division = :division  ';
        $query .= '   AND ad.code IN (' . implode(',', $bindArr) . ')  ';
        $query .= '   AND tban.time_box_id = (  ';
        $query .= '     SELECT ';
        $query .= '       MAX(id)  ';
        $query .= '     FROM ';
        $query .= '       time_boxes  ';
        $query .= '     WHERE ';
        $query .= "       (:startDateTime <= ended_at - interval '5 hours' AND started_at - interval '5 hours' <= :endDateTime ) ";
        $query .= '       AND region_id = :regionId ';
        $query .= '   )  ';
        $query .= ' ORDER BY  ';
        $query .= '   ad.display_order  ';

        $result = $this->select($query, $bindings);

        return $result;
    }

    public function getOriginalNumbers(string $division, array $codes, string $startDateTime, string $endDateTime, int $regionId, bool $isRt): array
    {
        $bindings = [];

        $bindings[':division'] = $division;
        $bindings[':regionId'] = $regionId;
        $bindings[':startDateTime'] = $startDateTime;
        $bindings[':endDateTime'] = $endDateTime;

        $query = '';
        $query .= ' SELECT ';
        $query .= '  cnt.count number, ';
        $query .= '  cnt.code, ';
        $query .= '  ad.name';
        $query .= ' FROM ';
        $query .= ' (';
        $query .= ' SELECT ';
        $query .= '   COUNT(paneler_id) ';
        $query .= '   , codes.code ';
        $query .= ' FROM ';

        if ($isRt) {
            $query .= '   time_box_panelers tbp  ';
        } else {
            $query .= '   ts_time_box_panelers tbp  ';
        }
        $tmpArr = [];

        foreach ($codes as $code) {
            $key = ':cross_join' . $code;
            $bindings[$key] = $code;
            array_push($tmpArr, "SELECT ${key} ::varchar(255) code");
        }
        $query .= ' CROSS JOIN (' . implode(' UNION ALL ', $tmpArr) . ') codes ';
        $query .= ' WHERE ';
        $query .= '   (  ';
        $query .= $this->createCrossJoinWhereClause($division, $codes, $bindings);
        $query .= '   )  ';
        $query .= '   AND tbp.time_box_id = (  ';
        $query .= '     SELECT ';
        $query .= '       MAX(id)  ';
        $query .= '     FROM ';
        $query .= '       time_boxes  ';
        $query .= '     WHERE ';
        $query .= "       (:startDateTime <= ended_at - interval '5 hours' AND started_at - interval '5 hours' <= :endDateTime ) ";
        $query .= '       AND region_id = :regionId ';
        $query .= '   )  ';
        $query .= ' GROUP BY ';
        $query .= '   codes.code ';
        $query .= ' ) cnt';
        $query .= ' INNER JOIN attr_divs ad  ';
        $query .= '   ON :division = ad.division  ';
        $query .= '      AND cnt.code = ad.code  ';
        $query .= ' ORDER BY  ';
        $query .= '   ad.display_order  ';

        $result = $this->select($query, $bindings);

        return $result;
    }

    public function getPersonalHouseholdNumbers(string $startDateTime, string $endDateTime, int $regionId, bool $isRt): array
    {
        $bindings = [];

        $bindings[':startDateTime'] = $startDateTime;
        $bindings[':endDateTime'] = $endDateTime;
        $bindings[':regionId'] = $regionId;

        $query = '';
        $query .= ' SELECT ';
        $query .= '   ad.division ';
        $query .= '   , ad.code ';
        $query .= '   , ad.name ';
        $query .= '   , tban.number  ';
        $query .= ' FROM ';

        if ($isRt) {
            $query .= '   time_box_attr_numbers tban  ';
        } else {
            $query .= '   ts_time_box_attr_numbers tban  ';
        }

        $query .= '   INNER JOIN attr_divs ad  ';
        $query .= '     ON tban.division = ad.division  ';
        $query .= '     AND tban.code = ad.code  ';
        $query .= ' WHERE ';
        $query .= "   tban.division IN ('personal', 'household')  ";
        $query .= "   AND ad.code = '1'  ";
        $query .= '   AND tban.time_box_id = (  ';
        $query .= '     SELECT ';
        $query .= '       MAX(id)  ';
        $query .= '     FROM ';
        $query .= '       time_boxes  ';
        $query .= '     WHERE ';
        $query .= "       (:startDateTime <= ended_at - interval '5 hours' AND started_at - interval '5 hours' <= :endDateTime ) ";
        $query .= '       AND region_id = :regionId ';
        $query .= '   )  ';
        $query .= ' ORDER BY  ';
        $query .= '   ad.display_order  ';

        $result = $this->select($query, $bindings);

        return $result;
    }

    public function getGenres(array $genres): array
    {
        $bindings = [];

        $bindStrings = $this->createArrayBindParam('genres', [
            'genres' => $genres,
        ], $bindings);

        $query = 'SELECT genre_id, name FROM mdata_prog_genres WHERE genre_id IN (' . implode(',', $bindStrings) . ') ORDER BY genre_id';

        $result = $this->select($query, $bindings);

        return $result;
    }

    public function getCmLargeGenreNames(array $cmLargeGenres): array
    {
        $bindings = [];

        $bindArr = $this->createArrayBindParam('cmLargeGenres', [
            'cmLargeGenres' => $cmLargeGenres,
        ], $bindings);

        $query = '';
        $query .= ' SELECT ';
        $query .= '   name ';
        $query .= ' FROM ';
        $query .= '   codes ';
        $query .= ' WHERE ';
        $query .= "   division = 'cm_large_genre' ";
        $query .= '   AND code IN (' . implode(',', $bindArr) . ') ';
        $query .= ' ORDER BY ';
        $query .= '   display_order ';

        $result = $this->select($query, $bindings);

        return $result;
    }

    public function getChannelCodeNames(array $channelIds): array
    {
        $bindings = [];

        $bindChannelIds = $this->createArrayBindParam('channelIds', [
            'channelIds' => $channelIds,
        ], $bindings);

        $query = '';
        $query .= ' SELECT ';
        $query .= '   id, ';
        $query .= '   code_name ';
        $query .= ' FROM ';
        $query .= '   channels ';
        $query .= ' WHERE ';
        $query .= '   id IN (' . implode(',', $bindChannelIds) . ')';

        $result = $this->select($query, $bindings);

        return $result;
    }
}
