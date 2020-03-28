<?php

namespace Switchm\SmartApi\Queries\Dao\Dwh;

use Carbon\Carbon;

class RankingDao extends Dao
{
    public function searchCommercial(String $startDate, String $endDate, String $startTime, String $endTime, ?array $wdays, bool $holiday, ?String $cmType, int $regionId, String $division, ?array $codes, ?array $conditionCross, array $channels, ?array $order, ?bool $conv_15_sec_flag, string $period, bool $straddlingFlg, int $length, int $page, string $csvFlag, array $dataType, ?array $cmLargeGenres, string $axisType, array $exclusionCompanyIds, string $axisTypeCompany, string $axisTypeProduct): array
    {
        $bindings = [];
        $isConditionCross = $division == 'condition_cross';
        $divisionKey = $division . '_';
        $maxLength = 500;

        $axis = '';
        $axisNames = [];
        $axisJoins = '';

        if ($axisType == $axisTypeCompany) {
            //企業別
            $axis .= 'company_id';
            $axisNames['c.name'] = 'company_name';

            $axisJoins .= ' LEFT JOIN companies c ';
            $axisJoins .= '   ON gl.company_id = c.id ';
        } elseif ($axisType == $axisTypeProduct) {
            //商品別
            $axis .= 'company_id';
            $axis .= ', product_id';
            $axis .= ', cm_large_genre_name';
            $axisNames['c.name'] = 'company_name';
            $axisNames['p.name'] = 'product_name';
            $axisNames['cm_large_genre_name'] = 'cm_large_genre_name';

            $axisJoins .= ' LEFT JOIN companies c ';
            $axisJoins .= '   ON gl.company_id = c.id ';
            $axisJoins .= ' LEFT JOIN products p ';
            $axisJoins .= '   ON gl.product_id = p.id ';
        }

        list(
            $withWhere,
            $codeBind,
            $channelBind) = $this->createListWhere($bindings, $isConditionCross, $startDate, $endDate, $startTime, $endTime, $cmType, $regionId, $division, $codes, $channels, $conv_15_sec_flag, $straddlingFlg);

        $st = new Carbon($startDate);
        $et = new Carbon($endDate);
        $bindings[':startTimestamp'] = $st->subDay()->toDateTimeString();
        $bindings[':endTimestamp'] = $et->addDay(2)->toDateTimeString();

        $cmLargeGenreBind = $this->createArrayBindParam('cmLargeGenres', [
            'cmLargeGenres' => $cmLargeGenres,
        ], $bindings);
        $exclusionCompanyIdsBind = $this->createArrayBindParam('exclusionCompanyIds', [
            'exclusionCompanyIds' => $exclusionCompanyIds,
        ], $bindings);

        $divCodes = [];

        if ($isConditionCross) {
            $divCodes[] = 'condition_cross';
        } else {
            $divCodes = array_filter($codes, function ($v, $k) {
                return $v != 'personal' && $v != 'household';
            }, ARRAY_FILTER_USE_BOTH);
        }

        $with = '';
        $with .= ' WITH cm_list AS ( ';
        $with .= '   SELECT ';
        $with .= '     c.cm_id ';
        $with .= '     , c.prog_id ';
        $with .= '     , c.time_box_id ';
        $with .= '     , c.started_at ';
        $with .= '     , c.date ';
        $with .= '     , c.duration ';
        $with .= '     , c.product_id ';
        $with .= '     , c.company_id ';
        $with .= '     , clg.cm_large_genre_name ';
        $with .= "     , COUNT(*) OVER (PARTITION BY ${axis}) total_count ";
        $with .= "     , SUM(duration) OVER (PARTITION BY ${axis}) total_duration ";
        $with .= '     , SUM( ';
        $with .= '       ROUND( ';
        $with .= '         ( ';
        $with .= '           c.personal_viewing_rate * CASE ';
        $with .= '             WHEN :conv15SecFlag = 1 ';
        $with .= '               THEN duration::real / 15 ';
        $with .= '             ELSE 1 ';
        $with .= '             END ';
        $with .= '         ) ';
        $with .= '         , 1 ';
        $with .= '       ) ';
        $with .= "     ) OVER (PARTITION BY ${axis})::real rt_personal_viewing_grp ";
        $with .= '     , SUM( ';
        $with .= '       ROUND( ';
        $with .= '         ( ';
        $with .= '           c.ts_personal_viewing_rate * CASE ';
        $with .= '             WHEN :conv15SecFlag = 1 ';
        $with .= '               THEN duration ::real / 15 ';
        $with .= '             ELSE 1 ';
        $with .= '             END ';
        $with .= '         ) ';
        $with .= '         , 1 ';
        $with .= '       ) ';
        $with .= "     ) OVER (PARTITION BY ${axis})::real ts_personal_viewing_grp ";
        $with .= '     , SUM( ';
        $with .= '       ROUND( ';
        $with .= '         ( ';
        $with .= '           c.ts_personal_total_viewing_rate * CASE ';
        $with .= '             WHEN :conv15SecFlag = 1 ';
        $with .= '               THEN duration ::real / 15 ';
        $with .= '             ELSE 1 ';
        $with .= '             END ';
        $with .= '         ) ';
        $with .= '         , 1 ';
        $with .= '       ) ';
        $with .= "     ) OVER (PARTITION BY ${axis})::real total_personal_viewing_grp ";
        $with .= '     , SUM( ';
        $with .= '       ROUND( ';
        $with .= '         ( ';
        $with .= '           COALESCE( ';
        $with .= '             c.ts_personal_gross_viewing_rate ';
        $with .= '             , c.personal_viewing_rate ';
        $with .= '           ) * CASE ';
        $with .= '             WHEN :conv15SecFlag = 1 ';
        $with .= '               THEN duration ::real / 15 ';
        $with .= '             ELSE 1 ';
        $with .= '             END ';
        $with .= '         ) ';
        $with .= '         , 1 ';
        $with .= '       ) ';
        $with .= "     ) OVER (PARTITION BY ${axis})::real gross_personal_viewing_grp ";
        $with .= '     , SUM( ';
        $with .= '       ROUND( ';
        $with .= '         ( ';
        $with .= '           c.household_viewing_rate * CASE ';
        $with .= '             WHEN :conv15SecFlag = 1 ';
        $with .= '               THEN duration ::real / 15 ';
        $with .= '             ELSE 1 ';
        $with .= '             END ';
        $with .= '         ) ';
        $with .= '         , 1 ';
        $with .= '       ) ';
        $with .= "     ) OVER (PARTITION BY ${axis})::real rt_household_viewing_grp ";
        $with .= '     , SUM( ';
        $with .= '       ROUND( ';
        $with .= '         ( ';
        $with .= '           c.ts_household_viewing_rate * CASE ';
        $with .= '             WHEN :conv15SecFlag = 1 ';
        $with .= '               THEN duration ::real / 15 ';
        $with .= '             ELSE 1 ';
        $with .= '             END ';
        $with .= '         ) ';
        $with .= '         , 1 ';
        $with .= '       ) ';
        $with .= "     ) OVER (PARTITION BY ${axis})::real ts_household_viewing_grp ";
        $with .= '     , SUM( ';
        $with .= '       ROUND( ';
        $with .= '         ( ';
        $with .= '           c.ts_household_total_viewing_rate * CASE ';
        $with .= '             WHEN :conv15SecFlag = 1 ';
        $with .= '               THEN duration ::real / 15 ';
        $with .= '             ELSE 1 ';
        $with .= '             END ';
        $with .= '         ) ';
        $with .= '         , 1 ';
        $with .= '       ) ';
        $with .= "     ) OVER (PARTITION BY ${axis})::real total_household_viewing_grp ";
        $with .= '     , SUM( ';
        $with .= '       ROUND( ';
        $with .= '         ( ';
        $with .= '           COALESCE( ';
        $with .= '             c.ts_household_gross_viewing_rate ';
        $with .= '             , c.household_viewing_rate ';
        $with .= '           ) * CASE ';
        $with .= '             WHEN :conv15SecFlag = 1 ';
        $with .= '               THEN duration ::real / 15 ';
        $with .= '             ELSE 1 ';
        $with .= '             END ';
        $with .= '         ) ';
        $with .= '         , 1 ';
        $with .= '       ) ';
        $with .= "     ) OVER (PARTITION BY ${axis})::real gross_household_viewing_grp ";
        $with .= '   FROM ';
        $with .= '     commercials c ';
        $with .= '   INNER JOIN ';
        $with .= '     ( ';
        $with .= '       SELECT ';
        $with .= '         mcg.genre_id ';
        $with .= '         , mcg.cm_large_genre ';
        $with .= '         , c.name cm_large_genre_name ';
        $with .= '       FROM ';
        $with .= '         mdata_cm_genres mcg ';
        $with .= '       INNER JOIN ';
        $with .= '         codes c ';
        $with .= "         ON c.division = 'cm_large_genre' ";
        $with .= '         AND c.code = mcg.cm_large_genre ';
        $with .= "       WHERE mcg.genre_id <> '40001' "; // 番組宣伝を省く
        $with .= '     ) clg ';
        $with .= '   ON c.genre_id = clg.genre_id ';

        if (count($cmLargeGenreBind) > 0) {
            $with .= '   AND clg.cm_large_genre IN (' . implode(',', $cmLargeGenreBind) . ') ';
        }
        $with .= $withWhere;
        $with .= '   AND c.company_id NOT IN (' . implode(',', $exclusionCompanyIdsBind) . ') ';

        // 曜日＆祝日
        $wdayBind = $this->createArrayBindParam('wdays', [
            'wdays' => $wdays,
        ], $bindings);

        $with .= "   AND (EXTRACT(DOW FROM c.started_at - interval '5 hours') IN (" . implode(',', $wdayBind) . ')) ';

        if (!$holiday) {
            $with .= '   AND c.date NOT IN (SELECT holiday FROM holidays) ';
        }

        $with .= ' ) ';
        $with .= ' , union_reports AS ( ';
        $with .= '   SELECT ';
        $with .= '     cm_id ';
        $with .= '     , prog_id ';
        $with .= '     , started_at ';
        $with .= '     , division ';
        $with .= '     , code ';
        $with .= '     , MAX(rt_viewing_rate) rt_viewing_rate ';
        $with .= '     , MAX(rt_viewing_number) rt_viewing_number ';
        $with .= '     , MAX(ts_viewing_rate) ts_viewing_rate ';
        $with .= '     , MAX(ts_viewing_number) ts_viewing_number ';
        $with .= '     , MAX(total_viewing_rate) total_viewing_rate ';
        $with .= '     , MAX(total_viewing_number) total_viewing_number ';
        $with .= '     , COALESCE(MAX(gross_viewing_rate), MAX(rt_viewing_rate)) gross_viewing_rate ';
        $with .= '     , COALESCE( ';
        $with .= '       MAX(gross_viewing_number) ';
        $with .= '       , MAX(rt_viewing_number) ';
        $with .= '     ) gross_viewing_number ';
        $with .= '   FROM ';
        $with .= '     ( ';
        $with .= '       ( ';
        $with .= '         SELECT ';
        $with .= '           cl.cm_id ';
        $with .= '           , cl.prog_id ';
        $with .= '           , cl.started_at ';
        $with .= '           , division ';
        $with .= '           , code ';
        $with .= '           , viewing_rate rt_viewing_rate ';
        $with .= '           , viewing_number rt_viewing_number ';
        $with .= '           , null ts_viewing_rate ';
        $with .= '           , null ts_viewing_number ';
        $with .= '           , null total_viewing_rate ';
        $with .= '           , null total_viewing_number ';
        $with .= '           , null gross_viewing_rate ';
        $with .= '           , null gross_viewing_number ';
        $with .= '         FROM ';
        $with .= '           cm_reports cr ';
        $with .= '           INNER JOIN cm_list cl ';
        $with .= '             ON cr.division = :division AND cr.code IN (' . implode(',', $codeBind) . ') ';
        $with .= '             AND cr.cm_id = cl.cm_id ';
        $with .= '             AND cr.started_at = cl.started_at ';
        $with .= '             AND cr.prog_id = cl.prog_id ';
        $with .= '             AND cr.started_at BETWEEN :startTimestamp AND :endTimestamp ';
        $with .= '             AND cl.time_box_id = ';
        $with .= $this->createTimeBoxCaseClause($startDate, $endDate, $regionId, 'cr.started_at');
        $with .= '       ) ';
        $with .= '       UNION ( ';
        $with .= '         SELECT ';
        $with .= '           cl.cm_id ';
        $with .= '           , cl.prog_id ';
        $with .= '           , cl.started_at ';
        $with .= '           , division ';
        $with .= '           , code ';
        $with .= '           , null rt_viewing_rate ';
        $with .= '           , null rt_viewing_number ';
        $with .= '           , viewing_rate ts_viewing_rate ';
        $with .= '           , viewing_number ts_viewing_number ';
        $with .= '           , total_viewing_rate ';
        $with .= '           , total_viewing_number ';
        $with .= '           , gross_viewing_rate ';
        $with .= '           , gross_viewing_number ';
        $with .= '         FROM ';
        $with .= '           ts_cm_reports tcr ';
        $with .= '           INNER JOIN cm_list cl ';
        $with .= '             ON tcr.division = :division AND tcr.code IN (' . implode(',', $codeBind) . ') ';
        $with .= '             AND tcr.cm_id = cl.cm_id ';
        $with .= '             AND tcr.started_at = cl.started_at ';
        $with .= '             AND tcr.prog_id = cl.prog_id ';
        $with .= '             AND tcr.started_at BETWEEN :startTimestamp AND :endTimestamp ';
        $with .= '             AND cl.time_box_id = ';
        $with .= $this->createTimeBoxCaseClause($startDate, $endDate, $regionId, 'tcr.started_at');
        $with .= '       ) ';
        $with .= '     ) union_reports ';
        $with .= '   GROUP BY ';
        $with .= '     cm_id ';
        $with .= '     , prog_id ';
        $with .= '     , started_at ';
        $with .= '     , division ';
        $with .= '     , code ';
        $with .= ' ) ';
        $with .= ' , cm_data AS ( ';
        $with .= '   SELECT ';
        $with .= '     cl.cm_id ';
        $with .= '     , cl.prog_id ';
        $with .= '     , cl.product_id ';
        $with .= '     , cl.company_id ';
        $with .= '     , cl.time_box_id ';
        $with .= '     , cl.started_at ';
        $with .= '     , cl.duration ';
        $with .= '     , cl.cm_large_genre_name ';
        $with .= '     , total_count ';
        $with .= '     , total_duration ';
        $with .= '     , rt_personal_viewing_grp ';
        $with .= '     , ts_personal_viewing_grp ';
        $with .= '     , total_personal_viewing_grp ';
        $with .= '     , gross_personal_viewing_grp ';
        $with .= '     , rt_household_viewing_grp ';
        $with .= '     , ts_household_viewing_grp ';
        $with .= '     , total_household_viewing_grp ';
        $with .= '     , gross_household_viewing_grp ';
        $with .= '     , cr.code ';
        $with .= '     , cr.division ';
        $with .= '     , cr.rt_viewing_rate ';
        $with .= '     , cr.rt_viewing_number ';
        $with .= '     , cr.ts_viewing_rate ';
        $with .= '     , cr.total_viewing_rate ';
        $with .= '     , cr.gross_viewing_rate ';
        $with .= '     , cr.ts_viewing_number ';
        $with .= '     , cr.total_viewing_number ';
        $with .= '     , cr.gross_viewing_number ';
        $with .= '   FROM ';
        $with .= '     cm_list cl ';
        $with .= '     LEFT JOIN union_reports cr ';
        $with .= '       ON cr.division = :division AND cr.code IN (' . implode(',', $codeBind) . ') ';
        $with .= '       AND cr.cm_id = cl.cm_id ';
        $with .= '       AND cr.started_at = cl.started_at ';
        $with .= '       AND cr.prog_id = cl.prog_id ';
        $with .= '       AND cl.time_box_id = ';
        $with .= $this->createTimeBoxCaseClause($startDate, $endDate, $regionId, 'cr.started_at');
        $with .= ' ) ';

        $with .= ' , grp_list AS ( ';
        $with .= '   SELECT ';
        $with .= '     SUM( ';
        $with .= '       ROUND( ';
        $with .= '         ( ';
        $with .= '           cd.rt_viewing_rate ::real * CASE ';
        $with .= '             WHEN :conv15SecFlag = 1 ';
        $with .= '               THEN duration ::real / 15 ';
        $with .= '             ELSE 1 ';
        $with .= '             END ';
        $with .= '         ) ::real ';
        $with .= '         , 1 ';
        $with .= '       ) ';
        $with .= '     ) grp ';
        $with .= '     , SUM( ';
        $with .= '       ROUND( ';
        $with .= '         ( ';
        $with .= '           cd.ts_viewing_rate ::real * CASE ';
        $with .= '             WHEN :conv15SecFlag = 1 ';
        $with .= '               THEN duration ::real / 15 ';
        $with .= '             ELSE 1 ';
        $with .= '             END ';
        $with .= '         ) ::real ';
        $with .= '         , 1 ';
        $with .= '       ) ';
        $with .= '     ) ts_grp ';
        $with .= '     , SUM( ';
        $with .= '       ROUND( ';
        $with .= '         ( ';
        $with .= '           cd.total_viewing_rate ::real * CASE ';
        $with .= "             WHEN '15' = 1 ";
        $with .= '               THEN duration ::real / 15 ';
        $with .= '             ELSE 1 ';
        $with .= '             END ';
        $with .= '         ) ::real ';
        $with .= '         , 1 ';
        $with .= '       ) ';
        $with .= '     ) total_grp ';
        $with .= '     , SUM( ';
        $with .= '       ROUND( ';
        $with .= '         ( ';
        $with .= '           cd.gross_viewing_rate ::real * CASE ';
        $with .= '             WHEN :conv15SecFlag = 1 ';
        $with .= '               THEN duration ::real / 15 ';
        $with .= '             ELSE 1 ';
        $with .= '             END ';
        $with .= '         ) ::real ';
        $with .= '         , 1 ';
        $with .= '       ) ';
        $with .= '     ) gross_grp ';
        $with .= '     , rt_personal_viewing_grp ';
        $with .= '     , ts_personal_viewing_grp ';
        $with .= '     , total_personal_viewing_grp ';
        $with .= '     , gross_personal_viewing_grp ';
        $with .= '     , rt_household_viewing_grp ';
        $with .= '     , ts_household_viewing_grp ';
        $with .= '     , total_household_viewing_grp ';
        $with .= '     , gross_household_viewing_grp ';
        $with .= '     , total_duration ';
        $with .= '     , total_count ';
        $with .= '     , cd.code ';
        $with .= "     , ${axis}";
        $with .= '   FROM ';
        $with .= '     cm_data cd ';
        $with .= '   GROUP BY ';
        $with .= '     total_count ';
        $with .= '     , total_duration ';
        $with .= '     , cd.code ';
        $with .= '     , rt_personal_viewing_grp ';
        $with .= '     , ts_personal_viewing_grp ';
        $with .= '     , total_personal_viewing_grp ';
        $with .= '     , gross_personal_viewing_grp ';
        $with .= '     , rt_household_viewing_grp ';
        $with .= '     , ts_household_viewing_grp ';
        $with .= '     , total_household_viewing_grp ';
        $with .= '     , gross_household_viewing_grp ';
        $with .= "     , ${axis}";
        $with .= ' ) ';

        $with .= ' , horizontal AS ( ';
        $with .= '   SELECT ';
        $with .= implode(',', $axisNames);
        $with .= '     , total_count ';
        $with .= '     , total_duration ';
        $with .= '     , rt_personal_viewing_grp ';
        $with .= '     , ts_personal_viewing_grp ';
        $with .= '     , total_personal_viewing_grp ';
        $with .= '     , gross_personal_viewing_grp ';
        $with .= '     , rt_household_viewing_grp ';
        $with .= '     , ts_household_viewing_grp ';
        $with .= '     , total_household_viewing_grp ';
        $with .= '     , gross_household_viewing_grp ';

        if ($isConditionCross) {
            $with .= '     , SUM(ROUND(rt_condition_cross, 1)) rt_condition_cross';
            $with .= '     , SUM(ROUND(ts_condition_cross), 1) ts_condition_cross';
            $with .= '     , SUM(ROUND(total_condition_cross), 1) total_condition_cross';
            $with .= '     , SUM(ROUND(gross_condition_cross), 1) gross_condition_cross';
        } else {
            foreach ($divCodes as $key => $val) {
                $with .= '     , SUM(ROUND(rt_' . $divisionKey . $val . ', 1)) rt_' . $divisionKey . $val;
                $with .= '     , SUM(ROUND(ts_' . $divisionKey . $val . ', 1)) ts_' . $divisionKey . $val;
                $with .= '     , SUM(ROUND(total_' . $divisionKey . $val . ', 1)) total_' . $divisionKey . $val;
                $with .= '     , SUM(ROUND(gross_' . $divisionKey . $val . ', 1)) gross_' . $divisionKey . $val;
            }
        }
        $with .= '   from ';
        $with .= '     ( ';
        $with .= '       SELECT ';
        $with .= '         total_count ';
        $with .= '         , total_duration ';

        if ($isConditionCross) {
            $with .= "   , CASE WHEN gl.code = 'condition_cross' THEN gl.grp END AS rt_condition_cross ";
            $with .= "   , CASE WHEN gl.code = 'condition_cross' THEN gl.ts_grp END AS ts_condition_cross ";
            $with .= "   , CASE WHEN gl.code = 'condition_cross' THEN gl.total_grp END AS total_condition_cross ";
            $with .= "   , CASE WHEN gl.code = 'condition_cross' THEN gl.gross_grp END AS gross_condition_cross ";
        } else {
            foreach ($divCodes as $key => $val) {
                $with .= '   , CASE WHEN gl.code = :codes' . $key . ' THEN gl.grp END AS rt_' . $divisionKey . $val . ' ';
                $with .= '   , CASE WHEN gl.code = :codes' . $key . ' THEN gl.ts_grp END AS ts_' . $divisionKey . $val . ' ';
                $with .= '   , CASE WHEN gl.code = :codes' . $key . ' THEN gl.total_grp END AS total_' . $divisionKey . $val . ' ';
                $with .= '   , CASE WHEN gl.code = :codes' . $key . ' THEN gl.gross_grp END AS gross_' . $divisionKey . $val . ' ';
            }
        }
        $with .= '         , COALESCE(rt_personal_viewing_grp, 0) rt_personal_viewing_grp ';
        $with .= '         , COALESCE(ts_personal_viewing_grp, 0) ts_personal_viewing_grp ';
        $with .= '         , COALESCE(total_personal_viewing_grp, 0) total_personal_viewing_grp ';
        $with .= '         , COALESCE(gross_personal_viewing_grp, rt_personal_viewing_grp, 0) gross_personal_viewing_grp ';
        $with .= '         , COALESCE(rt_household_viewing_grp, 0) rt_household_viewing_grp ';
        $with .= '         , COALESCE(ts_household_viewing_grp, 0) ts_household_viewing_grp ';
        $with .= '         , COALESCE(total_household_viewing_grp, 0) total_household_viewing_grp ';
        $with .= '         , COALESCE(gross_household_viewing_grp, rt_household_viewing_grp, 0) gross_household_viewing_grp ';
        $with .= '         , gl.code ';

        foreach ($axisNames as $key => $value) {
            $with .= "         , ${key} ${value} ";
        }
        $with .= '       FROM ';
        $with .= '         grp_list gl ';
        $with .= $axisJoins;
        $with .= '     ) vertical ';
        $with .= '   GROUP BY ';
        $with .= implode(',', $axisNames);
        $with .= '     , total_count ';
        $with .= '     , total_duration ';
        $with .= '     , rt_personal_viewing_grp';
        $with .= '     , ts_personal_viewing_grp ';
        $with .= '     , total_personal_viewing_grp ';
        $with .= '     , gross_personal_viewing_grp ';
        $with .= '     , rt_household_viewing_grp';
        $with .= '     , ts_household_viewing_grp ';
        $with .= '     , total_household_viewing_grp ';
        $with .= '     , gross_household_viewing_grp ';
        $with .= ' ) ';

        $with .= ' , ranking AS ( ';
        $with .= '   SELECT ';
        $with .= implode(',', $axisNames);
        $with .= '     , RANK() OVER (ORDER BY rt_household_viewing_grp DESC) rank ';
        $with .= '     , total_count ';
        $with .= '     , total_duration ';
        $with .= '     , rt_personal_viewing_grp ';
        $with .= '     , ts_personal_viewing_grp ';
        $with .= '     , total_personal_viewing_grp ';
        $with .= '     , gross_personal_viewing_grp ';
        $with .= '     , rt_household_viewing_grp ';
        $with .= '     , ts_household_viewing_grp ';
        $with .= '     , total_household_viewing_grp ';
        $with .= '     , gross_household_viewing_grp ';

        if ($isConditionCross) {
            $with .= '     , COALESCE(rt_condition_cross, 0) rt_condition_cross';
            $with .= '     , COALESCE(ts_condition_cross, 0) ts_condition_cross';
            $with .= '     , COALESCE(total_condition_cross, 0) total_condition_cross';
            $with .= '     , COALESCE(gross_condition_cross, rt_condition_cross, 0) gross_condition_cross';
        } else {
            foreach ($divCodes as $key => $val) {
                $with .= '     , COALESCE(rt_' . $divisionKey . $val . ', 0) rt_' . $divisionKey . $val;
                $with .= '     , COALESCE(ts_' . $divisionKey . $val . ', 0) ts_' . $divisionKey . $val;
                $with .= '     , COALESCE(total_' . $divisionKey . $val . ', 0) total_' . $divisionKey . $val;
                $with .= '     , COALESCE(gross_' . $divisionKey . $val . ', rt_' . $divisionKey . $val . ', 0) gross_' . $divisionKey . $val;
            }
        }
        $with .= '   FROM ';
        $with .= '     horizontal ';
        $with .= ' ) ';

        $select = '';
        $select .= ' SELECT ';
        $select .= '   rank ';
        $select .= '   , ' . implode(',', $axisNames);

        $select .= '   , total_count ';
        $select .= '   , total_duration ';

        if (in_array('personal', $codes)) {
            $select .= '     , rt_personal_viewing_grp ';
        }

        if ($isConditionCross) {
            $select .= '     , COALESCE(rt_condition_cross, 0) rt_condition_cross';
        } else {
            foreach ($divCodes as $key => $val) {
                $select .= '     , COALESCE(rt_' . $divisionKey . $val . ', 0) rt_' . $divisionKey . $val;
            }
        }

        if (in_array('household', $codes)) {
            $select .= '     , rt_household_viewing_grp ';
        }

        $from = '';
        $from .= ' FROM ';
        $from .= '   ranking ';
        $orderBy = ' ORDER BY ';
        $order = empty($order) ? [] : $order;
        array_push($order, ['column' => 'rank', 'dir' => 'asc']);
        array_push($order, ['column' => 'total_count', 'dir' => 'asc']);

        foreach ($axisNames as $key => $value) {
            array_push($order, ['column' => $value, 'dir' => 'asc']);
        }

        if (isset($order) && count($order) > 0) {
            $orderArr = [];

            foreach ($order as $key => $val) {
                array_push($orderArr, " ${val['column']} ${val['dir']}");
            }
            $orderBy .= implode(',', $orderArr);
        }

        $limit = '';
        $offset = '';

        if ($csvFlag == '0') {
            if (isset($length)) {
                if ($length * $page > $maxLength) {
                    $length = 0;
                }
                $limit .= " LIMIT ${length} ";
            }

            if (isset($page)) {
                $offsetNum = $length * ($page - 1);
                $limit .= " OFFSET ${offsetNum} ";
            }
        } else {
            $limit .= " LIMIT ${maxLength} ";
        }

        $query = $with . $select . $from . $orderBy . $limit . $offset;
        $result = $this->select($query, $bindings);

        // 件数取得
        $query = $with . ' SELECT COUNT(*) cnt ' . $from;
        $resultCnt = $this->selectOne($query, $bindings);

        if ($resultCnt->cnt > $maxLength) {
            $resultCnt->cnt = $maxLength;
        }
        return [
            'list' => $result,
            'cnt' => $resultCnt->cnt,
        ];
    }

    /**
     * @param array $bindings
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param string $cmType
     * @param int $regionId
     * @param string $division
     * @param array $codes
     * @param array $channels
     * @param bool $conv15SecFlag
     * @param bool $straddlingFlg
     * @param bool $isConditionCross
     * @return array
     */
    private function createListWhere(array &$bindings, bool $isConditionCross, String $startDate, String $endDate, String $startTime, String $endTime, ?String $cmType, int $regionId, String $division, ?array $codes, array $channels, ?bool $conv15SecFlag, bool $straddlingFlg): array
    {
        $sql = '';

        // 15秒換算フラグ
        if ($conv15SecFlag === null) {
        } elseif ($conv15SecFlag == 1) {
            // する
            $bindings[':conv15SecFlag'] = 1;
        } else {
            // しない
            $bindings[':conv15SecFlag'] = 15;
        }

        $codeBind = [];

        if (!$isConditionCross) {
            // CMテーブルに存在するため、コード値には含めない
            unset($codes['personal'], $codes['household']);

            if (!empty($division)) {
                // 属性
                $bindings[':division'] = $division;
                // コード
                $codeBind = $this->createArrayBindParam('codes', [
                    'codes' => $codes,
                ], $bindings);
            }
        }

        // 日付
        $bindings[':startDate'] = $startDate;
        $bindings[':endDate'] = $endDate;

        // 放送局
        $channelBind = $this->createArrayBindParam('channels', [
            'channels' => $channels,
        ], $bindings);

        // 地域コード
        $bindings[':region_id'] = $regionId;

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

        $sql .= ' AND c.region_id = :region_id ';

        // 広告種別
        if (isset($cmType) && $cmType == '1') {
            $sql .= ' AND c.cm_type = 2';
        } elseif (isset($cmType) && $cmType == '2') {
            $sql .= ' AND c.cm_type IN (0, 1)';
        }

        return [
            $sql,
            $codeBind,
            $channelBind,
        ];
    }
}
