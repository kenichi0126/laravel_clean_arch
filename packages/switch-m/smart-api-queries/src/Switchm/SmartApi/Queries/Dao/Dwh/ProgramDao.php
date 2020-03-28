<?php

namespace Switchm\SmartApi\Queries\Dao\Dwh;

use Carbon\Carbon;
use stdClass;

class ProgramDao extends Dao
{
    /**
     * 番組リスト・検索.
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param null|array $wdays
     * @param bool $holiday
     * @param null|array $channels
     * @param null|array $genres
     * @param null|array $programNames
     * @param string $division
     * @param null|array $conditionCross
     * @param null|array $codes
     * @param $order
     * @param null|int $length
     * @param int $regionId
     * @param null|int $page
     * @param bool $straddlingFlg
     * @param bool $bsFlg
     * @param string $csvFlag
     * @param bool $programListExtensionFlag
     * @param array $dataType
     * @param array $dataTypeConst
     * @param array $prefixes
     * @param array $dataTypeFlags
     * @param string $selectedPersonalName
     * @param int $codeNumber
     * @return array
     */
    public function search(string $startDate, string $endDate, string $startTime, string $endTime, ?array $wdays, bool $holiday, ?array $channels, ?array $genres, ?array $programNames, string $division, ?array $conditionCross, ?array $codes, $order, ?int $length, int $regionId, ?int $page, bool $straddlingFlg, bool $bsFlg, string $csvFlag, bool $programListExtensionFlag, array $dataType, array $dataTypeFlags, array $dataTypeConst, array $prefixes, string $selectedPersonalName, int $codeNumber): array
    {
        list($rsTimeBoxIds, $rsProgIds, $rsPanelers) = $this->createProgramListWhere($startDate, $endDate, $channels, $genres, $programNames, $division, $conditionCross, $codes, $regionId, $bsFlg);

        if (empty($rsTimeBoxIds) && empty($rsProgIds)) {
            return [
                'list' => [],
                'cnt' => 0,
            ];
        }

        $sampleCodePrefix = $prefixes['code'];
        $sampleCodeNumberPrefix = $prefixes['number'];

        // コード種別の頭部分
        $divisionKey = "{$division}_";

        $divCodes = array_filter($codes, function ($v, $k) {
            return $v != 'personal' && $v != 'household';
        }, ARRAY_FILTER_USE_BOTH);
        $divCodes = array_merge($divCodes); //keyを連番に

        $digit = 1;

        if ($bsFlg) {
            $digit = 2;
        }

        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = array_values($dataTypeFlags);

        $cmTypes = [
            0 => 'pt',
            1 => 'sb',
            2 => 'time',
        ];

        $progStartDate = min($rsProgIds);
        $progEndDate = max($rsProgIds);

        // BS指定時はテーブル名の頭にBSを付ける
        $bsKey = '';

        if ($bsFlg) {
            $bsKey = 'bs_';
        }

        $this->createProgramListTempTable($startDate, $endDate, $startTime, $endTime, $progStartDate, $progEndDate, $wdays, $holiday, $channels, $genres, $regionId, $programNames, $bsFlg);

        if ($isRt || $isGross) {
            $bindings = [];
            $bindings[':division'] = $division;
            $reportSelectSql = '';

            foreach ($divCodes as $i => $name) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                $codeNameBinding = ":codeName${i}";
                $bindings[$codeNameBinding] = $name;

                $reportSelectSql .= ' , MAX( ';
                $reportSelectSql .= '   CASE ';
                $reportSelectSql .= "     WHEN division = :division AND code = ${codeNameBinding} ";
                $reportSelectSql .= '       THEN pr.viewing_rate ';
                $reportSelectSql .= '     END ';
                $reportSelectSql .= " ) AS viewing_rate_${code} ";
                $reportSelectSql .= ' , MAX( ';
                $reportSelectSql .= '   CASE ';
                $reportSelectSql .= "     WHEN division = :division AND code = ${codeNameBinding} ";
                $reportSelectSql .= '       THEN pr.ts_samples_viewing_rate ';
                $reportSelectSql .= '     END ';
                $reportSelectSql .= " ) AS ts_samples_viewing_rate_${code} ";
                $reportSelectSql .= ' , MAX( ';
                $reportSelectSql .= '   CASE ';
                $reportSelectSql .= "     WHEN division = :division AND code = ${codeNameBinding} ";
                $reportSelectSql .= '       THEN pr.end_viewing_rate ';
                $reportSelectSql .= '     END ';
                $reportSelectSql .= " ) AS end_viewing_rate_${code} ";
            }

            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE program_reports_pivot AS ';
            $sql .= ' SELECT ';
            $sql .= ' pl.prog_id ';
            $sql .= ' , pl.time_box_id ';
            $sql .= $reportSelectSql;
            $sql .= ' , MAX( ';
            $sql .= '     CASE ';
            $sql .= "       WHEN division = 'personal' ";
            $sql .= '         THEN pr.end_viewing_rate ';
            $sql .= '       END ';
            $sql .= '   ) personal_end_viewing_rate ';
            $sql .= ' FROM ';
            $sql .= '   program_list pl ';
            $sql .= " LEFT JOIN ${bsKey}program_reports pr ";
            $sql .= '   ON pl.prog_id = pr.prog_id ';
            $sql .= '   AND pl.time_box_id = pr.time_box_id ';
            $sql .= "   AND pr.prog_id BETWEEN ${progStartDate} AND ${progEndDate} ";
            $sql .= "   AND pr.division IN ('personal', :division) ";
            $sql .= ' GROUP BY ';
            $sql .= '   pl.prog_id ';
            $sql .= '   , pl.time_box_id; ';
            $this->select($sql, $bindings);
        }

        if ($isTs || $isGross || $isTotal) {
            $bindings = [];
            $reportSelectSql = '';

            foreach ($divCodes as $i => $name) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                $codeNameBinding = ":codeName${i}";
                $bindings[$codeNameBinding] = $name;

                foreach ($dataType as $type) {
                    $columnName = '';

                    switch ($type) {
                        case $dataTypeConst['ts']:
                            $columnName = 'viewing_rate';
                            break;
                        case $dataTypeConst['gross']:
                            $columnName = 'gross_viewing_rate';
                            break;
                        case $dataTypeConst['total']:
                            $columnName = 'total_viewing_rate';
                            break;
                    }

                    if ($columnName === '') {
                        continue;
                    }
                    $reportSelectSql .= ' , MAX( ';
                    $reportSelectSql .= '   CASE ';
                    $reportSelectSql .= "     WHEN division = :division AND code = ${codeNameBinding} ";
                    $reportSelectSql .= "       THEN pr.${columnName} ";
                    $reportSelectSql .= '     END ';
                    $reportSelectSql .= " ) AS ${columnName}_${code} ";
                }
            }
            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE ts_program_reports_pivot AS ';
            $sql .= ' SELECT ';
            $sql .= ' pl.prog_id ';
            $sql .= ' , pl.time_box_id ';
            $sql .= $reportSelectSql;
            $sql .= ' FROM ';
            $sql .= '   program_list pl ';

            if ($reportSelectSql !== '') {
                $bindings[':division'] = $division;
                $sql .= '   LEFT JOIN ts_program_reports pr ';
                $sql .= '     ON pl.prog_id = pr.prog_id ';
                $sql .= '     AND pl.time_box_id = pr.time_box_id ';
                $sql .= "     AND pr.prog_id BETWEEN ${progStartDate} AND ${progEndDate} ";
                $sql .= '     AND pr.c_index = 7 ';
                $sql .= '     AND pr.division = :division ';
            }
            $sql .= ' GROUP BY ';
            $sql .= '   pl.prog_id ';
            $sql .= '   , pl.time_box_id; ';
            $this->select($sql, $bindings);
        }

        if (!$bsFlg && $csvFlag === '1' && $programListExtensionFlag) {
            $this->createRtSampleTempTable(false, $conditionCross, $division, $divCodes, $rsTimeBoxIds, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName, false, $codeNumber);

            $progStart = (new Carbon($startDate))->subDay();
            $progEnd = (new Carbon($endDate))->addDay();
            $bindings = [];
            $bindings[':startDate'] = $startDate;
            $bindings[':endDate'] = $endDate;
            $bindings[':progStartDate'] = $progStart->format('Ymd') . '0000000000';
            $bindings[':progEndDate'] = $progEnd->format('Ymd') . '9999999999';

            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE cm_list AS ';
            $sql .= ' SELECT ';
            $sql .= '   c.cm_id ';
            $sql .= '   , c.company_id ';
            $sql .= '   , c.started_at ';
            $sql .= '   , c.prog_id ';
            $sql .= '   , c.time_box_id ';
            $sql .= '   , c.cm_type ';
            $sql .= '   , c.personal_viewing_rate ';
            $sql .= '   , c.household_viewing_rate ';
            $sql .= ' FROM ';
            $sql .= '   commercials c ';
            $sql .= ' WHERE ';
            $sql .= '   c.date BETWEEN :startDate AND :endDate ';

            $sql .= '   AND c.prog_id BETWEEN :progStartDate AND :progEndDate ';
            $sql .= ' ; ';
            $this->select($sql, $bindings);

            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE cv_list ( ';
            $sql .= '   cm_id VARCHAR(32) ';
            $sql .= '   , prog_id VARCHAR(32) ';
            $sql .= '   , started_at datetime ';
            $sql .= '   , time_box_id int ';
            $sql .= '   , paneler_id int ';
            $sql .= ' ) DISTSTYLE ALL SORTKEY(paneler_id); ';
            $this->select($sql);

            $bindings = [];
            $bindings[':startDate'] = $startDate;
            $bindings[':endDate'] = $endDate;
            $bindings[':progStartDate'] = $progStart->format('Ymd') . '0000000000';
            $bindings[':progEndDate'] = $progEnd->format('Ymd') . '9999999999';
            $bindings[':regionId'] = $regionId;
            $sql = '';
            $sql .= ' INSERT INTO cv_list ';
            $sql .= ' SELECT ';
            $sql .= '   c.cm_id ';
            $sql .= '   , c.prog_id ';
            $sql .= '   , c.started_at ';
            $sql .= '   , c.time_box_id ';
            $sql .= '   , cv.paneler_id ';
            $sql .= ' FROM ';
            $sql .= '   cm_list c ';
            $sql .= ' LEFT JOIN ';
            $sql .= '   cm_viewers cv ';
            $sql .= ' ON ';
            $sql .= '   c.cm_id = cv.cm_id AND c.started_at = cv.started_at AND c.prog_id = cv.prog_id  ';
            $sql .= ' WHERE ';
            $sql .= '   cv.date BETWEEN :startDate AND :endDate ';
            $sql .= '   AND cv.prog_id BETWEEN :progStartDate AND :progEndDate ';
            $sql .= '   AND cv.region_id = :regionId ';
            $sql .= ' ; ';
            $this->insertTemporaryTable($sql, $bindings);

            $sql = '';
            $sql .= ' ANALYZE cv_list; ';
            $this->select($sql);

            $cvUnionSqlArr = [];
            $reportSelectSqlArr = [];
            $typeSelectSqlArr = [];
            $cn = count($divCodes);

            for ($i = 0; $i < $cn; $i++) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                $number = sprintf('%s%02d', $sampleCodeNumberPrefix, $i);
                $cvUnionSql = '';
                $cvUnionSql .= ' SELECT ';
                $cvUnionSql .= '   cm_id ';
                $cvUnionSql .= '   , started_at ';
                $cvUnionSql .= '   , time_box_id ';
                $cvUnionSql .= '   , prog_id ';
                $cvUnionSql .= '   , COUNT(paneler_id) number ';
                $cvUnionSql .= "   , '${code}' code ";
                $cvUnionSql .= ' FROM ';
                $cvUnionSql .= '   cv_list cv ';
                $cvUnionSql .= ' WHERE ';
                $cvUnionSql .= '   EXISTS ( ';
                $cvUnionSql .= '     SELECT ';
                $cvUnionSql .= '       1 ';
                $cvUnionSql .= '     FROM ';
                $cvUnionSql .= '       samples s ';
                $cvUnionSql .= '     WHERE ';
                $cvUnionSql .= '       s.time_box_id = cv.time_box_id ';
                $cvUnionSql .= '       AND s.paneler_id = cv.paneler_id ';
                $cvUnionSql .= "       AND ${code} = 1 ";
                $cvUnionSql .= '   ) ';
                $cvUnionSql .= ' GROUP BY ';
                $cvUnionSql .= '   cm_id ';
                $cvUnionSql .= '   , time_box_id ';
                $cvUnionSql .= '   , started_at ';
                $cvUnionSql .= '   , prog_id ';
                $cvUnionSqlArr[] = $cvUnionSql;

                $reportSelectSql = '';
                $reportSelectSql .= '   , MAX( ';
                $reportSelectSql .= '     CASE ';
                $reportSelectSql .= "       WHEN code = '${code}' ";
                $reportSelectSql .= '         THEN number ';
                $reportSelectSql .= '       END ::numeric / ( ';
                $reportSelectSql .= '       SELECT ';
                $reportSelectSql .= "         ${number} ";
                $reportSelectSql .= '       FROM ';
                $reportSelectSql .= '         rt_numbers s ';
                $reportSelectSql .= '       WHERE ';
                $reportSelectSql .= '         cu.time_box_id = s.time_box_id ';
                $reportSelectSql .= '     ) * 100 ';
                $reportSelectSql .= "   ) AS viewing_rate_${code} ";
                $reportSelectSqlArr[] = $reportSelectSql;

                $typeSelectSql = '';

                foreach ($cmTypes as $key => $value) {
                    $typeSelectSql .= '   , SUM( ';
                    $typeSelectSql .= '     CASE ';
                    $typeSelectSql .= "       WHEN cl.cm_type = '${key}' ";
                    $typeSelectSql .= "         THEN cr.viewing_rate_${code} ";
                    $typeSelectSql .= '       ELSE 0 ';
                    $typeSelectSql .= '       END ';
                    $typeSelectSql .= '   ) / COALESCE( ';
                    $typeSelectSql .= "     SUM(CASE WHEN cl.cm_type = '${key}' THEN 1 ELSE NULL END) ";
                    $typeSelectSql .= '     , 1 ';
                    $typeSelectSql .= "   ) viewing_rate_${code}_${value} ";
                }
                $typeSelectSqlArr[] = $typeSelectSql;
            }

            if (count($cvUnionSqlArr) > 0) {
                $sql = '';
                $sql .= ' CREATE TEMPORARY TABLE cv_unioned AS ';
                $sql .= implode(' UNION ALL ', $cvUnionSqlArr);
                $this->select($sql);
            }

            if (count($reportSelectSqlArr) > 0) {
                $sql = '';
                $sql .= ' CREATE TEMPORARY TABLE cv_reports_pivot AS ';
                $sql .= ' SELECT ';
                $sql .= '   cm_id ';
                $sql .= '   , prog_id ';
                $sql .= '   , started_at ';
                $sql .= implode('', $reportSelectSqlArr);
                $sql .= ' FROM ';
                $sql .= '   cv_unioned cu  ';
                $sql .= ' GROUP BY ';
                $sql .= '   cm_id ';
                $sql .= '   , prog_id ';
                $sql .= '   , started_at ';
                $this->select($sql);
            }

            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE cm_type_list AS ';
            $sql .= ' SELECT ';
            $sql .= '   cl.prog_id ';
            $sql .= '   , cl.time_box_id ';

            if (count($typeSelectSqlArr) > 0) {
                $sql .= implode('', $typeSelectSqlArr);
            }

            foreach ($cmTypes as $key => $value) {
                $sql .= '   , SUM( ';
                $sql .= '     CASE ';
                $sql .= "       WHEN cl.cm_type = '${key}' ";
                $sql .= '         THEN cl.personal_viewing_rate ';
                $sql .= '       ELSE 0 ';
                $sql .= '       END ';
                $sql .= '   ) / COALESCE( ';
                $sql .= "     SUM(CASE WHEN cl.cm_type = '${key}' THEN 1 ELSE NULL END) ";
                $sql .= '     , 1 ';
                $sql .= "   ) personal_viewing_rate_${value} ";
                $sql .= '   , SUM( ';
                $sql .= '     CASE ';
                $sql .= "       WHEN cl.cm_type = '${key}' ";
                $sql .= '         THEN cl.household_viewing_rate ';
                $sql .= '       ELSE 0 ';
                $sql .= '       END ';
                $sql .= '   ) / COALESCE( ';
                $sql .= "     SUM(CASE WHEN cl.cm_type = '${key}' THEN 1 ELSE NULL END) ";
                $sql .= '     , 1 ';
                $sql .= "   ) household_viewing_rate_${value} ";
            }
            $sql .= ' FROM ';
            $sql .= '   cm_list cl ';

            if (count($typeSelectSqlArr) > 0) {
                $sql .= '   LEFT JOIN cv_reports_pivot cr ';
                $sql .= '     ON cl.cm_id = cr.cm_id ';
                $sql .= '     AND cl.started_at = cr.started_at ';
                $sql .= '     AND cl.prog_id = cr.prog_id ';
            }
            $sql .= ' GROUP BY ';
            $sql .= '   cl.prog_id ';
            $sql .= '   , cl.time_box_id; ';
            $this->select($sql);
        }

        $hasPersonal = in_array('personal', $codes);
        $hasHousehold = in_array('household', $codes) || $division == 'condition_cross';

        $sql = '';
        $sql .= ' SELECT ';
        $sql .= "   to_char(pl.date, 'yyyy/mm/dd') AS date ";

        if ($csvFlag !== '1') {
            $sql .= ' , pl.prog_id ';
            $sql .= ' , pl.time_box_id ';

            if ($holiday) {
                $sql .= ' , holiday ';
            }
        }
        $sql .= '   , CASE ';
        $sql .= '     WHEN pl.d = 0 ';
        $sql .= "       THEN '日' ";
        $sql .= '     WHEN pl.d = 1 ';
        $sql .= "       THEN '月' ";
        $sql .= '     WHEN pl.d = 2 ';
        $sql .= "       THEN '火' ";
        $sql .= '     WHEN pl.d = 3 ';
        $sql .= "       THEN '水' ";
        $sql .= '     WHEN pl.d = 4 ';
        $sql .= "       THEN '木' ";
        $sql .= '     WHEN pl.d = 5 ';
        $sql .= "       THEN '金' ";
        $sql .= '     WHEN pl.d = 6 ';
        $sql .= "       THEN '土' ";
        $sql .= '     END ';

        if ($csvFlag === '1' && $holiday) {
            $sql .= "  || CASE WHEN holiday IS NOT NULL THEN  '(祝)' ELSE '' END ";
        }
        $sql .= '     dow ';

        $sql .= '   , lpad( ';
        $sql .= "     to_char(pl.calc_started_at - interval '5 hours', 'HH24') ::numeric + 5 || to_char(pl.calc_started_at, ':MI:SS') ";
        $sql .= '     , 8 ';
        $sql .= "     , '0' ";
        $sql .= '   ) real_started_at ';
        $sql .= '   , lpad( ';
        $sql .= '     to_char( ';
        $sql .= "       pl.calc_ended_at - interval '5 hours 1 seconds' ";
        $sql .= "       , 'HH24' ";
        $sql .= '     ) ::numeric + 5 || to_char( ';
        $sql .= "       pl.calc_ended_at - interval '1 seconds' ";
        $sql .= "       , ':MI:SS' ";
        $sql .= '     ) ';
        $sql .= '     , 8 ';
        $sql .= "     , '0' ";
        $sql .= '   ) real_ended_at ';

        $sql .= '   , TRUNC(pl.fraction / 60) fraction ';

        if ($csvFlag !== '1') {
            $sql .= '   , pl.channel_id ';
        }
        $sql .= '   , pl.channel_code_name channel_code_name ';
        $sql .= '   , pl.genre genre ';
        $sql .= '   , pl.title title ';

        // rt
        if ($isRt) {
            if ($hasPersonal) {
                $sql .= " , ROUND(COALESCE(pl.personal_viewing_rate, 0), ${digit}) rt_personal_viewing_rate ";
            }

            if (count($divCodes) > 0) {
                foreach ($divCodes as $key => $value) {
                    $code = sprintf('%s%02d', $sampleCodePrefix, $key);
                    $divCode = 'rt_' . $divisionKey . $value;
                    $sql .= "   ,ROUND(COALESCE(pr.viewing_rate_${code}, 0), ${digit}) ${divCode} ";
                }
            }

            if ($hasHousehold) {
                $sql .= " ,ROUND(COALESCE(pl.household_viewing_rate, 0), ${digit}) rt_household_viewing_rate ";
            }
        }

        // ts
        if ($isTs) {
            if ($hasPersonal) {
                $sql .= " ,ROUND(COALESCE(pl.ts_personal_viewing_rate, 0), ${digit}) ts_personal_viewing_rate ";
            }

            if (count($divCodes) > 0) {
                foreach ($divCodes as $key => $value) {
                    $code = sprintf('%s%02d', $sampleCodePrefix, $key);
                    $divCode = 'ts_' . $divisionKey . $value;
                    $sql .= "   ,ROUND(COALESCE(tspr.viewing_rate_${code}, 0), ${digit}) ${divCode} ";
                }
            }

            if ($hasHousehold) {
                $sql .= " ,ROUND(COALESCE(pl.ts_household_viewing_rate, 0), ${digit}) ts_household_viewing_rate ";
            }
        }

        // gross
        if ($isGross) {
            if ($hasPersonal) {
                $sql .= " , ROUND(COALESCE(pl.gross_personal_viewing_rate, pl.ts_samples_personal_viewing_rate, 0), ${digit}) gross_personal_viewing_rate ";
            }

            if (count($divCodes) > 0) {
                foreach ($divCodes as $key => $value) {
                    $code = sprintf('%s%02d', $sampleCodePrefix, $key);
                    $divCode = 'gross_' . $divisionKey . $value;
                    $sql .= "   ,ROUND(COALESCE(tspr.gross_viewing_rate_${code}, ts_samples_viewing_rate_${code}, 0), ${digit}) ${divCode} ";
                }
            }

            if ($hasHousehold) {
                $sql .= " ,ROUND(COALESCE(pl.gross_household_viewing_rate, pl.ts_samples_household_viewing_rate, 0), ${digit}) gross_household_viewing_rate ";
            }
        }

        // End Rating ----------------------------------------
        if (!$bsFlg && $csvFlag === '1' && $programListExtensionFlag) {
            if ($hasPersonal) {
                $sql .= ' ,ROUND(COALESCE(pr.personal_end_viewing_rate, 0), 1) end_personal_viewing_rate  ';
            }

            if (count($divCodes) > 0) {
                foreach ($divCodes as $key => $value) {
                    $code = sprintf('%s%02d', $sampleCodePrefix, $key);
                    $divCode = 'end_' . $divisionKey . $value;
                    $sql .= "   ,ROUND(COALESCE(pr.end_viewing_rate_${code}, 0), 1) ${divCode} ";
                }
            }
        }

        if ($hasHousehold && !$bsFlg) {
            $sql .= ' ,ROUND(COALESCE(pl.household_end_viewing_rate, 0), 1) household_end_viewing_rate ';
        }

        if (!$bsFlg && $csvFlag === '1' && $programListExtensionFlag) {
            // Time Rating ----------------------------------------
            if ($hasPersonal) {
                $sql .= '   ,ROUND(COALESCE(ctl.personal_viewing_rate_time, 0), 1) personal_cm_time_viewing_rate ';
            }

            if (count($divCodes) > 0) {
                foreach ($divCodes as $key => $value) {
                    $code = sprintf('%s%02d', $sampleCodePrefix, $key);
                    $divCode = $divisionKey . $value . '_cm_time_viewing_rate';
                    $sql .= "   ,ROUND(COALESCE(ctl.viewing_rate_${code}_time, 0), 1) ${divCode} ";
                }
            }

            if ($hasHousehold) {
                $sql .= '   ,ROUND(COALESCE(ctl.household_viewing_rate_time, 0), 1) household_cm_time_viewing_rate ';
            }

            // Pt Rating ----------------------------------------
            if ($hasPersonal) {
                $sql .= '   ,ROUND(COALESCE(ctl.personal_viewing_rate_pt, 0), 1) personal_cm_pt_viewing_rate ';
            }

            if (count($divCodes) > 0) {
                foreach ($divCodes as $key => $value) {
                    $code = sprintf('%s%02d', $sampleCodePrefix, $key);
                    $divCode = $divisionKey . $value . '_cm_pt_viewing_rate';
                    $sql .= "   ,ROUND(COALESCE(ctl.viewing_rate_${code}_pt, 0), 1) ${divCode} ";
                }
            }

            if ($hasHousehold) {
                $sql .= '   ,ROUND(COALESCE(ctl.household_viewing_rate_pt, 0), 1) household_cm_pt_viewing_rate ';
            }

            // Sb Rating ----------------------------------------
            if ($hasPersonal) {
                $sql .= '   ,ROUND(COALESCE(ctl.personal_viewing_rate_sb, 0), 1) personal_cm_sb_viewing_rate ';
            }

            if (count($divCodes) > 0) {
                foreach ($divCodes as $key => $value) {
                    $code = sprintf('%s%02d', $sampleCodePrefix, $key);
                    $divCode = $divisionKey . $value . '_cm_sb_viewing_rate';
                    $sql .= "   ,ROUND(COALESCE(ctl.viewing_rate_${code}_sb, 0), 1) ${divCode} ";
                }
            }

            if ($hasHousehold) {
                $sql .= '   ,ROUND(COALESCE(ctl.household_viewing_rate_sb, 0), 1) household_cm_sb_viewing_rate ';
            }
        }

        if ($hasHousehold && !$bsFlg) {
            $sql .= '   ,ROUND(COALESCE(pl.household_viewing_share, 0), 1) household_viewing_share ';
        }

        $sql .= ' FROM ';
        $sql .= '   program_list pl ';

        if ($isRt || $isGross) {
            $sql .= '   LEFT JOIN program_reports_pivot pr ';
            $sql .= '     ON pl.prog_id = pr.prog_id ';
            $sql .= '     AND pl.time_box_id = pr.time_box_id ';
        }

        if ($isTs || $isGross || $isTotal) {
            $sql .= '   LEFT JOIN ts_program_reports_pivot tspr ';
            $sql .= '     ON pl.prog_id = tspr.prog_id ';
            $sql .= '     AND pl.time_box_id = tspr.time_box_id ';
        }

        if (!$bsFlg && $csvFlag === '1' && $programListExtensionFlag) {
            $sql .= '   LEFT JOIN cm_type_list ctl ';
            $sql .= '     ON pl.prog_id = ctl.prog_id ';
            $sql .= '     AND pl.time_box_id = ctl.time_box_id ';
        }
        $sql .= ' ORDER BY ';

        if ($csvFlag !== '1') {
            if (count($order) > 0) {
                $orderArr = [];

                foreach ($order as $key => $val) {
                    if ($val['column'] === 'date') {
                        $val['column'] = 'calc_started_at';
                    }

                    if ($val['dir'] === 'desc') {
                        $val['dir'] = $val['dir'] . ' NULLS LAST ';
                    } else {
                        $val['dir'] = $val['dir'] . ' NULLS FIRST ';
                    }
                    array_push($orderArr, "   ${val['column']} ${val['dir']}");
                }
                $sql .= implode(',', $orderArr) . ',';
            }
            $sql .= '   channel_code_name asc ';
            $sql .= '   , calc_started_at asc ';

            $offsetNum = $length * ($page - 1);
            $sql .= " LIMIT ${length} ";
            $sql .= " OFFSET ${offsetNum}; ";
        } else {
            $sql .= '   channel_code_name asc ';
            $sql .= '   , calc_started_at asc; ';
        }
        $records = $this->select($sql);

        // 件数取得
        $recordCount = $this->selectOne('SELECT COUNT(*) cnt FROM program_list p;');

        return [
            'list' => $records,
            'cnt' => $recordCount->cnt,
        ];
    }

    /**
     * 番組リスト・検索.（拡張、オリジナル）.
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param null|array $wdays
     * @param bool $holiday
     * @param null|array $channels
     * @param null|array $genres
     * @param null|array $programNames
     * @param string $division
     * @param null|array $conditionCross
     * @param null|array $codes
     * @param $order
     * @param null|int $length
     * @param int $regionId
     * @param null|int $page
     * @param bool $straddlingFlg
     * @param bool $bsFlg
     * @param string $csvFlag
     * @param bool $programListExtensionFlag
     * @param array $dataType
     * @param array $prefixes
     * @param array $dataTypeFlags
     * @param array $dataTypeConst
     * @param string $selectedPersonalName
     * @param int $codeNumber
     * @return array
     */
    public function searchOriginal(string $startDate, string $endDate, string $startTime, string $endTime, ?array $wdays, bool $holiday, ?array $channels, ?array $genres, ?array $programNames, string $division, ?array $conditionCross, ?array $codes, $order, ?int $length, int $regionId, ?int $page, bool $straddlingFlg, bool $bsFlg, string $csvFlag, bool $programListExtensionFlag, array $dataType, array $dataTypeFlags, array $dataTypeConst, array $prefixes, string $selectedPersonalName, int $codeNumber): array
    {
        list($rsTimeBoxIds, $rsProgIds, $rsPanelers) = $this->createProgramListWhere($startDate, $endDate, $channels, $genres, $programNames, $division, $conditionCross, $codes, $regionId, $bsFlg);

        if (empty($rsTimeBoxIds) && empty($rsProgIds)) {
            return [
                'list' => [],
                'cnt' => 0,
            ];
        }

        $sampleCodePrefix = $prefixes['code'];
        $sampleCodeNumberPrefix = $prefixes['number'];

        // コード種別の頭部分
        $divisionKey = "{$division}_";

        $isConditionCross = $division == 'condition_cross';

        $hasPersonal = !$isConditionCross && in_array('personal', $codes);
        $hasHousehold = $isConditionCross || in_array('household', $codes);

        $divCodes = [];

        if ($isConditionCross) {
            $divCodes[] = 'condition_cross';
        } else {
            $divCodes = array_filter($codes, function ($v, $k) {
                return $v != 'personal' && $v != 'household';
            }, ARRAY_FILTER_USE_BOTH);
        }
        $divCodes = array_merge($divCodes); //keyを連番に

        $digit = 1;

        if ($bsFlg) {
            $digit = 2;
        }

        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = array_values($dataTypeFlags);

        $cmTypes = [
            0 => 'pt',
            1 => 'sb',
            2 => 'time',
        ];

        // BS指定時はテーブル名の頭にBSを付ける
        $bsKey = '';

        if ($bsFlg) {
            $bsKey = 'bs_';
        }

        $progStartDate = min($rsProgIds);
        $progEndDate = max($rsProgIds);

        $this->createProgramListTempTable($startDate, $endDate, $startTime, $endTime, $progStartDate, $progEndDate, $wdays, $holiday, $channels, $genres, $regionId, $programNames, $bsFlg);
        $this->createPvUniondTempTables($isConditionCross, $conditionCross, $division, $divCodes, $rsTimeBoxIds, $regionId, $dataType, $progStartDate, $progEndDate, $bsKey, $dataTypeFlags, $prefixes, $sampleCodeNumberPrefix, $selectedPersonalName, $codeNumber);

        if ($isRt) {
            $reportSelectSqlArr = [];
            $cnt = count($divCodes);

            for ($i = 0; $i < $cnt; $i++) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                $number = sprintf('%s%02d', $sampleCodeNumberPrefix, $i);

                $reportSelectSql = '';
                $reportSelectSql .= ' , MAX( ';
                $reportSelectSql .= '   CASE ';
                $reportSelectSql .= "     WHEN pu.code = '${code}' ";
                $reportSelectSql .= '       THEN pu.viewing_seconds ::numeric / ( ';
                $reportSelectSql .= '       ( ';
                $reportSelectSql .= '         SELECT ';
                $reportSelectSql .= "           ${number} ";
                $reportSelectSql .= '         FROM ';
                $reportSelectSql .= '           rt_numbers rtn ';
                $reportSelectSql .= '         WHERE ';
                $reportSelectSql .= '           pl.time_box_id = rtn.time_box_id ';
                $reportSelectSql .= '       ) * pl.fraction ';
                $reportSelectSql .= '     ) ';
                $reportSelectSql .= '     END ';
                $reportSelectSql .= " ) * 100 AS viewing_rate_${code} ";
                $reportSelectSql .= ' , MAX( ';
                $reportSelectSql .= '   CASE ';
                $reportSelectSql .= "     WHEN pu.code = '${code}' ";
                $reportSelectSql .= '       THEN pu.end_viewing_seconds ::numeric / ( ';
                $reportSelectSql .= '       ( ';
                $reportSelectSql .= '         SELECT ';
                $reportSelectSql .= "           ${number} ";
                $reportSelectSql .= '         FROM ';
                $reportSelectSql .= '           rt_numbers rtn ';
                $reportSelectSql .= '         WHERE ';
                $reportSelectSql .= '           pl.time_box_id = rtn.time_box_id ';
                $reportSelectSql .= '       ) * 60 ';
                $reportSelectSql .= '     ) ';
                $reportSelectSql .= '     END ';
                $reportSelectSql .= " ) * 100 AS end_viewing_rate_${code} ";
                $reportSelectSqlArr[] = $reportSelectSql;
            }

            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE program_reports_pivot AS ';
            $sql .= ' SELECT ';
            $sql .= ' pl.prog_id ';
            $sql .= ' , pl.time_box_id ';

            if (count($reportSelectSqlArr) > 0) {
                $sql .= implode('', $reportSelectSqlArr);
            }
            $sql .= ' , MAX( ';
            $sql .= '     CASE ';
            $sql .= "       WHEN division = 'personal' ";
            $sql .= '         THEN pr.end_viewing_rate ';
            $sql .= '       END ';
            $sql .= '   ) personal_end_viewing_rate ';
            $sql .= ' FROM ';
            $sql .= '   program_list pl ';

            if (count($reportSelectSqlArr) > 0) {
                $sql .= '   LEFT JOIN pv_unioned pu ';
                $sql .= '     ON pl.prog_id = pu.prog_id ';
                $sql .= '     AND pl.time_box_id = pu.time_box_id ';
            }
            $sql .= "   LEFT JOIN ${bsKey}program_reports pr ";
            $sql .= '     ON pl.prog_id = pr.prog_id ';
            $sql .= '     AND pl.time_box_id = pr.time_box_id ';
            $sql .= "     AND pr.prog_id BETWEEN ${progStartDate} AND ${progEndDate} ";
            $sql .= "     AND pr.division = 'personal' ";
            $sql .= ' GROUP BY ';
            $sql .= '   pl.prog_id ';
            $sql .= '   , pl.time_box_id; ';
            $this->select($sql);
        }

        if ($isTs) {
            $reportSelectSqlArr = [];
            $cnt = count($divCodes);

            for ($i = 0; $i < $cnt; $i++) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                $number = sprintf('%s%02d', $sampleCodeNumberPrefix, $i);

                $reportSelectSql = '';
                $reportSelectSql .= ' , MAX( ';
                $reportSelectSql .= '   CASE ';
                $reportSelectSql .= "     WHEN pu.code = '${code}' ";
                $reportSelectSql .= '       THEN pu.viewing_seconds ::numeric / ( ';
                $reportSelectSql .= '       ( ';
                $reportSelectSql .= '         SELECT ';
                $reportSelectSql .= "           ${number} ";
                $reportSelectSql .= '         FROM ';
                $reportSelectSql .= '           ts_numbers rtn ';
                $reportSelectSql .= '         WHERE ';
                $reportSelectSql .= '           pl.time_box_id = rtn.time_box_id ';
                $reportSelectSql .= '       ) * pl.fraction ';
                $reportSelectSql .= '     ) ';
                $reportSelectSql .= '     END ';
                $reportSelectSql .= " ) * 100 AS viewing_rate_${code} ";
                $reportSelectSqlArr[] = $reportSelectSql;
            }

            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE ts_program_reports_pivot AS ';
            $sql .= ' SELECT ';
            $sql .= ' pl.prog_id ';
            $sql .= ' , pl.time_box_id ';

            if (count($reportSelectSqlArr) > 0) {
                $sql .= implode('', $reportSelectSqlArr);
            }
            $sql .= ' FROM ';
            $sql .= '   program_list pl ';

            if (count($reportSelectSqlArr) > 0) {
                $sql .= '   LEFT JOIN ts_pv_unioned pu ';
                $sql .= '     ON pl.prog_id = pu.prog_id ';
                $sql .= '     AND pl.time_box_id = pu.time_box_id ';
            }
            $sql .= ' GROUP BY ';
            $sql .= '   pl.prog_id ';
            $sql .= '   , pl.time_box_id; ';
            $this->select($sql);
        }

        if ($isGross) {
            $reportSelectSqlArr = [];
            $cnt = count($divCodes);

            for ($i = 0; $i < $cnt; $i++) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                $number = sprintf('%s%02d', $sampleCodeNumberPrefix, $i);

                $reportSelectSql = '';
                $reportSelectSql .= ' , MAX( ';
                $reportSelectSql .= '     CASE ';
                $reportSelectSql .= "       WHEN pu.code = '${code}' ";
                $reportSelectSql .= '         THEN pu.viewing_seconds ::numeric / ( ';
                $reportSelectSql .= '         ( ';
                $reportSelectSql .= '           SELECT ';
                $reportSelectSql .= "             ${number} ";
                $reportSelectSql .= '           FROM ';
                $reportSelectSql .= '             ts_numbers rtn ';
                $reportSelectSql .= '           WHERE ';
                $reportSelectSql .= '             pl.time_box_id = rtn.time_box_id ';
                $reportSelectSql .= '         ) * pl.fraction ';
                $reportSelectSql .= '       ) ';
                $reportSelectSql .= '       END ';
                $reportSelectSql .= "   ) * 100 AS viewing_rate_${code} ";
                $reportSelectSqlArr[] = $reportSelectSql;
            }
            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE gross_program_reports_pivot AS ';
            $sql .= ' SELECT ';
            $sql .= ' pl.prog_id ';
            $sql .= ' , pl.time_box_id ';

            if (count($reportSelectSqlArr) > 0) {
                $sql .= implode('', $reportSelectSqlArr);
            }
            $sql .= ' FROM ';
            $sql .= '   program_list pl ';

            if (count($reportSelectSqlArr) > 0) {
                $sql .= '   LEFT JOIN gross_pv_unioned pu ';
                $sql .= '     ON pl.prog_id = pu.prog_id ';
                $sql .= '     AND pl.time_box_id = pu.time_box_id ';
            }
            $sql .= ' GROUP BY ';
            $sql .= '   pl.prog_id ';
            $sql .= '   , pl.time_box_id; ';
            $this->select($sql);
        }

        if (!$bsFlg && $csvFlag === '1' && $programListExtensionFlag) {
            if (!($isRt || $isGross || $isRtTotal)) {
                if (count($divCodes) > 0) {
                    $this->createRtSampleTempTable($isConditionCross, $conditionCross, $division, $divCodes, $rsTimeBoxIds, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName, false, $codeNumber);
                }
            }
            $progStart = (new Carbon($startDate))->subDay();
            $progEnd = (new Carbon($endDate))->addDay();
            $bindings = [];
            $bindings[':startDate'] = $startDate;
            $bindings[':endDate'] = $endDate;
            $bindings[':progStartDate'] = $progStart->format('Ymd') . '0000000000';
            $bindings[':progEndDate'] = $progEnd->format('Ymd') . '9999999999';

            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE cm_list AS ';
            $sql .= ' SELECT ';
            $sql .= '   c.cm_id ';
            $sql .= '   , c.company_id ';
            $sql .= '   , c.started_at ';
            $sql .= '   , c.prog_id ';
            $sql .= '   , c.time_box_id ';
            $sql .= '   , c.cm_type ';
            $sql .= '   , c.personal_viewing_rate ';
            $sql .= '   , c.household_viewing_rate ';
            $sql .= ' FROM ';
            $sql .= '   commercials c ';
            $sql .= ' WHERE ';
            $sql .= '   c.date BETWEEN :startDate AND :endDate ';

            $sql .= '   AND c.prog_id BETWEEN :progStartDate AND :progEndDate ';
            $sql .= ' ; ';
            $this->select($sql, $bindings);

            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE cv_list ( ';
            $sql .= '   cm_id VARCHAR(32) ';
            $sql .= '   , prog_id VARCHAR(32) ';
            $sql .= '   , started_at datetime ';
            $sql .= '   , time_box_id int ';
            $sql .= '   , paneler_id int ';
            $sql .= ' ) DISTSTYLE ALL SORTKEY(paneler_id); ';
            $this->select($sql);

            $bindings = [];
            $bindings[':startDate'] = $startDate;
            $bindings[':endDate'] = $endDate;
            $bindings[':progStartDate'] = $progStart->format('Ymd') . '0000000000';
            $bindings[':progEndDate'] = $progEnd->format('Ymd') . '9999999999';
            $bindings[':regionId'] = $regionId;
            $sql = '';
            $sql .= ' INSERT INTO cv_list ';
            $sql .= ' SELECT ';
            $sql .= '   c.cm_id ';
            $sql .= '   , c.prog_id ';
            $sql .= '   , c.started_at ';
            $sql .= '   , c.time_box_id ';
            $sql .= '   , cv.paneler_id ';
            $sql .= ' FROM ';
            $sql .= '   cm_list c ';
            $sql .= ' LEFT JOIN ';
            $sql .= '   cm_viewers cv ';
            $sql .= ' ON ';
            $sql .= '   c.cm_id = cv.cm_id AND c.started_at = cv.started_at AND c.prog_id = cv.prog_id ';
            $sql .= ' WHERE ';
            $sql .= '   cv.date BETWEEN :startDate AND :endDate ';
            $sql .= '   AND cv.prog_id BETWEEN :progStartDate AND :progEndDate ';
            $sql .= '   AND cv.region_id = :regionId ';
            $sql .= ' ; ';
            $this->insertTemporaryTable($sql, $bindings);

            $sql = '';
            $sql .= ' ANALYZE cv_list; ';
            $this->select($sql);

            $cvUnionSqlArr = [];
            $reportSelectSqlArr = [];
            $typeSelectSqlArr = [];
            $cnt = count($divCodes);

            for ($i = 0; $i < $cnt; $i++) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                $number = sprintf('%s%02d', $sampleCodeNumberPrefix, $i);
                $cvUnionSql = '';
                $cvUnionSql .= ' SELECT ';
                $cvUnionSql .= '   cm_id ';
                $cvUnionSql .= '   , started_at ';
                $cvUnionSql .= '   , time_box_id ';
                $cvUnionSql .= '   , prog_id ';
                $cvUnionSql .= '   , COUNT(paneler_id) number ';
                $cvUnionSql .= "   , '${code}' code ";
                $cvUnionSql .= ' FROM ';
                $cvUnionSql .= '   cv_list cv ';
                $cvUnionSql .= ' WHERE ';
                $cvUnionSql .= '   EXISTS ( ';
                $cvUnionSql .= '     SELECT ';
                $cvUnionSql .= '       1 ';
                $cvUnionSql .= '     FROM ';
                $cvUnionSql .= '       samples s ';
                $cvUnionSql .= '     WHERE ';
                $cvUnionSql .= '       s.time_box_id = cv.time_box_id ';
                $cvUnionSql .= '       AND s.paneler_id = cv.paneler_id ';
                $cvUnionSql .= "       AND ${code} = 1 ";
                $cvUnionSql .= '   ) ';
                $cvUnionSql .= ' GROUP BY ';
                $cvUnionSql .= '   cm_id ';
                $cvUnionSql .= '   , time_box_id ';
                $cvUnionSql .= '   , started_at ';
                $cvUnionSql .= '   , prog_id ';
                $cvUnionSqlArr[] = $cvUnionSql;

                $reportSelectSql = '';
                $reportSelectSql .= '   , MAX( ';
                $reportSelectSql .= '     CASE ';
                $reportSelectSql .= "       WHEN code = '${code}' ";
                $reportSelectSql .= '         THEN number ';
                $reportSelectSql .= '       END / ( ';
                $reportSelectSql .= '       SELECT ';
                $reportSelectSql .= "         ${number} ::numeric ";
                $reportSelectSql .= '       FROM ';
                $reportSelectSql .= '         rt_numbers s ';
                $reportSelectSql .= '       WHERE ';
                $reportSelectSql .= '         cu.time_box_id = s.time_box_id ';
                $reportSelectSql .= '     ) * 100 ';
                $reportSelectSql .= "   ) AS viewing_rate_${code} ";
                $reportSelectSqlArr[] = $reportSelectSql;

                $typeSelectSql = '';

                foreach ($cmTypes as $key => $value) {
                    $typeSelectSql .= '   , SUM( ';
                    $typeSelectSql .= '     CASE ';
                    $typeSelectSql .= "       WHEN cl.cm_type = '${key}' ";
                    $typeSelectSql .= "         THEN cr.viewing_rate_${code} ";
                    $typeSelectSql .= '       ELSE 0 ';
                    $typeSelectSql .= '       END ';
                    $typeSelectSql .= '   ) / COALESCE( ';
                    $typeSelectSql .= "     SUM(CASE WHEN cl.cm_type = '${key}' THEN 1 ELSE NULL END) ";
                    $typeSelectSql .= '     , 1 ';
                    $typeSelectSql .= "   ) viewing_rate_${code}_${value} ";
                }
                $typeSelectSqlArr[] = $typeSelectSql;
            }

            if (count($cvUnionSqlArr) > 0) {
                $sql = '';
                $sql .= ' CREATE TEMPORARY TABLE cv_unioned AS ';
                $sql .= implode(' UNION ALL ', $cvUnionSqlArr);
                $this->select($sql);
            }

            if (count($reportSelectSqlArr) > 0) {
                $sql = '';
                $sql .= ' CREATE TEMPORARY TABLE cv_reports_pivot AS ';
                $sql .= ' SELECT ';
                $sql .= '   cm_id ';
                $sql .= '   , prog_id ';
                $sql .= '   , started_at ';
                $sql .= implode('', $reportSelectSqlArr);
                $sql .= ' FROM ';
                $sql .= '   cv_unioned cu  ';
                $sql .= ' GROUP BY ';
                $sql .= '   cm_id ';
                $sql .= '   , prog_id ';
                $sql .= '   , started_at ';
                $this->select($sql);
            }

            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE cm_type_list AS ';
            $sql .= ' SELECT ';
            $sql .= '   cl.prog_id ';
            $sql .= '   , cl.time_box_id ';

            if (count($typeSelectSqlArr) > 0) {
                $sql .= implode('', $typeSelectSqlArr);
            }

            foreach ($cmTypes as $key => $value) {
                $sql .= '   , SUM( ';
                $sql .= '     CASE ';
                $sql .= "       WHEN cl.cm_type = '${key}' ";
                $sql .= '         THEN cl.personal_viewing_rate ';
                $sql .= '       ELSE 0 ';
                $sql .= '       END ';
                $sql .= '   ) / COALESCE( ';
                $sql .= "     SUM(CASE WHEN cl.cm_type = '${key}' THEN 1 ELSE NULL END) ";
                $sql .= '     , 1 ';
                $sql .= "   ) personal_viewing_rate_${value} ";
                $sql .= '   , SUM( ';
                $sql .= '     CASE ';
                $sql .= "       WHEN cl.cm_type = '${key}' ";
                $sql .= '         THEN cl.household_viewing_rate ';
                $sql .= '       ELSE 0 ';
                $sql .= '       END ';
                $sql .= '   ) / COALESCE( ';
                $sql .= "     SUM(CASE WHEN cl.cm_type = '${key}' THEN 1 ELSE NULL END) ";
                $sql .= '     , 1 ';
                $sql .= "   ) household_viewing_rate_${value} ";
            }
            $sql .= ' FROM ';
            $sql .= '   cm_list cl ';

            if (count($typeSelectSqlArr) > 0) {
                $sql .= '   LEFT JOIN cv_reports_pivot cr ';
                $sql .= '     ON cl.cm_id = cr.cm_id ';
                $sql .= '     AND cl.started_at = cr.started_at ';
                $sql .= '     AND cl.prog_id = cr.prog_id ';
            }
            $sql .= ' GROUP BY ';
            $sql .= '   cl.prog_id ';
            $sql .= '   , cl.time_box_id; ';
            $this->select($sql);
        }

        $sql = '';
        $sql .= ' SELECT ';
        $sql .= "   to_char(pl.date, 'yyyy/mm/dd') AS date ";

        if ($csvFlag !== '1') {
            $sql .= ' , pl.prog_id ';
            $sql .= ' , pl.time_box_id ';

            if ($holiday) {
                $sql .= ' , holiday ';
            }
        }
        $sql .= '   , CASE ';
        $sql .= '     WHEN pl.d = 0 ';
        $sql .= "       THEN '日' ";
        $sql .= '     WHEN pl.d = 1 ';
        $sql .= "       THEN '月' ";
        $sql .= '     WHEN pl.d = 2 ';
        $sql .= "       THEN '火' ";
        $sql .= '     WHEN pl.d = 3 ';
        $sql .= "       THEN '水' ";
        $sql .= '     WHEN pl.d = 4 ';
        $sql .= "       THEN '木' ";
        $sql .= '     WHEN pl.d = 5 ';
        $sql .= "       THEN '金' ";
        $sql .= '     WHEN pl.d = 6 ';
        $sql .= "       THEN '土' ";
        $sql .= '     END ';

        if ($csvFlag === '1' && $holiday) {
            $sql .= "  || CASE WHEN holiday IS NOT NULL THEN  '(祝)' ELSE '' END ";
        }
        $sql .= '     dow ';

        $sql .= '   , lpad( ';
        $sql .= "     to_char(pl.calc_started_at - interval '5 hours', 'HH24') ::numeric + 5 || to_char(pl.calc_started_at, ':MI:SS') ";
        $sql .= '     , 8 ';
        $sql .= "     , '0' ";
        $sql .= '   ) real_started_at ';
        $sql .= '   , lpad( ';
        $sql .= '     to_char( ';
        $sql .= "       pl.calc_ended_at - interval '5 hours 1 seconds' ";
        $sql .= "       , 'HH24' ";
        $sql .= '     ) ::numeric + 5 || to_char( ';
        $sql .= "       pl.calc_ended_at - interval '1 seconds' ";
        $sql .= "       , ':MI:SS' ";
        $sql .= '     ) ';
        $sql .= '     , 8 ';
        $sql .= "     , '0' ";
        $sql .= '   ) real_ended_at ';

        $sql .= '   , TRUNC(pl.fraction / 60) fraction ';

        if ($csvFlag !== '1') {
            $sql .= '   , pl.channel_id ';
        }
        $sql .= '   , pl.channel_code_name channel_code_name ';
        $sql .= '   , pl.genre genre ';
        $sql .= '   , pl.title title ';

        // rt
        if ($isRt) {
            if ($hasPersonal) {
                $sql .= " , ROUND(COALESCE(pl.personal_viewing_rate, 0), ${digit}) rt_personal_viewing_rate ";
            }

            if (count($divCodes) > 0) {
                foreach ($divCodes as $key => $value) {
                    $code = sprintf('%s%02d', $sampleCodePrefix, $key);
                    $divCode = 'rt_' . $divisionKey . $value;

                    if ($isConditionCross && $value === 'condition_cross') {
                        $divCode = 'rt_condition_cross_viewing_rate';
                    }
                    $sql .= "   ,ROUND(COALESCE(pr.viewing_rate_${code}, 0), ${digit}) ${divCode} ";
                }
            }

            if ($hasHousehold) {
                $sql .= " ,ROUND(COALESCE(pl.household_viewing_rate, 0), ${digit}) rt_household_viewing_rate ";
            }
        }

        // ts
        if ($isTs) {
            if ($hasPersonal) {
                $sql .= " ,ROUND(COALESCE(pl.ts_personal_viewing_rate, 0), ${digit}) ts_personal_viewing_rate ";
            }

            if (count($divCodes) > 0) {
                foreach ($divCodes as $key => $value) {
                    $code = sprintf('%s%02d', $sampleCodePrefix, $key);
                    $divCode = 'ts_' . $divisionKey . $value;

                    if ($isConditionCross && $value === 'condition_cross') {
                        $divCode = 'ts_condition_cross_viewing_rate';
                    }
                    $sql .= "   ,ROUND(COALESCE(tspr.viewing_rate_${code}, 0), ${digit}) ${divCode} ";
                }
            }

            if ($hasHousehold) {
                $sql .= " ,ROUND(COALESCE(pl.ts_household_viewing_rate, 0), ${digit}) ts_household_viewing_rate ";
            }
        }

        // gross
        if ($isGross) {
            if ($hasPersonal) {
                $sql .= " , ROUND(COALESCE(pl.gross_personal_viewing_rate, pl.ts_samples_personal_viewing_rate, 0), ${digit}) gross_personal_viewing_rate ";
            }

            if (count($divCodes) > 0) {
                foreach ($divCodes as $key => $value) {
                    $code = sprintf('%s%02d', $sampleCodePrefix, $key);
                    $divCode = 'gross_' . $divisionKey . $value;

                    if ($isConditionCross && $value === 'condition_cross') {
                        $divCode = 'gross_condition_cross_viewing_rate';
                    }
                    $sql .= "   ,ROUND(COALESCE(gspr.viewing_rate_${code}, 0), ${digit}) ${divCode} ";
                }
            }

            if ($hasHousehold) {
                $sql .= " ,ROUND(COALESCE(pl.gross_household_viewing_rate, pl.ts_samples_household_viewing_rate, 0), ${digit}) gross_household_viewing_rate ";
            }
        }

        // End Rating ----------------------------------------
        if (!$bsFlg && $csvFlag === '1' && $programListExtensionFlag) {
            if ($hasPersonal) {
                $sql .= ' ,ROUND(COALESCE(pr.personal_end_viewing_rate, 0), 1) end_personal_viewing_rate  ';
            }

            if (count($divCodes) > 0) {
                foreach ($divCodes as $key => $value) {
                    $code = sprintf('%s%02d', $sampleCodePrefix, $key);
                    $divCode = 'end_' . $divisionKey . $value;

                    if ($isConditionCross && $value === 'condition_cross') {
                        $divCode = 'end_condition_cross_viewing_rate';
                    }
                    $sql .= "   ,ROUND(COALESCE(pr.end_viewing_rate_${code}, 0), 1) ${divCode} ";
                }
            }
        }

        if ($hasHousehold && !$bsFlg) {
            $sql .= ' ,ROUND(COALESCE(pl.household_end_viewing_rate, 0), 1) household_end_viewing_rate ';
        }

        if (!$bsFlg && $csvFlag === '1' && $programListExtensionFlag) {
            // Time Rating ----------------------------------------
            if ($hasPersonal) {
                $sql .= '   ,ROUND(COALESCE(ctl.personal_viewing_rate_time, 0), 1) personal_cm_time_viewing_rate ';
            }

            if (count($divCodes) > 0) {
                foreach ($divCodes as $key => $value) {
                    $code = sprintf('%s%02d', $sampleCodePrefix, $key);
                    $divCode = $divisionKey . $value . '_cm_time_viewing_rate';

                    if ($isConditionCross && $value === 'condition_cross') {
                        $divCode = 'condition_cross_cm_time_viewing_rate';
                    }
                    $sql .= "   ,ROUND(COALESCE(ctl.viewing_rate_${code}_time, 0), 1) ${divCode} ";
                }
            }

            if ($hasHousehold) {
                $sql .= '   ,ROUND(COALESCE(ctl.household_viewing_rate_time, 0), 1) household_cm_time_viewing_rate ';
            }

            // Pt Rating ----------------------------------------
            if ($hasPersonal) {
                $sql .= '   ,ROUND(COALESCE(ctl.personal_viewing_rate_pt, 0), 1) personal_cm_pt_viewing_rate ';
            }

            if (count($divCodes) > 0) {
                foreach ($divCodes as $key => $value) {
                    $code = sprintf('%s%02d', $sampleCodePrefix, $key);
                    $divCode = $divisionKey . $value . '_cm_pt_viewing_rate';

                    if ($isConditionCross && $value === 'condition_cross') {
                        $divCode = 'condition_cross_cm_pt_viewing_rate';
                    }
                    $sql .= "   ,ROUND(COALESCE(ctl.viewing_rate_${code}_pt, 0), 1) ${divCode} ";
                }
            }

            if ($hasHousehold) {
                $sql .= '   ,ROUND(COALESCE(ctl.household_viewing_rate_pt, 0), 1) household_cm_pt_viewing_rate ';
            }

            // Sb Rating ----------------------------------------
            if ($hasPersonal) {
                $sql .= '   ,ROUND(COALESCE(ctl.personal_viewing_rate_sb, 0), 1) personal_cm_sb_viewing_rate ';
            }

            if (count($divCodes) > 0) {
                foreach ($divCodes as $key => $value) {
                    $code = sprintf('%s%02d', $sampleCodePrefix, $key);
                    $divCode = $divisionKey . $value . '_cm_sb_viewing_rate';

                    if ($isConditionCross && $value === 'condition_cross') {
                        $divCode = 'condition_cross_cm_sb_viewing_rate';
                    }
                    $sql .= "   ,ROUND(COALESCE(ctl.viewing_rate_${code}_sb, 0), 1) ${divCode} ";
                }
            }

            if ($hasHousehold) {
                $sql .= '   ,ROUND(COALESCE(ctl.household_viewing_rate_sb, 0), 1) household_cm_sb_viewing_rate ';
            }
        }

        if ($hasHousehold && !$bsFlg) {
            $sql .= '   ,ROUND(COALESCE(pl.household_viewing_share, 0), 1) household_viewing_share ';
        }

        $sql .= ' FROM ';
        $sql .= '   program_list pl ';

        if ($isRt) {
            $sql .= '   LEFT JOIN program_reports_pivot pr ';
            $sql .= '     ON pl.prog_id = pr.prog_id ';
            $sql .= '     AND pl.time_box_id = pr.time_box_id ';
        }

        if ($isTs) {
            $sql .= '   LEFT JOIN ts_program_reports_pivot tspr ';
            $sql .= '     ON pl.prog_id = tspr.prog_id ';
            $sql .= '     AND pl.time_box_id = tspr.time_box_id ';
        }

        if ($isGross) {
            $sql .= '   LEFT JOIN gross_program_reports_pivot gspr ';
            $sql .= '     ON pl.prog_id = gspr.prog_id ';
            $sql .= '     AND pl.time_box_id = gspr.time_box_id ';
        }

        if (!$bsFlg && $csvFlag === '1' && $programListExtensionFlag) {
            $sql .= '   LEFT JOIN cm_type_list ctl ';
            $sql .= '     ON pl.prog_id = ctl.prog_id ';
            $sql .= '     AND pl.time_box_id = ctl.time_box_id ';
        }
        $sql .= ' ORDER BY ';

        if ($csvFlag !== '1') {
            if (count($order) > 0) {
                $orderArr = [];

                foreach ($order as $key => $val) {
                    if ($val['column'] === 'date') {
                        $val['column'] = 'calc_started_at';
                    }

                    if ($val['dir'] === 'desc') {
                        $val['dir'] = $val['dir'] . ' NULLS LAST ';
                    } else {
                        $val['dir'] = $val['dir'] . ' NULLS FIRST ';
                    }
                    array_push($orderArr, "   ${val['column']} ${val['dir']}");
                }
                $sql .= implode(',', $orderArr) . ',';
            }
            $sql .= '   channel_code_name asc ';
            $sql .= '   , calc_started_at asc ';

            $offsetNum = $length * ($page - 1);
            $sql .= " LIMIT ${length} ";
            $sql .= " OFFSET ${offsetNum}; ";
        } else {
            $sql .= '   channel_code_name asc ';
            $sql .= '   , calc_started_at asc ';
        }
        $records = $this->select($sql);

        // 件数取得
        $recordCount = $this->selectOne('SELECT COUNT(*) cnt FROM program_list p;');

        return [
            'list' => $records,
            'cnt' => $recordCount->cnt,
        ];
    }

    public function createProgramListTempTable(string $startDate, string $endDate, string $startTime, string $endTime, string $progStartDate, string $progEndDate, ?array $wdays, bool $holiday, ?array $channels, ?array $genres, int $regionId, ?array $programNames, bool $bsFlg): void
    {
        $bindings = [];
        $bindGenres = [];
        $bindWdays = [];
        $bindChannels = [];
        $bindProgramNames = [];

        $bindings[':startDate'] = $startDate;
        $bindings[':endDate'] = $endDate;
        $bindings[':regionId'] = $regionId;

        if (count($genres) > 0) {
            $bindGenres = $this->createArrayBindParam('genres', [
                'genres' => $genres,
            ], $bindings);
        }

        if (count($channels) > 0) {
            $bindChannels = $this->createArrayBindParam('channels', [
                'channels' => $channels,
            ], $bindings);
        }

        if (count($wdays) > 0) {
            $bindWdays = $this->createArrayBindParam('wdays', [
                'wdays' => $wdays,
            ], $bindings);
        }

        if (count($programNames) > 0) {
            $bindProgramNames = $this->createArrayBindParam('programNames', [
                'programNames' => $programNames,
            ], $bindings);
        }

        $bsKey = '';

        if ($bsFlg) {
            $bsKey = 'bs_';
        }

        $sql = '';
        $sql .= ' CREATE TEMPORARY TABLE program_list AS ';
        $sql .= ' SELECT ';
        $sql .= '   p.date ';
        $sql .= '   , h.holiday ';
        $sql .= '   , p.prog_id ';
        $sql .= '   , p.time_box_id ';
        $sql .= '   , EXTRACT(DOW FROM p.date) d ';
        $sql .= '   , CASE  ';
        $sql .= '     WHEN p.real_started_at < tb.started_at  ';
        $sql .= '       THEN tb.started_at  ';
        $sql .= '     ELSE p.real_started_at  ';
        $sql .= '     END AS calc_started_at ';
        $sql .= '   , CASE  ';
        $sql .= '     WHEN p.real_ended_at > tb.ended_at  ';
        $sql .= '       THEN tb.ended_at  ';
        $sql .= '     ELSE p.real_ended_at  ';
        $sql .= '     END AS calc_ended_at ';
        $sql .= '   , EXTRACT(EPOCH FROM calc_ended_at - calc_started_at) fraction ';
        $sql .= "   , TO_CHAR(calc_started_at - interval '5:00:00', 'HH24MISS') as shift_start_time ";
        $sql .= "   , TO_CHAR(calc_ended_at - interval '5:00:01', 'HH24MISS') as shift_end_time ";
        $sql .= '   , p.channel_id ';
        $sql .= '   , c.code_name channel_code_name ';
        $sql .= '   , CASE  ';
        $sql .= "     WHEN p.genre_id = '20001'  ";
        $sql .= "       THEN 'その他'  ";
        $sql .= "     WHEN p.genre_id = '20002'  ";
        $sql .= "       THEN 'ニュース/報道'  ";
        $sql .= "     WHEN p.genre_id = '20003'  ";
        $sql .= "       THEN '情報/ワイドショー'  ";
        $sql .= "     WHEN p.genre_id = '20004'  ";
        $sql .= "       THEN '音楽'  ";
        $sql .= "     WHEN p.genre_id = '20005'  ";
        $sql .= "       THEN 'バラエティー'  ";
        $sql .= "     WHEN p.genre_id = '20006'  ";
        $sql .= "       THEN 'ドラマ'  ";
        $sql .= "     WHEN p.genre_id = '20007'  ";
        $sql .= "       THEN 'アニメ/特撮'  ";
        $sql .= "     WHEN p.genre_id = '20008'  ";
        $sql .= "       THEN '映画'  ";
        $sql .= "     WHEN p.genre_id = '20009'  ";
        $sql .= "       THEN 'スポーツ'  ";
        $sql .= "     WHEN p.genre_id = '20010'  ";
        $sql .= "       THEN 'ドキュメンタリー'  ";
        $sql .= "     WHEN p.genre_id = '20011'  ";
        $sql .= "       THEN '趣味/教育'  ";
        $sql .= "     WHEN p.genre_id = '20012'  ";
        $sql .= "       THEN '演劇/公演'  ";
        $sql .= "     WHEN p.genre_id = '20013'  ";
        $sql .= "       THEN '福祉'  ";
        $sql .= "     WHEN p.genre_id = '20014'  ";
        $sql .= "       THEN '放送休止'  ";
        $sql .= '     END genre ';
        $sql .= '   , p.title ';
        $sql .= '   , p.personal_viewing_rate ';
        $sql .= '   , p.household_viewing_rate ';
        $sql .= '   , p.household_end_viewing_rate ';
        $sql .= '   , p.household_viewing_share ';

        if (!$bsFlg) {
            $sql .= '   , p.ts_samples_personal_viewing_rate ';
            $sql .= '   , p.ts_samples_household_viewing_rate ';
            $sql .= '   , p.ts_personal_viewing_rate ';
            $sql .= '   , p.ts_household_viewing_rate ';
            $sql .= '   , p.ts_personal_total_viewing_rate ';
            $sql .= '   , p.ts_household_total_viewing_rate ';
            $sql .= '   , p.ts_personal_gross_viewing_rate gross_personal_viewing_rate ';
            $sql .= '   , p.ts_household_gross_viewing_rate gross_household_viewing_rate ';
        } else {
            $sql .= '   , 0 ts_samples_personal_viewing_rate ';
            $sql .= '   , 0 ts_samples_household_viewing_rate ';
            $sql .= '   , 0 ts_personal_viewing_rate ';
            $sql .= '   , 0 ts_household_viewing_rate ';
            $sql .= '   , 0 ts_personal_total_viewing_rate ';
            $sql .= '   , 0 ts_household_total_viewing_rate ';
            $sql .= '   , 0 gross_personal_viewing_rate ';
            $sql .= '   , 0 gross_household_viewing_rate ';
        }
        $sql .= ' FROM ';
        $sql .= "   ${bsKey}programs p  ";
        $sql .= '   INNER JOIN time_boxes tb  ';
        $sql .= '     ON p.time_box_id = tb.id  ';
        $sql .= '     AND tb.region_id = :regionId  ';
        $sql .= '   INNER JOIN channels c  ';
        $sql .= '     ON p.channel_id = c.id  ';

        if ($bsFlg) {
            $sql .= "     AND c.type = 'bs' ";
        } else {
            $sql .= "     AND c.type = 'dt' ";
        }
        $sql .= '   LEFT JOIN holidays h  ';
        $sql .= '     ON p.date = h.holiday  ';
        $sql .= ' WHERE ';
        $sql .= '   p.prepared = 1  ';
        // 日付
        $sql .= '   AND p.date BETWEEN :startDate AND :endDate ';

        // 時間帯
        if ($startTime === '050000' && $endTime === '045959') {
            // 全選択の場合は検索条件に含めない
        } else {
            $bindings[':startTime'] = $startTime;
            $bindings[':endTime'] = $endTime;

            if ($endTime >= $startTime) {
                $sql .= '  AND (( ';
                $sql .= '   shift_end_time <  shift_start_time ';
                $sql .= '   AND ( ';
                $sql .= '    shift_start_time <= :endTime ';
                $sql .= '    OR shift_end_time > :startTime ';
                $sql .= '   ) ';
                $sql .= '  ) ';

                $sql .= '  OR ( ';
                $sql .= '   shift_end_time >= shift_start_time ';
                $sql .= '   AND shift_start_time <= :endTime ';
                $sql .= '   AND shift_end_time > :startTime ';
                $sql .= '  )) ';
            } else {
                $sql .= ' AND  (( ';
                $sql .= '   shift_end_time < shift_start_time ';
                $sql .= '  ) ';
                $sql .= '  OR ( ';
                $sql .= '   shift_end_time >= shift_start_time ';
                $sql .= '   AND ( ';
                $sql .= '    shift_start_time <= :endTime ';
                $sql .= '    OR shift_end_time > :startTime ';
                $sql .= '   ) ';
                $sql .= '  )) ';
            }
        }

        $sql .= '   AND (EXTRACT(DOW FROM p.date) IN (' . implode(',', $bindWdays) . ')) ';

        if (!$holiday) {
            $sql .= '   AND p.date NOT IN (SELECT holiday FROM holidays) ';
        }

        // 放送局
        if (count($bindChannels) > 0) {
            $sql .= '   AND p.channel_id IN (' . implode(',', $bindChannels) . ')  ';
        }

        // ジャンル
        $sql .= "   AND p.genre_id <> '20014'  ";

        if (count($bindGenres) > 0) {
            $sql .= '   AND p.genre_id IN (' . implode(',', $bindGenres) . ')';
        }
        $sql .= " AND prog_id BETWEEN ${progStartDate} AND ${progEndDate} ";
        // 番組名
        if (count($bindProgramNames) > 0) {
            $sql .= "   AND p.title IN (SELECT title From ${bsKey}programs WHERE prog_id IN (" . implode(',', $bindProgramNames) . ')) ';
        }
        $this->select($sql, $bindings);
    }

    public function createPvUniondTempTables(bool $isConditionCross, ?array $conditionCross, string $division, ?array $divCodes, array $timeBoxIds, string $regionId, array $dataType, string $progStartDate, string $progEndDate, string $bsKey, array $dataTypeFlags, array $prefixes, string $sampleCodeNumberPrefix, string $selectedPersonalName, int $codeNumber): void
    {
        $sampleCodePrefix = $prefixes['code'];

        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = array_values($dataTypeFlags);

        if ($isRt || $isGross || $isRtTotal) {
            if (count($divCodes) > 0) {
                $this->createRtSampleTempTable($isConditionCross, $conditionCross, $division, $divCodes, $timeBoxIds, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName, false, $codeNumber);

                $sql = '';
                $sql .= ' CREATE TEMPORARY TABLE rt_viewers AS ';
                $sql .= ' SELECT ';
                $sql .= '   pv.prog_id ';
                $sql .= '   , pv.time_box_id ';
                $sql .= '   , pv.viewing_seconds ';
                $sql .= '   , pv.end_viewing_seconds ';
                $sql .= '   , pv.paneler_id  ';
                $sql .= ' FROM ';
                $sql .= "   ${bsKey}program_viewers pv  ";
                $sql .= ' WHERE ';
                $sql .= "   pv.prog_id BETWEEN ${progStartDate} AND ${progEndDate} ";
                $sql .= ';';
                $this->select($sql);
            }
        }

        if ($isTs || $isGross || $isTotal || $isRtTotal) {
            if (count($divCodes) > 0) {
                $this->createTsSampleTempTable($isConditionCross, $conditionCross, $division, $divCodes, $timeBoxIds, $regionId, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName, false, $codeNumber);

                $sql = '';
                $sql .= ' CREATE TEMPORARY TABLE ts_viewers AS  ';
                $sql .= ' SELECT ';
                $sql .= '   pv.prog_id ';
                $sql .= '   , pv.time_box_id ';
                $sql .= '   , pv.viewing_seconds ';
                $sql .= '   , pv.gross_viewing_seconds ';
                $sql .= '   , pv.paneler_id  ';
                $sql .= ' FROM ';
                $sql .= '   ts_program_viewers pv  ';
                $sql .= ' WHERE ';
                $sql .= "   pv.prog_id BETWEEN ${progStartDate} AND ${progEndDate} ";
                $sql .= '   AND pv.c_index = 7 ';
                $sql .= ';';
                $this->select($sql);
            }
        }

        if ($isGross || $isRtTotal) {
            if (count($divCodes) > 0) {
                $sql = '';
                $sql .= ' CREATE TEMPORARY TABLE gross_viewers AS  ';
                $sql .= ' SELECT ';
                $sql .= '   prog_id ';
                $sql .= '   , time_box_id ';
                $sql .= '   , MAX(  ';
                $sql .= '     CASE  ';
                $sql .= '       WHEN gross_viewing_seconds IS NULL  ';
                $sql .= '         THEN viewing_seconds  ';
                $sql .= '       ELSE gross_viewing_seconds  ';
                $sql .= '       END ';
                $sql .= '   ) viewing_seconds ';
                $sql .= '   , paneler_id  ';
                $sql .= ' FROM ';
                $sql .= '   (  ';
                $sql .= '     SELECT ';
                $sql .= '       pv.prog_id ';
                $sql .= '       , pv.time_box_id ';
                $sql .= '       , pv.viewing_seconds ';
                $sql .= '       , NULL AS gross_viewing_seconds ';
                $sql .= '       , pv.paneler_id  ';
                $sql .= '     FROM ';
                $sql .= '       rt_viewers pv  ';
                $sql .= '     UNION ALL  ';
                $sql .= '     SELECT ';
                $sql .= '       pv.prog_id ';
                $sql .= '       , pv.time_box_id ';
                $sql .= '       , NULL ';
                $sql .= '       , pv.gross_viewing_seconds ';
                $sql .= '       , pv.paneler_id  ';
                $sql .= '     FROM ';
                $sql .= '       ts_viewers pv ';
                $sql .= '   ) pv  ';
                $sql .= ' GROUP BY ';
                $sql .= '   pv.prog_id ';
                $sql .= '   , pv.time_box_id ';
                $sql .= '   , paneler_id; ';
                $this->select($sql);
            }
        }

        if ($isRt) {
            $pvUnionSqlArr = [];
            $cnt = count($divCodes);

            for ($i = 0; $i < $cnt; $i++) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                $pvUnionSql = '';
                $pvUnionSql .= ' SELECT ';
                $pvUnionSql .= '   pv.prog_id ';
                $pvUnionSql .= '   , pv.time_box_id ';
                $pvUnionSql .= '   , SUM(pv.viewing_seconds) AS viewing_seconds ';
                $pvUnionSql .= '   , SUM(pv.end_viewing_seconds) AS end_viewing_seconds ';
                $pvUnionSql .= "   , '${code}' AS code ";
                $pvUnionSql .= ' FROM ';
                $pvUnionSql .= '   rt_viewers pv ';
                $pvUnionSql .= ' WHERE ';
                $pvUnionSql .= "   pv.prog_id BETWEEN ${progStartDate} AND ${progEndDate} ";
                $pvUnionSql .= '   AND EXISTS ( ';
                $pvUnionSql .= '     SELECT ';
                $pvUnionSql .= '       * ';
                $pvUnionSql .= '     FROM ';
                $pvUnionSql .= '       samples s ';
                $pvUnionSql .= '     WHERE ';
                $pvUnionSql .= '       s.time_box_id = pv.time_box_id ';
                $pvUnionSql .= '       AND s.paneler_id = pv.paneler_id ';
                $pvUnionSql .= "       AND ${code} = 1 ";
                $pvUnionSql .= '   ) ';
                $pvUnionSql .= ' GROUP BY ';
                $pvUnionSql .= '   prog_id ';
                $pvUnionSql .= '   , time_box_id ';
                $pvUnionSqlArr[] = $pvUnionSql;
            }

            if (count($pvUnionSqlArr) > 0) {
                $sql = '';
                $sql .= ' CREATE TEMPORARY TABLE pv_unioned AS ';
                $sql .= implode(' UNION ALL ', $pvUnionSqlArr);
                $this->select($sql);
            }
        }

        if ($isTs) {
            $pvUnionSqlArr = [];
            $cnt = count($divCodes);

            for ($i = 0; $i < $cnt; $i++) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                $pvUnionSql = '';
                $pvUnionSql .= ' SELECT ';
                $pvUnionSql .= '   pv.prog_id ';
                $pvUnionSql .= '   , pv.time_box_id ';
                $pvUnionSql .= '   , SUM(pv.viewing_seconds) AS viewing_seconds ';
                $pvUnionSql .= "   , '${code}' AS code ";
                $pvUnionSql .= ' FROM ';
                $pvUnionSql .= '   ts_viewers pv ';
                $pvUnionSql .= ' WHERE ';
                $pvUnionSql .= "   pv.prog_id BETWEEN ${progStartDate} AND ${progEndDate} ";
                $pvUnionSql .= '   AND EXISTS ( ';
                $pvUnionSql .= '     SELECT ';
                $pvUnionSql .= '       * ';
                $pvUnionSql .= '     FROM ';
                $pvUnionSql .= '       ts_samples s ';
                $pvUnionSql .= '     WHERE ';
                $pvUnionSql .= '       s.time_box_id = pv.time_box_id ';
                $pvUnionSql .= '       AND s.paneler_id = pv.paneler_id ';
                $pvUnionSql .= "       AND ${code} = 1 ";
                $pvUnionSql .= '   ) ';
                $pvUnionSql .= ' GROUP BY ';
                $pvUnionSql .= '   prog_id ';
                $pvUnionSql .= '   , time_box_id ';
                $pvUnionSqlArr[] = $pvUnionSql;
            }

            if (count($pvUnionSqlArr) > 0) {
                $sql = '';
                $sql .= ' CREATE TEMPORARY TABLE ts_pv_unioned AS ';
                $sql .= implode(' UNION ALL ', $pvUnionSqlArr);
                $this->select($sql);
            }
        }

        if ($isGross) {
            $pvUnionSqlArr = [];
            $cnt = count($divCodes);

            for ($i = 0; $i < $cnt; $i++) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                $pvUnionSql = '';
                $pvUnionSql .= ' SELECT ';
                $pvUnionSql .= '   pv.prog_id ';
                $pvUnionSql .= '   , pv.time_box_id ';
                $pvUnionSql .= '   , SUM(pv.viewing_seconds) AS viewing_seconds ';
                $pvUnionSql .= "   , '${code}' AS code ";
                $pvUnionSql .= ' FROM ';
                $pvUnionSql .= '   gross_viewers pv ';
                $pvUnionSql .= ' WHERE ';
                $pvUnionSql .= "   pv.prog_id BETWEEN ${progStartDate} AND ${progEndDate} ";
                $pvUnionSql .= '   AND EXISTS ( ';
                $pvUnionSql .= '     SELECT ';
                $pvUnionSql .= '       * ';
                $pvUnionSql .= '     FROM ';
                $pvUnionSql .= '       ts_samples s ';
                $pvUnionSql .= '     WHERE ';
                $pvUnionSql .= '       s.time_box_id = pv.time_box_id ';
                $pvUnionSql .= '       AND s.paneler_id = pv.paneler_id ';
                $pvUnionSql .= "       AND ${code} = 1 ";
                $pvUnionSql .= '   ) ';
                $pvUnionSql .= ' GROUP BY ';
                $pvUnionSql .= '   prog_id ';
                $pvUnionSql .= '   , time_box_id ';
                $pvUnionSqlArr[] = $pvUnionSql;
            }

            if (count($pvUnionSqlArr) > 0) {
                $sql = '';
                $sql .= ' CREATE TEMPORARY TABLE gross_pv_unioned AS  ';
                $sql .= implode(' UNION ALL ', $pvUnionSqlArr);
                $this->select($sql);
            }
        }
    }

    /**
     * まとめて加重平均 or まとめて単純平均.
     * @param string $averageType
     * @param array $progIds
     * @param array $timeBoxIds
     * @param string $division
     * @param null|array $conditionCross
     * @param null|array $codes
     * @param bool $bsFlg
     * @param int $regionId
     * @param array $dataTypeFlags
     * @param array $dataType
     * @param array $dataTypeConst
     * @return array
     */
    public function average(string $averageType, array $progIds, array $timeBoxIds, string $division, ?array $conditionCross, ?array $codes, bool $bsFlg, int $regionId, array $dataTypeFlags, array $dataType, array $dataTypeConst): array
    {
        $bindings = [];
        $modeAndTimeBindings = [];

        // コード種別の頭部分
        $divisionKey = "{$division}_";

        // 個人指定フラグ
        $personalFlg = false;
        // 世帯指定フラグ
        $householdFlg = false;

        $rtType = $dataTypeConst['rt'];
        $tsType = $dataTypeConst['ts'];
        $grossType = $dataTypeConst['gross'];
        $totalType = $dataTypeConst['total'];

        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = array_values($dataTypeFlags);

        // 個人が指定されている場合
        $personalIndex = array_search('personal', $codes);

        if ($personalIndex !== false) {
            $personalFlg = true;
            unset($codes[$personalIndex]);
        }

        // 世帯が指定されている場合
        $householdIndex = array_search('household', $codes);

        if ($householdIndex !== false) {
            $householdFlg = true;
            unset($codes[$householdIndex]);
        }
        // コード
        $codeBind = $this->createArrayBindParam($divisionKey, [
            $divisionKey => $codes,
        ], $bindings);

        // BS指定時はテーブル名の頭にBSを付ける
        $bsKey = '';

        $digit = 1;

        if ($bsFlg) {
            $bsKey = 'bs_';
            $digit = 2;
        }
        $bindings[':digit'] = $digit;
        $bindings[':regionId'] = $regionId;
        $modeAndTimeBindings[':regionId'] = $regionId;

        // プログラムID
        $progTimeBoxArr = [];
        $bindIndex = 0;
        $cnt = count($progIds);

        for ($i = 0; $i < $cnt; $i++) {
            if (empty($progIds[$i]) || empty($timeBoxIds[$i])) {
                continue; // TODO - konno: デッドコード リクエストバリデーション後削除
            }
            $progId = $progIds[$i];
            $timeBoxId = $timeBoxIds[$i];
            $progBindKey = ':progId' . $bindIndex++;
            $timeBoxBindKey = ':timeBoxId' . $bindIndex++;
            $bindings[$progBindKey] = $progId;
            $bindings[$timeBoxBindKey] = $timeBoxId;
            $modeAndTimeBindings[$progBindKey] = $progId;
            $modeAndTimeBindings[$timeBoxBindKey] = $timeBoxId;
            $progTimeBoxArr[] = " (${progBindKey}, ${timeBoxBindKey}) ";
        }

        // 属性
        if (!empty($codeBind)) {
            $bindings[':division'] = $division;
        }

        if (!empty($codeBind)) {
            $timeBoxIdsBind = $this->createArrayBindParam('timeBoxIds', [
                'timeBoxIds' => $timeBoxIds,
            ], $bindings);
        }

        $sql = '';
        $sql .= ' WITH program_list AS ( ';
        $sql .= '   SELECT  ';
        $sql .= '     prog_id,  ';
        $sql .= '     time_box_id, ';
        $sql .= '     personal_viewing_rate, ';
        $sql .= '     household_viewing_rate, ';

        if (!$bsFlg) {
            $sql .= '     ts_personal_viewing_rate, ';
            $sql .= '     ts_household_viewing_rate, ';
            $sql .= '     ts_personal_total_viewing_rate, ';
            $sql .= '     ts_household_total_viewing_rate, ';
            $sql .= '     COALESCE(ts_personal_gross_viewing_rate, ts_samples_personal_viewing_rate) ts_personal_gross_viewing_rate , ';
            $sql .= '     COALESCE(ts_household_gross_viewing_rate, ts_samples_household_viewing_rate) ts_household_gross_viewing_rate, ';
        } else {
            // for bs dummy
            $sql .= '     0 ts_personal_viewing_rate, ';
            $sql .= '     0 ts_household_viewing_rate, ';
            $sql .= '     0 ts_personal_total_viewing_rate, ';
            $sql .= '     0 ts_household_total_viewing_rate, ';
            $sql .= '     0 ts_personal_gross_viewing_rate, ';
            $sql .= '     0 ts_household_gross_viewing_rate, ';
        }

        $sql .= '     CASE WHEN p.real_started_at < tb.started_at THEN tb.started_at ELSE p.real_started_at END AS calc_started_at, ';
        $sql .= '     CASE WHEN p.real_ended_at > tb.ended_at THEN tb.ended_at ELSE p.real_ended_at END AS calc_ended_at, ';
        $sql .= '     EXTRACT(EPOCH FROM calc_ended_at - calc_started_at ) fraction, ';
        $sql .= '     COUNT(*) OVER () cnt, ';
        $sql .= '     SUM(fraction) OVER () / 60 total_minute ';

        $sql .= ' FROM ';
        $sql .= '  ' . $bsKey . 'programs p ';
        $sql .= '   INNER JOIN ';
        $sql .= '     time_boxes tb ';
        $sql .= '       ON p.time_box_id = tb.id ';
        $sql .= '       AND tb.region_id = :regionId ';
        $sql .= ' WHERE ';
        $sql .= '  (p.prog_id, p.time_box_id) IN ';
        $sql .= '   ( ';
        $sql .= implode(',', $progTimeBoxArr);
        $sql .= '   ) ';
        $sql .= ' ) ';

        if (!empty($codeBind)) {
            $sql .= ' , samples AS( ';
            $sql .= ' SELECT ';
            $sql .= '   * ';
            $sql .= ' FROM ';
            $sql .= '   time_box_attr_numbers tban ';
            $sql .= ' WHERE ';
            $sql .= '   time_box_id IN (' . implode(',', $timeBoxIdsBind) . ') ';
            $sql .= '   AND division = :division ';
            $sql .= '   AND code IN (' . implode(',', $codeBind) . ') ';
            $sql .= ' ) ';
        }
        $sql .= ' , program_reports_pivoted AS ( ';
        $sql .= ' SELECT ';
        $sql .= '   prog_id ';
        $sql .= '   ,time_box_id ';

        if (!empty($codeBind)) {
            foreach ($codeBind as $key => $value) {
                $sql .= "   ,MAX(CASE WHEN code = ${value} THEN viewing_rate  END) AS column${key} ";

                if (!$bsFlg) {
                    $sql .= "   ,MAX(CASE WHEN code = ${value} THEN ts_samples_viewing_rate  END) AS ts_samples_column${key} ";
                }
                $sql .= "   ,NULL AS ts_column${key} ";
                $sql .= "   ,NULL AS gross_column${key} ";
                $sql .= "   ,NULL AS total_column${key} ";
            }
        }
        $sql .= ' FROM ';
        $sql .= $bsKey . 'program_reports pr ';
        $sql .= ' WHERE ';
        $sql .= '   (prog_id, time_box_id) in ';
        $sql .= '   ( ';
        $sql .= implode(',', $progTimeBoxArr);
        $sql .= '   ) ';

        if (!empty($codeBind)) {
            $sql .= '   AND division = :division ';
            $sql .= '   AND code IN (' . implode(',', $codeBind) . ') ';
        }
        $sql .= ' GROUP BY ';
        $sql .= '   prog_id, ';
        $sql .= '   time_box_id ';
        $sql .= ' ), ts_program_reports_pivoted AS ( ';
        $sql .= ' SELECT ';
        $sql .= '   prog_id ';
        $sql .= '   ,time_box_id ';

        if (!empty($codeBind)) {
            foreach ($codeBind as $key => $value) {
                $sql .= "   ,NULL AS column${key} ";

                if (!$bsFlg) {
                    $sql .= "   ,NULL AS ts_samples_column${key} ";
                }
                $sql .= "   ,MAX(CASE WHEN code = ${value} THEN viewing_rate  END) AS ts_column${key} ";
                $sql .= "   ,MAX(CASE WHEN code = ${value} THEN gross_viewing_rate  END) AS gross_column${key} ";
                $sql .= "   ,MAX(CASE WHEN code = ${value} THEN total_viewing_rate  END) AS total_column${key} ";
            }
        }
        $sql .= ' FROM ';
        $sql .= '   ts_program_reports pr ';
        $sql .= ' WHERE ';
        $sql .= '   (prog_id, time_box_id) in ';
        $sql .= '   ( ';
        $sql .= implode(',', $progTimeBoxArr);
        $sql .= '   ) ';

        if (!empty($codeBind)) {
            $sql .= '   AND division = :division ';
            $sql .= '   AND code IN (' . implode(',', $codeBind) . ') ';
        }
        $sql .= '   AND c_index = 7 ';
        $sql .= ' GROUP BY ';
        $sql .= '   prog_id, ';
        $sql .= '   time_box_id ';
        $sql .= ' ) ';

        $sql .= ' , union_program_reports_pivoted AS ( ';
        $sql .= '   SELECT ';
        $sql .= '     prog_id ';
        $sql .= '     ,time_box_id ';

        if (!empty($codeBind)) {
            foreach ($codeBind as $key => $value) {
                $sql .= "   ,MAX(column${key}) AS column${key} ";

                if (!$bsFlg) {
                    $sql .= "   ,MAX(ts_samples_column${key}) AS ts_samples_column${key} ";
                }
                $sql .= "   ,MAX(ts_column${key}) AS ts_column${key} ";
                $sql .= "   ,MAX(gross_column${key}) AS gross_column${key} ";
                $sql .= "   ,MAX(total_column${key}) AS total_column${key} ";
            }
        }
        $sql .= '   FROM ( ';
        $sql .= '     SELECT ';
        $sql .= '       * ';
        $sql .= '     FROM ';
        $sql .= '       program_reports_pivoted ';
        $sql .= '     UNION ALL ';
        $sql .= '     SELECT ';
        $sql .= '       * ';
        $sql .= '     FROM ';
        $sql .= '       ts_program_reports_pivoted ';
        $sql .= '   ) u ';
        $sql .= '   GROUP BY ';
        $sql .= '     prog_id ';
        $sql .= '     , time_box_id ';
        $sql .= ' ) ';

        $sql .= ' , program_average AS ( ';
        $sql .= ' SELECT ';

        if ($averageType === 'weight') {
            // まとめて加重平均
            foreach ($codeBind as $key => $val) {
                $name = ltrim($val, ':');

                if ($isRt) {
                    $sql .= "COALESCE(ROUND(SUM(pr.column${key} * fraction) / SUM(fraction), :digit),0) AS rt_${name}, ";
                }

                if ($isTs) {
                    $sql .= "COALESCE(ROUND(SUM(pr.ts_column${key} * fraction) / SUM(fraction), :digit),0) AS ts_${name}, ";
                }

                if ($isTotal) {
                    $sql .= "COALESCE(ROUND(SUM(pr.total_column${key} * fraction) / SUM(fraction), :digit),0) AS total_${name}, ";
                }

                if ($isGross) {
                    $sql .= "COALESCE(ROUND(SUM(COALESCE(pr.gross_column${key}, pr.ts_samples_column${key}) * fraction) / SUM(fraction), :digit),0) AS gross_${name}, ";
                }
            }

            // 個人
            if ($personalFlg) {
                if ($isRt) {
                    $sql .= 'COALESCE(ROUND(SUM(p.personal_viewing_rate * fraction) / SUM(fraction), :digit),0) AS rt_personal, ';
                }

                if ($isTs) {
                    $sql .= 'COALESCE(ROUND(SUM(p.ts_personal_viewing_rate * fraction) / SUM(fraction), :digit),0) AS ts_personal, ';
                }

                if ($isTotal) {
                    $sql .= 'COALESCE(ROUND(SUM(p.ts_personal_total_viewing_rate * fraction) / SUM(fraction), :digit),0) AS total_personal, ';
                }

                if ($isGross) {
                    $sql .= 'COALESCE(ROUND(SUM(p.ts_personal_gross_viewing_rate * fraction) / SUM(fraction), :digit),0) AS gross_personal, ';
                }
            }

            // 世帯
            if ($householdFlg) {
                if ($isRt) {
                    $sql .= 'COALESCE(ROUND(SUM(p.household_viewing_rate * fraction) / SUM(fraction), :digit),0) AS rt_household, ';
                }

                if ($isTs) {
                    $sql .= 'COALESCE(ROUND(SUM(p.ts_household_viewing_rate * fraction) / SUM(EXTRACT(EPOCH FROM p.calc_ended_at - p.calc_started_at)), :digit),0) AS ts_household, ';
                }

                if ($isTotal) {
                    $sql .= 'COALESCE(ROUND(SUM(p.ts_household_total_viewing_rate * fraction) / SUM(fraction), :digit),0) AS total_household, ';
                }

                if ($isGross) {
                    $sql .= 'COALESCE(ROUND(SUM(p.ts_household_gross_viewing_rate * fraction) / SUM(fraction), :digit),0) AS gross_household, ';
                }
            }
        } elseif ($averageType === 'simple') {
            // まとめて単純平均
            foreach ($codeBind as $key => $val) {
                $name = ltrim($val, ':');

                if ($isRt) {
                    $sql .= "ROUND(AVG(COALESCE(pr.column${key}, 0)), :digit) AS rt_${name},";
                }

                if ($isTs) {
                    $sql .= "ROUND(AVG(COALESCE(pr.ts_column${key}, 0)), :digit) AS ts_${name},";
                }

                if ($isTotal) {
                    $sql .= "ROUND(AVG(COALESCE(pr.total_column${key}, 0)), :digit) AS total_${name},";
                }

                if ($isGross) {
                    $sql .= "ROUND(AVG(COALESCE(pr.gross_column${key}, pr.ts_samples_column${key}, 0)), :digit) AS gross_${name},";
                }
            }
            // 個人
            if ($personalFlg) {
                if ($isRt) {
                    $sql .= 'ROUND(AVG(COALESCE(p.personal_viewing_rate, 0)), :digit) AS rt_personal,';
                }

                if ($isTs) {
                    $sql .= 'ROUND(AVG(COALESCE(p.ts_personal_viewing_rate, 0)), :digit) AS ts_personal,';
                }

                if ($isTotal) {
                    $sql .= 'ROUND(AVG(COALESCE(p.ts_personal_total_viewing_rate, 0)), :digit) AS total_personal,';
                }

                if ($isGross) {
                    $sql .= 'ROUND(AVG(COALESCE(p.ts_personal_gross_viewing_rate, 0)), :digit) AS gross_personal,';
                }
            }
            // 世帯
            if ($householdFlg) {
                if ($isRt) {
                    $sql .= 'ROUND(AVG(COALESCE(p.household_viewing_rate, 0)), :digit) AS rt_household,';
                }

                if ($isTs) {
                    $sql .= 'ROUND(AVG(COALESCE(p.ts_household_viewing_rate, 0)), :digit) AS ts_household,';
                }

                if ($isTotal) {
                    $sql .= 'ROUND(AVG(COALESCE(p.ts_household_total_viewing_rate, 0)), :digit) AS total_household,';
                }

                if ($isGross) {
                    $sql .= 'ROUND(AVG(COALESCE(p.ts_household_gross_viewing_rate, 0)), :digit) AS gross_household,';
                }
            }
        }
        $sql .= '    MAX(cnt) cnt, ';
        $sql .= '    MAX(total_minute) total_minute ';

        $sql .= ' FROM ';
        $sql .= '   program_list p ';
        $sql .= ' LEFT JOIN ';
        $sql .= '   union_program_reports_pivoted pr ';
        $sql .= ' ON ';
        $sql .= '   p.prog_id = pr.prog_id AND p.time_box_id = pr.time_box_id ';
        $sql .= ' ) ';
        $sql .= 'SELECT * FROM program_average;';
        $resultListAverage = $this->select($sql, $bindings);

        $modeAndTime = $this->averageModeAndTime($modeAndTimeBindings, $progTimeBoxArr, $bsKey);
        $resultListMode = $modeAndTime['mode'];
        $resultListTime = $modeAndTime['time'];

        // 結果を基に情報生成
        $resultAverage = get_object_vars($resultListAverage[0]);
        $resultMode = get_object_vars($resultListMode[0]);
        // 時間は最頻値に一致するタイトルから取得
        $resultTime = [];

        foreach ($resultListTime as $list) {
            $list = get_object_vars($list);

            if ($list['title'] === $resultMode['title']) {
                $resultTime = $list;
                break;
            }
        }

        $resultAll = [];

        // 通常開始時刻
        $resultAll[] = $resultTime['start_time'];

        // 通常終了時刻
        $resultAll[] = $resultTime['end_time'];

        // 曜日
        $wdays = '';
        $wdays_array = [
            '1' => '月',
            '2' => '火',
            '3' => '水',
            '4' => '木',
            '5' => '金',
            '6' => '土',
            '0' => '日',
        ];

        foreach ($wdays_array as $k => $v) {
            if (strpos($resultMode['dow'], (string) $k) !== false) {
                $wdays .= $v;
            }
        }
        $resultAll[] = $wdays;

        // 放送局
        $resultAll[] = $resultMode['channel_code'];

        // 番組名
        $etc = '';

        if (count($resultListMode) > 1) {
            $etc = ' など';
        }
        $resultAll[] = $resultMode['title'] . $etc;

        // 本数
        $resultAll[] = $resultAverage['cnt'];

        // 通常分数
        $resultAll[] = $resultTime['minute'];

        // 分数計
        $resultAll[] = $resultAverage['total_minute'];

        if (in_array($rtType, $dataType)) {
            if ($personalFlg) {
                $resultAll[] = $resultAverage['rt_personal'];
            }

            foreach ($codeBind as $val) {
                $name = 'rt_' . ltrim($val, ':');
                $resultAll[] = $resultAverage[$name];
            }

            if ($householdFlg) {
                $resultAll[] = $resultAverage['rt_household'];
            }
        }

        if (in_array($tsType, $dataType)) {
            if ($personalFlg) {
                $resultAll[] = $resultAverage['ts_personal'];
            }

            foreach ($codeBind as $val) {
                $name = 'ts_' . ltrim($val, ':');
                $resultAll[] = $resultAverage[$name];
            }

            if ($householdFlg) {
                $resultAll[] = $resultAverage['ts_household'];
            }
        }

        if (in_array($totalType, $dataType)) {
            if ($personalFlg) {
                $resultAll[] = $resultAverage['total_personal'];
            }

            foreach ($codeBind as $val) {
                $name = 'total_' . ltrim($val, ':');
                $resultAll[] = $resultAverage[$name];
            }

            if ($householdFlg) {
                $resultAll[] = $resultAverage['total_household'];
            }
        }

        if (in_array($grossType, $dataType)) {
            if ($personalFlg) {
                $resultAll[] = $resultAverage['gross_personal'];
            }

            foreach ($codeBind as $val) {
                $name = 'gross_' . ltrim($val, ':');
                $resultAll[] = $resultAverage[$name];
            }

            if ($householdFlg) {
                $resultAll[] = $resultAverage['gross_household'];
            }
        }

        return $resultAll;
    }

    public function averageModeAndTime(array $bindings, array $progTimeBoxArr, string $bsKey): array
    {
        $results = [];
        $modeSelectArr = ['p.title', 'c.code_name channel_code', 'COUNT(*) AS cnt', "LISTAGG(DATE_PART('dow' ,p.date)) AS dow"];
        $timeSelectArr = [
            'p.title',
            'c.code_name as channel_code',
            "lpad(to_char(p.real_started_at - interval '5 hours', 'HH24')::numeric + 5 || to_char(p.real_started_at, ':MI:SS'),8,'0') AS start_time",
            "lpad(to_char(p.real_ended_at - interval '5 hours 1 seconds', 'HH24')::numeric + 5 || to_char(p.real_ended_at - interval '1 seconds', ':MI:SS'),8,'0') AS end_time",
            'count(*) as cnt',
            'TRUNC(EXTRACT(epoch from case when real_ended_at > tb.ended_at then tb.ended_at else real_ended_at end - case when real_started_at < tb.started_at then tb.started_at else real_started_at end) / 60) as minute',
        ];
        $modeGroupByArr = ['p.title', 'channel_code'];
        $timeGroupByArr = ['p.title', 'channel_code', 'start_time', 'end_time', 'minute'];
        $modeOrderByArr = ['cnt DESC', 'title ASC'];
        $timeOrderByArr = ['cnt DESC', 'minute ASC', 'title ASC', 'channel_code ASC', 'start_time ASC'];

        $commonSql = '';
        $commonSql .= ' FROM ';
        $commonSql .= "   ${bsKey}programs p ";
        $commonSql .= ' INNER JOIN time_boxes tb ';
        $commonSql .= '   ON p.time_box_id = tb.id ';
        $commonSql .= '   AND tb.region_id = :regionId ';
        $commonSql .= ' INNER JOIN channels c ';
        $commonSql .= '   ON p.channel_id = c.id ';
        $commonSql .= ' WHERE ';
        $commonSql .= '   (prog_id, time_box_id) in ';
        $commonSql .= '   ( ';
        $commonSql .= implode(',', $progTimeBoxArr);
        $commonSql .= '   ) ';

        // 最頻値取得用SQL
        $select = implode(',', $modeSelectArr);
        $groupBy = implode(',', $modeGroupByArr);
        $orderBy = implode(',', $modeOrderByArr);
        $query = sprintf('SELECT %s %s GROUP BY %s ORDER BY %s;', $select, $commonSql, $groupBy, $orderBy);
        $results['mode'] = $this->select($query, $bindings);

        // 通常開始時刻、通常終了時刻、通常分数取得用SQL
        $select = implode(',', $timeSelectArr);
        $groupBy = implode(',', $timeGroupByArr);
        $orderBy = implode(',', $timeOrderByArr);
        $query = sprintf('SELECT %s %s GROUP BY %s ORDER BY %s;', $select, $commonSql, $groupBy, $orderBy);
        $results['time'] = $this->select($query, $bindings);

        return $results;
    }

    /**
     * まとめて加重平均 or まとめて単純平均.（拡張、オリジナル用）.
     * @param string $averageType
     * @param array $progIds
     * @param array $timeBoxIds
     * @param string $division
     * @param null|array $conditionCross
     * @param null|array $codes
     * @param bool $bsFlg
     * @param int $regionId
     * @param $dataTypeFlags
     * @param array $dataType
     * @param array $dataTypeConst
     * @param array $prefixes
     * @param string $selectedPersonalName
     * @param int $codeNumber
     * @return array
     */
    public function averageOriginal(string $averageType, array $progIds, array $timeBoxIds, string $division, ?array $conditionCross, ?array $codes, bool $bsFlg, int $regionId, $dataTypeFlags, array $dataType, array $dataTypeConst, array $prefixes, string $selectedPersonalName, int $codeNumber): array
    {
        // コード種別の頭部分
        $divisionKey = "{$division}_";

        $isConditionCross = $division == 'condition_cross';
        // 個人指定フラグ
        $personalFlg = false;
        // 世帯指定フラグ
        $householdFlg = false;

        if ($isConditionCross) {
            $householdFlg = true;
        }

        $sampleCodePrefix = $prefixes['code'];
        $sampleCodeNumberPrefix = $prefixes['number'];

        $rtType = $dataTypeConst['rt'];
        $tsType = $dataTypeConst['ts'];
        $grossType = $dataTypeConst['gross'];
        $totalType = $dataTypeConst['total'];

        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = array_values($dataTypeFlags);

        // 個人が指定されている場合
        $personalIndex = array_search('personal', $codes);

        if ($personalIndex !== false) {
            $personalFlg = true;
            unset($codes[$personalIndex]);
        }

        // 世帯が指定されている場合
        $householdIndex = array_search('household', $codes);

        if ($householdIndex !== false) {
            $householdFlg = true;
            unset($codes[$householdIndex]);
        }

        if ($isConditionCross) {
            $codes = [];
            $codes[] = 'condition_cross';
        }
        $codes = array_merge($codes); //keyを連番に

        // BS指定時はテーブル名の頭にBSを付ける
        $bsKey = '';

        $digit = 1;

        if ($bsFlg) {
            $bsKey = 'bs_';
            $digit = 2;
        }

        // プログラムID
        $progTimeBoxArr = [];
        $progTimeBoxBindings = [];
        $bindIndex = 0;
        $cnt = count($progIds);

        for ($i = 0; $i < $cnt; $i++) {
            if (empty($progIds[$i]) || empty($timeBoxIds[$i])) {
                continue; // TODO - konno: デッドコード リクエストバリデーション後削除
            }
            $progId = $progIds[$i];
            $timeBoxId = $timeBoxIds[$i];
            $progBindKey = ':progId' . $bindIndex++;
            $timeBoxBindKey = ':timeBoxId' . $bindIndex++;
            $progTimeBoxBindings[$progBindKey] = $progId;
            $progTimeBoxBindings[$timeBoxBindKey] = $timeBoxId;
            $progTimeBoxArr[] = " (${progBindKey}, ${timeBoxBindKey}) ";
        }

        $bindings = $progTimeBoxBindings;
        $bindings['regionId'] = $regionId;
        $sql = '';
        $sql .= ' CREATE TEMPORARY TABLE program_list AS ';
        $sql .= '   SELECT  ';
        $sql .= '     prog_id,  ';
        $sql .= '     time_box_id, ';
        $sql .= '     personal_viewing_rate, ';
        $sql .= '     household_viewing_rate, ';

        if (!$bsFlg) {
            $sql .= '     ts_personal_viewing_rate, ';
            $sql .= '     ts_personal_total_viewing_rate, ';
            $sql .= '     COALESCE(ts_personal_gross_viewing_rate, ts_samples_personal_viewing_rate) ts_personal_gross_viewing_rate, ';
            $sql .= '     ts_household_viewing_rate, ';
            $sql .= '     ts_household_total_viewing_rate, ';
            $sql .= '     COALESCE(ts_household_gross_viewing_rate, ts_samples_household_viewing_rate) ts_household_gross_viewing_rate, ';
        } else {
            // for bs dummy
            $sql .= '     0 ts_personal_viewing_rate, ';
            $sql .= '     0 ts_household_viewing_rate, ';
            $sql .= '     0 ts_personal_total_viewing_rate, ';
            $sql .= '     0 ts_household_total_viewing_rate, ';
            $sql .= '     0 ts_personal_gross_viewing_rate, ';
            $sql .= '     0 ts_household_gross_viewing_rate, ';
        }

        $sql .= '     CASE WHEN p.real_started_at < tb.started_at THEN tb.started_at ELSE p.real_started_at END AS calc_started_at, ';
        $sql .= '     CASE WHEN p.real_ended_at > tb.ended_at THEN tb.ended_at ELSE p.real_ended_at END AS calc_ended_at, ';
        $sql .= '     EXTRACT(EPOCH FROM calc_ended_at - calc_started_at ) fraction ';
        $sql .= '   FROM ';
        $sql .= "     ${bsKey}programs p ";
        $sql .= '   INNER JOIN time_boxes tb  ';
        $sql .= '     ON p.time_box_id = tb.id  ';
        $sql .= '     AND tb.region_id = :regionId  ';
        $sql .= '   WHERE ';
        $sql .= '     (prog_id, time_box_id) in ';
        $sql .= '     ( ';
        $sql .= implode(',', $progTimeBoxArr);
        $sql .= '     ) ';
        $sql .= ' ;';
        $this->select($sql, $bindings);

        if ($isRt || $isGross || $isRtTotal) {
            if (count($codes) > 0) {
                $this->createRtSampleTempTable($isConditionCross, $conditionCross, $division, $codes, $timeBoxIds, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName, false, $codeNumber);

                $sql = '';
                $sql .= ' CREATE TEMPORARY TABLE rt_viewers AS ';
                $sql .= ' SELECT pv.prog_id, pv.time_box_id, pv.viewing_seconds, pv.paneler_id ';
                $sql .= " FROM ${bsKey}program_viewers pv ";
                $sql .= ' WHERE ';
                $sql .= '     (pv.prog_id, pv.time_box_id) in ';
                $sql .= '   ( ';
                $sql .= implode(',', $progTimeBoxArr);
                $sql .= '   ) ';
                $sql .= ' ;';
                $this->select($sql, $progTimeBoxBindings);
            }
        }

        if ($isTs || $isGross || $isTotal || $isRtTotal) {
            if (count($codes) > 0) {
                $this->createTsSampleTempTable($isConditionCross, $conditionCross, $division, $codes, $timeBoxIds, $regionId, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName, false, $codeNumber);

                $sql = '';
                $sql .= ' CREATE TEMPORARY TABLE ts_viewers AS ';
                $sql .= ' SELECT pv.prog_id, pv.time_box_id, pv.viewing_seconds, pv.total_viewing_seconds, pv.gross_viewing_seconds, pv.paneler_id ';
                $sql .= ' FROM ts_program_viewers pv ';
                $sql .= ' WHERE ';
                $sql .= '     (pv.prog_id, pv.time_box_id) in ';
                $sql .= '   ( ';
                $sql .= implode(',', $progTimeBoxArr);
                $sql .= '   ) ';
                $sql .= '   AND c_index = 7 ';
                $sql .= ' ; ';
                $this->select($sql, $progTimeBoxBindings);
            }
        }

        if ($isGross || $isRtTotal) {
            if (count($codes) > 0) {
                $sql = '';
                $sql .= ' CREATE TEMPORARY TABLE gross_viewers AS ';
                $sql .= ' SELECT ';
                $sql .= '   prog_id, time_box_id, MAX(CASE WHEN gross_viewing_seconds IS NULL THEN viewing_seconds ELSE gross_viewing_seconds END) viewing_seconds , paneler_id ';
                $sql .= ' FROM ';
                $sql .= '   ( ';
                $sql .= '     SELECT pv.prog_id, pv.time_box_id, pv.viewing_seconds, NULL AS gross_viewing_seconds, pv.paneler_id FROM rt_viewers pv ';
                $sql .= '     UNION ALL ';
                $sql .= '     SELECT pv.prog_id, pv.time_box_id, NULL, pv.gross_viewing_seconds, pv.paneler_id FROM ts_viewers pv ';
                $sql .= '   ) pv ';
                $sql .= ' GROUP BY ';
                $sql .= '   pv.prog_id, pv.time_box_id, paneler_id ';
                $this->select($sql);
            }
        }

        if ($isRt) {
            $pvUnionSqlArr = [];
            $reportSelectSqlArr = [];
            $cnt = count($codes);

            for ($i = 0; $i < $cnt; $i++) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                $number = sprintf('%s%02d', $sampleCodeNumberPrefix, $i);
                $pvUnionSql = '';
                $pvUnionSql .= ' SELECT ';
                $pvUnionSql .= '   pv.prog_id ';
                $pvUnionSql .= '   , pv.time_box_id ';
                $pvUnionSql .= '   , SUM(pv.viewing_seconds) AS viewing_seconds ';
                $pvUnionSql .= "   , '${code}' AS code ";
                $pvUnionSql .= ' FROM ';
                $pvUnionSql .= '   rt_viewers pv ';
                $pvUnionSql .= ' WHERE ';
                $pvUnionSql .= '   EXISTS ( ';
                $pvUnionSql .= '     SELECT ';
                $pvUnionSql .= '       * ';
                $pvUnionSql .= '     FROM ';
                $pvUnionSql .= '       samples s ';
                $pvUnionSql .= '     WHERE ';
                $pvUnionSql .= '       s.time_box_id = pv.time_box_id ';
                $pvUnionSql .= '       AND s.paneler_id = pv.paneler_id ';
                $pvUnionSql .= "       AND ${code} = 1 ";
                $pvUnionSql .= '   ) ';
                $pvUnionSql .= ' GROUP BY ';
                $pvUnionSql .= '   prog_id ';
                $pvUnionSql .= '   , time_box_id ';
                $pvUnionSqlArr[] = $pvUnionSql;

                $reportSelectSql = '';
                $reportSelectSql .= '   , MAX(  ';
                $reportSelectSql .= '     CASE  ';
                $reportSelectSql .= "       WHEN pu.code = '${code}'  ";
                $reportSelectSql .= '         THEN pu.viewing_seconds ::numeric / (  ';
                $reportSelectSql .= '         (  ';
                $reportSelectSql .= '           SELECT ';
                $reportSelectSql .= "             ${number}  ";
                $reportSelectSql .= '           FROM ';
                $reportSelectSql .= '             rt_numbers rtn  ';
                $reportSelectSql .= '           WHERE ';
                $reportSelectSql .= '             pl.time_box_id = rtn.time_box_id ';
                $reportSelectSql .= '         ) * pl.fraction ';
                $reportSelectSql .= '       )  ';
                $reportSelectSql .= '       END ';
                $reportSelectSql .= "   ) * 100 AS viewing_rate_${code} ";
                $reportSelectSqlArr[] = $reportSelectSql;
            }

            if (count($pvUnionSqlArr) > 0) {
                $sql = '';
                $sql .= ' CREATE TEMPORARY TABLE pv_unioned AS ';
                $sql .= implode(' UNION ALL ', $pvUnionSqlArr);
                $this->select($sql);
            }

            $bindings = [];
            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE program_reports_pivot AS ';
            $sql .= ' SELECT ';
            $sql .= ' pl.prog_id ';
            $sql .= ' , pl.time_box_id ';

            if (count($reportSelectSqlArr) > 0) {
                $sql .= implode('', $reportSelectSqlArr);
            }
            $sql .= ' FROM ';
            $sql .= '   program_list pl ';

            if (count($reportSelectSqlArr) > 0) {
                $sql .= '   LEFT JOIN pv_unioned pu ';
                $sql .= '     ON pl.prog_id = pu.prog_id ';
                $sql .= '     AND pl.time_box_id = pu.time_box_id ';
            }
            $sql .= ' GROUP BY ';
            $sql .= '   pl.prog_id ';
            $sql .= '   , pl.time_box_id; ';
            $this->select($sql, $bindings);
        }

        if ($isTs || $isTotal) {
            $pvUnionSqlArr = [];
            $reportSelectSqlArr = [];
            $cnt = count($codes);

            for ($i = 0; $i < $cnt; $i++) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                $number = sprintf('%s%02d', $sampleCodeNumberPrefix, $i);
                $pvUnionSql = '';
                $pvUnionSql .= ' SELECT ';
                $pvUnionSql .= '   pv.prog_id ';
                $pvUnionSql .= '   , pv.time_box_id ';
                $pvUnionSql .= '   , SUM(pv.viewing_seconds)::numeric AS viewing_seconds ';
                $pvUnionSql .= '   , SUM(pv.total_viewing_seconds) AS total_viewing_seconds ';
                $pvUnionSql .= "   , '${code}' AS code ";
                $pvUnionSql .= ' FROM ';
                $pvUnionSql .= '   ts_viewers pv ';
                $pvUnionSql .= ' WHERE ';
                $pvUnionSql .= '   EXISTS ( ';
                $pvUnionSql .= '     SELECT ';
                $pvUnionSql .= '       * ';
                $pvUnionSql .= '     FROM ';
                $pvUnionSql .= '       ts_samples s ';
                $pvUnionSql .= '     WHERE ';
                $pvUnionSql .= '       s.time_box_id = pv.time_box_id ';
                $pvUnionSql .= '       AND s.paneler_id = pv.paneler_id ';
                $pvUnionSql .= "       AND ${code} = 1 ";
                $pvUnionSql .= '   ) ';
                $pvUnionSql .= ' GROUP BY ';
                $pvUnionSql .= '   prog_id ';
                $pvUnionSql .= '   , time_box_id ';
                $pvUnionSqlArr[] = $pvUnionSql;

                $reportSelectSql = '';
                $reportSelectSql .= '   , MAX(  ';
                $reportSelectSql .= '     CASE  ';
                $reportSelectSql .= "       WHEN pu.code = '${code}'  ";
                $reportSelectSql .= '         THEN pu.viewing_seconds ::numeric / (  ';
                $reportSelectSql .= '         (  ';
                $reportSelectSql .= '           SELECT ';
                $reportSelectSql .= "             ${number}  ";
                $reportSelectSql .= '           FROM ';
                $reportSelectSql .= '             ts_numbers tsn  ';
                $reportSelectSql .= '           WHERE ';
                $reportSelectSql .= '             pl.time_box_id = tsn.time_box_id ';
                $reportSelectSql .= '         ) * pl.fraction ';
                $reportSelectSql .= '       )  ';
                $reportSelectSql .= '       END ';
                $reportSelectSql .= "   ) * 100 AS viewing_rate_${code} ";
                $reportSelectSql .= '   , MAX(  ';
                $reportSelectSql .= '     CASE  ';
                $reportSelectSql .= "       WHEN pu.code = '${code}'  ";
                $reportSelectSql .= '         THEN pu.total_viewing_seconds ::numeric / (  ';
                $reportSelectSql .= '         (  ';
                $reportSelectSql .= '           SELECT ';
                $reportSelectSql .= "             ${number}  ";
                $reportSelectSql .= '           FROM ';
                $reportSelectSql .= '             ts_numbers tsn  ';
                $reportSelectSql .= '           WHERE ';
                $reportSelectSql .= '             pl.time_box_id = tsn.time_box_id ';
                $reportSelectSql .= '         ) * pl.fraction ';
                $reportSelectSql .= '       )  ';
                $reportSelectSql .= '       END ';
                $reportSelectSql .= "   ) * 100 AS total_viewing_rate_${code} ";
                $reportSelectSqlArr[] = $reportSelectSql;
            }

            if (count($pvUnionSqlArr) > 0) {
                $sql = '';
                $sql .= ' CREATE TEMPORARY TABLE ts_pv_unioned AS ';
                $sql .= implode(' UNION ALL ', $pvUnionSqlArr);
                $this->select($sql);
            }

            $bindings = [];
            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE ts_program_reports_pivot AS ';
            $sql .= ' SELECT ';
            $sql .= ' pl.prog_id ';
            $sql .= ' , pl.time_box_id ';

            if (count($reportSelectSqlArr) > 0) {
                $sql .= implode('', $reportSelectSqlArr);
            }
            $sql .= ' FROM ';
            $sql .= '   program_list pl ';

            if (count($reportSelectSqlArr) > 0) {
                $sql .= '   LEFT JOIN ts_pv_unioned pu ';
                $sql .= '     ON pl.prog_id = pu.prog_id ';
                $sql .= '     AND pl.time_box_id = pu.time_box_id ';
            }
            $sql .= ' GROUP BY ';
            $sql .= '   pl.prog_id ';
            $sql .= '   , pl.time_box_id; ';
            $this->select($sql, $bindings);
        }

        if ($isGross) {
            $pvUnionSqlArr = [];
            $reportSelectSqlArr = [];
            $cnt = count($codes);

            for ($i = 0; $i < $cnt; $i++) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                $number = sprintf('%s%02d', $sampleCodeNumberPrefix, $i);
                $pvUnionSql = '';
                $pvUnionSql .= ' SELECT ';
                $pvUnionSql .= '   pv.prog_id ';
                $pvUnionSql .= '   , pv.time_box_id ';
                $pvUnionSql .= '   , SUM(pv.viewing_seconds) AS viewing_seconds ';
                $pvUnionSql .= "   , '${code}' AS code ";
                $pvUnionSql .= ' FROM ';
                $pvUnionSql .= '   gross_viewers pv ';
                $pvUnionSql .= ' WHERE ';
                $pvUnionSql .= '   EXISTS ( ';
                $pvUnionSql .= '     SELECT ';
                $pvUnionSql .= '       * ';
                $pvUnionSql .= '     FROM ';
                $pvUnionSql .= '       ts_samples s ';
                $pvUnionSql .= '     WHERE ';
                $pvUnionSql .= '       s.time_box_id = pv.time_box_id ';
                $pvUnionSql .= '       AND s.paneler_id = pv.paneler_id ';
                $pvUnionSql .= "       AND ${code} = 1 ";
                $pvUnionSql .= '   ) ';
                $pvUnionSql .= ' GROUP BY ';
                $pvUnionSql .= '   prog_id ';
                $pvUnionSql .= '   , time_box_id ';
                $pvUnionSqlArr[] = $pvUnionSql;

                $reportSelectSql = '';
                $reportSelectSql .= '   , MAX(  ';
                $reportSelectSql .= '     CASE  ';
                $reportSelectSql .= "       WHEN pu.code = '${code}'  ";
                $reportSelectSql .= '         THEN pu.viewing_seconds ::numeric / (  ';
                $reportSelectSql .= '         (  ';
                $reportSelectSql .= '           SELECT ';
                $reportSelectSql .= "             ${number}  ";
                $reportSelectSql .= '           FROM ';
                $reportSelectSql .= '             ts_numbers tsn  ';
                $reportSelectSql .= '           WHERE ';
                $reportSelectSql .= '             pl.time_box_id = tsn.time_box_id ';
                $reportSelectSql .= '         ) * pl.fraction ';
                $reportSelectSql .= '       )  ';
                $reportSelectSql .= '       END ';
                $reportSelectSql .= "   ) * 100 AS viewing_rate_${code} ";
                $reportSelectSqlArr[] = $reportSelectSql;
            }

            if (count($pvUnionSqlArr) > 0) {
                $sql = '';
                $sql .= ' CREATE TEMPORARY TABLE gross_pv_unioned AS  ';
                $sql .= implode(' UNION ALL ', $pvUnionSqlArr);
                $this->select($sql);
            }
            $bindings = [];
            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE gross_program_reports_pivot AS ';
            $sql .= ' SELECT ';
            $sql .= ' pl.prog_id ';
            $sql .= ' , pl.time_box_id ';

            if (count($reportSelectSqlArr) > 0) {
                $sql .= implode('', $reportSelectSqlArr);
            }
            $sql .= ' FROM ';
            $sql .= '   program_list pl ';

            if (count($reportSelectSqlArr) > 0) {
                $sql .= '   LEFT JOIN gross_pv_unioned pu ';
                $sql .= '     ON pl.prog_id = pu.prog_id ';
                $sql .= '     AND pl.time_box_id = pu.time_box_id ';
            }
            $sql .= ' GROUP BY ';
            $sql .= '   pl.prog_id ';
            $sql .= '   , pl.time_box_id; ';
            $this->select($sql, $bindings);
        }

        $bindings[':digit'] = $digit;
        $sql = '';
        $sql .= ' SELECT ';

        if ($averageType === 'weight') {
            // まとめて加重平均
            foreach ($codes as $key => $val) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $key);
                $name = $divisionKey . $val;

                if ($isConditionCross) {
                    $name = 'condition_cross';
                }

                if ($isRt) {
                    $sql .= "COALESCE(ROUND(SUM(pr.viewing_rate_${code} * fraction) / SUM(fraction), :digit), 0) AS rt_${name}, ";
                }

                if ($isTs) {
                    $sql .= "COALESCE(ROUND(SUM(tspr.viewing_rate_${code} * fraction) / SUM(fraction), :digit), 0) AS ts_${name}, ";
                }

                if ($isTotal) {
                    $sql .= "COALESCE(ROUND(SUM(tspr.total_viewing_rate_${code} * fraction) / SUM(fraction), :digit), 0) AS total_${name}, ";
                }

                if ($isGross) {
                    $sql .= "COALESCE(ROUND(SUM(COALESCE(gspr.viewing_rate_${code}) * fraction) / SUM(fraction), :digit), 0) AS gross_${name}, ";
                }
            }
            // 個人
            if ($personalFlg) {
                if ($isRt) {
                    $sql .= 'COALESCE(ROUND(SUM(pl.personal_viewing_rate * fraction) / SUM(fraction), :digit), 0) AS rt_personal, ';
                }

                if ($isTs) {
                    $sql .= 'COALESCE(ROUND(SUM(pl.ts_personal_viewing_rate * fraction) / SUM(fraction), :digit), 0) AS ts_personal, ';
                }

                if ($isTotal) {
                    $sql .= 'COALESCE(ROUND(SUM(pl.ts_personal_total_viewing_rate * fraction) / SUM(fraction), :digit), 0) AS total_personal, ';
                }

                if ($isGross) {
                    $sql .= ' COALESCE(ROUND(SUM(pl.ts_personal_gross_viewing_rate * fraction) / SUM(fraction), :digit),0) AS gross_personal, ';
                }
            }
            // 世帯
            if ($householdFlg) {
                if ($isRt) {
                    $sql .= 'COALESCE(ROUND(SUM(pl.household_viewing_rate * fraction) / SUM(fraction), :digit), 0) AS rt_household, ';
                }

                if ($isTs) {
                    $sql .= 'COALESCE(ROUND(SUM(pl.ts_household_viewing_rate * fraction) / SUM(fraction), :digit), 0) AS ts_household, ';
                }

                if ($isTotal) {
                    $sql .= 'COALESCE(ROUND(SUM(pl.ts_household_total_viewing_rate * fraction) / SUM(fraction), :digit), 0) AS total_household, ';
                }

                if ($isGross) {
                    $sql .= ' COALESCE(ROUND(SUM(pl.ts_household_gross_viewing_rate * fraction) / SUM(fraction), :digit),0) AS gross_household, ';
                }
            }
        } elseif ($averageType === 'simple') {
            // まとめて単純平均
            foreach ($codes as $key => $val) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $key);
                $name = $divisionKey . $val;

                if ($isConditionCross) {
                    $name = 'condition_cross';
                }

                if ($isRt) {
                    $sql .= "COALESCE(ROUND(AVG(COALESCE(pr.viewing_rate_${code}, 0)), :digit), 0) AS rt_${name},";
                }

                if ($isTs) {
                    $sql .= "COALESCE(ROUND(AVG(COALESCE(tspr.viewing_rate_${code}, 0)), :digit), 0) AS ts_${name},";
                }

                if ($isTotal) {
                    $sql .= "COALESCE(ROUND(AVG(COALESCE(tspr.total_viewing_rate_${code}, 0)), :digit), 0) AS total_${name},";
                }

                if ($isGross) {
                    $sql .= "COALESCE(ROUND(AVG(COALESCE(gspr.viewing_rate_${code}, 0)), :digit), 0) AS gross_${name},";
                }
            }
            // 個人
            if ($personalFlg) {
                if ($isRt) {
                    $sql .= 'COALESCE(ROUND(AVG(COALESCE(pl.personal_viewing_rate, 0)), :digit), 0) AS rt_personal,';
                }

                if ($isTs) {
                    $sql .= 'COALESCE(ROUND(AVG(COALESCE(pl.ts_personal_viewing_rate, 0)), :digit), 0) AS ts_personal,';
                }

                if ($isTotal) {
                    $sql .= 'COALESCE(ROUND(AVG(COALESCE(pl.ts_personal_total_viewing_rate, 0)), :digit), 0) AS total_personal,';
                }

                if ($isGross) {
                    $sql .= 'COALESCE(ROUND(AVG(COALESCE(pl.ts_personal_gross_viewing_rate, 0)), :digit), 0) AS gross_personal,';
                }
            }
            // 世帯
            if ($householdFlg) {
                if ($isRt) {
                    $sql .= 'COALESCE(ROUND(AVG(COALESCE(pl.household_viewing_rate, 0)), :digit), 0) AS rt_household,';
                }

                if ($isTs) {
                    $sql .= 'COALESCE(ROUND(AVG(COALESCE(pl.ts_household_viewing_rate, 0)), :digit), 0) AS ts_household,';
                }

                if ($isTotal) {
                    $sql .= 'COALESCE(ROUND(AVG(COALESCE(pl.ts_household_total_viewing_rate, 0)), :digit), 0) AS total_household,';
                }

                if ($isGross) {
                    $sql .= 'COALESCE(ROUND(AVG(COALESCE(pl.ts_household_gross_viewing_rate, 0)), :digit), 0) AS gross_household,';
                }
            }
        }
        $sql .= 'COUNT(*) AS cnt, ';
        $sql .= 'SUM(fraction) / 60 total_minute ';

        $sql .= ' FROM ';
        $sql .= '   program_list pl ';

        if ($isRt) {
            $sql .= ' LEFT JOIN ';
            $sql .= '   program_reports_pivot pr ';
            $sql .= ' ON ';
            $sql .= '   pl.prog_id = pr.prog_id ';
            $sql .= '   AND pl.time_box_id = pr.time_box_id ';
        }

        if ($isTs || $isTotal) {
            $sql .= ' LEFT JOIN ';
            $sql .= '   ts_program_reports_pivot tspr ';
            $sql .= ' ON ';
            $sql .= '   pl.prog_id = tspr.prog_id ';
            $sql .= '   AND pl.time_box_id = tspr.time_box_id ';
        }

        if ($isGross) {
            $sql .= ' LEFT JOIN ';
            $sql .= '   gross_program_reports_pivot gspr ';
            $sql .= ' ON ';
            $sql .= '   pl.prog_id = gspr.prog_id ';
            $sql .= '   AND pl.time_box_id = gspr.time_box_id ';
        }
        $sql .= ' ; ';
        $resultListAverage = $this->select($sql, $bindings);

        $modeAndTimeBindings = $progTimeBoxBindings;
        $modeAndTimeBindings[':regionId'] = $regionId;

        $modeAndTime = $this->averageModeAndTime($modeAndTimeBindings, $progTimeBoxArr, $bsKey);
        $resultListMode = $modeAndTime['mode'];
        $resultListTime = $modeAndTime['time'];

        // 結果を基に情報生成
        $resultAverage = get_object_vars($resultListAverage[0]);
        $resultMode = get_object_vars($resultListMode[0]);
        // 時間は最頻値に一致するタイトルから取得
        $resultTime = [];

        foreach ($resultListTime as $list) {
            $list = get_object_vars($list);

            if ($list['title'] === $resultMode['title']) {
                $resultTime = $list;
                break;
            }
        }

        $resultAll = [];
        // 通常開始時刻
        $resultAll[] = $resultTime['start_time'];

        // 通常終了時刻
        $resultAll[] = $resultTime['end_time'];

        // 曜日
        $wdays = '';
        $wdays_array = [
            '1' => '月',
            '2' => '火',
            '3' => '水',
            '4' => '木',
            '5' => '金',
            '6' => '土',
            '0' => '日',
        ];

        foreach ($wdays_array as $k => $v) {
            if (strpos($resultMode['dow'], (string) $k) !== false) {
                $wdays .= $v;
            }
        }
        $resultAll[] = $wdays;

        // 放送局
        $resultAll[] = $resultMode['channel_code'];

        // 番組名
        $etc = '';

        if (count($resultListMode) > 1) {
            $etc = ' など';
        }
        $resultAll[] = $resultMode['title'] . $etc;

        // 本数
        $resultAll[] = $resultAverage['cnt'];

        // 通常分数
        $resultAll[] = $resultTime['minute'];

        // 分数計
        $resultAll[] = $resultAverage['total_minute'];

        if (in_array($rtType, $dataType)) {
            if ($personalFlg) {
                $resultAll[] = $resultAverage['rt_personal'];
            }

            foreach ($codes as $val) {
                $name = $divisionKey . $val;
                $name = 'rt_' . ltrim($name, ':');

                if ($isConditionCross) {
                    $name = 'rt_condition_cross';
                }
                $resultAll[] = $resultAverage[$name];
            }

            if ($householdFlg) {
                $resultAll[] = $resultAverage['rt_household'];
            }
        }

        if (in_array($tsType, $dataType)) {
            if ($personalFlg) {
                $resultAll[] = $resultAverage['ts_personal'];
            }

            foreach ($codes as $val) {
                $name = $divisionKey . $val;
                $name = 'ts_' . ltrim($name, ':');

                if ($isConditionCross) {
                    $name = 'ts_condition_cross';
                }
                $resultAll[] = $resultAverage[$name];
            }

            if ($householdFlg) {
                $resultAll[] = $resultAverage['ts_household'];
            }
        }

        if (in_array($totalType, $dataType)) {
            if ($personalFlg) {
                $resultAll[] = $resultAverage['total_personal'];
            }

            foreach ($codes as $val) {
                $name = $divisionKey . $val;
                $name = 'total_' . ltrim($name, ':');

                if ($isConditionCross) {
                    $name = 'total_condition_cross';
                }
                $resultAll[] = $resultAverage[$name];
            }

            if ($householdFlg) {
                $resultAll[] = $resultAverage['total_household'];
            }
        }

        if (in_array($grossType, $dataType)) {
            if ($personalFlg) {
                $resultAll[] = $resultAverage['gross_personal'];
            }

            foreach ($codes as $val) {
                $name = $divisionKey . $val;
                $name = 'gross_' . ltrim($name, ':');

                if ($isConditionCross) {
                    $name = 'gross_condition_cross';
                }
                $resultAll[] = $resultAverage[$name];
            }

            if ($householdFlg) {
                $resultAll[] = $resultAverage['gross_household'];
            }
        }

        return $resultAll;
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param string $progStartDate
     * @param string $progEndDate
     * @param null|array $wdays
     * @param bool $holiday
     * @param null|array $channels
     * @param null|array $genres
     * @param null|array $programTypes
     * @param null|int $length
     * @param int $regionId
     */
    public function createObiProgramListTempTable(string $startDate, string $endDate, string $startTime, string $endTime, string $progStartDate, string $progEndDate, ?array $wdays, bool $holiday, ?array $channels, ?array $genres, ?array $programTypes, ?int $length, int $regionId): void
    {
        $bindings = [];
        $bindings[':startDate'] = $startDate;
        $bindings[':endDate'] = $endDate;
        $bindings[':regionId'] = $regionId;

        $bindGenres = [];

        if (count($genres) > 0) {
            $bindGenres = $this->createArrayBindParam('genres', [
                'genres' => $genres,
            ], $bindings);
        }
        $bindChannels = [];

        if (count($channels) > 0) {
            $bindChannels = $this->createArrayBindParam('channels', [
                'channels' => $channels,
            ], $bindings);
        }
        $bindWdays = [];

        if (count($wdays) > 0) {
            $bindWdays = $this->createArrayBindParam('wdays', [
                'wdays' => $wdays,
            ], $bindings);
        }

        $sql = '';
        $sql .= ' CREATE TEMPORARY TABLE day_of_week AS  ';
        $sql .= "   SELECT 1 odr, '月' dow_name ";
        $sql .= '   UNION  ';
        $sql .= "   SELECT 2 odr, '火' dow_name ";
        $sql .= '   UNION  ';
        $sql .= "   SELECT 3 odr, '水' dow_name ";
        $sql .= '   UNION  ';
        $sql .= "   SELECT 4 odr, '木' dow_name ";
        $sql .= '   UNION  ';
        $sql .= "   SELECT 5 odr, '金' dow_name ";
        $sql .= '   UNION  ';
        $sql .= "   SELECT 6 odr, '土' dow_name ";
        $sql .= '   UNION  ';
        $sql .= "   SELECT 7 odr, '日' dow_name ";
        $sql .= ' ; ';
        $this->select($sql);

        $sql = '';
        $sql .= ' CREATE TEMPORARY TABLE list AS WITH  obi AS ( ';
        $sql .= '   SELECT ';
        $sql .= '     prog_id ';
        $sql .= '     , title1 obi_title ';
        $sql .= '     , prog_type  ';
        $sql .= '   FROM ';
        $sql .= '     obi_programs  ';
        $sql .= '   WHERE ';
        $sql .= "     title1 <> ''  ";
        $sql .= '     AND title1 IS NOT NULL  ';
        $sql .= "     AND prog_id BETWEEN ${progStartDate} AND ${progEndDate} ";
        $sql .= '   UNION ALL  ';

        $sql .= '   SELECT ';
        $sql .= '     prog_id ';
        $sql .= '     , title2 obi_title ';
        $sql .= '     , prog_type  ';
        $sql .= '   FROM ';
        $sql .= '     obi_programs  ';
        $sql .= '   WHERE ';
        $sql .= "     title2 <> ''  ";
        $sql .= '     AND title2 IS NOT NULL  ';
        $sql .= "     AND prog_id BETWEEN ${progStartDate} AND ${progEndDate} ";
        $sql .= '   UNION ALL  ';

        $sql .= '   SELECT ';
        $sql .= '     prog_id ';
        $sql .= '     , title3 obi_title ';
        $sql .= '     , prog_type  ';
        $sql .= '   FROM ';
        $sql .= '     obi_programs  ';
        $sql .= '   WHERE ';
        $sql .= "     title3 <> ''  ";
        $sql .= '     AND title3 IS NOT NULL ';
        $sql .= "     AND prog_id BETWEEN ${progStartDate} AND ${progEndDate} ";
        $sql .= ' )  ';
        $sql .= ' , list AS (  ';
        $sql .= '   SELECT ';
        $sql .= '     p.prog_id ';
        $sql .= '     , p.time_box_id ';
        $sql .= '     , o.obi_title AS title ';
        $sql .= '     , o.prog_type ';
        $sql .= '     , real_started_at ';
        $sql .= '     , greatest(p.real_started_at, tb.started_at) AS calc_start_time '; //-- time_box 跨ぎを考慮した時間(timestamp
        $sql .= '     , least(p.real_ended_at, tb.ended_at) AS calc_end_time '; //-- time_box 跨ぎを考慮した時間(timestamp
        $sql .= "     , TO_CHAR(calc_start_time - interval '5:00:00', 'HH24MISS') as shift_start_time ";
        $sql .= "     , TO_CHAR(calc_end_time - interval '5:00:01', 'HH24MISS') as shift_end_time ";
        $sql .= '     , LPAD(  ';
        $sql .= "       TO_CHAR(real_started_at - interval '5 hours', 'HH24') ::numeric + 5 || TO_CHAR(real_started_at, ':MI:SS') ";
        $sql .= '       , 8 ';
        $sql .= "       , '0' ";
        $sql .= '     ) disp_start_time '; //-- 29時間制(char
        $sql .= '     , LPAD(  ';
        $sql .= '       TO_CHAR(  ';
        $sql .= "         real_ended_at - interval '5 hours 1 seconds' ";
        $sql .= "         , 'HH24' ";
        $sql .= "       ) ::numeric + 5 || TO_CHAR(real_ended_at - Interval '1 seconds', ':MI:SS') ";
        $sql .= '       , 8 ';
        $sql .= "       , '0' ";
        $sql .= '     ) disp_end_time '; //-- 29時間制(char
        $sql .= '     , TRUNC(  ';
        $sql .= '       EXTRACT(  ';
        $sql .= '         EPOCH  ';
        $sql .= '         FROM ';
        $sql .= '           (real_ended_at - real_started_at) ';
        $sql .= '       ) ';
        $sql .= '     ) / 60 min '; //-- 分数
        $sql .= '     , COUNT(*) OVER (  ';
        $sql .= '       PARTITION BY ';
        $sql .= '         o.obi_title ';
        $sql .= '         , o.prog_type ';
        $sql .= '         , c.code_name ';
        $sql .= '         , disp_start_time ';
        $sql .= '         , disp_end_time  ';
        $sql .= '       ORDER BY ';
        $sql .= '         disp_start_time ';
        $sql .= '         , disp_end_time ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW ';
        $sql .= '     ) cnt ';
        $sql .= '     , CASE  ';
        $sql .= '       WHEN EXTRACT(DOW FROM date) = 0  ';
        $sql .= '         THEN 7  ';
        $sql .= '       ELSE EXTRACT(DOW FROM date)  ';
        $sql .= '       END dow ';
        $sql .= "     , real_started_at - interval '5 hours' as dow_time ";
        $sql .= '     , c.code_name ';
        $sql .= '     , EXTRACT(EPOCH FROM calc_end_time - calc_start_time) AS fraction ';
        $sql .= '     , personal_viewing_rate ';
        $sql .= '     , household_viewing_rate ';
        $sql .= '     , ts_personal_viewing_rate ';
        $sql .= '     , ts_household_viewing_rate ';
        $sql .= '     , COALESCE(ts_personal_gross_viewing_rate, ts_samples_personal_viewing_rate) ts_personal_gross_viewing_rate ';
        $sql .= '     , COALESCE(ts_household_gross_viewing_rate, ts_samples_household_viewing_rate) ts_household_gross_viewing_rate ';
        $sql .= '     , p.channel_id  ';
        $sql .= '   FROM ';
        $sql .= '     programs p  ';
        $sql .= '     INNER JOIN channels c  ';
        $sql .= '       ON p.channel_id = c.id  ';
        $sql .= '     INNER JOIN time_boxes tb  ';
        $sql .= '       ON p.time_box_id = tb.id  ';
        $sql .= '       AND tb.region_id = :regionId  ';
        $sql .= '     INNER JOIN obi o  ';
        $sql .= '       ON p.prog_id = o.prog_id  ';
        $sql .= '   WHERE ';
        $sql .= '     prepared = 1 ';
        $sql .= '     AND p.date BETWEEN :startDate AND :endDate ';

        // 時間帯
        if ($startTime === '050000' && $endTime === '045959') {
            // 全選択の場合は検索条件に含めない
        } else {
            $bindings[':startTime'] = $startTime;
            $bindings[':endTime'] = $endTime;

            if ($endTime >= $startTime) {
                $sql .= '  AND (( ';
                $sql .= '   shift_end_time <  shift_start_time ';
                $sql .= '   AND ( ';
                $sql .= '    shift_start_time <= :endTime ';
                $sql .= '    OR shift_end_time > :startTime ';
                $sql .= '   ) ';
                $sql .= '  ) ';
                $sql .= '  OR ( ';
                $sql .= '   shift_end_time >= shift_start_time ';
                $sql .= '   AND shift_start_time <= :endTime ';
                $sql .= '   AND shift_end_time > :startTime ';
                $sql .= '  )) ';
            } else {
                $sql .= ' AND  (( ';
                $sql .= '   shift_end_time < shift_start_time ';
                $sql .= '  ) ';
                $sql .= '  OR ( ';
                $sql .= '   shift_end_time >=  shift_start_time ';
                $sql .= '   AND ( ';
                $sql .= '    shift_start_time <= :endTime ';
                $sql .= '    OR shift_end_time > :startTime ';
                $sql .= '   ) ';
                $sql .= '  )) ';
            }
        }

        // 曜日＆祝日
        $sql .= '   AND (EXTRACT(DOW FROM dow_time) IN (' . implode(',', $bindWdays) . ')) ';

        if (!$holiday) {
            $sql .= '   AND p.date NOT IN (SELECT holiday FROM holidays) ';
        }

        // 放送局
        if (count($bindChannels) > 0) {
            $sql .= '   AND p.channel_id IN (' . implode(',', $bindChannels) . ')  ';
        }

        // ジャンル
        $sql .= "   AND p.genre_id <> '20014'  ";

        if (count($bindGenres) > 0) {
            $sql .= '   AND p.genre_id IN (' . implode(',', $bindGenres) . ')';
        }
        $sql .= " AND p.prog_id BETWEEN ${progStartDate} AND ${progEndDate} ";

        // 番組種別
        if (!empty($programTypes) || $programTypes . $length != 5) {
            // 検索条件をテキストに変換する
            $programTypesArray = [
                '1' => 'レギュラー',
                '2' => 'スペシャル',
                '3' => 'ミニ番',
                '4' => '再放送',
                '5' => '番宣',
            ];
            $programTypesCharaArray = [];

            foreach ($programTypesArray as $k => $v) {
                if (in_array($k, $programTypes)) {
                    array_push($programTypesCharaArray, $v);
                }
            }

            $programTypesBind = $this->createArrayBindParam('programTypes', [
                'programTypes' => $programTypesCharaArray,
            ], $bindings);

            $sql .= '   AND prog_type IN (' . implode(',', $programTypesBind) . ') ';
        }
        $sql .= ' )  ';
        $sql .= ' SELECT * FROM list;';
        $this->select($sql, $bindings);
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param string $division
     * @param null|array $conditionCross
     * @param null|array $codes
     * @param string $dispAverage
     * @param array $dataType
     * @param null|array $wdays
     * @param bool $holiday
     * @param null|array $channels
     * @param null|array $genres
     * @param null|array $programTypes
     * @param null|int $length
     * @param int $regionId
     * @param null|int $page
     * @param bool $straddlingFlg
     * @param string $csvFlag
     * @param array $dataTypeFlags
     * @param array $prefixes
     * @return array
     */
    public function periodAverage(string $startDate, string $endDate, string $startTime, string $endTime, String $division, ?array $conditionCross, ?array $codes, string $dispAverage, array $dataType, ?array $wdays, bool $holiday, ?array $channels, ?array $genres, ?array $programTypes, ?int $length, int $regionId, ?int $page, bool $straddlingFlg, string $csvFlag, array $dataTypeFlags, array $prefixes): array
    {
        list($rsTimeBoxIds, $rsProgIds, $rsPanelers) = $this->createProgramListWhere($startDate, $endDate, $channels, $genres, [], $division, $conditionCross, $codes, $regionId, false);

        if (empty($rsTimeBoxIds) && empty($rsProgIds)) {
            return [
                'list' => [],
                'cnt' => 0,
            ];
        }

        $offset = $length * ($page - 1);

        $sampleCodePrefix = $prefixes['code'];

        // コード種別の頭部分
        $divisionKey = "{$division}_";

        $isConditionCross = $division == 'condition_cross';

        $divCodes = [];

        if ($isConditionCross) {
            $divCodes[] = 'condition_cross';
        } else {
            $divCodes = array_filter($codes, function ($v, $k) {
                return $v != 'personal' && $v != 'household';
            }, ARRAY_FILTER_USE_BOTH);
        }
        $divCodes = array_merge($divCodes); //keyを連番に

        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = array_values($dataTypeFlags);

        $hasPersonal = !$isConditionCross && in_array('personal', $codes);
        $hasHousehold = $isConditionCross || in_array('household', $codes);

        $progStartDate = min($rsProgIds);
        $progEndDate = max($rsProgIds);

        $this->createObiProgramListTempTable($startDate, $endDate, $startTime, $endTime, $progStartDate, $progEndDate, $wdays, $holiday, $channels, $genres, $programTypes, $length, $regionId);

        $sql = '';
        $sql .= ' SELECT COUNT(*) cnt FROM ( SELECT l.title FROM list l GROUP BY l.title, l.prog_type, l.channel_id ) list ';
        $count = $this->selectOne($sql);

        $bindings = [];

        $sql = '';
        $sql .= ' WITH dummy AS (SELECT 1) ';
        $bindings[':division'] = $division;

        if ($isRt) {
            $reportSelectSql = '';
            $codeNamesBinding = [];

            foreach ($divCodes as $i => $name) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                $codeNameBinding = ":codeName${i}";
                $codeNamesBinding[] = $codeNameBinding;
                $bindings[$codeNameBinding] = $name;

                // -- 加重平均
                $reportSelectSql .= '     , SUM(  ';
                $reportSelectSql .= '       CASE  ';
                $reportSelectSql .= "         WHEN pr.code = ${codeNameBinding}  ";
                $reportSelectSql .= '           THEN pr.viewing_rate * fraction  ';
                $reportSelectSql .= '         END ';
                $reportSelectSql .= "     ) AS ${code} ";

                // -- 単純平均
                $reportSelectSql .= '     , SUM(  ';
                $reportSelectSql .= '       CASE  ';
                $reportSelectSql .= "         WHEN pr.code = ${codeNameBinding}  ";
                $reportSelectSql .= '           THEN pr.viewing_rate  ';
                $reportSelectSql .= '         END ';
                $reportSelectSql .= "     ) AS s_${code} ";
            }

            $sql .= ' , program_report_pivoted AS ( ';
            $sql .= ' SELECT ';
            $sql .= '   l.title ';
            $sql .= '   , l.prog_type ';
            $sql .= '   , l.code_name ';
            $sql .= $reportSelectSql;
            $sql .= ' FROM ';
            $sql .= '   list l  ';
            $sql .= ' LEFT JOIN program_reports pr  ';
            $sql .= '   ON l.prog_id = pr.prog_id  ';
            $sql .= '   AND l.time_box_id = pr.time_box_id  ';
            $sql .= "   AND pr.prog_id BETWEEN ${progStartDate} AND ${progEndDate} ";
            $sql .= '   AND pr.division = :division  ';

            if (count($codeNamesBinding) > 0) {
                $sql .= '   AND pr.code IN (' . implode(',', $codeNamesBinding) . ') ';
            }
            $sql .= ' GROUP BY ';
            $sql .= '   l.title ';
            $sql .= '   , l.code_name ';
            $sql .= '   , l.prog_type ';
            $sql .= ' )';
        }

        if ($isTs) {
            $reportSelectSql = '';
            $codeNamesBinding = [];

            foreach ($divCodes as $i => $name) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                $codeNameBinding = ":codeName${i}";
                $codeNamesBinding[] = $codeNameBinding;
                $bindings[$codeNameBinding] = $name;

                // -- 加重平均
                $reportSelectSql .= '     , SUM(  ';
                $reportSelectSql .= '       CASE  ';
                $reportSelectSql .= "         WHEN pr.code = ${codeNameBinding}  ";
                $reportSelectSql .= '           THEN pr.viewing_rate * fraction  ';
                $reportSelectSql .= '         END ';
                $reportSelectSql .= "     ) AS ${code} ";

                //-- 単純平均
                $reportSelectSql .= '     , SUM(  ';
                $reportSelectSql .= '       CASE  ';
                $reportSelectSql .= "         WHEN pr.code = ${codeNameBinding}  ";
                $reportSelectSql .= '           THEN pr.viewing_rate  ';
                $reportSelectSql .= '         END ';
                $reportSelectSql .= "     ) AS s_${code} ";
            }

            $sql .= ' ,ts_program_report_pivoted AS ( ';
            $sql .= ' SELECT ';
            $sql .= '   l.title ';
            $sql .= '   , l.prog_type ';
            $sql .= '   , l.code_name ';
            $sql .= $reportSelectSql;
            $sql .= ' FROM ';
            $sql .= '     list l  ';
            $sql .= ' LEFT JOIN ts_program_reports pr  ';
            $sql .= '     ON l.prog_id = pr.prog_id  ';
            $sql .= '     AND l.time_box_id = pr.time_box_id  ';
            $sql .= "     AND pr.prog_id BETWEEN ${progStartDate} AND ${progEndDate} ";
            $sql .= '     AND pr.division = :division ';

            if (count($codeNamesBinding) > 0) {
                $sql .= '   AND pr.code IN (' . implode(',', $codeNamesBinding) . ') ';
            }
            $sql .= '     AND pr.c_index = 7  ';
            $sql .= '   GROUP BY ';
            $sql .= '     l.title ';
            $sql .= '     , l.code_name ';
            $sql .= '     , l.prog_type ';
            $sql .= ' )';
        }

        if ($isGross) {
            $reportSelectSql = '';
            $codeNamesBinding = [];

            foreach ($divCodes as $i => $name) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                $codeNameBinding = ":codeName${i}";
                $codeNamesBinding[] = $codeNameBinding;
                $bindings[$codeNameBinding] = $name;

                // -- 加重平均
                $reportSelectSql .= '     , SUM(  ';
                $reportSelectSql .= '       CASE  ';
                $reportSelectSql .= "         WHEN pr.code = ${codeNameBinding}  ";
                $reportSelectSql .= '           THEN pr.viewing_rate * fraction  ';
                $reportSelectSql .= '         END ';
                $reportSelectSql .= "     ) AS ${code} ";

                // -- 単純平均
                $reportSelectSql .= '     , SUM(  ';
                $reportSelectSql .= '       CASE  ';
                $reportSelectSql .= "         WHEN pr.code = ${codeNameBinding}  ";
                $reportSelectSql .= '           THEN pr.viewing_rate  ';
                $reportSelectSql .= '         END ';
                $reportSelectSql .= "     ) AS s_${code} ";
            }

            $sql .= ' , gross_program_reports AS (  ';
            $sql .= '   SELECT ';
            $sql .= '     prog_id ';
            $sql .= '     , time_box_id ';
            $sql .= '     , division ';
            $sql .= '     , code ';
            $sql .= '     , COALESCE(MAX(gross_viewing_rate), MAX(ts_samples_viewing_rate)) viewing_rate  ';
            $sql .= '   FROM ';
            $sql .= '     (  ';
            $sql .= '       SELECT ';
            $sql .= '         prog_id ';
            $sql .= '         , time_box_id ';
            $sql .= '         , division ';
            $sql .= '         , code ';
            $sql .= '         , ts_samples_viewing_rate ';
            $sql .= '         , NULL gross_viewing_rate  ';
            $sql .= '       FROM ';
            $sql .= '         program_reports pr  ';
            $sql .= '       WHERE ';
            $sql .= "         pr.prog_id BETWEEN ${progStartDate} AND ${progEndDate} ";
            $sql .= '         AND pr.division = :division ';

            if (count($codeNamesBinding) > 0) {
                $sql .= '   AND pr.code IN (' . implode(',', $codeNamesBinding) . ') ';
            }
            $sql .= '       UNION ALL  ';
            $sql .= '       SELECT ';
            $sql .= '         prog_id ';
            $sql .= '         , time_box_id ';
            $sql .= '         , division ';
            $sql .= '         , code ';
            $sql .= '         , NULL ts_samples_viewing_rate ';
            $sql .= '         , gross_viewing_rate  ';
            $sql .= '       FROM ';
            $sql .= '         ts_program_reports tpr  ';
            $sql .= '       WHERE ';
            $sql .= "         tpr.prog_id BETWEEN ${progStartDate} AND ${progEndDate} ";
            $sql .= '         AND tpr.division = :division ';

            if (count($codeNamesBinding) > 0) {
                $sql .= '   AND tpr.code IN (' . implode(',', $codeNamesBinding) . ') ';
            }
            $sql .= '         AND tpr.c_index = 7 ';
            $sql .= '     ) unioned  ';
            $sql .= '   GROUP BY ';
            $sql .= '     prog_id ';
            $sql .= '     , time_box_id ';
            $sql .= '     , division ';
            $sql .= '     , code ';
            $sql .= ' )  ';

            $sql .= ' , gross_program_report_pivoted AS (  ';
            $sql .= '   SELECT ';
            $sql .= '     l.title ';
            $sql .= '     , l.prog_type ';
            $sql .= '     , l.code_name ';
            $sql .= $reportSelectSql;
            $sql .= '   FROM ';
            $sql .= '     list l  ';
            $sql .= '     LEFT JOIN gross_program_reports pr  ';
            $sql .= '       ON l.prog_id = pr.prog_id  ';
            $sql .= '       AND l.time_box_id = pr.time_box_id  ';
            $sql .= '   GROUP BY ';
            $sql .= '     l.title ';
            $sql .= '     , l.code_name ';
            $sql .= '     , l.prog_type ';
            $sql .= ' )  ';
        }

        $sql .= ' , targeted AS (  ';
        $sql .= '   SELECT ';
        $sql .= '     title ';
        $sql .= '     , calc_start_time ';
        $sql .= '     , calc_end_time ';
        $sql .= '     , disp_start_time ';
        $sql .= '     , disp_end_time ';
        $sql .= '     , real_started_at ';
        $sql .= '     , min ';
        $sql .= '     , COUNT(*) OVER (  ';
        $sql .= '       PARTITION BY ';
        $sql .= '         title ';
        $sql .= '         , prog_type ';
        $sql .= '         , code_name  ';
        $sql .= '       ORDER BY ';
        $sql .= '         cnt DESC ';
        $sql .= '         , min ';
        $sql .= '         , disp_start_time ';
        $sql .= '         , disp_end_time DESC ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW ';
        $sql .= '     ) target ';
        $sql .= '     , dow ';
        $sql .= '     , dow_name ';
        $sql .= '     , code_name ';
        $sql .= '     , DENSE_RANK() OVER (  ';
        $sql .= '       PARTITION BY ';
        $sql .= '         title ';
        $sql .= '         , prog_type ';
        $sql .= '         , code_name  ';
        $sql .= '       ORDER BY ';
        $sql .= '         dow ';
        $sql .= '     ) dow_number ';
        $sql .= '     , prog_type ';
        $sql .= '     , fraction ';
        $sql .= '     , personal_viewing_rate ';
        $sql .= '     , household_viewing_rate ';
        $sql .= '     , ts_personal_viewing_rate ';
        $sql .= '     , ts_household_viewing_rate ';
        $sql .= '     , ts_personal_gross_viewing_rate ';
        $sql .= '     , ts_household_gross_viewing_rate  ';
        $sql .= '   FROM ';
        $sql .= '     list  ';
        $sql .= '     INNER JOIN day_of_week d  ';
        $sql .= '       ON d.odr = dow ';
        $sql .= ' )  ';

        $sql .= ' , result AS (  ';
        $sql .= '   SELECT ';
        $sql .= '     title ';
        $sql .= '     , MAX(CASE WHEN target = 1 THEN disp_start_time END) disp_start_time ';
        $sql .= '     , MAX(CASE WHEN target = 1 THEN disp_end_time END) disp_end_time ';
        $sql .= '     , MAX(CASE WHEN target = 1 THEN calc_start_time END) calc_start_time ';
        $sql .= '     , MAX(CASE WHEN target = 1 THEN calc_end_time END) calc_end_time ';
        $sql .= '     , MAX(CASE WHEN target = 1 THEN real_started_at END) real_started_at ';
        $sql .= '     , MAX(CASE WHEN target = 1 THEN min END) min ';
        $sql .= '     , LISTAGG(DISTINCT dow_name) WITHIN GROUP (ORDER BY dow) dows ';
        $sql .= '     , LISTAGG(DISTINCT dow) WITHIN GROUP (ORDER BY dow) dows_num ';
        $sql .= '     , COUNT(*) cnt ';
        $sql .= '     , code_name ';
        $sql .= '     , MAX(dow_number) dow_cnt ';
        $sql .= '     , TRUNC(  ';
        $sql .= '       SUM(  ';
        $sql .= '         EXTRACT(EPOCH FROM calc_end_time - calc_start_time) ';
        $sql .= '       ) / 60 ';
        $sql .= '     ) total_min ';
        $sql .= '     , prog_type ';
        $sql .= '     , SUM(fraction) as fraction ';
        $sql .= '     , SUM(personal_viewing_rate * fraction) AS personal_viewing_rate ';
        $sql .= '     , SUM(household_viewing_rate * fraction) AS household_viewing_rate ';
        $sql .= '     , SUM(ts_personal_viewing_rate * fraction) AS ts_personal_viewing_rate ';
        $sql .= '     , SUM(ts_household_viewing_rate * fraction) AS ts_household_viewing_rate ';
        $sql .= '     , SUM(ts_personal_gross_viewing_rate * fraction) AS gross_personal_viewing_rate ';
        $sql .= '     , SUM(ts_household_gross_viewing_rate * fraction) AS gross_household_viewing_rate  ';
        $sql .= '     , SUM(personal_viewing_rate) AS s_personal_viewing_rate ';
        $sql .= '     , SUM(household_viewing_rate) AS s_household_viewing_rate ';
        $sql .= '     , SUM(ts_personal_viewing_rate) AS ts_s_personal_viewing_rate ';
        $sql .= '     , SUM(ts_household_viewing_rate) AS ts_s_household_viewing_rate ';
        $sql .= '     , SUM(ts_personal_gross_viewing_rate) AS gross_s_personal_viewing_rate ';
        $sql .= '     , SUM(ts_household_gross_viewing_rate) AS gross_s_household_viewing_rate  ';
        $sql .= '   FROM ';
        $sql .= '     targeted  ';
        $sql .= '   GROUP BY ';
        $sql .= '     title ';
        $sql .= '     , prog_type ';
        $sql .= '     , code_name ';
        $sql .= ' )  ';

        $sql .= ' SELECT ';
        $sql .= '   r.disp_start_time mode_start ';
        $sql .= '   , r.disp_end_time mode_end ';
        $sql .= '   , r.dows dow ';
        $sql .= '   , r.code_name ';
        $sql .= '   , r.title ';
        $sql .= '   , r.prog_type ';
        $sql .= '   , r.cnt number_of_program ';
        $sql .= '   , r.min mode_minute ';
        $sql .= '   , r.total_min mode_minute_total ';

        if ($dispAverage === 'weight') {
            // まとめて加重平均
            if ($isRt) {
                if ($hasPersonal) {
                    $sql .= '   , COALESCE(ROUND(r.personal_viewing_rate / fraction, 1), 0) rt_personal_viewing_rate ';
                }

                foreach ($divCodes as $i => $val) {
                    $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                    $name = $divisionKey . $val;

                    if ($isConditionCross && $val === 'condition_cross') {
                        $name = 'condition_cross';
                    }
                    $sql .= "   , COALESCE(ROUND(pr.${code} / fraction, 1), 0) rt_${name} ";
                }

                if ($hasHousehold) {
                    $sql .= '   , COALESCE(  ';
                    $sql .= '     ROUND(r.household_viewing_rate / fraction, 1) ';
                    $sql .= '     , 0 ';
                    $sql .= '   ) rt_household_viewing_rate ';
                }
            }

            if ($isTs) {
                if ($hasPersonal) {
                    $sql .= '   , COALESCE(ROUND(r.ts_personal_viewing_rate / fraction, 1), 0) ts_personal_viewing_rate ';
                }

                foreach ($divCodes as $i => $val) {
                    $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                    $name = $divisionKey . $val;

                    if ($isConditionCross && $val === 'condition_cross') {
                        $name = 'condition_cross';
                    }
                    $sql .= "   , COALESCE(ROUND(tpr.${code} / fraction, 1), 0) ts_${name} ";
                }

                if ($hasHousehold) {
                    $sql .= '   , COALESCE(  ';
                    $sql .= '     ROUND(r.ts_household_viewing_rate / fraction, 1) ';
                    $sql .= '     , 0 ';
                    $sql .= '   ) ts_household_viewing_rate ';
                }
            }

            if ($isGross) {
                if ($hasPersonal) {
                    $sql .= '   , COALESCE(ROUND(r.gross_personal_viewing_rate / fraction, 1), 0) gross_personal_viewing_rate ';
                }

                foreach ($divCodes as $i => $val) {
                    $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                    $name = $divisionKey . $val;

                    if ($isConditionCross && $val === 'condition_cross') {
                        $name = 'condition_cross';
                    }
                    $sql .= "   , COALESCE(ROUND(gpr.${code} / fraction, 1), 0) gross_${name} ";
                }

                if ($hasHousehold) {
                    $sql .= '   , COALESCE(  ';
                    $sql .= '     ROUND(r.gross_household_viewing_rate / fraction, 1) ';
                    $sql .= '     , 0 ';
                    $sql .= '   ) gross_household_viewing_rate ';
                }
            }
        } elseif ($dispAverage === 'simple') {
            // まとめて単純平均
            if ($isRt) {
                if ($hasPersonal) {
                    $sql .= '   , COALESCE(ROUND(r.s_personal_viewing_rate / cnt, 1), 0) rt_personal_viewing_rate ';
                }

                foreach ($divCodes as $i => $val) {
                    $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                    $name = $divisionKey . $val;

                    if ($isConditionCross && $val === 'condition_cross') {
                        $name = 'condition_cross';
                    }
                    $sql .= "   , COALESCE(ROUND(pr.s_${code} / cnt, 1), 0) rt_${name} ";
                }

                if ($hasHousehold) {
                    $sql .= '   , COALESCE(ROUND(r.s_household_viewing_rate / cnt, 1), 0) rt_household_viewing_rate ';
                }
            }

            if ($isTs) {
                if ($hasPersonal) {
                    $sql .= '   , COALESCE(ROUND(r.ts_s_personal_viewing_rate / cnt, 1), 0) ts_personal_viewing_rate ';
                }

                foreach ($divCodes as $i => $val) {
                    $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                    $name = $divisionKey . $val;

                    if ($isConditionCross && $val === 'condition_cross') {
                        $name = 'condition_cross';
                    }
                    $sql .= "   , COALESCE(ROUND(tpr.s_${code} / cnt, 1), 0) ts_${name} ";
                }

                if ($hasHousehold) {
                    $sql .= '   , COALESCE(  ';
                    $sql .= '     ROUND(r.ts_s_household_viewing_rate / cnt, 1) ';
                    $sql .= '     , 0 ';
                    $sql .= '   ) ts_household_viewing_rate ';
                }
            }

            if ($isGross) {
                if ($hasPersonal) {
                    $sql .= '   , COALESCE(ROUND(r.gross_s_personal_viewing_rate / cnt, 1), 0) gross_personal_viewing_rate ';
                }

                foreach ($divCodes as $i => $val) {
                    $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                    $name = $divisionKey . $val;

                    if ($isConditionCross && $val === 'condition_cross') {
                        $name = 'condition_cross';
                    }
                    $sql .= "   , COALESCE(ROUND(gpr.s_${code} / cnt, 1), 0) gross_${name} ";
                }

                if ($hasHousehold) {
                    $sql .= '   , COALESCE(  ';
                    $sql .= '     ROUND(r.gross_s_household_viewing_rate / cnt, 1) ';
                    $sql .= '     , 0 ';
                    $sql .= '   ) gross_household_viewing_rate ';
                }
            }
        }

        $sql .= ' FROM ';
        $sql .= '   result r  ';

        if ($isRt) {
            $sql .= '   LEFT JOIN program_report_pivoted pr  ';
            $sql .= '     ON r.title = pr.title  ';
            $sql .= '     AND r.code_name = pr.code_name  ';
            $sql .= '     AND r.prog_type = pr.prog_type  ';
        }

        if ($isTs) {
            $sql .= '   LEFT JOIN ts_program_report_pivoted tpr  ';
            $sql .= '     ON r.title = tpr.title  ';
            $sql .= '     AND r.code_name = tpr.code_name  ';
            $sql .= '     AND r.prog_type = tpr.prog_type  ';
        }

        if ($isGross) {
            $sql .= '   LEFT JOIN gross_program_report_pivoted gpr  ';
            $sql .= '     ON r.title = gpr.title  ';
            $sql .= '     AND r.code_name = gpr.code_name  ';
            $sql .= '     AND r.prog_type = gpr.prog_type  ';
        }
        $sql .= ' ORDER BY ';
        $sql .= '   r.code_name ';
        $sql .= '   , CASE  ';
        $sql .= "     WHEN r.prog_type = 'レギュラー'  ";
        $sql .= '       THEN 1  ';
        $sql .= "     WHEN r.prog_type = 'スペシャル'  ";
        $sql .= '       THEN 2  ';
        $sql .= "     WHEN r.prog_type = 'ミニ番'  ";
        $sql .= '       THEN 3  ';
        $sql .= "     WHEN r.prog_type = '再放送'  ";
        $sql .= '       THEN 4  ';
        $sql .= "     WHEN r.prog_type = '番宣'  ";
        $sql .= '       THEN 5  ';
        $sql .= '     END ';
        $sql .= '   , CASE  ';
        $sql .= '     WHEN r.dow_cnt = 1  ';
        $sql .= '       THEN 0  ';
        $sql .= '     ELSE 1  ';
        $sql .= '     END ';
        $sql .= '   , SUBSTRING(r.dows_num, 1, 1) ';
        $sql .= "   , CASE WHEN split_part(disp_start_time,':',1)::numeric >= 24 THEN '0' || split_part(disp_start_time,':',1)::numeric - 24 || ':' ||  split_part(disp_start_time,':',2) || ':' || split_part(disp_start_time,':',3) ELSE disp_start_time END ";
        $sql .= '   , r.title  ';

        if ($csvFlag == '0') {
            $sql .= " OFFSET ${offset} LIMIT ${length} ";
        }

        $results = $this->select($sql, $bindings);

        return [
            'list' => $results,
            'cnt' => $count,
        ];
    }

    /**
     * 番組期間平均 視聴率計算（拡張属性・オリジナル属性用）.
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param string $division
     * @param ?array $conditionCross
     * @param ?array $codes
     * @param string $dispAverage
     * @param array $dataType
     * @param ?array $wdays
     * @param bool $holiday
     * @param ?array $channels
     * @param ?array $genres
     * @param ?array $programTypes
     * @param ?int $length
     * @param int $regionId
     * @param ?int $page
     * @param bool $straddlingFlg
     * @param string $csvFlag
     * @param array $dataTypeFlags
     * @param array $prefixes
     * @param string $selectedPersonalName
     * @param int $codeNumber
     */
    public function periodAverageOriginal(string $startDate, string $endDate, string $startTime, string $endTime, String $division, ?array $conditionCross, ?array $codes, string $dispAverage, array $dataType, ?array $wdays, bool $holiday, ?array $channels, ?array $genres, ?array $programTypes, ?int $length, int $regionId, ?int $page, bool $straddlingFlg, string $csvFlag, array $dataTypeFlags, array $prefixes, string $selectedPersonalName, int $codeNumber): array
    {
        list($rsTimeBoxIds, $rsProgIds, $rsPanelers) = $this->createProgramListWhere($startDate, $endDate, $channels, $genres, [], $division, $conditionCross, $codes, $regionId, false);

        if (empty($rsTimeBoxIds) && empty($rsProgIds)) {
            return [
                'list' => [],
                'cnt' => 0,
            ];
        }

        $offset = $length * ($page - 1);

        $sampleCodePrefix = $prefixes['code'];
        $sampleCodeNumberPrefix = $prefixes['number'];

        // コード種別の頭部分
        $divisionKey = "{$division}_";

        $isConditionCross = $division == 'condition_cross';

        $divCodes = [];

        if ($isConditionCross) {
            $divCodes[] = 'condition_cross';
        } else {
            $divCodes = array_filter($codes, function ($v, $k) {
                return $v != 'personal' && $v != 'household';
            }, ARRAY_FILTER_USE_BOTH);
        }
        $divCodes = array_merge($divCodes); //keyを連番に

        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = array_values($dataTypeFlags);

        $hasPersonal = !$isConditionCross && in_array('personal', $codes);
        $hasHousehold = $isConditionCross || in_array('household', $codes);

        $progStartDate = min($rsProgIds);
        $progEndDate = max($rsProgIds);

        $this->createObiProgramListTempTable($startDate, $endDate, $startTime, $endTime, $progStartDate, $progEndDate, $wdays, $holiday, $channels, $genres, $programTypes, $length, $regionId);

        $sql = '';
        $sql .= ' SELECT COUNT(*) cnt FROM ( SELECT l.title FROM list l GROUP BY l.title, l.prog_type, l.channel_id ) list ';
        $count = $this->selectOne($sql);

        $this->createPvUniondTempTables($isConditionCross, $conditionCross, $division, $divCodes, $rsTimeBoxIds, $regionId, $dataType, $progStartDate, $progEndDate, '', $dataTypeFlags, $prefixes, $sampleCodeNumberPrefix, $selectedPersonalName, $codeNumber);

        if ($isRt) {
            $reportSelectSqlArr = [];
            $cnt = count($divCodes);

            for ($i = 0; $i < $cnt; $i++) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                $number = sprintf('%s%02d', $sampleCodeNumberPrefix, $i);

                $reportSelectSql = '';
                // -- 加重平均
                $reportSelectSql .= '   , SUM(  ';
                $reportSelectSql .= '     CASE  ';
                $reportSelectSql .= "       WHEN pu.code = '${code}'  ";
                $reportSelectSql .= '         THEN pu.viewing_seconds ::numeric / (  ';
                $reportSelectSql .= '         (  ';
                $reportSelectSql .= '           SELECT ';
                $reportSelectSql .= "             ${number}  ";
                $reportSelectSql .= '           FROM ';
                $reportSelectSql .= '             rt_numbers rtn  ';
                $reportSelectSql .= '           WHERE ';
                $reportSelectSql .= '             l.time_box_id = rtn.time_box_id ';
                $reportSelectSql .= '         ) * l.fraction ';
                $reportSelectSql .= '       )  ';
                $reportSelectSql .= '       END * 100 * fraction ';
                $reportSelectSql .= "   ) AS ${code} ";
                // -- 単純平均
                $reportSelectSql .= '   , SUM(  ';
                $reportSelectSql .= '     CASE  ';
                $reportSelectSql .= "       WHEN pu.code = '${code}'  ";
                $reportSelectSql .= '         THEN pu.viewing_seconds ::numeric / (  ';
                $reportSelectSql .= '         (  ';
                $reportSelectSql .= '           SELECT ';
                $reportSelectSql .= "             ${number}  ";
                $reportSelectSql .= '           FROM ';
                $reportSelectSql .= '             rt_numbers rtn  ';
                $reportSelectSql .= '           WHERE ';
                $reportSelectSql .= '             l.time_box_id = rtn.time_box_id ';
                $reportSelectSql .= '         ) * l.fraction ';
                $reportSelectSql .= '       )  ';
                $reportSelectSql .= '       END * 100 ';
                $reportSelectSql .= "   ) AS s_${code} ";

                $reportSelectSqlArr[] = $reportSelectSql;
            }

            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE program_report_pivoted AS ';
            $sql .= ' SELECT ';
            $sql .= '   l.title ';
            $sql .= '   , l.prog_type ';
            $sql .= '   , l.code_name ';

            if (count($reportSelectSqlArr) > 0) {
                $sql .= implode('', $reportSelectSqlArr);
            }
            $sql .= ' FROM ';
            $sql .= '   list l ';

            if (count($reportSelectSqlArr) > 0) {
                $sql .= '   LEFT JOIN pv_unioned pu ';
                $sql .= '     ON l.prog_id = pu.prog_id ';
                $sql .= '     AND l.time_box_id = pu.time_box_id ';
            }
            $sql .= ' GROUP BY ';
            $sql .= '   l.title ';
            $sql .= '   , l.code_name ';
            $sql .= '   , l.prog_type; ';
            $this->select($sql);
        }

        if ($isTs) {
            $reportSelectSqlArr = [];
            $cnt = count($divCodes);

            for ($i = 0; $i < $cnt; $i++) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                $number = sprintf('%s%02d', $sampleCodeNumberPrefix, $i);

                $reportSelectSql = '';
                // -- 加重平均
                $reportSelectSql .= '   , SUM(  ';
                $reportSelectSql .= '     CASE  ';
                $reportSelectSql .= "       WHEN pu.code = '${code}'  ";
                $reportSelectSql .= '         THEN pu.viewing_seconds ::numeric / (  ';
                $reportSelectSql .= '         (  ';
                $reportSelectSql .= '           SELECT ';
                $reportSelectSql .= "             ${number}  ";
                $reportSelectSql .= '           FROM ';
                $reportSelectSql .= '             ts_numbers tsn  ';
                $reportSelectSql .= '           WHERE ';
                $reportSelectSql .= '             l.time_box_id = tsn.time_box_id ';
                $reportSelectSql .= '         ) * l.fraction ';
                $reportSelectSql .= '       )  ';
                $reportSelectSql .= '       END * 100 * fraction ';
                $reportSelectSql .= "   ) AS ${code} ";
                // -- 単純平均
                $reportSelectSql .= '   , SUM(  ';
                $reportSelectSql .= '     CASE  ';
                $reportSelectSql .= "       WHEN pu.code = '${code}'  ";
                $reportSelectSql .= '         THEN pu.viewing_seconds ::numeric / (  ';
                $reportSelectSql .= '         (  ';
                $reportSelectSql .= '           SELECT ';
                $reportSelectSql .= "             ${number}  ";
                $reportSelectSql .= '           FROM ';
                $reportSelectSql .= '             ts_numbers tsn  ';
                $reportSelectSql .= '           WHERE ';
                $reportSelectSql .= '             l.time_box_id = tsn.time_box_id ';
                $reportSelectSql .= '         ) * l.fraction ';
                $reportSelectSql .= '       )  ';
                $reportSelectSql .= '       END * 100 ';
                $reportSelectSql .= "   ) AS s_${code} ";

                $reportSelectSqlArr[] = $reportSelectSql;
            }

            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE ts_program_report_pivoted AS ';
            $sql .= ' SELECT ';
            $sql .= '   l.title ';
            $sql .= '   , l.prog_type ';
            $sql .= '   , l.code_name ';

            if (count($reportSelectSqlArr) > 0) {
                $sql .= implode('', $reportSelectSqlArr);
            }
            $sql .= ' FROM ';
            $sql .= '   list l ';

            if (count($reportSelectSqlArr) > 0) {
                $sql .= '   LEFT JOIN ts_pv_unioned pu ';
                $sql .= '     ON l.prog_id = pu.prog_id ';
                $sql .= '     AND l.time_box_id = pu.time_box_id ';
            }
            $sql .= ' GROUP BY ';
            $sql .= '   l.title ';
            $sql .= '   , l.code_name ';
            $sql .= '   , l.prog_type; ';
            $this->select($sql);
        }

        if ($isGross) {
            $reportSelectSqlArr = [];
            $cnt = count($divCodes);

            for ($i = 0; $i < $cnt; $i++) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                $number = sprintf('%s%02d', $sampleCodeNumberPrefix, $i);

                $reportSelectSql = '';
                // -- 加重平均
                $reportSelectSql .= '   , SUM(  ';
                $reportSelectSql .= '     CASE  ';
                $reportSelectSql .= "       WHEN pu.code = '${code}'  ";
                $reportSelectSql .= '         THEN pu.viewing_seconds ::numeric / (  ';
                $reportSelectSql .= '         (  ';
                $reportSelectSql .= '           SELECT ';
                $reportSelectSql .= "             ${number}  ";
                $reportSelectSql .= '           FROM ';
                $reportSelectSql .= '             ts_numbers tsn  ';
                $reportSelectSql .= '           WHERE ';
                $reportSelectSql .= '             l.time_box_id = tsn.time_box_id ';
                $reportSelectSql .= '         ) * l.fraction ';
                $reportSelectSql .= '       )  ';
                $reportSelectSql .= '       END * 100 * fraction ';
                $reportSelectSql .= "   ) AS ${code} ";
                // -- 単純平均
                $reportSelectSql .= '   , SUM(  ';
                $reportSelectSql .= '     CASE  ';
                $reportSelectSql .= "       WHEN pu.code = '${code}'  ";
                $reportSelectSql .= '         THEN pu.viewing_seconds ::numeric / (  ';
                $reportSelectSql .= '         (  ';
                $reportSelectSql .= '           SELECT ';
                $reportSelectSql .= "             ${number}  ";
                $reportSelectSql .= '           FROM ';
                $reportSelectSql .= '             ts_numbers tsn  ';
                $reportSelectSql .= '           WHERE ';
                $reportSelectSql .= '             l.time_box_id = tsn.time_box_id ';
                $reportSelectSql .= '         ) * l.fraction ';
                $reportSelectSql .= '       )  ';
                $reportSelectSql .= '       END * 100 ';
                $reportSelectSql .= "   ) AS s_${code} ";
                $reportSelectSqlArr[] = $reportSelectSql;
            }
            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE gross_program_report_pivoted AS ';
            $sql .= ' SELECT ';
            $sql .= '   l.title ';
            $sql .= '   , l.prog_type ';
            $sql .= '   , l.code_name ';

            if (count($reportSelectSqlArr) > 0) {
                $sql .= implode('', $reportSelectSqlArr);
            }
            $sql .= ' FROM ';
            $sql .= '   list l ';

            if (count($reportSelectSqlArr) > 0) {
                $sql .= '   LEFT JOIN gross_pv_unioned pu ';
                $sql .= '     ON l.prog_id = pu.prog_id ';
                $sql .= '     AND l.time_box_id = pu.time_box_id ';
            }
            $sql .= ' GROUP BY ';
            $sql .= '   l.title ';
            $sql .= '   , l.code_name ';
            $sql .= '   , l.prog_type; ';
            $this->select($sql);
        }

        // -- ここから、番組期間平均の最頻値関連
        $sql = '';
        $sql .= ' CREATE TEMPORARY TABLE result AS WITH targeted AS (  ';
        $sql .= '   SELECT ';
        $sql .= '     title ';
        $sql .= '     , calc_start_time ';
        $sql .= '     , calc_end_time ';
        $sql .= '     , disp_start_time ';
        $sql .= '     , disp_end_time ';
        $sql .= '     , real_started_at ';
        $sql .= '     , min ';
        $sql .= '     , COUNT(*) OVER (  ';
        $sql .= '       PARTITION BY ';
        $sql .= '         title ';
        $sql .= '         , prog_type ';
        $sql .= '         , code_name  ';
        $sql .= '       ORDER BY ';
        $sql .= '         cnt DESC ';
        $sql .= '         , min ';
        $sql .= '         , disp_start_time ';
        $sql .= '         , disp_end_time DESC ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW ';
        $sql .= '     ) target ';
        $sql .= '     , dow ';
        $sql .= '     , dow_name ';
        $sql .= '     , code_name ';
        $sql .= '     , DENSE_RANK() OVER (  ';
        $sql .= '       PARTITION BY ';
        $sql .= '         title ';
        $sql .= '         , prog_type ';
        $sql .= '         , code_name  ';
        $sql .= '       ORDER BY ';
        $sql .= '         dow ';
        $sql .= '     ) dow_number ';
        $sql .= '     , prog_type ';
        $sql .= '     , fraction ';
        $sql .= '     , personal_viewing_rate ';
        $sql .= '     , household_viewing_rate ';
        $sql .= '     , ts_personal_viewing_rate ';
        $sql .= '     , ts_household_viewing_rate ';
        $sql .= '     , ts_personal_gross_viewing_rate ';
        $sql .= '     , ts_household_gross_viewing_rate  ';
        $sql .= '   FROM ';
        $sql .= '     list  ';
        $sql .= '     INNER JOIN day_of_week d  ';
        $sql .= '       ON d.odr = dow ';
        $sql .= ' )  ';
        $sql .= ' , result AS (  ';
        $sql .= '   SELECT ';
        $sql .= '     title ';
        $sql .= '     , MAX(CASE WHEN target = 1 THEN disp_start_time END) disp_start_time ';
        $sql .= '     , MAX(CASE WHEN target = 1 THEN disp_end_time END) disp_end_time ';
        $sql .= '     , MAX(CASE WHEN target = 1 THEN calc_start_time END) calc_start_time ';
        $sql .= '     , MAX(CASE WHEN target = 1 THEN calc_end_time END) calc_end_time ';
        $sql .= '     , MAX(CASE WHEN target = 1 THEN real_started_at END) real_started_at ';
        $sql .= '     , MAX(CASE WHEN target = 1 THEN min END) min ';
        $sql .= '     , LISTAGG(DISTINCT dow_name) WITHIN GROUP (ORDER BY dow) dows ';
        $sql .= '     , LISTAGG(DISTINCT dow) WITHIN GROUP (ORDER BY dow) dows_num ';
        $sql .= '     , COUNT(*) cnt ';
        $sql .= '     , code_name ';
        $sql .= '     , MAX(dow_number) dow_cnt ';
        $sql .= '     , TRUNC(  ';
        $sql .= '       SUM(  ';
        $sql .= '         EXTRACT(EPOCH FROM calc_end_time - calc_start_time) ';
        $sql .= '       ) / 60 ';
        $sql .= '     ) total_min ';
        $sql .= '     , prog_type ';
        $sql .= '     , SUM(fraction) as fraction ';
        // -- 加重平均
        $sql .= '     , SUM(personal_viewing_rate * fraction) AS personal_viewing_rate ';
        $sql .= '     , SUM(household_viewing_rate * fraction) AS household_viewing_rate ';
        $sql .= '     , SUM(ts_personal_viewing_rate * fraction) AS ts_personal_viewing_rate ';
        $sql .= '     , SUM(ts_household_viewing_rate * fraction) AS ts_household_viewing_rate ';
        $sql .= '     , SUM(ts_personal_gross_viewing_rate * fraction) AS gross_personal_viewing_rate ';
        $sql .= '     , SUM(ts_household_gross_viewing_rate * fraction) AS gross_household_viewing_rate ';
        // 単純平均
        $sql .= '     , SUM(personal_viewing_rate) AS s_personal_viewing_rate ';
        $sql .= '     , SUM(household_viewing_rate) AS s_household_viewing_rate ';
        $sql .= '     , SUM(ts_personal_viewing_rate) AS ts_s_personal_viewing_rate ';
        $sql .= '     , SUM(ts_household_viewing_rate) AS ts_s_household_viewing_rate ';
        $sql .= '     , SUM(ts_personal_gross_viewing_rate) AS gross_s_personal_viewing_rate ';
        $sql .= '     , SUM(ts_household_gross_viewing_rate) AS gross_s_household_viewing_rate  ';
        $sql .= '   FROM ';
        $sql .= '     targeted  ';
        $sql .= '   GROUP BY ';
        $sql .= '     title ';
        $sql .= '     , prog_type ';
        $sql .= '     , code_name ';
        $sql .= ' )  ';
        $sql .= ' SELECT ';
        $sql .= '   *  ';
        $sql .= ' FROM ';
        $sql .= '   result;  ';
        $this->select($sql);

        $sql = '';
        $sql .= ' SELECT ';
        $sql .= '   r.disp_start_time mode_start ';
        $sql .= '   , r.disp_end_time mode_end ';
        $sql .= '   , r.dows dow ';
        $sql .= '   , r.code_name ';
        $sql .= '   , r.title ';
        $sql .= '   , r.prog_type ';
        $sql .= '   , r.cnt number_of_program ';
        $sql .= '   , r.min mode_minute ';
        $sql .= '   , r.total_min mode_minute_total ';

        if ($dispAverage === 'weight') {
            // まとめて加重平均
            if ($isRt) {
                if ($hasPersonal) {
                    $sql .= '   , COALESCE(ROUND(r.personal_viewing_rate / fraction, 1), 0) rt_personal_viewing_rate ';
                }

                foreach ($divCodes as $i => $val) {
                    $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                    $name = $divisionKey . $val;

                    if ($isConditionCross && $val === 'condition_cross') {
                        $name = 'condition_cross';
                    }
                    $sql .= "   , COALESCE(ROUND(pr.${code} / fraction, 1), 0) rt_${name} ";
                }

                if ($hasHousehold) {
                    $sql .= '   , COALESCE(  ';
                    $sql .= '     ROUND(r.household_viewing_rate / fraction, 1) ';
                    $sql .= '     , 0 ';
                    $sql .= '   ) rt_household_viewing_rate ';
                }
            }

            if ($isTs) {
                if ($hasPersonal) {
                    $sql .= '   , COALESCE(ROUND(r.ts_personal_viewing_rate / fraction, 1), 0) ts_personal_viewing_rate ';
                }

                foreach ($divCodes as $i => $val) {
                    $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                    $name = $divisionKey . $val;

                    if ($isConditionCross && $val === 'condition_cross') {
                        $name = 'condition_cross';
                    }
                    $sql .= "   , COALESCE(ROUND(tpr.${code} / fraction, 1), 0) ts_${name} ";
                }

                if ($hasHousehold) {
                    $sql .= '   , COALESCE(  ';
                    $sql .= '     ROUND(r.ts_household_viewing_rate / fraction, 1) ';
                    $sql .= '     , 0 ';
                    $sql .= '   ) ts_household_viewing_rate ';
                }
            }

            if ($isGross) {
                if ($hasPersonal) {
                    $sql .= '   , COALESCE(ROUND(r.gross_personal_viewing_rate / fraction, 1), 0) gross_personal_viewing_rate ';
                }

                foreach ($divCodes as $i => $val) {
                    $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                    $name = $divisionKey . $val;

                    if ($isConditionCross && $val === 'condition_cross') {
                        $name = 'condition_cross';
                    }
                    $sql .= "   , COALESCE(ROUND(gpr.${code} / fraction, 1), 0) gross_${name} ";
                }

                if ($hasHousehold) {
                    $sql .= '   , COALESCE(  ';
                    $sql .= '     ROUND(r.gross_household_viewing_rate / fraction, 1) ';
                    $sql .= '     , 0 ';
                    $sql .= '   ) gross_household_viewing_rate ';
                }
            }
        } elseif ($dispAverage === 'simple') {
            // まとめて単純平均
            if ($isRt) {
                if ($hasPersonal) {
                    $sql .= '   , COALESCE(ROUND(r.s_personal_viewing_rate / cnt, 1), 0) rt_personal_viewing_rate ';
                }

                foreach ($divCodes as $i => $val) {
                    $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                    $name = $divisionKey . $val;

                    if ($isConditionCross && $val === 'condition_cross') {
                        $name = 'condition_cross';
                    }
                    $sql .= "   , COALESCE(ROUND(pr.s_${code} / cnt, 1), 0) rt_${name} ";
                }

                if ($hasHousehold) {
                    $sql .= '   , COALESCE(ROUND(r.s_household_viewing_rate / cnt, 1), 0) rt_household_viewing_rate ';
                }
            }

            if ($isTs) {
                if ($hasPersonal) {
                    $sql .= '   , COALESCE(ROUND(r.ts_s_personal_viewing_rate / cnt, 1), 0) ts_personal_viewing_rate ';
                }

                foreach ($divCodes as $i => $val) {
                    $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                    $name = $divisionKey . $val;

                    if ($isConditionCross && $val === 'condition_cross') {
                        $name = 'condition_cross';
                    }
                    $sql .= "   , COALESCE(ROUND(tpr.s_${code} / cnt, 1), 0) ts_${name} ";
                }

                if ($hasHousehold) {
                    $sql .= '   , COALESCE(  ';
                    $sql .= '     ROUND(r.ts_s_household_viewing_rate / cnt, 1) ';
                    $sql .= '     , 0 ';
                    $sql .= '   ) ts_household_viewing_rate ';
                }
            }

            if ($isGross) {
                if ($hasPersonal) {
                    $sql .= '   , COALESCE(ROUND(r.gross_s_personal_viewing_rate / cnt, 1), 0) gross_personal_viewing_rate ';
                }

                foreach ($divCodes as $i => $val) {
                    $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                    $name = $divisionKey . $val;

                    if ($isConditionCross && $val === 'condition_cross') {
                        $name = 'condition_cross';
                    }
                    $sql .= "   , COALESCE(ROUND(gpr.s_${code} / cnt, 1), 0) gross_${name} ";
                }

                if ($hasHousehold) {
                    $sql .= '   , COALESCE(  ';
                    $sql .= '     ROUND(r.gross_s_household_viewing_rate / cnt, 1) ';
                    $sql .= '     , 0 ';
                    $sql .= '   ) gross_household_viewing_rate ';
                }
            }
        }

        $sql .= ' FROM ';
        $sql .= '   result r  ';

        if ($isRt) {
            $sql .= '   LEFT JOIN program_report_pivoted pr  ';
            $sql .= '     ON r.title = pr.title  ';
            $sql .= '     AND r.code_name = pr.code_name  ';
            $sql .= '     AND r.prog_type = pr.prog_type  ';
        }

        if ($isTs) {
            $sql .= '   LEFT JOIN ts_program_report_pivoted tpr  ';
            $sql .= '     ON r.title = tpr.title  ';
            $sql .= '     AND r.code_name = tpr.code_name  ';
            $sql .= '     AND r.prog_type = tpr.prog_type  ';
        }

        if ($isGross) {
            $sql .= '   LEFT JOIN gross_program_report_pivoted gpr  ';
            $sql .= '     ON r.title = gpr.title  ';
            $sql .= '     AND r.code_name = gpr.code_name  ';
            $sql .= '     AND r.prog_type = gpr.prog_type  ';
        }
        $sql .= ' ORDER BY ';
        $sql .= '   r.code_name ';
        $sql .= '   , CASE  ';
        $sql .= "     WHEN r.prog_type = 'レギュラー'  ";
        $sql .= '       THEN 1  ';
        $sql .= "     WHEN r.prog_type = 'スペシャル'  ";
        $sql .= '       THEN 2  ';
        $sql .= "     WHEN r.prog_type = 'ミニ番'  ";
        $sql .= '       THEN 3  ';
        $sql .= "     WHEN r.prog_type = '再放送'  ";
        $sql .= '       THEN 4  ';
        $sql .= "     WHEN r.prog_type = '番宣'  ";
        $sql .= '       THEN 5  ';
        $sql .= '     END ';
        $sql .= '   , CASE  ';
        $sql .= '     WHEN r.dow_cnt = 1  ';
        $sql .= '       THEN 0  ';
        $sql .= '     ELSE 1  ';
        $sql .= '     END ';
        $sql .= '   , SUBSTRING(r.dows_num, 1, 1) ';
        $sql .= "   , CASE WHEN split_part(disp_start_time,':',1)::numeric >= 24 THEN '0' || split_part(disp_start_time,':',1)::numeric - 24 || ':' ||  split_part(disp_start_time,':',2) || ':' || split_part(disp_start_time,':',3) ELSE disp_start_time END ";
        $sql .= '   , r.title  ';

        if ($csvFlag == '0') {
            $sql .= " OFFSET ${offset} LIMIT ${length} ";
        }

        $results = $this->select($sql);

        return [
            'list' => $results,
            'cnt' => $count,
        ];
    }

    /**
     * 番組表.
     * @param string $startDateTime
     * @param string $endDateTime
     * @param string $startTime
     * @param string $endTime
     * @param null|array $channels
     * @param string $division
     * @param null|array $conditionCross
     * @param null|array $codes
     * @param bool $bsFlg
     * @param int $regionId
     * @return array
     */
    public function table(string $startDateTime, string $endDateTime, string $startTime, string $endTime, ?array $channels, string $division, ?array $conditionCross, ?array $codes, bool $bsFlg, int $regionId): array
    {
        $digit = 1;

        if ($bsFlg) {
            $digit = 2;
        }

        $bindings = [];

        // 地域コード
        $bindings[':region_id'] = $regionId;
        $bindings[':digit'] = $digit;

        // コード種別の頭部分
        $divisionKey = "{$division}_";

        // 個人指定フラグ
        $personalFlg = false;
        // 世帯指定フラグ
        $householdFlg = false;

        // 個人が指定されている場合
        $personalIndex = array_search('personal', $codes);

        if ($personalIndex !== false) {
            $personalFlg = true;
            unset($codes[$personalIndex]);
        }

        // 世帯が指定されている場合
        $householdIndex = array_search('household', $codes);

        if ($householdIndex !== false) {
            $householdFlg = true;
            unset($codes[$householdIndex]);
        }

        // コード
        $codeBind = $this->createArrayBindParam($divisionKey, [
            $divisionKey => $codes,
        ], $bindings);

        // BS指定時はテーブル名の頭にBSを付ける
        $bsKey = '';

        if ($bsFlg) {
            $bsKey = 'bs_';
        }

        $withName = 'program_data';

        $with = '';
        $with .= 'WITH ' . $withName . ' AS ';
        $with .= ' ( ';
        $with .= '  SELECT ';
        $with .= '   p.date, ';
        $with .= '   p.real_started_at, ';
        $with .= '   p.real_ended_at, ';
        $with .= '   p.channel_id, ';
        $with .= "   to_char(p.real_started_at - interval '5 hours', 'HH24MISS') AS shift_start_time, ";
        $with .= "   to_char(p.real_ended_at - interval '5 hours', 'HH24MISS') AS shift_end_time, ";
        $with .= '   c.code_name AS channel_code_name, ';
        $with .= '   p.title, ';

        if ($personalFlg) {
            $with .= '   p.personal_viewing_rate, ';
            $with .= '   pmr.viewing_rate AS personald_end_viewing_rate, ';
        }

        foreach ($codeBind as $val) {
            $name = $divisionKey . $codes[preg_replace('!:' . $divisionKey . '!', '', $val, 1)];
            $with .= "   pr.viewing_rate AS ${name}, ";
            $with .= "   pr.end_viewing_rate AS ${name}_end, ";
        }

        if ($householdFlg) {
            $with .= '   p.household_viewing_rate, ';
            $with .= '   p.household_end_viewing_rate, ';
        }

        $with .= '   p.prog_id, ';
        $with .= '   p.genre_id, ';
        $with .= '   p.time_box_id, ';
        $with .= '   p.prepared, ';
        $with .= '   greatest(p.real_started_at, tb.started_at) as tb_start_time, ';
        $with .= '   least(p.real_ended_at, tb.ended_at) as tb_end_time ';
        $with .= '  FROM ';
        $with .= '   ' . $bsKey . 'programs p ';
        $with .= '  INNER JOIN ';
        $with .= '   time_boxes tb ';
        $with .= '    ON ';
        $with .= '     tb.id = p.time_box_id ';
        $with .= '     AND tb.region_id = :region_id ';
        $with .= '  LEFT JOIN ';
        $with .= '   channels c ';
        $with .= '    ON ';
        $with .= '     c.id = p.channel_id ';

        if ($bsFlg) {
            $with .= "     AND c.type = 'bs' ";
        } else {
            $with .= "     AND c.type = 'dt' ";
        }
        $with .= '  LEFT JOIN ';

        $with .= '   ' . $bsKey . 'program_reports pr ';
        $with .= '    ON ';
        $with .= '     p.prog_id = pr.prog_id ';
        $with .= '     AND p.time_box_id = pr.time_box_id ';
        // サンプル（属性）
        if (isset($division)) {
            $bindings[':division'] = $division;
            $with .= '     AND pr.division = :division ';
        }
        // サンプル（コード）
        if (!empty($codeBind)) {
            $with .= '  AND pr.code IN (' . implode(',', $codeBind) . ') ';
        }

        if ($personalFlg) {
            $bindings[':dwhStartDateTime'] = (new Carbon($startDateTime))->subDay(1);
            $bindings[':dwhEndDateTime'] = (new Carbon($endDateTime))->addDay(2);

            $with .= '   LEFT JOIN per_minute_reports pmr ';
            $with .= '    ON ';
            $with .= '     pmr.time_box_id = p.time_box_id ';
            $with .= "     AND pmr.datetime = p.real_ended_at - interval '1 minute' ";
            $with .= '     AND pmr.channel_id = p.channel_id ';
            $with .= "     AND pmr.division = 'personal' ";
            $with .= '     AND pmr.datetime BETWEEN :dwhStartDateTime AND :dwhEndDateTime ';
        }
        $with .= '  WHERE ';
        $with .= '   p.prepared = 1 ';

        // 日時
        if (isset($startDateTime, $endDateTime)) {
            $bindings[':startDateTime'] = $startDateTime;
            $bindings[':endDateTime'] = $endDateTime;

            $with .= '   AND p.real_started_at <= :endDateTime ';
            $with .= '   AND p.real_ended_at >= :startDateTime ';
        }

        // 放送
        if (!empty($channels)) {
            $channelsBind = $this->createArrayBindParam('channels', [
                'channels' => $channels,
            ], $bindings);

            $with .= '   AND p.channel_id IN (' . implode(',', $channelsBind) . ') ';
        }

        $with .= ' ), ';

        $with .= ' program_grouped AS ';
        $with .= ' ( ';
        $with .= '  SELECT ';
        $with .= '   p.date as org_date, ';
        $with .= '   p.real_started_at, ';
        $with .= '   p.real_ended_at, ';
        $with .= '   p.channel_id, ';
        $with .= "   DATE_PART('dow' ,p.date) d, ";
        $with .= "   CASE WHEN to_number(to_char(p.real_started_at, 'HH24'), '999999') < 5 ";
        $with .= "    THEN to_number(to_char(p.real_started_at, 'HH24'), '999999') + 24 || to_char(p.real_started_at, ':MI') ";
        $with .= "    ELSE to_char(p.real_started_at, 'HH24:MI') ";
        $with .= '   END AS from_hh_mm, ';
        $with .= "   CASE WHEN to_number(to_char(p.real_ended_at, 'HH24'), '999999') < 5 ";
        $with .= "    THEN to_number(to_char(p.real_ended_at, 'HH24'), '999999') + 24 || to_char(p.real_ended_at, ':MI') ";
        $with .= "    ELSE to_char(p.real_ended_at, 'HH24:MI') ";
        $with .= '   END AS to_hh_mm, ';
        $with .= '   p.channel_code_name, ';
        $with .= '   p.title, ';

        if ($personalFlg) {
            $with .= '   COALESCE(ROUND(p.personal_viewing_rate,:digit), 0) AS rate, ';
            $with .= '   COALESCE(ROUND(p.personald_end_viewing_rate, :digit), 0) AS end_rate, ';
        }

        foreach ($codeBind as $val) {
            $name = $divisionKey . $codes[preg_replace('!:' . $divisionKey . '!', '', $val, 1)];
            $with .= "   COALESCE(ROUND(MAX(p.${name}),:digit), 0) AS rate, ";
            $with .= "   COALESCE(ROUND(MAX(p.${name}_end),:digit), 0) AS end_rate, ";
        }

        if ($householdFlg) {
            $with .= '   COALESCE(ROUND(p.household_viewing_rate,:digit), 0) AS rate, ';
            $with .= '   COALESCE(ROUND(p.household_end_viewing_rate,:digit), 0) AS end_rate, ';
        }

        $with .= '   p.prog_id, ';
        $with .= '   p.genre_id, ';
        $with .= '   p.time_box_id, ';
        $with .= '   p.prepared, ';
        $with .= '   p.tb_start_time, ';
        $with .= '   p.tb_end_time ';
        $with .= '  FROM ';
        $with .= '   ' . $withName . ' p ';

        $with .= ' WHERE ';
        $with .= '   p.tb_start_time < :endDateTime';
        $with .= '   AND p.tb_end_time > :startDateTime ';
        // 時間帯
        if (isset($startTime, $endTime)) {
            // startTime, endTime is subed 5 hour
            if (!($startTime === '000000' && $endTime === '235959')) {
                $bindings[':startTime'] = $startTime;
                $bindings[':endTime'] = $endTime;
                $with .= '  AND ( ';
                $with .= '   p.shift_end_time <  p.shift_start_time ';
                $with .= '   AND ( ';
                $with .= '    p.shift_start_time <= :endTime ';
                $with .= '    OR p.shift_end_time > :startTime ';
                $with .= '   ) ';
                $with .= '  ) ';
                $with .= '  OR ( ';
                $with .= '   p.shift_end_time >= p.shift_start_time ';
                $with .= '   AND p.shift_start_time <= :endTime ';
                $with .= '   AND p.shift_end_time > :startTime ';
                $with .= '  ) ';
            }
        }

        $with .= '  GROUP BY ';
        $with .= '   p.date, ';
        $with .= '   p.real_started_at, ';
        $with .= '   p.real_ended_at, ';
        $with .= '   p.channel_code_name, ';
        $with .= '   p.channel_id, ';
        $with .= '   p.title, ';

        if ($personalFlg) {
            $with .= '   p.personal_viewing_rate, ';
            $with .= '   p.personald_end_viewing_rate, ';
        }

        if ($householdFlg) {
            $with .= '   p.household_viewing_rate, ';
            $with .= '   p.household_end_viewing_rate, ';
        }

        $with .= '   p.prog_id, ';
        $with .= '   p.genre_id, ';
        $with .= '   p.time_box_id, ';
        $with .= '   p.prepared, ';
        $with .= '   p.tb_start_time, ';
        $with .= '   p.tb_end_time ';

        $cEndDateTime = new Carbon($endDateTime);
        $cStartDateTime = new Carbon($startDateTime);

        if ($cEndDateTime->hour < $cStartDateTime->hour) {
            $cStartDateTime->addDay();
        }
        $toBoundary = $cStartDateTime->hour($cEndDateTime->hour)->minute($cEndDateTime->minute)->second($cEndDateTime->second);
        $bindings['toBoundary'] = $toBoundary;
        $with .= ' ), master AS ( ';
        $with .= ' SELECT ';
        $with .= "     startend.start + nums.num * interval  '1 day' as date ";
        $with .= "     , startend.start + nums.num * interval  '1 day' as start ";
        $with .= "     , startend.end + nums.num * interval  '1 day' as end ";
        $with .= '     , ch.id as channel_id  ';
        $with .= ' FROM ';
        $with .= '     (SELECT 0 as num UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 ) nums ';
        $with .= '     CROSS JOIN ';
        $with .= '     (SELECT :startDateTime::timestamp as start, :toBoundary::timestamp as end) startend ';
        $with .= '     CROSS JOIN ';
        $strArr = [];

        foreach ($channelsBind as $val) {
            $strArr[] = 'SELECT ' . $val . ' as id ';
        }
        $with .= '     (' . implode(' UNION ', $strArr) . ') ch ';
        $with .= '     WHERE startend.start < :endDateTime AND startend.end > :startDateTime ';
        $with .= '           AND ch.id IN (' . implode(',', $channelsBind) . ') ';
        $with .= '), with_master AS ( ';
        $with .= ' SELECT  ';
        $with .= '    p.* ';
        $with .= "    , TO_CHAR(m.date - interval '5hours' , 'YYYY/MM/DD') as date  ";
        $with .= ' FROM  ';
        $with .= '    program_grouped p  ';
        $with .= ' INNER JOIN  ';
        $with .= '    master m  ';
        $with .= ' ON  ';
        $with .= '   p.real_started_at < m.end  ';
        $with .= '   AND p.real_ended_at > m.start  ';
        $with .= '   AND p.channel_id = m.channel_id  ';
        $with .= ' )';

        $select = '*';

        $orderBy = '';
        $orderBy .= 'p.channel_code_name asc ';
        $orderBy .= ', p.date asc ';
        $orderBy .= ', p.tb_start_time asc ';

        $query = sprintf('%s SELECT %s FROM with_master p ORDER BY %s;', $with, $select, $orderBy);
        $records = $this->select($query, $bindings);

        return [
            'list' => $records,
        ];
    }

    /**
     * 番組表.（拡張、オリジナル）.
     * @param string $startDateTime
     * @param string $endDateTime
     * @param string $startTime
     * @param string $endTime
     * @param null|array $channels
     * @param string $division
     * @param null|array $conditionCross
     * @param null|array $codes
     * @param bool $bsFlg
     * @param int $regionId
     * @return array
     */
    public function tableOriginal(string $startDateTime, string $endDateTime, string $startTime, string $endTime, ?array $channels, string $division, ?array $conditionCross, ?array $codes, bool $bsFlg, int $regionId): array
    {
        $digit = 1;

        if ($bsFlg) {
            $digit = 2;
        }

        $bindings = [];

        // 地域コード
        $bindings[':region_id'] = $regionId;
        $bindings[':digit'] = $digit;

        // コード種別の頭部分
        $divisionKey = "{$division}_";

        $isConditionCross = $division == 'condition_cross';

        // BS指定時はテーブル名の頭にBSを付ける
        $bsKey = '';

        if ($bsFlg) {
            $bsKey = 'bs_';
        }

        $with = '';
        $with .= 'WITH program_data AS ';
        $with .= ' ( ';
        $with .= '  SELECT ';
        $with .= '   p.date, ';
        $with .= '   p.real_started_at, ';
        $with .= '   p.real_ended_at, ';
        $with .= "   to_char(p.real_started_at - interval '5 hours', 'HH24MISS') AS shift_start_time, ";
        $with .= "   to_char(p.real_ended_at - interval '5 hours', 'HH24MISS') AS shift_end_time, ";
        $with .= '   c.code_name channel_code_name, ';
        $with .= '   p.channel_id, ';
        $with .= '   p.title, ';
        $with .= '   p.prog_id, ';
        $with .= '   p.genre_id, ';
        $with .= '   p.time_box_id, ';
        $with .= '   p.prepared, ';
        $with .= '   greatest(p.real_started_at, tb.started_at) as tb_start_time, ';
        $with .= '   least(p.real_ended_at, tb.ended_at) as tb_end_time ';
        $with .= '  FROM ';
        $with .= '   ' . $bsKey . 'programs p ';
        $with .= '  INNER JOIN ';
        $with .= '     time_boxes tb ';
        $with .= '  ON ';
        $with .= '     tb.id = p.time_box_id ';
        $with .= '     AND tb.region_id = :region_id ';
        $with .= '  INNER JOIN ';
        $with .= '     channels c ';
        $with .= '  ON ';
        $with .= '     c.id = p.channel_id ';

        if ($bsFlg) {
            $with .= "     AND c.type = 'bs' ";
        } else {
            $with .= "     AND c.type = 'dt' ";
        }
        $with .= '  WHERE ';
        $with .= '   p.prepared = 1 ';

        // 日時
        if (isset($startDateTime, $endDateTime)) {
            $bindings[':startDateTime'] = $startDateTime;
            $bindings[':endDateTime'] = $endDateTime;

            $with .= '   AND p.real_started_at <= :endDateTime ';
            $with .= '   AND p.real_ended_at >= :startDateTime ';
        }

        // 放送
        $channelsBind = $this->createArrayBindParam('channels', [
            'channels' => $channels,
        ], $bindings);

        $with .= '   AND p.channel_id IN (' . implode(',', $channelsBind) . ') ';

        $with .= '   AND EXISTS ';
        $with .= '    ( ';
        $with .= '     SELECT 1 ';
        $with .= '     FROM time_boxes tb ';
        $with .= '     WHERE ';
        $with .= '      tb.id = p.time_box_id ';
        $with .= '      AND tb.region_id = :region_id ';
        $with .= '    ) ';

        $with .= ' ), samples AS ( ';
        $with .= ' SELECT ';
        $with .= '   tbp.paneler_id, ';
        $with .= '   tbp.time_box_id, ';
        $with .= '   codes.code, ';
        $with .= '   COUNT(tbp.paneler_id) OVER (PARTITION BY codes.code, tbp.time_box_id ) number ';
        $with .= ' FROM ';
        $with .= '   time_box_panelers tbp ';

        $with .= ' CROSS JOIN ';

        if ($isConditionCross) {
            $bindings[':condition_cross_code'] = 'condition_cross';
            $with .= ' ( ';
            $with .= ' (SELECT :condition_cross_code ::varchar(255) as code) ';
            $with .= ' ) codes ';
        } else {
            $tmpArr = [];

            foreach ($codes as $code) {
                $key = ':union_' . $divisionKey . $code;
                $bindings[$key] = $code;
                $tmpSql = ' ( ';
                $tmpSql .= ' SELECT ' . $key . '::varchar(255) as code ';
                $tmpSql .= ' ) ';
                array_push($tmpArr, $tmpSql);
            }
            $with .= ' ( ' . implode(' UNION ', $tmpArr) . ' ) AS codes ';
        }
        $with .= ' WHERE   ';

        if ($isConditionCross) {
            $with .= " codes.code = 'condition_cross' ";
            $with .= $this->createConditionCrossSql($conditionCross, $bindings);
        } else {
            $with .= $this->createCrossJoinWhereClause($division, $codes, $bindings);
        }
        $with .= ' ), w_program_reports AS (  ';
        $with .= ' SELECT';
        $with .= '   pv.prog_id ';
        $with .= '   , pv.time_box_id ';
        $with .= '   , s.code code ';
        $with .= '   , SUM(pv.viewing_seconds)::numeric viewing_seconds ';
        $with .= '   , s.number number ';
        $with .= ' FROM ';
        $with .= '   ' . $bsKey . 'program_viewers pv ';
        $with .= ' INNER JOIN ';
        $with .= '   samples s ';
        $with .= ' ON ';
        $with .= '   pv.time_box_id = s.time_box_id  ';
        $with .= '   AND pv.paneler_id = s.paneler_id ';
        $with .= ' GROUP BY ';
        $with .= '   pv.prog_id, ';
        $with .= '   pv.time_box_id, ';
        $with .= '   s.code, ';
        $with .= '   s.number ';

        $with .= ' ), vertical AS( ';
        $with .= ' SELECT  ';
        $with .= '   p.date as org_date, ';
        $with .= '   p.channel_id, ';
        $with .= '   p.real_started_at, ';
        $with .= '   p.real_ended_at, ';
        $with .= '   p.shift_start_time, ';
        $with .= '   p.shift_end_time, ';
        $with .= '   p.channel_code_name, ';
        $with .= '   p.title, ';

        if ($isConditionCross) {
            $with .= '   COALESCE(ROUND(CASE WHEN pr.code = :condition_cross_code THEN pr.viewing_seconds END / ( EXTRACT(EPOCH FROM (p.tb_end_time - p.tb_start_time )) * pr.number) * 100, :digit),0) AS rate, ';
            $with .= "   '----'::varchar(255) AS end_rate, ";
        } else {
            foreach ($codes as $key => $val) {
                $key = ':vertical_' . $divisionKey . $code;
                $bindings[$key] = $code;
                $with .= "   COALESCE(ROUND(CASE WHEN pr.code = ${key} THEN pr.viewing_seconds END / ( EXTRACT(EPOCH FROM (p.tb_end_time - p.tb_start_time )) * pr.number) * 100, :digit),0) AS rate, ";
                $with .= "   '----'::varchar(255) AS end_rate, ";
            }
        }

        $with .= '   p.prog_id ';
        $with .= '   ,p.genre_id ';
        $with .= '   ,p.time_box_id ';
        $with .= '   ,p.prepared ';
        $with .= '   ,p.tb_start_time ';
        $with .= '   ,p.tb_end_time ';
        $with .= '   ,pr.number ';
        $with .= ' FROM ';
        $with .= '   program_data p ';
        $with .= ' LEFT JOIN ';
        $with .= '   w_program_reports pr ';
        $with .= ' ON ';
        $with .= '   p.prog_id = pr.prog_id AND ';
        $with .= '   p.time_box_id = pr.time_box_id ';
        $with .= ' ), horizontal AS( ';
        $with .= ' SELECT ';
        $with .= '  org_date, ';
        $with .= '  p.real_started_at, ';
        $with .= '   p.channel_id, ';
        $with .= '  p.real_ended_at, ';
        $with .= "   CASE WHEN to_number(to_char(p.real_started_at, 'HH24'), '999999') < 5 ";
        $with .= "    THEN to_number(to_char(p.real_started_at, 'HH24'), '999999') + 24 || to_char(p.real_started_at, ':MI') ";
        $with .= "    ELSE to_char(p.real_started_at, 'HH24:MI') ";
        $with .= '   END AS from_hh_mm, ';
        $with .= "   CASE WHEN to_number(to_char(p.real_ended_at, 'HH24'), '999999') < 5 ";
        $with .= "    THEN to_number(to_char(p.real_ended_at, 'HH24'), '999999') + 24 || to_char(p.real_ended_at, ':MI') ";
        $with .= "    ELSE to_char(p.real_ended_at, 'HH24:MI') ";
        $with .= '   END AS to_hh_mm, ';
        $with .= '  p.channel_code_name, ';
        $with .= '  p.title, ';
        $with .= '  p.rate, ';
        $with .= '  p.end_rate, ';
        $with .= '  p.prog_id, ';
        $with .= '  p.genre_id, ';
        $with .= '  p.time_box_id, ';
        $with .= '  p.prepared, ';
        $with .= '  p.tb_start_time, ';
        $with .= '  p.tb_end_time ';
        $with .= ' FROM ';
        $with .= '   vertical p ';
        $with .= ' WHERE ';
        $with .= '   p.tb_start_time < :endDateTime';
        $with .= '   AND p.tb_end_time > :startDateTime ';

        // 時間帯
        if (isset($startTime, $endTime)) {
            if (!($startTime === '000000' && $endTime === '235959')) {
                $bindings[':startTime'] = $startTime;
                $bindings[':endTime'] = $endTime;

                $with .= ' AND ';
                $with .= '  ( ';
                $with .= '   p.shift_end_time <  p.shift_start_time ';
                $with .= '   AND ( ';
                $with .= '    p.shift_start_time <= :endTime ';
                $with .= '    OR p.shift_end_time > :startTime ';
                $with .= '   ) ';
                $with .= '  ) ';
                $with .= '  OR ( ';
                $with .= '   p.shift_end_time >= p.shift_start_time ';
                $with .= '   AND p.shift_start_time <= :endTime ';
                $with .= '   AND p.shift_end_time > :startTime ';
                $with .= '  ) ';
            }
        }

        $with .= ' GROUP BY ';
        $with .= '   p.org_date, ';
        $with .= '   p.channel_id, ';
        $with .= '   p.real_started_at, ';
        $with .= '   p.real_ended_at, ';
        $with .= '   p.channel_code_name, ';
        $with .= '   p.title, ';
        $with .= '   p.prog_id, ';
        $with .= '   p.genre_id, ';
        $with .= '   p.time_box_id, ';
        $with .= '   p.prepared, ';
        $with .= '   p.tb_start_time, ';
        $with .= '   p.tb_end_time, ';
        $with .= '   p.rate, ';
        $with .= '   p.end_rate ';

        $cEndDateTime = new Carbon($endDateTime);
        $cStartDateTime = new Carbon($startDateTime);

        if ($cEndDateTime->hour < $cStartDateTime->hour) {
            $cStartDateTime->addDay();
        }
        $toBoundary = $cStartDateTime->hour($cEndDateTime->hour)->minute($cEndDateTime->minute)->second($cEndDateTime->second);
        $tempBinds = [];
        $tempChanels = $this->createArrayBindParam('channels', [
            'channels' => $channels,
        ], $tempBinds);
        $tempBinds[':toBoundary'] = $toBoundary;
        $tempBinds[':startDateTime'] = $startDateTime;
        $tempBinds[':endDateTime'] = $endDateTime;

        $temp = ' CREATE TEMPORARY TABLE master AS ';
        $temp .= ' SELECT ';
        $temp .= "     startend.start + nums.num * interval  '1 day' as date ";
        $temp .= "     , startend.start + nums.num * interval  '1 day' as start ";
        $temp .= "     , startend.end + nums.num * interval  '1 day' as end ";
        $temp .= '     , ch.id as channel_id  ';
        $temp .= ' FROM ';
        $temp .= '     (SELECT 0 as num UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 ) nums ';
        $temp .= '     CROSS JOIN ';
        $temp .= '     (SELECT :startDateTime::timestamp as start, :toBoundary::timestamp as end) startend ';
        $temp .= '     CROSS JOIN ';
        $strArr = [];

        foreach ($tempChanels as $val) {
            $strArr[] = 'SELECT ' . $val . '::numeric as id ';
        }
        $temp .= '     (' . implode(' UNION ', $strArr) . ') ch ';
        $temp .= '     WHERE startend.start < :endDateTime::timestamp AND startend.end > :startDateTime::timestamp ';
        $temp .= '           AND ch.id IN (' . implode(',', $tempChanels) . ') ';
        $this->select($temp, $tempBinds);

        $with .= '), with_master AS ( ';
        $with .= ' SELECT  ';
        $with .= '    p.* ';
        $with .= "    , TO_CHAR(m.date - interval '5hours' , 'YYYY/MM/DD') as date  ";
        $with .= ' FROM  ';
        $with .= '    horizontal p  ';
        $with .= ' INNER JOIN  ';
        $with .= '    master m  ';
        $with .= ' ON  ';
        $with .= '   p.real_started_at < m.end  ';
        $with .= '   AND p.real_ended_at > m.start  ';
        $with .= '   AND p.channel_id = m.channel_id  ';
        $with .= ' )';

        $select = '*';

        $orderBy = '';
        $orderBy .= 'p.channel_code_name asc ';
        $orderBy .= ', p.date asc ';
        $orderBy .= ', p.tb_start_time asc ';

        $query = sprintf('%s SELECT %s FROM with_master p ORDER BY %s;', $with, $select, $orderBy);
        $records = $this->select($query, $bindings);

        return [
            'list' => $records,
        ];
    }

    /**
     * 番組情報取得.
     *
     * prog_idに一致する番組情報を取得する。
     * @param string $progId
     * @param string $timeBoxId
     */
    public function findProgram(String $progId, String $timeBoxId): ?stdClass
    {
        $bindings = [];
        $bindings[':prog_id'] = $progId;
        $bindings[':time_box_id'] = $timeBoxId;

        $select = '';
        $select .= 'p.channel_id, ';
        $select .= 'p.title, ';
        $select .= 'p.date, ';
        $select .= 'p.real_started_at, ';
        $select .= 'p.real_ended_at, ';
        $select .= 'p.personal_viewing_rate, ';
        $select .= 'p.household_viewing_rate, ';
        $select .= 'p.household_end_viewing_rate, ';
        $select .= 'p.time_box_id, ';
        $select .= 'p.prepared, ';
        $select .= 'c.display_name AS channel_name, ';
        $select .= 'pmr.viewing_rate AS personald_end_viewing_rate ';

        $from = '';
        $from .= 'programs p ';
        $from .= ' LEFT JOIN ';
        $from .= '  channels c ';
        $from .= '   ON p.channel_id = c.id';
        $from .= '  LEFT JOIN per_minute_reports pmr ';
        $from .= '   ON ';
        $from .= '    pmr.time_box_id = :time_box_id ';
        $from .= "     AND pmr.datetime = p.real_ended_at - interval '1 minute' ";
        $from .= '     AND pmr.channel_id = p.channel_id ';
        $from .= "     AND pmr.division = 'personal' ";

        $where = '';
        $where .= 'p.prog_id = :prog_id AND p.time_box_id = :time_box_id ';
        $query = sprintf('SELECT %s FROM %s WHERE %s;', $select, $from, $where);

        return $this->selectOne($query, $bindings);
    }

    public function getLatestObiProgramsDate(): stdClass
    {
        $query = '';
        $query .= ' SELECT ';
        $query .= "   DATE_TRUNC('day', p.started_at - interval '5 hours')::date date ";
        $query .= ' FROM ';
        $query .= '   programs p ';
        $query .= ' WHERE ';
        $query .= '   p.prepared = 1 AND ';
        $query .= '   EXISTS (SELECT * FROM obi_programs op WHERE p.prog_id = op.prog_id ) ';
        $query .= ' ORDER BY ';
        $query .= '   p.started_at DESC ';
        $query .= ' LIMIT ';
        $query .= '   1; ';

        return $this->selectOne($query);
    }

    public function createEnqMultiChannelProfileTables(): void
    {
        $query = '';
        $query .= ' CREATE TEMPORARY TABLE converted_enq_genres AS ';
        $query .= '   SELECT ';
        $query .= '     id, ';
        $query .= "     lpad(ROW_NUMBER() over (order by display_order), 2, 0) || '.' || genre AS genre ";
        $query .= '   FROM ';
        $query .= '     enq_question_genres eqg ';
        $query .= '   WHERE ';
        $query .= '     EXISTS(SELECT 1 FROM enq_questions eq where eq.genre_id = eqg.id) ';
        $this->select($query);

        // start 各アンケート選択肢の回答者パネラーIDリスト
        $query = '';
        $query .= ' CREATE TEMPORARY TABLE enq_question_panelers AS ';
        $query .= '   SELECT ';
        $query .= '     eq.id, ';
        $query .= '     q_no, ';
        $query .= '     item, ';
        $query .= '     genre, ';
        $query .= '     question, ';
        $query .= '     option, ';
        $query .= '     paneler_id ';
        $query .= '   FROM ';
        $query .= '     enq_questions eq ';
        $query .= '     INNER JOIN ';
        $query .= '       converted_enq_genres ceg ';
        $query .= '       ON ceg.id = eq.genre_id ';
        $query .= '     INNER JOIN enq_answers ea ';
        $query .= '       ON eq.answer_column = ea.answer_column AND ';
        $query .= "       ((eq.a_type = 'MA' AND ea.answer != 0) OR (eq.a_type = 'SA' AND ea.answer = eq.option_no)); ";
        $this->select($query);
        // end 各アンケート選択肢の回答者パネラーIDリスト

        $query = '';
        $query .= ' CREATE TEMPORARY TABLE group_enq_questions AS ';
        $query .= '   SELECT ';
        $query .= '     eq.id, ';
        $query .= '     MIN(eq.id) OVER (PARTITION BY q_no, item, question) + DENSE_RANK() over (PARTITION BY q_no, item, question ORDER BY eqgr.group_id) - 1 AS new_enq_id, ';
        $query .= '     eq.genre, ';
        $query .= '     eq.question, ';
        $query .= '     eqg.option, ';
        $query .= '     eq.paneler_id ';
        $query .= '   FROM ';
        $query .= '     enq_question_panelers eq ';
        $query .= '   INNER JOIN enq_question_group_relations eqgr ';
        $query .= '     ON eq.id = eqgr.enq_id ';
        $query .= '   INNER JOIN enq_question_groups eqg ';
        $query .= '     ON eqgr.group_id = eqg.id; ';
        $this->select($query);

        $query = '';
        $query .= ' CREATE TEMPORARY TABLE new_enq_questions AS ';
        $query .= '   SELECT ';
        $query .= '     * ';
        $query .= '   FROM ( ';
        $query .= '     SELECT ';
        $query .= '       id, ';
        $query .= '       genre, ';
        $query .= '       question, ';
        $query .= '       option ';
        $query .= '     FROM ';
        $query .= '       enq_question_panelers eqp ';
        $query .= '     WHERE ';
        $query .= '       NOT EXISTS(SELECT 1 FROM group_enq_questions geq WHERE eqp.id = geq.id) ';
        $query .= '     GROUP BY ';
        $query .= '       id, ';
        $query .= '       genre, ';
        $query .= '       question, ';
        $query .= '       option ';
        $query .= '   ) UNION ALL ( ';
        $query .= '     SELECT ';
        $query .= '       new_enq_id AS id, ';
        $query .= '       genre, ';
        $query .= '       question, ';
        $query .= '       option ';
        $query .= '     FROM ';
        $query .= '       group_enq_questions ';
        $query .= '     GROUP BY ';
        $query .= '       new_enq_id, ';
        $query .= '       genre, ';
        $query .= '       question, ';
        $query .= '       option ';
        $query .= '   ); ';
        $this->select($query);

        $query = '';
        $query .= ' CREATE TEMPORARY TABLE convert_enq AS ';
        $query .= '   SELECT ';
        $query .= '     id enq_id, ';
        $query .= '     paneler_id ';
        $query .= '   FROM ';
        $query .= '     enq_question_panelers eqp ';
        $query .= '   WHERE ';
        $query .= '     NOT EXISTS(SELECT 1 FROM group_enq_questions geq WHERE eqp.id = geq.id) ';
        $query .= '   UNION ALL ';
        $query .= '   SELECT ';
        $query .= '     new_enq_id enq_id, ';
        $query .= '     paneler_id ';
        $query .= '   FROM ';
        $query .= '     group_enq_questions; ';
        $this->select($query);
    }

    public function createDivCodeMultiChannelProfileTables(string $division, ?array $conditionCross, ?array $codes): void
    {
        $isConditionCross = $division === 'condition_cross';

        $divCodeBindings = [];

        if ($isConditionCross) {
            $caseWhenArr = $this->createConditionCrossArray($conditionCross, $divCodeBindings);
        } else {
            $caseWhenArr = $this->createCrossJoinArray($division, $codes, $divCodeBindings);
        }

        // start 基本・カスタム区分をアンケート形式に変換
        $unions = [];
        $divCodeToEnqBindings = [];

        foreach ($caseWhenArr as $key => $case) {
            $enqIdBind = ":enqId${key}";
            $genreIdBind = ":genreId${key}";
            $enqQuestionBind = ":question${key}";
            $enqOptionBind = ":option${key}";
            $divCodeToEnqBindings[$enqIdBind] = $key;
            $divCodeToEnqBindings[$enqQuestionBind] = $case['divisionName'];
            $divCodeToEnqBindings[$enqOptionBind] = $case['codeName'];
            $divCodeToEnqBindings[$genreIdBind] = '';

            $query = '';
            $query .= ' SELECT ';
            $query .= "   ${enqIdBind}::int AS id, ";
            $query .= "   ${genreIdBind}::VARCHAR(500) AS genre, ";
            $query .= "   ${enqQuestionBind}::VARCHAR(2048) AS question, ";
            $query .= "   ${enqOptionBind}::VARCHAR(500) AS option ";
            $unions[] = $query;
        }

        $query = '';
        $query .= ' CREATE TEMPORARY TABLE div_code_to_enq_format AS ';
        $query .= implode(' UNION ALL ', $unions);
        $query .= ';';
        $this->select($query, $divCodeToEnqBindings);
        // end 基本・カスタム区分をアンケート形式に変換

        // start 各アンケート選択肢の回答者パネラーIDリスト
        $unions = [];

        foreach ($caseWhenArr as $key => $case) {
            $enqIdBind = ":enqId${key}";
            $divCodeBindings[$enqIdBind] = $key;

            $query = '';
            $query .= '(';
            $query .= ' SELECT ';
            $query .= '   time_box_id, ';
            $query .= "   ${enqIdBind}::int enq_id, ";
            $query .= '   paneler_id ';
            $query .= ' FROM ';
            $query .= '   time_box_panelers tbp';
            $query .= ' WHERE EXISTS(SELECT 1 FROM target_programs tp WHERE tbp.time_box_id = tp.time_box_id) ';
            $query .= '   AND ' . $case['condition'];
            $query .= ' )';
            $unions[] = $query;
        }
        $query = '';
        $query .= ' CREATE TEMPORARY TABLE convert_enq AS ';
        $query .= implode(' UNION ALL ', $unions);
        $query .= ' ;';
        $this->select($query, $divCodeBindings);
        // end 各アンケート選択肢の回答者パネラーIDリスト
    }

    public function createMultiChannelProfileTables(bool $isEnq, int $regionId, string $startDate, string $endDate, array $progIds, array $timeBoxIds, array $channelIds, ?string $division, ?array $conditionCross, ?array $codes): void
    {
        $this->createTargetProgramsTable($regionId, $startDate, $endDate, $progIds, $timeBoxIds, $channelIds);

        if ($isEnq) {
            $this->createEnqMultiChannelProfileTables();
        } else {
            $this->createDivCodeMultiChannelProfileTables($division, $conditionCross, $codes);
        }
        $this->createMultiChannelProfileCommonTables($isEnq);
    }

    public function getDetailMultiChannelProfileResults(bool $isEnq, array $channelIds, int $ptThreshold): array
    {
        // start results
        $rateTypes = [
            'trp',
            'tci',
        ];
        $bindings = [];

        $query = '';
        $query .= ' SELECT ';

        if ($isEnq) {
            $query .= '   enq_id ';
        } else {
            $query .= '   null AS enq_id ';
        }
        $query .= '   , genre';
        $query .= '   , question ';
        $query .= '   , option ';
        $query .= '   , ROUND(AVG(number), 0) avg_number ';

        foreach ($rateTypes as $type) {
            $isMain = true;

            foreach ($channelIds as $channelId) {
                $bindName = "channel_${channelId}";
                $bindings[":${bindName}"] = $channelId;

                if (count($channelIds) > 1 && $isMain) {
                    $query .= "   , MAX(CASE WHEN target_title = '01' AND channel_id = :${bindName} THEN ${type}_rank ELSE NULL END) ${type}_rank ";
                    $isMain = false;
                }
                $query .= "   , ROUND(COALESCE(MAX(CASE WHEN target_title = '01' AND channel_id = :${bindName} THEN ${type} ELSE NULL END), 0), 1) ${bindName}_${type} ";
            }
        }

        $query .= ' FROM ';
        $query .= '   channel_rate cr ';
        $query .= ' GROUP BY ';
        $query .= '   enq_id, ';
        $query .= '   genre, ';
        $query .= '   question, ';
        $query .= '   option ';

        if ($isEnq) {
            $sampleThresholdName = ':sampleThreshold';
            $bindings[$sampleThresholdName] = $ptThreshold;
            $query .= ' HAVING ';
            $query .= "   ROUND(AVG(number), 0) >= ${sampleThresholdName} ";
        }
        $query .= ' ORDER BY ';
        $query .= '   genre, ';
        $query .= '   cr.enq_id; ';
        $results = $this->select($query, $bindings);

        return $results;
    }

    public function getHeaderProfileResults(array $channelIds): array
    {
        // start results
        $rateTypes = [
            'personal' => '個人全体',
            'household' => '世帯',
        ];
        $bindings = [];

        $query = '';
        $query .= 'WITH horizontal AS ( ';
        $query .= '  SELECT ';
        $query .= "   '' AS dummy"; //[,]でエラー出るので回避用
        foreach ($rateTypes as $type => $typeName) {
            foreach ($channelIds as $channelId) {
                $bindName = "channel_${channelId}";
                $bindings[":${bindName}"] = $channelId;

                $query .= "   , ROUND(COALESCE(MAX(CASE WHEN target_title = '01' AND channel_id = :${bindName} THEN ${type} ELSE NULL END), 0), 1) ${bindName}_${type} ";
            }
        }

        $query .= ' FROM ';
        $query .= '   channel_rate ';
        $query .= ')';

        $query .= ' SELECT ';
        $query .= '   p.name ';

        foreach ($channelIds as $channelId) {
            $name = "channel_${channelId}";

            $query .= ' , CASE p.code ';

            foreach ($rateTypes as $type => $typeName) {
                $query .= " WHEN '${type}' THEN ${name}_${type} ";
            }
            $query .= " END ${name}_grp ";
        }
        $query .= ' FROM ';
        $query .= '   horizontal h ';

        $unionCodes = [];

        foreach ($rateTypes as $type => $typeName) {
            $unionCodes[] = "  SELECT '${type}' AS code, '${typeName}' AS name ";
        }
        $query .= '   CROSS JOIN ( ';
        $query .= implode(' UNION ALL ', $unionCodes);
        $query .= '   ) p ';
        $query .= ' order by p.code DESC; ';

        $results = $this->select($query, $bindings);

        return $results;
    }

    public function createTargetProgramsTable(int $regionId, string $startDate, string $endDate, array $progIds, array $timeBoxIds, array $channelIds): void
    {
        $bindings = [];
        // プログラムID
        $progTimeBoxArr = [];
        $bindIndex = 0;
        $bindings[':startDate'] = $startDate;
        $bindings[':endDate'] = $endDate;
        $bindings[':regionId'] = $regionId;
        $cnt = count($progIds);

        for ($i = 0; $i < $cnt; $i++) {
            if (empty($progIds[$i]) || empty($timeBoxIds[$i])) {
                continue; // TODO - konno:デッドコード リクエストでバリデーション入れてから削除
            }
            $progId = $progIds[$i];
            $timeBoxId = $timeBoxIds[$i];
            $progBindKey = ':progId' . $bindIndex++;
            $timeBoxBindKey = ':timeBoxId' . $bindIndex++;
            $bindings[$progBindKey] = $progId;
            $bindings[$timeBoxBindKey] = $timeBoxId;

            $progTimeBoxArr[] = " (${progBindKey}, ${timeBoxBindKey}) ";
        }

        $query = '';
        $query .= ' CREATE TEMPORARY TABLE target_programs AS ';
        $query .= '   SELECT ';
        $query .= '     tp.*, ';
        $query .= '     ch.id channel_id ';
        $query .= '   FROM ';
        $query .= '   ( ';
        $query .= '       SELECT ';
        $query .= "         '01' target_title, ";
        $query .= '         prog_id, ';
        $query .= '         channel_id main_channel_id, ';
        $query .= '         title, ';
        $query .= '         time_box_id, ';
        $query .= '         greatest(tb.started_at, real_started_at) real_started_at, ';
        $query .= '         least(tb.ended_at, real_ended_at) real_ended_at, ';
        $query .= '         personal_viewing_rate, ';
        $query .= '         household_viewing_rate, ';
        $query .= '         EXTRACT(EPOCH FROM (least(tb.ended_at, real_ended_at) - greatest(tb.started_at, real_started_at))) fraction ';
        $query .= '       FROM ';
        $query .= '         programs p ';
        $query .= '         INNER JOIN ';
        $query .= '           time_boxes tb ';
        $query .= '           ON tb.id = p.time_box_id AND tb.region_id = :regionId ';
        $query .= '       WHERE ';
        $query .= '         date BETWEEN :startDate AND :endDate ';
        $query .= '         AND (p.prog_id, p.time_box_id) IN ';
        $query .= '             ( ' . implode(',', $progTimeBoxArr) . ' ) ';
        $query .= '   ) tp ';
        $query .= '   CROSS JOIN ( ';
        $query .= '     SELECT ';
        $query .= '       id ';
        $query .= '     FROM ';
        $query .= '       channels ';
        $query .= '     WHERE ';

        $bindChannelIds = $this->createArrayBindParam('channelIds', [
            'channelIds' => $channelIds,
        ], $bindings);

        $query .= '       id IN (' . implode(',', $bindChannelIds) . ') ';
        $query .= '   ) ch; ';
        $this->select($query, $bindings);
    }

    public function createMultiChannelProfileCommonTables(bool $isEnq): void
    {
        // start サンプル
        $query = '';
        $query .= ' CREATE TEMPORARY TABLE samples AS ';
        $query .= '   WITH time_box_ids AS (SELECT time_box_id FROM target_programs tp GROUP BY time_box_id) ';
        $query .= '   SELECT ';
        $query .= '     time_box_id, ';
        $query .= '     paneler_id, ';
        $query .= '     household_id ';
        $query .= '   FROM ';
        $query .= '     time_box_panelers tbp ';
        $query .= '   WHERE EXISTS(SELECT 1 FROM time_box_ids tbi WHERE tbp.time_box_id = tbi.time_box_id); ';
        $this->select($query);
        // end サンプル
        // start サンプル数
        $query = '';
        $query .= ' CREATE TEMPORARY TABLE sample_numbers AS ';
        $query .= '   SELECT ';
        $query .= '     time_box_id, ';
        $query .= '     COUNT(paneler_id) personal, ';
        $query .= '     COUNT(DISTINCT household_id) household ';
        $query .= '   FROM ';
        $query .= '     samples ';
        $query .= '   GROUP BY ';
        $query .= '     time_box_id; ';
        $this->select($query);
        // end サンプル数
        // start 裏局のaudience data
        $query = '';
        $query .= ' CREATE TEMPORARY TABLE target_audience_data AS ';
        $query .= '   WITH extracted_audience_data AS ( ';
        $query .= '     select ';
        $query .= '       ad.started_at, ';
        $query .= '       ad.ended_at, ';
        $query .= '       ad.channel_id, ';
        $query .= '       ad.paneler_id ';
        $query .= '     FROM ';
        $query .= '       audience_data_real ad ';
        $query .= '     WHERE ';
        $query .= '       ad.started_at < (SELECT MAX(real_ended_at) FROM target_programs) ';
        $query .= '       AND ad.ended_at > (SELECT MIN(real_started_at) FROM target_programs) ';
        $query .= '       AND EXISTS(SELECT 1 FROM target_programs tp WHERE ad.channel_id = tp.channel_id AND tp.main_channel_id != ad.channel_id) ';
        $query .= '   ) ';
        $query .= '   SELECT ';
        $query .= '     tp.time_box_id, ';
        $query .= '     tp.target_title, ';
        $query .= '     tp.prog_id, ';
        $query .= '     ad.channel_id, ';
        $query .= '     tp.real_started_at, ';
        $query .= '     tp.real_ended_at, ';
        $query .= '     tp.fraction, ';
        $query .= '     greatest(ad.started_at, tp.real_started_at) started_at, ';
        $query .= '     least(ad.ended_at, tp.real_ended_at) ended_at, ';
        $query .= '     ad.paneler_id, ';
        $query .= '     s.household_id ';
        $query .= '   FROM ';
        $query .= '     extracted_audience_data ad ';
        $query .= '   INNER JOIN ';
        $query .= '     target_programs tp ';
        $query .= '     ON ad.started_at < tp.real_ended_at ';
        $query .= '     AND ad.ended_at > tp.real_started_at ';
        $query .= '     AND ad.channel_id = tp.channel_id ';
        $query .= '   INNER JOIN ';
        $query .= '     samples s ';
        $query .= '     ON ad.paneler_id = s.paneler_id ';
        $query .= '     AND tp.time_box_id = s.time_box_id; ';
        $this->select($query);
        // end 裏局のaudience data
        // start 世帯audience_data
        $query = '';
        $query .= ' CREATE TEMPORARY TABLE household_audience_data AS ';
        $query .= ' WITH start_end_unions AS ( ';
        $query .= '     SELECT ';
        $query .= '     time_box_id, ';
        $query .= '     target_title, ';
        $query .= '     paneler_id, ';
        $query .= '     household_id, ';
        $query .= '     channel_id, ';
        $query .= '     prog_id, ';
        $query .= '     fraction, ';
        $query .= '     started_at union_date_time ';
        $query .= '   FROM target_audience_data ';
        $query .= '   UNION ALL ';
        $query .= '   SELECT ';
        $query .= '     time_box_id, ';
        $query .= '     target_title, ';
        $query .= '     paneler_id, ';
        $query .= '     household_id, ';
        $query .= '     channel_id, ';
        $query .= '     prog_id, ';
        $query .= '     fraction, ';
        $query .= '     ended_at union_date_time ';
        $query .= '   FROM target_audience_data ';
        $query .= ' ) ';
        $query .= ' , lead_start_end_data AS ( ';
        $query .= '     SELECT ';
        $query .= '     time_box_id, ';
        $query .= '     target_title, ';
        $query .= '     household_id, ';
        $query .= '     channel_id, ';
        $query .= '     prog_id, ';
        $query .= '     fraction, ';
        $query .= '     union_date_time, ';
        $query .= '     LEAD(union_date_time) OVER (PARTITION BY time_box_id, target_title, household_id, channel_id, prog_id ORDER BY union_date_time) lead_union_date_time ';
        $query .= '   FROM ';
        $query .= '     start_end_unions seu ';
        $query .= ' ) ';
        $query .= '   SELECT ';
        $query .= '     time_box_id, ';
        $query .= '     target_title, ';
        $query .= '     household_id, ';
        $query .= '     channel_id, ';
        $query .= '     prog_id, ';
        $query .= '     fraction, ';
        $query .= '     union_date_time, ';
        $query .= '     lead_union_date_time ';
        $query .= '   FROM ';
        $query .= '     lead_start_end_data sed ';
        $query .= '   WHERE ';
        $query .= '     sed.lead_union_date_time is NOT NULL AND sed.union_date_time != sed.lead_union_date_time ';
        $query .= '     AND EXISTS( ';
        $query .= '         SELECT 1 FROM target_audience_data tad ';
        $query .= '       WHERE ';
        $query .= '         sed.time_box_id = tad.time_box_id ';
        $query .= '         AND sed.household_id = tad.household_id ';
        $query .= '         AND sed.target_title = tad.target_title ';
        $query .= '         AND sed.prog_id = tad.prog_id ';
        $query .= '         AND sed.channel_id = tad.channel_id ';
        $query .= '         AND sed.union_date_time >= tad.started_at ';
        $query .= '         AND sed.lead_union_date_time <= tad.ended_at ';
        $query .= '     ); ';
        $this->select($query);
        // end 世帯audience data
        // start 世帯視聴率
        $query = '';
        $query .= ' CREATE TEMPORARY TABLE household_reports AS ';
        $query .= '   SELECT ';
        $query .= '     target_title, ';
        $query .= '     had.time_box_id, ';
        $query .= '     prog_id, ';
        $query .= '     channel_id, ';
        $query .= '     fraction, ';
        $query .= '     SUM(EXTRACT(EPOCH FROM (lead_union_date_time - union_date_time))) household_viewing_seconds, ';
        $query .= '     SUM(EXTRACT(EPOCH FROM (lead_union_date_time - union_date_time)))::numeric / (fraction * sn.household) * 100 household_viewing_rate ';
        $query .= '   FROM ';
        $query .= '     household_audience_data had ';
        $query .= '   INNER JOIN ';
        $query .= '     sample_numbers sn ';
        $query .= '   ON had.time_box_id = sn.time_box_id ';
        $query .= '   GROUP BY ';
        $query .= '     sn.household, ';
        $query .= '     had.time_box_id, ';
        $query .= '     target_title, ';
        $query .= '     channel_id, ';
        $query .= '     prog_id, ';
        $query .= '     fraction; ';
        $this->select($query);
        // end 世帯視聴率
        // start 裏局の視聴秒数
        $query = '';
        $query .= ' CREATE TEMPORARY TABLE target_program_viewers AS ';
        $query .= '   SELECT ';
        $query .= '     target_title, ';
        $query .= '     time_box_id, ';
        $query .= '     prog_id, ';
        $query .= '     channel_id, ';
        $query .= '     fraction, ';
        $query .= '     paneler_id, ';
        $query .= '     SUM(EXTRACT(EPOCH FROM (ended_at - started_at))) viewing_seconds ';
        $query .= '   FROM ';
        $query .= '     target_audience_data tad ';
        $query .= '   GROUP BY ';
        $query .= '     target_title, ';
        $query .= '     time_box_id, ';
        $query .= '     prog_id, ';
        $query .= '     channel_id, ';
        $query .= '     fraction, ';
        $query .= '     paneler_id; ';
        $this->select($query);
        // end 裏局の視聴秒数
        // start 世帯視聴率
        $query = '';
        $query .= ' CREATE TEMPORARY TABLE personal_reports AS ';
        $query .= '   SELECT ';
        $query .= '     target_title, ';
        $query .= '     tpv.time_box_id, ';
        $query .= '     prog_id, ';
        $query .= '     channel_id, ';
        $query .= '     fraction, ';
        $query .= '     SUM(viewing_seconds) personal_viewing_seconds, ';
        $query .= '     SUM(viewing_seconds)::numeric / (fraction * sn.personal) * 100 personal_viewing_rate ';
        $query .= '   FROM ';
        $query .= '     target_program_viewers tpv ';
        $query .= '   INNER JOIN ';
        $query .= '     sample_numbers sn ';
        $query .= '   ON tpv.time_box_id = sn.time_box_id ';
        $query .= '   GROUP BY ';
        $query .= '     sn.personal, ';
        $query .= '     tpv.time_box_id, ';
        $query .= '     target_title, ';
        $query .= '     channel_id, ';
        $query .= '     prog_id, ';
        $query .= '     fraction; ';
        $this->select($query);
        // end 世帯視聴率
        // start アンケート毎のサンプル
        $query = '';
        $query .= ' CREATE TEMPORARY TABLE enq_samples AS ';
        $query .= '   SELECT ';
        $query .= '     s.time_box_id, ';
        $query .= '     ce.enq_id, ';
        $query .= '     ce.paneler_id ';
        $query .= '   FROM ';
        $query .= '     samples s ';
        $query .= '     INNER JOIN convert_enq ce ';
        $query .= '     ON s.paneler_id = ce.paneler_id ';

        if (!$isEnq) {
            $query .= '     AND s.time_box_id = ce.time_box_id ';
        }
        $query .= ';';
        $this->select($query);
        // end アンケート毎のサンプル
        // start アンケート毎のサンプル数
        $query = '';
        $query .= ' CREATE TEMPORARY TABLE enq_sample_numbers AS ';
        $query .= '   SELECT ';
        $query .= '     time_box_id, ';
        $query .= '     enq_id, ';
        $query .= '     COUNT(paneler_id) number ';
        $query .= '   FROM ';
        $query .= '     enq_samples ';
        $query .= '   GROUP BY ';
        $query .= '     time_box_id, ';
        $query .= '     enq_id; ';
        $this->select($query);
        // end アンケート毎のサンプル数
        // start メイン・裏番組とアンケートのマスター（各放送で全体視聴が全くないと抜け落ちてしまうため）
        $query = '';
        $query .= ' CREATE TEMPORARY TABLE target_program_enq_master AS ';
        $query .= '   SELECT ';
        $query .= '     tp.time_box_id, ';
        $query .= '     tp.target_title, ';
        $query .= '     tp.prog_id, ';
        $query .= '     tp.main_channel_id, ';
        $query .= '     tp.channel_id, ';
        $query .= '     tp.fraction, ';
        $query .= '     household_viewing_rate, ';
        $query .= '     personal_viewing_rate, ';
        $query .= '     eq.id enq_id, ';
        $query .= '     eq.genre, ';
        $query .= '     eq.question, ';
        $query .= '     eq.option ';
        $query .= '   FROM ';

        if ($isEnq) {
            $query .= ' new_enq_questions eq ';
        } else {
            $query .= ' div_code_to_enq_format eq ';
        }
        $query .= '     CROSS JOIN ';
        $query .= '       target_programs tp; ';
        $this->select($query);
        // end メイン・裏番組とアンケートのマスター
        // start 裏局アンケート毎の視聴秒数
        $query = '';
        $query .= ' CREATE TEMPORARY TABLE target_enq_viewers AS ';
        $query .= '   SELECT ';
        $query .= '     tpv.time_box_id, ';
        $query .= '     tpv.target_title, ';
        $query .= '     tpv.prog_id, ';
        $query .= '     tpv.channel_id, ';
        $query .= '     es.enq_id, ';
        $query .= '     SUM(tpv.viewing_seconds) viewing_seconds ';
        $query .= '   FROM ';
        $query .= '     enq_samples es ';
        $query .= '   INNER JOIN ';
        $query .= '     target_program_viewers tpv ';
        $query .= '     ON tpv.time_box_id = es.time_box_id ';
        $query .= '     AND tpv.paneler_id = es.paneler_id ';
        $query .= '   GROUP BY ';
        $query .= '     tpv.time_box_id, ';
        $query .= '     tpv.target_title, ';
        $query .= '     tpv.prog_id, ';
        $query .= '     tpv.channel_id, ';
        $query .= '     es.enq_id; ';
        $this->select($query);
        // end 裏局アンケート毎の視聴秒数
        // start メイン局アンケート毎の視聴秒数
        $query = '';
        $query .= ' CREATE TEMPORARY TABLE main_target_enq_viewers AS ';
        $query .= '   WITH main_target_programs AS ( ';
        $query .= '     SELECT ';
        $query .= '       time_box_id, ';
        $query .= '       target_title, ';
        $query .= '       prog_id, ';
        $query .= '       main_channel_id channel_id ';
        $query .= '     FROM ';
        $query .= '       target_programs ';
        $query .= '     GROUP BY ';
        $query .= '       time_box_id, ';
        $query .= '       target_title, ';
        $query .= '       prog_id, ';
        $query .= '       main_channel_id ';
        $query .= '   )';
        $query .= '   , main_target_program_viewers AS ( ';
        $query .= '     SELECT ';
        $query .= '       tp.time_box_id, ';
        $query .= '       tp.target_title, ';
        $query .= '       tp.prog_id, ';
        $query .= '       tp.channel_id, ';
        $query .= '       pv.paneler_id, ';
        $query .= '       pv.viewing_seconds ';
        $query .= '     FROM ';
        $query .= '       main_target_programs tp';
        $query .= '     INNER JOIN ';
        $query .= '       program_viewers pv ';
        $query .= '       ON ';
        $query .= '         tp.time_box_id = pv.time_box_id ';
        $query .= '         AND tp.prog_id = pv.prog_id ';
        $query .= '   ) ';
        $query .= '   SELECT ';
        $query .= '     tpv.time_box_id, ';
        $query .= '     tpv.target_title, ';
        $query .= '     tpv.prog_id, ';
        $query .= '     tpv.channel_id, ';
        $query .= '     es.enq_id, ';
        $query .= '     SUM(tpv.viewing_seconds) viewing_seconds ';
        $query .= '   FROM ';
        $query .= '     enq_samples es ';
        $query .= '   INNER JOIN ';
        $query .= '     main_target_program_viewers tpv ';
        $query .= '     ON tpv.time_box_id = es.time_box_id ';
        $query .= '     AND tpv.paneler_id = es.paneler_id ';
        $query .= '   GROUP BY ';
        $query .= '     tpv.time_box_id, ';
        $query .= '     tpv.target_title, ';
        $query .= '     tpv.prog_id, ';
        $query .= '     tpv.channel_id, ';
        $query .= '     es.enq_id; ';
        $this->select($query);
        // end メイン局アンケート毎の視聴秒数

        // start アンケート毎の視聴率
        $query = '';
        $query .= ' CREATE TEMPORARY TABLE enq_rate_list AS ';
        $query .= '   SELECT ';
        $query .= '     tpem.time_box_id, ';
        $query .= '     tpem.target_title, ';
        $query .= '     tpem.prog_id, ';
        $query .= '     tpem.channel_id, ';
        $query .= '     tpem.fraction, ';
        $query .= '     CASE WHEN tpem.main_channel_id = tpem.channel_id THEN tpem.household_viewing_rate ELSE hr.household_viewing_rate END AS household_viewing_rate, ';
        $query .= '     CASE WHEN tpem.main_channel_id = tpem.channel_id THEN tpem.personal_viewing_rate ELSE pr.personal_viewing_rate END AS personal_viewing_rate, ';
        $query .= '     tpem.enq_id, ';
        $query .= '     tpem.genre, ';
        $query .= '     tpem.question, ';
        $query .= '     tpem.option, ';
        $query .= '     number, ';
        $query .= '     (CASE WHEN tpem.main_channel_id = tpem.channel_id THEN mtev.viewing_seconds ELSE tev.viewing_seconds END)::numeric / (number * tpem.fraction) * 100 rate ';
        $query .= '   FROM ';
        $query .= '     target_program_enq_master tpem ';
        $query .= '   LEFT JOIN target_enq_viewers tev ';
        $query .= '     ON tpem.time_box_id = tev.time_box_id ';
        $query .= '     AND tpem.target_title = tev.target_title ';
        $query .= '     AND tpem.prog_id = tev.prog_id ';
        $query .= '     AND tpem.channel_id = tev.channel_id ';
        $query .= '     AND tpem.enq_id = tev.enq_id ';
        $query .= '   LEFT JOIN main_target_enq_viewers mtev ';
        $query .= '     ON tpem.time_box_id = mtev.time_box_id ';
        $query .= '     AND tpem.target_title = mtev.target_title ';
        $query .= '     AND tpem.prog_id = mtev.prog_id ';
        $query .= '     AND tpem.channel_id = mtev.channel_id ';
        $query .= '     AND tpem.enq_id = mtev.enq_id ';
        $query .= '   LEFT JOIN household_reports hr ';
        $query .= '     ON tpem.time_box_id = hr.time_box_id ';
        $query .= '     AND tpem.target_title = hr.target_title ';
        $query .= '     AND tpem.prog_id = hr.prog_id ';
        $query .= '     AND tpem.channel_id = hr.channel_id ';
        $query .= '   LEFT JOIN personal_reports pr ';
        $query .= '     ON tpem.time_box_id = pr.time_box_id ';
        $query .= '     AND tpem.target_title = pr.target_title ';
        $query .= '     AND tpem.prog_id = pr.prog_id ';
        $query .= '     AND tpem.channel_id = pr.channel_id ';
        $query .= '   LEFT JOIN enq_sample_numbers esn ';
        $query .= '     ON tpem.time_box_id = esn.time_box_id ';
        $query .= '     AND tpem.enq_id = esn.enq_id; ';
        $this->select($query);
        // end アンケート毎の視聴率
        // start チャンネル毎の視聴率、含有率
        $query = '';
        $query .= ' CREATE TEMPORARY TABLE channel_rate AS ';
        $query .= ' WITH target_rate AS ( ';
        $query .= '     SELECT ';
        $query .= '     SUM(rate * fraction) rate, ';
        $query .= '     SUM(household_viewing_rate * fraction) household_viewing_rate, ';
        $query .= '     SUM(personal_viewing_rate * fraction) personal_viewing_rate, ';
        $query .= '     target_title, ';
        $query .= '     channel_id, ';
        $query .= '     code_name, ';
        $query .= '     AVG(number) number, ';
        $query .= '     SUM(fraction) fraction, ';
        $query .= '     url.enq_id, ';
        $query .= '     genre, ';
        $query .= '     question, ';
        $query .= '     option ';
        $query .= '   FROM ';
        $query .= '     enq_rate_list url ';
        $query .= '   INNER JOIN ';
        $query .= '     channels c ';
        $query .= '   ON url.channel_id = c.id ';
        $query .= '   GROUP BY ';
        $query .= '     target_title, ';
        $query .= '     channel_id, ';
        $query .= '     code_name, ';
        $query .= '     url.enq_id, ';
        $query .= '     genre, ';
        $query .= '     question, ';
        $query .= '     option ';
        $query .= ' )';
        $query .= ' SELECT ';
        $query .= '   target_title, ';
        $query .= '   channel_id, ';
        $query .= '   enq_id, ';
        $query .= '   genre, ';
        $query .= '   question, ';
        $query .= '   option, ';
        $query .= '   number, ';
        $query .= '   RANK() OVER (PARTITION BY target_title, enq_id ORDER BY COALESCE(rate / fraction, 0) DESC) trp_rank, ';
        $query .= '   RANK() OVER (PARTITION BY target_title, enq_id ORDER BY COALESCE(((rate / fraction) / (household_viewing_rate / fraction)) * 100, 0) DESC) tci_rank, ';
        $query .= '   COALESCE(household_viewing_rate / fraction, 0) household, ';
        $query .= '   COALESCE(personal_viewing_rate / fraction, 0) personal, ';
        $query .= '   COALESCE(rate / fraction, 0) trp, ';
        $query .= '   COALESCE(((rate / fraction) / (household_viewing_rate / fraction)) * 100, 0) tci ';
        $query .= ' FROM ';
        $query .= '   target_rate ';
        $query .= ' GROUP BY ';
        $query .= '   target_title, ';
        $query .= '   channel_id, ';
        $query .= '   enq_id, ';
        $query .= '   genre, ';
        $query .= '   question, ';
        $query .= '   option, ';
        $query .= '   fraction, ';
        $query .= '   rate, ';
        $query .= '   household_viewing_rate, ';
        $query .= '   personal_viewing_rate, ';
        $query .= '   number; ';
        $this->select($query);
        // end チャンネル毎の視聴率、含有率
    }

    public function getSelectedProgramsForProfile(): array
    {
        $query = '';
        $query .= ' WITH target_program_title_list AS ( ';
        $query .= '   SELECT ';
        $query .= '     title, ';
        $query .= "     TO_CHAR(real_started_at - interval '5 hours', 'YYYY年MM月DD日') || ' ' || lpad(to_char(real_started_at - interval '5 hours', 'HH24')::numeric + 5 || to_char(real_started_at, '時MI分SS秒'),9,'0') real_started_at ";
        $query .= '   FROM ';
        $query .= '     target_programs ';
        $query .= '   GROUP BY ';
        $query .= '     title, ';
        $query .= '     real_started_at ';
        $query .= ' ) ';
        $query .= ' SELECT ';
        $query .= '   title, ';
        $query .= "   LISTAGG(DISTINCT real_started_at, '／') within group (order by real_started_at) real_started_at_list ";
        $query .= ' FROM ';
        $query .= '   target_program_title_list ';
        $query .= ' GROUP BY ';
        $query .= '   title ';
        $query .= ' ORDER BY ';
        $query .= '   COUNT(*) DESC,';
        $query .= '   title ';
        return $this->select($query);
    }
}
