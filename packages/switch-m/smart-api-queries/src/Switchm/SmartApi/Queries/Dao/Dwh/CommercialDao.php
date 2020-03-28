<?php

namespace Switchm\SmartApi\Queries\Dao\Dwh;

use Carbon\Carbon;

class CommercialDao extends Dao
{
    // CMリスト
    public function searchListOriginalDivs(String $startDate, String $endDate, String $startTime, String $endTime, ?String $cmType, ?String $cmSeconds, ?array $progIds, int $regionId, String $division, ?array $codes, ?array $conditionCross, ?array $companyIds, ?array $productIds, ?array $cmIds, array $channels, ?array $order, ?int $page, ?int $length, ?bool $conv_15_sec_flag, bool $straddlingFlg, ?array $codeList, string $csvFlag, array $dataType, bool $cmMaterialFlag, bool $cmTypeFlag, string $codeNumber, string $sampleCodePrefix, string $sampleCodeNumberPrefix, string $selectedPersonalName, array $dataTypes, array $dataTypeFlags): array
    {
        $bindings = [];
        $isConditionCross = $division == 'condition_cross';
        $divisionKey = $division . '_';

        list(
            $withWhere,
            $codeBind,
            $channelBind,
            $programBind,
            $companyBind,
            $productIdsBind,
            $cmIdsBind
            ) = $this->createListWhere($bindings, 'condition_cross', $startDate, $endDate, $startTime, $endTime, $cmType, $cmSeconds, $progIds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $conv_15_sec_flag, $straddlingFlg);
        // 冗長的なWHERE句用
        list($rsTimeBoxIds, $rsCmIds, $rsPanelers) = $this->createCommercialListWhere($startDate, $endDate, $cmType, $cmSeconds, $progIds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $straddlingFlg);

        if (empty($rsTimeBoxIds) && empty($rsCmIds)) {
            return [
                'list' => [],
                'cnt' => 0,
            ];
        }

        $rateBindings = [];

        if (isset($bindings[':conv15SecFlag'])) {
            $rateBindings[':conv15SecFlag'] = $bindings[':conv15SecFlag'];
            unset($bindings[':conv15SecFlag']);
        }

        $divCodes = [];

        if ($isConditionCross) {
            $divCodes[] = 'condition_cross';
        } else {
            $divCodes = array_filter($codes, function ($v, $k) {
                return $v != 'personal' && $v != 'household';
            }, ARRAY_FILTER_USE_BOTH);
        }
        $divCodes = array_merge($divCodes); //keyを連番に

        list($rtType, $tsType, $grossType, $totalType, $rtTotalType) = array_values($dataTypes);

        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = array_values($dataTypeFlags);

        $sql = '';
        $sql .= ' CREATE TEMPORARY TABLE cm_list AS ';
        $sql .= '   SELECT ';
        $sql .= '     c.date ';
        $sql .= '     , c.cm_id ';
        $sql .= '     , c.prog_id ';
        $sql .= '     , c.product_id ';
        $sql .= '     , c.company_id ';
        $sql .= '     , c.time_box_id ';
        $sql .= '     , c.started_at ';
        $sql .= '     , c.duration ';
        $sql .= '     , c.channel_id ';
        $sql .= '     , c.cm_type ';
        $sql .= '     , c.program_title ';
        $sql .= '     , c.setting ';
        $sql .= '     , c.talent ';
        $sql .= '     , c.bgm ';
        $sql .= '     , c.memo ';
        $sql .= '     , 0 total_cnt ';
        $sql .= '     , 0 total_duration ';
        $sql .= '     , c.personal_viewing_rate ';
        $sql .= '     , c.ts_personal_viewing_rate ';
        $sql .= '     , c.ts_personal_total_viewing_rate ';
        $sql .= '     , COALESCE(c.ts_personal_gross_viewing_rate, c.ts_samples_personal_viewing_rate) ts_personal_gross_viewing_rate ';
        $sql .= '     , COALESCE(c.ts_personal_rt_total_viewing_rate, c.ts_samples_personal_viewing_rate) ts_personal_rt_total_viewing_rate ';
        $sql .= '     , c.household_viewing_rate ';
        $sql .= '     , c.ts_household_viewing_rate ';
        $sql .= '     , c.ts_household_total_viewing_rate ';
        $sql .= '     , COALESCE(c.ts_household_gross_viewing_rate, c.ts_samples_household_viewing_rate) ts_household_gross_viewing_rate ';
        $sql .= '   FROM ';
        $sql .= '     commercials c  ';
        $sql .= $withWhere;
        $sql .= ' ; ';
        $this->select($sql, $bindings);

        if (count($divCodes) > 0) {
            $this->createCommonTempTables($isConditionCross, $startDate, $endDate, $regionId, $division, $divisionKey, $codes, $codeList, $conditionCross, $companyIds, $companyBind, $rsTimeBoxIds, $dataType, $sampleCodePrefix, $dataTypeFlags, $sampleCodeNumberPrefix, $selectedPersonalName, $codeNumber);

            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE horizontal AS ';
            $sql .= ' WITH vertical AS ( ';
            $sql .= '  SELECT';
            $sql .= '    c.date';
            $sql .= '    , c.started_at';
            $sql .= '    , c.time_box_id';
            $sql .= '    , c.prog_id';
            $sql .= '    , c.duration';
            $sql .= '    , c.cm_type';
            $sql .= '    , c.cm_id';
            $sql .= '    , c.program_title';
            $sql .= '    , c.channel_id';
            $sql .= '    , c.company_id';
            $sql .= '    , c.product_id';
            $sql .= '    , c.setting';
            $sql .= '    , c.talent';
            $sql .= '    , c.bgm';
            $sql .= '    , c.memo';
            $sql .= '    , c.total_cnt ';
            $sql .= '    , c.total_duration ';
            $sql .= '    , c.personal_viewing_rate';
            $sql .= '    , c.household_viewing_rate';
            $sql .= '    , c.ts_personal_viewing_rate';
            $sql .= '    , c.ts_personal_total_viewing_rate';
            $sql .= '    , c.ts_personal_gross_viewing_rate';
            $sql .= '    , c.ts_personal_rt_total_viewing_rate';
            $sql .= '    , c.ts_household_viewing_rate';
            $sql .= '    , c.ts_household_total_viewing_rate';
            $sql .= '    , c.ts_household_gross_viewing_rate';

            for ($i = 0; $i < $codeNumber; $i++) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                $number = sprintf('%s%02d', $sampleCodeNumberPrefix, $i);

                if ($isRt) {
                    $sql .= " , CASE WHEN ucl.data_type = 'rt' AND ucl.code = '${code}' AND rtn.${number} != 0 THEN ucl.viewing_number::real / rtn.${number} * 100 * CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END ELSE 0 END AS rt_${code}_viewing_rate";
                } else {
                    $sql .= " , 0 AS rt_${code}_viewing_rate";
                }

                if ($isTs) {
                    $sql .= " , CASE WHEN ucl.data_type = 'ts' AND ucl.code = '${code}' AND tsn.${number} != 0 THEN ucl.viewing_number::real / tsn.${number} * 100 * CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END ELSE 0 END AS ts_${code}_viewing_rate";
                } else {
                    $sql .= " , 0 AS ts_${code}_viewing_rate";
                }

                if ($isGross) {
                    $sql .= " , CASE WHEN ucl.data_type = 'gross' AND ucl.code = '${code}' AND tsn.${number} != 0 THEN ucl.viewing_number::real / tsn.${number} * 100 * CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END ELSE 0 END AS gross_${code}_viewing_rate";
                } else {
                    $sql .= " , 0 AS gross_${code}_viewing_rate";
                }

                if ($isTotal) {
                    $sql .= " , CASE WHEN ucl.data_type = 'total' AND ucl.code = '${code}' AND tsn.${number} != 0 THEN ucl.viewing_number::real / tsn.${number} * 100 * CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END ELSE 0 END AS total_${code}_viewing_rate";
                } else {
                    $sql .= " , 0 AS total_${code}_viewing_rate";
                }

                if ($isRtTotal) {
                    $sql .= " , CASE WHEN ucl.data_type = 'rt_total' AND ucl.code = '${code}' AND tsn.${number} != 0 THEN ucl.viewing_number::real / tsn.${number} * 100 * CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END ELSE 0 END AS rt_total_${code}_viewing_rate";
                } else {
                    $sql .= " , 0 AS rt_total_${code}_viewing_rate";
                }
            }

            $code = sprintf('%s_%s', $selectedPersonalName, $sampleCodePrefix);
            $number = sprintf('%s_%s', $selectedPersonalName, $sampleCodeNumberPrefix);
            $sql .= " , CASE WHEN ucl.data_type = 'rt' AND ucl.code = '${code}' THEN ucl.viewing_number ELSE 0 END AS rt_${code}_viewing_number ";
            $sql .= " , CASE WHEN ucl.data_type = 'ts' AND ucl.code = '${code}' THEN ucl.viewing_number ELSE 0 END AS ts_${code}_viewing_number";
            $sql .= " , CASE WHEN ucl.data_type = 'gross' AND ucl.code = '${code}' THEN ucl.viewing_number ELSE 0 END AS gross_${code}_viewing_number";
            $sql .= " , CASE WHEN ucl.data_type = 'total' AND ucl.code = '${code}' THEN ucl.viewing_number ELSE 0 END AS total_${code}_viewing_number";
            $sql .= " , CASE WHEN ucl.data_type = 'rt_total' AND ucl.code = '${code}' THEN ucl.viewing_number ELSE 0 END AS rt_total_${code}_viewing_number";

            if ($isRt) {
                $sql .= "     , rtn.${number} rt_${number} ";
            } else {
                $sql .= " , 0 AS rt_${number}";
            }

            if ($isTs || $isGross || $isTotal || $isRtTotal) {
                $sql .= "     , tsn.${number} ts_${number} ";
            } else {
                $sql .= " , 0 AS ts_${number}";
            }

            $sql .= '  FROM';
            $sql .= '    cm_list c';
            $sql .= '    LEFT JOIN union_cv_list ucl ';
            $sql .= '      ON c.cm_id = ucl.cm_id';
            $sql .= '      AND c.prog_id = ucl.prog_id';
            $sql .= '      AND c.started_at = ucl.started_at';

            if ($isTs || $isGross || $isTotal || $isRtTotal) {
                $sql .= '  LEFT JOIN';
                $sql .= '    ts_numbers tsn';
                $sql .= '  ON';
                $sql .= '    c.time_box_id = tsn.time_box_id ';
            }

            if ($isRt) {
                $sql .= '  LEFT JOIN';
                $sql .= '    rt_numbers rtn';
                $sql .= '  ON';
                $sql .= '    c.time_box_id = rtn.time_box_id ';
            }
            $sql .= ' )';
            $sql .= '  SELECT';
            $sql .= '    c.date';
            $sql .= '    , c.started_at';
            $sql .= '    , c.time_box_id';
            $sql .= '    , c.prog_id';
            $sql .= '    , c.duration';
            $sql .= '    , c.cm_type';
            $sql .= '    , c.cm_id';
            $sql .= '    , c.program_title';
            $sql .= '    , c.channel_id';
            $sql .= '    , c.company_id';
            $sql .= '    , c.product_id';
            $sql .= '    , c.setting';
            $sql .= '    , c.talent';
            $sql .= '    , c.bgm';
            $sql .= '    , c.memo';
            $sql .= '    , c.total_cnt ';
            $sql .= '    , c.total_duration ';
            $sql .= '    , c.personal_viewing_rate';
            $sql .= '    , c.household_viewing_rate';
            $sql .= '    , c.ts_personal_viewing_rate';
            $sql .= '    , c.ts_personal_total_viewing_rate';
            $sql .= '    , c.ts_personal_gross_viewing_rate';
            $sql .= '    , c.ts_personal_rt_total_viewing_rate';
            $sql .= '    , c.ts_household_viewing_rate';
            $sql .= '    , c.ts_household_total_viewing_rate';
            $sql .= '    , c.ts_household_gross_viewing_rate';

            for ($i = 0; $i < $codeNumber; $i++) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                $sql .= "    , MAX(rt_${code}_viewing_rate) rt_${code}_viewing_rate";
                $sql .= "    , MAX(ts_${code}_viewing_rate) ts_${code}_viewing_rate";
                $sql .= "    , MAX(gross_${code}_viewing_rate) gross_${code}_viewing_rate";
                $sql .= "    , MAX(total_${code}_viewing_rate) total_${code}_viewing_rate";
                $sql .= "    , MAX(rt_total_${code}_viewing_rate) rt_total_${code}_viewing_rate";
            }

            $code = sprintf('%s_%s', $selectedPersonalName, $sampleCodePrefix);
            $number = sprintf('%s_%s', $selectedPersonalName, $sampleCodeNumberPrefix);
            $sql .= "    , CASE WHEN rt_${number} != 0 THEN MAX(rt_${code}_viewing_number)::real * 100 * (CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END) / rt_${number} ELSE 0 END rt_${code}_viewing_rate";
            $sql .= "    , CASE WHEN ts_${number} != 0 THEN MAX(ts_${code}_viewing_number)::real * 100 * (CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END) / ts_${number} ELSE 0 END ts_${code}_viewing_rate";
            $sql .= "    , CASE WHEN ts_${number} != 0 THEN MAX(gross_${code}_viewing_number)::real * 100 * (CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END) / ts_${number} ELSE 0 END gross_${code}_viewing_rate";
            $sql .= "    , CASE WHEN ts_${number} != 0 THEN MAX(total_${code}_viewing_number)::real * 100 * (CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END) / ts_${number} ELSE 0 END total_${code}_viewing_rate";
            $sql .= "    , CASE WHEN ts_${number} != 0 THEN MAX(rt_total_${code}_viewing_number)::real * 100 * (CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END) / ts_${number} ELSE 0 END rt_total_${code}_viewing_rate";

            $sql .= '  FROM';
            $sql .= '    vertical c';
            $sql .= '  GROUP BY';
            $sql .= '    c.date,';
            $sql .= '    c.started_at,';
            $sql .= '    c.time_box_id,';
            $sql .= '    c.prog_id,';
            $sql .= '    c.duration,';
            $sql .= '    c.cm_type,';
            $sql .= '    c.cm_id,';
            $sql .= '    c.program_title,';
            $sql .= '    c.channel_id,';
            $sql .= '    c.company_id,';
            $sql .= '    c.product_id,';
            $sql .= '    c.setting,';
            $sql .= '    c.talent,';
            $sql .= '    c.bgm,';
            $sql .= '    c.memo,';
            $sql .= '    c.total_cnt, ';
            $sql .= '    c.total_duration, ';
            $sql .= '    c.personal_viewing_rate,';
            $sql .= '    c.household_viewing_rate,';
            $sql .= '    c.ts_personal_viewing_rate,';
            $sql .= '    c.ts_personal_total_viewing_rate,';
            $sql .= '    c.ts_personal_gross_viewing_rate,';
            $sql .= '    c.ts_personal_rt_total_viewing_rate,';
            $sql .= '    c.ts_household_viewing_rate,';
            $sql .= '    c.ts_household_total_viewing_rate,';
            $sql .= '    c.ts_household_gross_viewing_rate,';
            $sql .= "    rt_${number}, ";
            $sql .= "    ts_${number} ";
            $this->select($sql, $rateBindings);
        }

        $select = '';
        $select .= ' SELECT';
        $select .= '  date,';

        if ($csvFlag !== '1') {
            $select .= '   holiday, ';
        }
        $select .= '  CASE  ';
        $select .= '    WHEN d = 0  ';
        $select .= "      THEN '日'  ";
        $select .= '    WHEN d = 1  ';
        $select .= "      THEN '月'  ";
        $select .= '    WHEN d = 2  ';
        $select .= "      THEN '火'  ";
        $select .= '    WHEN d = 3  ';
        $select .= "      THEN '水'  ";
        $select .= '    WHEN d = 4  ';
        $select .= "      THEN '木'  ";
        $select .= '    WHEN d = 5  ';
        $select .= "      THEN '金'  ";
        $select .= '    WHEN d = 6  ';
        $select .= "      THEN '土'  ";
        $select .= '    END';

        if ($csvFlag === '1') {
            $select .= "  || CASE WHEN holiday IS NOT NULL THEN  '(祝)' ELSE '' END ";
        }
        $select .= '    dow,';
        $select .= ' started_at, ';
        $select .= ' duration, ';

        if ($cmTypeFlag) {
            $select .= ' cm_type, ';
        }
        $select .= ' company_name, ';
        $select .= ' cm_name, ';

        if ($cmMaterialFlag) {
            $select .= ' cm_id, ';
            $select .= ' setting, ';
            $select .= ' talent, ';
            $select .= ' bgm,';
            $select .= ' memo, ';
        }

        $hasPersonal = $division != 'condition_cross' && in_array('personal', $codes);
        $hasHousehold = $division == 'condition_cross' || in_array('household', $codes);

        // constのDATA_TYPE_NUMBER順に表示
        foreach ($dataTypes as $type) {
            if (!in_array($type, $dataType)) {
                continue;
            }
            $prefix;

            switch ($type) {
                case $rtType:
                    $prefix = 'rt_';
                    break;
                case $tsType:
                    $prefix = 'ts_';
                    break;
                case $grossType:
                    $prefix = 'gross_';
                    break;
                case $totalType:
                    $prefix = 'total_';
                    break;
                case $rtTotalType:
                    $prefix = 'rt_total_';
                    break;
            }

            // 個人全体
            if ($hasPersonal) {
                $select .= " COALESCE(${prefix}personal_viewing_rate,0) ${prefix}personal_viewing_rate, ";
            }
            // コードのcase文作成
            foreach ($divCodes as $val) {
                $name = $prefix . $divisionKey . $val;

                if ($isConditionCross && $val === 'condition_cross') {
                    $name = "${prefix}condition_cross";
                }
                $select .= "   COALESCE(${name},0) ${name}, ";
            }
            // 世帯
            if ($hasHousehold) {
                $select .= " COALESCE(${prefix}household_viewing_rate,0) ${prefix}household_viewing_rate, ";
            }
        }

        $select .= '  channel_code_name,';
        $select .= '  program_title';

        $from = '';
        $from .= ' FROM';
        $from .= '  (';
        $from .= '    SELECT';
        $from .= '      c.cm_id,';
        $from .= '      c.date,';
        $from .= "      DATE_PART('dow', c.date) d,";
        $from .= '      lpad(';
        $from .= "        to_char(c.started_at - interval '5 hours', 'HH24') ::numeric + 5 || to_char(c.started_at, ':MI:SS')";
        $from .= '        ,8';
        $from .= "        ,'0'";
        $from .= '      ) as started_at,';
        $from .= '      c.duration,';
        $from .= '      CASE';
        $from .= '        WHEN c.cm_type = 0';
        $from .= "          THEN 'PT'";
        $from .= '        WHEN c.cm_type = 1';
        $from .= "          THEN 'SB'";
        $from .= '        WHEN c.cm_type = 2';
        $from .= "          THEN 'TIME'";
        $from .= '        END as cm_type,';
        $from .= '      c.program_title,';
        $from .= '      code_name channel_code_name,';
        $from .= '      companies.name company_name,';
        $from .= '      products.name cm_name,';
        $from .= '      holiday,';
        $from .= '      c.setting,';
        $from .= '      c.talent,';
        $from .= '      c.bgm,';
        $from .= '      c.memo,';
        $from .= '      c.started_at as org_started_at,';

        if (count($divCodes) > 0) {
            foreach ($divCodes as $key => $value) {
                $divCode = $divisionKey . $value;

                if ($isConditionCross && $value === 'condition_cross') {
                    $divCode = 'condition_cross';
                }
                $code = sprintf('%s%02d', $sampleCodePrefix, $key);
                $number = sprintf('%s%02d', $sampleCodeNumberPrefix, $key);

                if ($isRt) {
                    $from .= "    ROUND(rt_${code}_viewing_rate, 1) rt_${divCode}, ";
                }

                if ($isTs) {
                    $from .= "    ROUND(ts_${code}_viewing_rate, 1) ts_${divCode}, ";
                }

                if ($isGross) {
                    $from .= "    ROUND(gross_${code}_viewing_rate, 1) gross_${divCode}, ";
                }

                if ($isTotal) {
                    $from .= "    ROUND(total_${code}_viewing_rate, 1) total_${divCode}, ";
                }

                if ($isRtTotal) {
                    $from .= "    ROUND(rt_total_${code}_viewing_rate, 1) rt_total_${divCode}, ";
                }
            }
        }

        $from .= '    ROUND(c.personal_viewing_rate * CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END,1) rt_personal_viewing_rate,';
        $from .= '    ROUND(c.ts_personal_viewing_rate * CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END,1) ts_personal_viewing_rate,';
        $from .= '    ROUND(c.ts_personal_total_viewing_rate * CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END,1) total_personal_viewing_rate,';
        $from .= '    ROUND(c.ts_personal_gross_viewing_rate * CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END,1) gross_personal_viewing_rate,';
        $from .= '    ROUND(c.ts_personal_rt_total_viewing_rate * CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END,1) rt_total_personal_viewing_rate,';
        $from .= '    ROUND(c.household_viewing_rate * CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END,1) rt_household_viewing_rate,';
        $from .= '    ROUND(c.ts_household_viewing_rate * CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END,1) ts_household_viewing_rate,';
        $from .= '    ROUND(c.ts_household_total_viewing_rate * CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END,1) total_household_viewing_rate,';
        $from .= '    ROUND(c.ts_household_gross_viewing_rate * CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END,1) gross_household_viewing_rate';

        $from .= '    FROM';

        if (count($divCodes) === 0) {
            $from .= '      cm_list c';
        } else {
            $from .= '      horizontal c';
        }

        $from .= '      LEFT JOIN channels ch';
        $from .= '        ON ch.id = c.channel_id';
        $from .= '      LEFT JOIN products';
        $from .= '        ON products.id = c.product_id';
        $from .= '      LEFT JOIN holidays h';
        $from .= '        ON h.holiday = c.date';
        $from .= '      LEFT JOIN companies';
        $from .= '        ON c.company_id = companies.id';
        $from .= '  ) odr ';

        $orderBy = '';
        $limit = '';
        $offset = '';

        if ($csvFlag == '0') {
            if (isset($order) && count($order) > 0) {
                $orderBy = ' ORDER BY ';
                $orderArr = [];

                array_push($order, ['column' => 'org_started_at', 'dir' => 'desc']);

                $channelOrder = array_filter($order, function ($v, $k) {
                    return $v['column'] === 'channel_code_name';
                }, ARRAY_FILTER_USE_BOTH);

                if (count($channelOrder) === 0) {
                    array_push($order, ['column' => 'channel_code_name', 'dir' => 'asc']);
                }

                foreach ($order as $key => $val) {
                    array_push($orderArr, "   odr.${val['column']} ${val['dir']}");
                }
                $orderBy .= implode(',', $orderArr);
            }

            if (isset($length)) {
                $limit .= " LIMIT ${length} ";
            }

            if (isset($page)) {
                $offsetNum = $length * ($page - 1);
                $limit .= " OFFSET ${offsetNum} ";
            }
        } else {
            $orderBy = ' ORDER BY org_started_at desc, channel_code_name asc ';
        }

        $query = $select . $from . $orderBy . $limit . $offset;
        $result = $this->select($query, $rateBindings);

        // 件数取得
        $query = ' SELECT COUNT(*) cnt ' . $from;
        $resultCnt = $this->selectOne($query, $rateBindings);
        return [
            'list' => $result,
            'cnt' => $resultCnt->cnt,
        ];
    }

    /**
     * 出稿数.
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param string $cmType
     * @param string $cmSeconds
     * @param array $progIds
     * @param int $regionId
     * @param array $companyIds
     * @param array $productIds
     * @param array $cmIds
     * @param array $channels
     * @param bool $straddlingFlg
     * @return array
     */
    public function searchAdvertising(String $startDate, String $endDate, String $startTime, String $endTime, ?String $cmType, ?String $cmSeconds, ?array $progIds, int $regionId, ?array $companyIds, ?array $productIds, ?array $cmIds, array $channels, bool $straddlingFlg): array
    {
        $bindings = [];

        list(
            $withWhere,
            $codeBind,
            $channelBind,
            $programBind,
            $companyBind,
            $productIdsBind,
            $cmIdsBind
            ) = $this->createListWhere($bindings, false, $startDate, $endDate, $startTime, $endTime, $cmType, $cmSeconds, $progIds, $regionId, '', null, null, $companyIds, $productIds, $cmIds, $channels, null, $straddlingFlg);

        $with = '';
        $with .= ' WITH list AS( ';
        $with .= ' SELECT ';
        $with .= '   c.channel_id, ';
        $with .= "   DATE_PART('dow',c.date) dow, ";
        $with .= "   to_char(c.started_at, 'HH24') hh, ";
        $with .= '   cm_id ';
        $with .= ' FROM ';
        $with .= '   commercials c ';
        $with .= $withWhere;
        $with .= ' 	AND EXISTS ( SELECT 1 FROM time_boxes tb WHERE tb.id = c.time_box_id AND tb.region_id = :region_id) ';
        $with .= ' ) ';

        $select = '';
        $select .= ' SELECT ';
        $select .= '   c.channel_id, ';
        $select .= '   c.dow, ';
        $select .= '   c.hh, ';
        $select .= "   CASE WHEN c.dow = 0 THEN '日' ";
        $select .= "        WHEN c.dow = 1 THEN '月' ";
        $select .= "        WHEN c.dow = 2 THEN '火' ";
        $select .= "        WHEN c.dow = 3 THEN '水' ";
        $select .= "        WHEN c.dow = 4 THEN '木' ";
        $select .= "        WHEN c.dow = 5 THEN '金' ";
        $select .= "        WHEN c.dow = 6 THEN '土' END disp_dow, ";
        $select .= '   COUNT(c.cm_id) ';
        $select .= ' FROM ';
        $select .= '   list c ';
        $select .= ' GROUP BY ';
        $select .= '   c.channel_id, ';
        $select .= '   c.dow, ';
        $select .= '   c.hh ';
        $select .= ' ORDER BY ';
        $select .= '   c.channel_id, ';
        $select .= '   c.dow, ';
        $select .= '   c.hh ';

        $query = $with . $select;
        $result = $this->select($query, $bindings);

        return $result;
    }

    // CM GRP
    public function searchGrpOriginalDivs(String $startDate, String $endDate, String $startTime, String $endTime, ?String $cmType, ?String $cmSeconds, ?array $progIds, int $regionId, String $division, ?array $codes, ?array $conditionCross, ?array $companyIds, ?array $productIds, ?array $cmIds, array $channels, ?bool $conv_15_sec_flag, string $period, ?string $allChannels, bool $straddlingFlg, int $length, int $page, string $csvFlag, array $dataType, string $codeNumber, string $sampleCodePrefix, string $sampleCodeNumberPrefix, string $selectedPersonalName, array $dataTypes, array $dataTypeFlags, ?array $codeList): array
    {
        $bindings = [];
        $isConditionCross = $division == 'condition_cross';
        $divisionKey = $division . '_';

        $dateSql = '';
        $dateParititionSql = '';

        switch ($period) {
            case 'period':
                $dateSql = " '期間計'::varchar(255) date,";
                $dateParititionSql = '';
                break;
            case 'day':
                $dateSql = " TO_CHAR(date, 'YYYY年MM月dd日') date,";
                $dateParititionSql = "TO_CHAR(date, 'YYYY年MM月dd日') ,";
                break;
            case 'week':
                $dateSql = " TO_CHAR(DATE_TRUNC('week', date),'YYYY年MM月dd日週') date,";
                $dateParititionSql = "TO_CHAR(DATE_TRUNC('week', date),'YYYY年MM月dd日週') ,";
                break;
            case 'month':
                $dateSql = " TO_CHAR(DATE_TRUNC('month', date),'YYYY年MM月') date,";
                $dateParititionSql = "TO_CHAR(DATE_TRUNC('month', date),'YYYY年MM月') ,";
                break;
        }

        if ($csvFlag === '0') {
            $allChannels = '0';
        }

        list(
            $withWhere,
            $codeBind,
            $channelBind,
            $programBind,
            $companyBind,
            $productIdsBind,
            $cmIdsBind
            ) = $this->createListWhere($bindings, 'condition_cross', $startDate, $endDate, $startTime, $endTime, $cmType, $cmSeconds, $progIds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $conv_15_sec_flag, $straddlingFlg);
        // 冗長的なWHERE句用
        list($rsTimeBoxIds, $rsCmIds, $rsPanelers) = $this->createCommercialListWhere($startDate, $endDate, $cmType, $cmSeconds, $progIds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $straddlingFlg);

        if (empty($rsTimeBoxIds) && empty($rsCmIds)) {
            return [];
        }

        $rateBindings = [];

        if (isset($bindings[':conv15SecFlag'])) {
            $rateBindings[':conv15SecFlag'] = $bindings[':conv15SecFlag'];
        }

        $divCodes = [];

        if ($isConditionCross) {
            $divCodes[] = 'condition_cross';
        } else {
            $divCodes = array_filter($codes, function ($v, $k) {
                return $v != 'personal' && $v != 'household';
            }, ARRAY_FILTER_USE_BOTH);
        }
        $divCodes = array_merge($divCodes); //keyを連番に

        list($rtType, $tsType, $grossType, $totalType, $rtTotalType) = array_values($dataTypes);

        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = array_values($dataTypeFlags);

        $sql = '';
        $sql .= ' CREATE TEMPORARY TABLE cm_list AS ';
        $sql .= '   SELECT ';
        $sql .= $dateSql;
        $sql .= '     c.cm_id ';
        $sql .= '     , c.prog_id ';
        $sql .= '     , c.product_id ';
        $sql .= '     , c.company_id ';
        $sql .= '     , c.time_box_id ';
        $sql .= '     , c.started_at ';
        $sql .= '     , c.duration ';
        $sql .= '     , c.channel_id ';
        $sql .= '     , c.cm_type ';
        $sql .= '     , c.program_title ';
        $sql .= '     , c.setting ';
        $sql .= '     , c.talent ';
        $sql .= '     , c.bgm ';
        $sql .= '     , c.memo ';
        $sql .= "     , COUNT(*) OVER (PARTITION BY ${dateParititionSql} company_id, product_id, channel_id) total_cnt ";
        $sql .= "     , SUM(duration) OVER (PARTITION BY ${dateParititionSql} company_id, product_id, channel_id) total_duration ";
        $sql .= '     , SUM(  ';
        $sql .= '       ROUND(  ';
        $sql .= '         c.personal_viewing_rate * CASE  ';
        $sql .= '           WHEN :conv15SecFlag = 1  ';
        $sql .= '             THEN duration::numeric / 15  ';
        $sql .= '           ELSE 1  ';
        $sql .= '           END ';
        $sql .= '         , 1 ';
        $sql .= '       )::decimal(16,1) ';
        $sql .= "     ) OVER (PARTITION BY ${dateParititionSql} company_id, product_id, channel_id) personal_viewing_rate ";
        $sql .= '     , SUM(  ';
        $sql .= '       ROUND(  ';
        $sql .= '         c.ts_personal_viewing_rate * CASE  ';
        $sql .= '           WHEN :conv15SecFlag = 1  ';
        $sql .= '             THEN duration::numeric / 15  ';
        $sql .= '           ELSE 1  ';
        $sql .= '           END ';
        $sql .= '         , 1 ';
        $sql .= '       )::decimal(16,1) ';
        $sql .= "     ) OVER (PARTITION BY ${dateParititionSql} company_id, product_id, channel_id) ts_personal_viewing_rate ";
        $sql .= '     , SUM(  ';
        $sql .= '       ROUND(  ';
        $sql .= '         c.ts_personal_total_viewing_rate * CASE  ';
        $sql .= '           WHEN :conv15SecFlag = 1  ';
        $sql .= '             THEN duration::numeric / 15  ';
        $sql .= '           ELSE 1  ';
        $sql .= '           END ';
        $sql .= '         , 1 ';
        $sql .= '       )::decimal(16,1) ';
        $sql .= "     ) OVER (PARTITION BY ${dateParititionSql} company_id, product_id, channel_id) ts_personal_total_viewing_rate ";
        $sql .= '     , SUM(  ';
        $sql .= '       ROUND(  ';
        $sql .= '         COALESCE(c.ts_personal_gross_viewing_rate, c.ts_samples_personal_viewing_rate) * CASE  ';
        $sql .= '           WHEN :conv15SecFlag = 1  ';
        $sql .= '             THEN duration::numeric / 15  ';
        $sql .= '           ELSE 1  ';
        $sql .= '           END ';
        $sql .= '         , 1 ';
        $sql .= '       )::decimal(16,1) ';
        $sql .= "     ) OVER (PARTITION BY ${dateParititionSql} company_id, product_id, channel_id) ts_personal_gross_viewing_rate ";
        $sql .= '     , SUM(  ';
        $sql .= '       ROUND(  ';
        $sql .= '         COALESCE(c.ts_personal_rt_total_viewing_rate, c.ts_samples_personal_viewing_rate) * CASE  ';
        $sql .= '           WHEN :conv15SecFlag = 1  ';
        $sql .= '             THEN duration::numeric / 15  ';
        $sql .= '           ELSE 1  ';
        $sql .= '           END ';
        $sql .= '         , 1 ';
        $sql .= '       ) ';
        $sql .= "     ) OVER (PARTITION BY ${dateParititionSql} company_id, product_id, channel_id) ts_personal_rt_total_viewing_rate ";
        $sql .= '     , SUM(  ';
        $sql .= '       ROUND(  ';
        $sql .= '         c.household_viewing_rate * CASE  ';
        $sql .= '           WHEN :conv15SecFlag = 1  ';
        $sql .= '             THEN duration::numeric / 15  ';
        $sql .= '           ELSE 1  ';
        $sql .= '           END ';
        $sql .= '         , 1 ';
        $sql .= '       )::decimal(16,1) ';
        $sql .= "     ) OVER (PARTITION BY ${dateParititionSql} company_id, product_id, channel_id) household_viewing_rate  ";
        $sql .= '     , SUM(  ';
        $sql .= '       ROUND(  ';
        $sql .= '         c.ts_household_viewing_rate * CASE  ';
        $sql .= '           WHEN :conv15SecFlag = 1  ';
        $sql .= '             THEN duration::numeric / 15  ';
        $sql .= '           ELSE 1  ';
        $sql .= '           END ';
        $sql .= '         , 1 ';
        $sql .= '       )::decimal(16,1) ';
        $sql .= "     ) OVER (PARTITION BY ${dateParititionSql} company_id, product_id, channel_id) ts_household_viewing_rate  ";
        $sql .= '     , SUM(  ';
        $sql .= '       ROUND(  ';
        $sql .= '         c.ts_household_total_viewing_rate * CASE  ';
        $sql .= '           WHEN :conv15SecFlag = 1  ';
        $sql .= '             THEN duration::numeric / 15  ';
        $sql .= '           ELSE 1  ';
        $sql .= '           END ';
        $sql .= '         , 1 ';
        $sql .= '       )::decimal(16,1) ';
        $sql .= "     ) OVER (PARTITION BY ${dateParititionSql} company_id, product_id, channel_id) ts_household_total_viewing_rate  ";
        $sql .= '     , SUM(  ';
        $sql .= '       ROUND(  ';
        $sql .= '         COALESCE(c.ts_household_gross_viewing_rate, c.ts_samples_household_viewing_rate) * CASE  ';
        $sql .= '           WHEN :conv15SecFlag = 1  ';
        $sql .= '             THEN duration::numeric / 15  ';
        $sql .= '           ELSE 1  ';
        $sql .= '           END ';
        $sql .= '         , 1 ';
        $sql .= '       )::decimal(16,1) ';
        $sql .= "     ) OVER (PARTITION BY ${dateParititionSql} company_id, product_id, channel_id) ts_household_gross_viewing_rate  ";
        $sql .= '   FROM ';
        $sql .= '     commercials c  ';
        $sql .= $withWhere;
        $sql .= ' ; ';
        $this->select($sql, $bindings);

        if (count($divCodes) > 0) {
            $this->createCommonTempTables($isConditionCross, $startDate, $endDate, $regionId, $division, $divisionKey, $codes, $codeList, $conditionCross, $companyIds, $companyBind, $rsTimeBoxIds, $dataType, $sampleCodePrefix, $dataTypeFlags, $sampleCodeNumberPrefix, $selectedPersonalName, $codeNumber);

            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE horizontal AS ';
            $sql .= ' WITH vertical AS ( ';
            $sql .= '  SELECT';
            $sql .= '    c.date';
            $sql .= '    , c.started_at';
            $sql .= '    , c.time_box_id';
            $sql .= '    , c.prog_id';
            $sql .= '    , c.duration';
            $sql .= '    , c.cm_type';
            $sql .= '    , c.cm_id';
            $sql .= '    , c.program_title';
            $sql .= '    , c.channel_id';
            $sql .= '    , c.company_id';
            $sql .= '    , c.product_id';
            $sql .= '    , c.setting';
            $sql .= '    , c.talent';
            $sql .= '    , c.bgm';
            $sql .= '    , c.memo';
            $sql .= '    , c.total_cnt ';
            $sql .= '    , c.total_duration ';
            $sql .= '    , c.personal_viewing_rate';
            $sql .= '    , c.household_viewing_rate';
            $sql .= '    , c.ts_personal_viewing_rate';
            $sql .= '    , c.ts_personal_total_viewing_rate';
            $sql .= '    , c.ts_personal_gross_viewing_rate';
            $sql .= '    , c.ts_personal_rt_total_viewing_rate';
            $sql .= '    , c.ts_household_viewing_rate';
            $sql .= '    , c.ts_household_total_viewing_rate';
            $sql .= '    , c.ts_household_gross_viewing_rate';

            for ($i = 0; $i < $codeNumber; $i++) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                $number = sprintf('%s%02d', $sampleCodeNumberPrefix, $i);

                if ($isRt) {
                    $sql .= " , CASE WHEN ucl.data_type = 'rt' AND ucl.code = '${code}' AND rtn.${number} != 0 THEN ucl.viewing_number::real / rtn.${number} * 100  ELSE 0 END AS rt_${code}_viewing_rate";
                } else {
                    $sql .= " , 0 AS rt_${code}_viewing_rate";
                }

                if ($isTs) {
                    $sql .= " , CASE WHEN ucl.data_type = 'ts' AND ucl.code = '${code}' AND tsn.${number} != 0 THEN ucl.viewing_number::real / tsn.${number} * 100  ELSE 0 END AS ts_${code}_viewing_rate";
                } else {
                    $sql .= " , 0 AS ts_${code}_viewing_rate";
                }

                if ($isGross) {
                    $sql .= " , CASE WHEN ucl.data_type = 'gross' AND ucl.code = '${code}' AND tsn.${number} != 0 THEN ucl.viewing_number::real / tsn.${number} * 100  ELSE 0 END AS gross_${code}_viewing_rate";
                } else {
                    $sql .= " , 0 AS gross_${code}_viewing_rate";
                }

                if ($isTotal) {
                    $sql .= " , CASE WHEN ucl.data_type = 'total' AND ucl.code = '${code}' AND tsn.${number} != 0 THEN ucl.viewing_number::real / tsn.${number} * 100  ELSE 0 END AS total_${code}_viewing_rate";
                } else {
                    $sql .= " , 0 AS total_${code}_viewing_rate";
                }

                if ($isRtTotal) {
                    $sql .= " , CASE WHEN ucl.data_type = 'rt_total' AND ucl.code = '${code}' AND tsn.${number} != 0 THEN ucl.viewing_number::real / tsn.${number} * 100  ELSE 0 END AS rt_total_${code}_viewing_rate";
                } else {
                    $sql .= " , 0 AS rt_total_${code}_viewing_rate";
                }
            }
            $code = sprintf('%s_%s', $selectedPersonalName, $sampleCodePrefix);
            $number = sprintf('%s_%s', $selectedPersonalName, $sampleCodeNumberPrefix);
            $sql .= " , CASE WHEN ucl.data_type = 'rt' AND ucl.code = '${code}' THEN ucl.viewing_number ELSE 0 END AS rt_${code}_viewing_number ";
            $sql .= " , CASE WHEN ucl.data_type = 'ts' AND ucl.code = '${code}' THEN ucl.viewing_number ELSE 0 END AS ts_${code}_viewing_number";
            $sql .= " , CASE WHEN ucl.data_type = 'gross' AND ucl.code = '${code}' THEN ucl.viewing_number ELSE 0 END AS gross_${code}_viewing_number";
            $sql .= " , CASE WHEN ucl.data_type = 'total' AND ucl.code = '${code}' THEN ucl.viewing_number ELSE 0 END AS total_${code}_viewing_number";
            $sql .= " , CASE WHEN ucl.data_type = 'rt_total' AND ucl.code = '${code}' THEN ucl.viewing_number ELSE 0 END AS rt_total_${code}_viewing_number";

            if ($isRt || $isGross) {
                $sql .= "     , FIRST_VALUE(rtn.${number}) OVER (PARTITION BY date, company_id, channel_id, product_id ORDER BY c.time_box_id DESC ROWS UNBOUNDED PRECEDING) rt_${number} ";
            } else {
                $sql .= " , 0 AS rt_${number}";
            }

            if ($isTs || $isGross || $isTotal || $isRtTotal) {
                $sql .= "     , FIRST_VALUE(tsn.${number}) OVER (PARTITION BY date, company_id, channel_id, product_id ORDER BY c.time_box_id DESC ROWS UNBOUNDED PRECEDING) ts_${number} ";
            } else {
                $sql .= " , 0 AS ts_${number}";
            }

            $sql .= '  FROM';
            $sql .= '    cm_list c';
            $sql .= '    LEFT JOIN union_cv_list ucl ';
            $sql .= '      ON c.cm_id = ucl.cm_id';
            $sql .= '      AND c.prog_id = ucl.prog_id';
            $sql .= '      AND c.started_at = ucl.started_at';

            if ($isTs || $isGross || $isTotal || $isRtTotal) {
                $sql .= '  LEFT JOIN';
                $sql .= '    ts_numbers tsn';
                $sql .= '  ON';
                $sql .= '    c.time_box_id = tsn.time_box_id ';
            }

            if ($isRt) {
                $sql .= '  LEFT JOIN';
                $sql .= '    rt_numbers rtn';
                $sql .= '  ON';
                $sql .= '    c.time_box_id = rtn.time_box_id ';
            }
            $sql .= ' )';
            $sql .= '  SELECT';
            $sql .= '    c.date';
            $sql .= '    , c.started_at';
            $sql .= '    , c.time_box_id';
            $sql .= '    , c.prog_id';
            $sql .= '    , c.duration';
            $sql .= '    , c.cm_type';
            $sql .= '    , c.cm_id';
            $sql .= '    , c.program_title';
            $sql .= '    , c.channel_id';
            $sql .= '    , c.company_id';
            $sql .= '    , c.product_id';
            $sql .= '    , c.setting';
            $sql .= '    , c.talent';
            $sql .= '    , c.bgm';
            $sql .= '    , c.memo';
            $sql .= '    , c.total_cnt ';
            $sql .= '    , c.total_duration ';
            $sql .= '    , c.personal_viewing_rate';
            $sql .= '    , c.household_viewing_rate';
            $sql .= '    , c.ts_personal_viewing_rate';
            $sql .= '    , c.ts_personal_total_viewing_rate';
            $sql .= '    , c.ts_personal_gross_viewing_rate';
            $sql .= '    , c.ts_personal_rt_total_viewing_rate';
            $sql .= '    , c.ts_household_viewing_rate';
            $sql .= '    , c.ts_household_total_viewing_rate';
            $sql .= '    , c.ts_household_gross_viewing_rate';

            for ($i = 0; $i < $codeNumber; $i++) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                $sql .= "    , MAX(rt_${code}_viewing_rate) rt_${code}_viewing_rate";
                $sql .= "    , MAX(ts_${code}_viewing_rate) ts_${code}_viewing_rate";
                $sql .= "    , MAX(gross_${code}_viewing_rate) gross_${code}_viewing_rate";
                $sql .= "    , MAX(total_${code}_viewing_rate) total_${code}_viewing_rate";
                $sql .= "    , MAX(rt_total_${code}_viewing_rate) rt_total_${code}_viewing_rate";
            }

            $code = sprintf('%s_%s', $selectedPersonalName, $sampleCodePrefix);
            $number = sprintf('%s_%s', $selectedPersonalName, $sampleCodeNumberPrefix);
            $sql .= "    , CASE WHEN rt_${number} != 0 THEN MAX(rt_${code}_viewing_number)::real * 100 * (CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END) / rt_${number} ELSE 0 END rt_${code}_viewing_rate";
            $sql .= "    , CASE WHEN ts_${number} != 0 THEN MAX(ts_${code}_viewing_number)::real * 100 * (CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END) / ts_${number} ELSE 0 END ts_${code}_viewing_rate";
            $sql .= "    , CASE WHEN ts_${number} != 0 THEN MAX(gross_${code}_viewing_number)::real * 100 * (CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END) / ts_${number} ELSE 0 END gross_${code}_viewing_rate";
            $sql .= "    , CASE WHEN ts_${number} != 0 THEN MAX(total_${code}_viewing_number)::real * 100 * (CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END) / ts_${number}  ELSE 0 END total_${code}_viewing_rate";
            $sql .= "    , CASE WHEN ts_${number} != 0 THEN MAX(rt_total_${code}_viewing_number)::real * 100 * (CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END) / ts_${number}  ELSE 0 END rt_total_${code}_viewing_rate";

            $sql .= '  FROM';
            $sql .= '    vertical c';
            $sql .= '  GROUP BY';
            $sql .= '    c.date,';
            $sql .= '    c.started_at,';
            $sql .= '    c.time_box_id,';
            $sql .= '    c.prog_id,';
            $sql .= '    c.duration,';
            $sql .= '    c.cm_type,';
            $sql .= '    c.cm_id,';
            $sql .= '    c.program_title,';
            $sql .= '    c.channel_id,';
            $sql .= '    c.company_id,';
            $sql .= '    c.product_id,';
            $sql .= '    c.setting,';
            $sql .= '    c.talent,';
            $sql .= '    c.bgm,';
            $sql .= '    c.memo,';
            $sql .= '    c.total_cnt, ';
            $sql .= '    c.total_duration, ';
            $sql .= '    c.personal_viewing_rate,';
            $sql .= '    c.household_viewing_rate,';
            $sql .= '    c.ts_personal_viewing_rate,';
            $sql .= '    c.ts_personal_total_viewing_rate,';
            $sql .= '    c.ts_personal_gross_viewing_rate,';
            $sql .= '    c.ts_personal_rt_total_viewing_rate,';
            $sql .= '    c.ts_household_viewing_rate,';
            $sql .= '    c.ts_household_total_viewing_rate,';
            $sql .= '    c.ts_household_gross_viewing_rate, ';
            $sql .= "    rt_${number}, ";
            $sql .= "    ts_${number} ";
            $this->select($sql, $rateBindings);

            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE grp_list AS ';
            $sql .= '   SELECT ';
            $sql .= '       rl.date ';

            for ($i = 0; $i < $codeNumber; $i++) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                $sql .= "   , SUM(ROUND(rt_${code}_viewing_rate::real * CASE WHEN :conv15SecFlag = 1 THEN duration::numeric / 15 ELSE 1 END, 1)) rt_${code}_grp ";
                $sql .= "   , SUM(ROUND(ts_${code}_viewing_rate::real * CASE WHEN :conv15SecFlag = 1 THEN duration::numeric / 15 ELSE 1 END, 1)) ts_${code}_grp ";
                $sql .= "   , SUM(ROUND(gross_${code}_viewing_rate::real * CASE WHEN :conv15SecFlag = 1 THEN duration::numeric / 15 ELSE 1 END, 1)) gross_${code}_grp ";
                $sql .= "   , SUM(ROUND(total_${code}_viewing_rate::real * CASE WHEN :conv15SecFlag = 1 THEN duration::numeric / 15 ELSE 1 END, 1)) total_${code}_grp ";
                $sql .= "   , SUM(ROUND(rt_total_${code}_viewing_rate::real * CASE WHEN :conv15SecFlag = 1 THEN duration::numeric / 15 ELSE 1 END, 1)) rt_total_${code}_grp ";
            }
            $code = sprintf('%s_%s', $selectedPersonalName, $sampleCodePrefix);
            $sql .= "     , SUM(rt_${code}_viewing_rate) rt_${code}_grp ";
            $sql .= "     , SUM(ts_${code}_viewing_rate) ts_${code}_grp ";
            $sql .= "     , SUM(gross_${code}_viewing_rate) gross_${code}_grp ";
            $sql .= "     , SUM(total_${code}_viewing_rate) total_${code}_grp ";
            $sql .= "     , SUM(rt_total_${code}_viewing_rate) rt_total_${code}_grp ";

            $sql .= '     , rl.personal_viewing_rate ';
            $sql .= '     , rl.ts_personal_viewing_rate ';
            $sql .= '     , rl.ts_personal_total_viewing_rate ';
            $sql .= '     , rl.ts_personal_gross_viewing_rate ';
            $sql .= '     , rl.ts_personal_rt_total_viewing_rate ';
            $sql .= '     , rl.household_viewing_rate ';
            $sql .= '     , rl.ts_household_viewing_rate ';
            $sql .= '     , rl.ts_household_total_viewing_rate ';
            $sql .= '     , rl.ts_household_gross_viewing_rate ';

            $sql .= '     , rl.total_duration ';
            $sql .= '     , rl.total_cnt ';
            $sql .= '     , rl.company_id ';
            $sql .= '     , rl.channel_id ';
            $sql .= '     , rl.product_id  ';
            $sql .= '     , rl.time_box_id  ';
            $sql .= '   FROM ';
            $sql .= '     horizontal rl  ';
            $sql .= '   GROUP BY ';
            $sql .= '       rl.date ';
            $sql .= '     , rl.company_id ';
            $sql .= '     , rl.personal_viewing_rate ';
            $sql .= '     , rl.ts_personal_viewing_rate ';
            $sql .= '     , rl.ts_personal_total_viewing_rate ';
            $sql .= '     , rl.ts_personal_gross_viewing_rate ';
            $sql .= '     , rl.ts_personal_rt_total_viewing_rate ';
            $sql .= '     , rl.household_viewing_rate ';
            $sql .= '     , rl.ts_household_viewing_rate ';
            $sql .= '     , rl.ts_household_total_viewing_rate ';
            $sql .= '     , rl.ts_household_gross_viewing_rate ';
            $sql .= '     , rl.total_cnt ';
            $sql .= '     , rl.total_duration ';
            $sql .= '     , rl.channel_id ';
            $sql .= '     , rl.product_id ';
            $sql .= '     , rl.time_box_id  ';
            $this->select($sql, $rateBindings);
        }
        $sql = '';
        $sql .= ' CREATE TEMPORARY TABLE with_count AS ';
        $sql .= ' SELECT ';
        $sql .= '   DENSE_RANK() OVER (ORDER BY date, c.name, p.name) count ';
        $sql .= '   , DENSE_RANK() OVER (ORDER BY date DESC, c.name DESC, p.name DESC) + DENSE_RANK() OVER (ORDER BY date, c.name, p.name) - 1 rowcount ';
        $sql .= '   , date ';
        $sql .= '   , gl.company_id ';
        $sql .= '   , gl.product_id ';
        $sql .= '   , c.name ';
        $sql .= '   , p.name product_name ';
        $sql .= '   , total_cnt ';
        $sql .= '   , total_duration ';
        $sql .= '   , display_name ';
        $sql .= '   , gl.channel_id ';
        $sql .= '   , personal_viewing_rate';
        $sql .= '   , ts_personal_viewing_rate ';
        $sql .= '   , ts_personal_total_viewing_rate ';
        $sql .= '   , ts_personal_gross_viewing_rate ';
        $sql .= '   , ts_personal_rt_total_viewing_rate ';
        $sql .= '   , household_viewing_rate';
        $sql .= '   , ts_household_viewing_rate ';
        $sql .= '   , ts_household_total_viewing_rate ';
        $sql .= '   , ts_household_gross_viewing_rate ';

        if (count($divCodes) > 0) {
            for ($i = 0; $i < $codeNumber; $i++) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                $sql .= "   , SUM(COALESCE(rt_${code}_grp, 0)) rt_${code}_grp ";
                $sql .= "   , SUM(COALESCE(ts_${code}_grp, 0)) ts_${code}_grp ";
                $sql .= "   , SUM(COALESCE(gross_${code}_grp, 0)) gross_${code}_grp ";
                $sql .= "   , SUM(COALESCE(total_${code}_grp, 0)) total_${code}_grp ";
                $sql .= "   , SUM(COALESCE(rt_total_${code}_grp, 0)) rt_total_${code}_grp ";
            }
            $code = sprintf('%s_%s', $selectedPersonalName, $sampleCodePrefix);
            $sql .= "   , ROUND( SUM(ROUND(rt_${code}_grp, 2)), 1) rt_${code}_grp ";
            $sql .= "   , ROUND( SUM(ROUND(ts_${code}_grp, 2)), 1) ts_${code}_grp ";
            $sql .= "   , ROUND( SUM(ROUND(gross_${code}_grp, 2)), 1) gross_${code}_grp ";
            $sql .= "   , ROUND( SUM(ROUND(total_${code}_grp, 2)), 1) total_${code}_grp ";
            $sql .= "   , ROUND( SUM(ROUND(rt_total_${code}_grp, 2)), 1) rt_total_${code}_grp ";
        }

        $sql .= ' FROM ';

        if (count($divCodes) > 0) {
            $sql .= '   grp_list gl ';
        } else {
            $sql .= '   cm_list gl ';
        }
        $sql .= ' INNER JOIN products p  ';
        $sql .= '   ON gl.product_id = p.id  ';
        $sql .= ' INNER JOIN companies c  ';
        $sql .= '   ON gl.company_id = c.id ';
        $sql .= ' INNER JOIN channels ch  ';
        $sql .= '   ON gl.channel_id = ch.id ';
        $sql .= ' GROUP BY ';
        $sql .= '     date ';
        $sql .= '   , gl.company_id ';
        $sql .= '   , gl.product_id ';
        $sql .= '   , p.name ';
        $sql .= '   , c.name ';
        $sql .= '   , display_name ';
        $sql .= '   , gl.channel_id ';
        $sql .= '   , total_cnt ';
        $sql .= '   , total_duration ';
        $sql .= '   , personal_viewing_rate';
        $sql .= '   , ts_personal_viewing_rate ';
        $sql .= '   , ts_personal_total_viewing_rate ';
        $sql .= '   , ts_personal_gross_viewing_rate ';
        $sql .= '   , ts_personal_rt_total_viewing_rate ';
        $sql .= '   , household_viewing_rate';
        $sql .= '   , ts_household_viewing_rate ';
        $sql .= '   , ts_household_total_viewing_rate ';
        $sql .= '   , ts_household_gross_viewing_rate ';
        $sql .= ' ORDER BY ';
        $sql .= '     date ';
        $sql .= '   , gl.company_id ';
        $sql .= '   , gl.product_id ';
        $sql .= '   , gl.channel_id  ';
        $this->select($sql);

        $sql = '';
        $resultBindings = [];
        // 全局表示フラグ
        if ($allChannels == 'true') {
            $sql .= ' WITH result AS ( ';
        }
        $sql .= ' SELECT ';
        $sql .= '     date ';
        $sql .= '   , company_id ';
        $sql .= '   , product_id ';
        $sql .= '   , name ';
        $sql .= '   , product_name ';
        $sql .= '   , total_cnt ';
        $sql .= '   , total_duration ';
        $sql .= '   , display_name ';
        $sql .= '   , channel_id ';
        $sql .= '   , COALESCE(personal_viewing_rate, 0) rt_personal_viewing_grp ';
        $sql .= '   , COALESCE(ts_personal_viewing_rate, 0) ts_personal_viewing_grp ';
        $sql .= '   , COALESCE(ts_personal_total_viewing_rate, 0) total_personal_viewing_grp ';
        $sql .= '   , COALESCE(ts_personal_gross_viewing_rate, 0) gross_personal_viewing_grp ';
        $sql .= '   , COALESCE(ts_personal_rt_total_viewing_rate, 0) rt_total_personal_viewing_grp ';
        $sql .= '   , COALESCE(household_viewing_rate, 0) rt_household_viewing_grp ';
        $sql .= '   , COALESCE(ts_household_viewing_rate, 0) ts_household_viewing_grp ';
        $sql .= '   , COALESCE(ts_household_total_viewing_rate, 0) total_household_viewing_grp ';
        $sql .= '   , COALESCE(ts_household_gross_viewing_rate, 0) gross_household_viewing_grp ';

        if (count($divCodes) > 0) {
            foreach ($divCodes as $key => $value) {
                $divCode = $divisionKey . $value;

                if ($isConditionCross && $value === 'condition_cross') {
                    $divCode = 'condition_cross';
                }
                $code = sprintf('%s%02d', $sampleCodePrefix, $key);

                foreach ($dataTypes as $type) {
                    if (!in_array($type, $dataType)) {
                        continue;
                    }

                    switch ($type) {
                        case $rtType:
                            $sql .= "    , COALESCE(rt_${code}_grp, 0) rt_${divCode} ";
                            break;
                        case $tsType:
                            $sql .= "    , COALESCE(ts_${code}_grp, 0) ts_${divCode} ";
                            break;
                        case $grossType:
                            $sql .= "    , COALESCE(gross_${code}_grp, rt_${code}_grp, 0) gross_${divCode} ";
                            break;
                        case $totalType:
                            $sql .= "    , COALESCE(total_${code}_grp, 0) total_${divCode} ";
                            break;
                        case $rtTotalType:
                            $sql .= "    , COALESCE(rt_total_${code}_grp, 0) rt_total_${divCode} ";
                            break;
                    }
                }
            }
        }

        if ($csvFlag === '0') {
            $sql .= '   , rowcount  ';
        }
        $sql .= ' FROM ';
        $sql .= '   with_count ';

        if ($csvFlag === '0') {
            $resultBindings[':from'] = ($page - 1) * $length + 1;
            $resultBindings[':to'] = $page * $length;
            $sql .= ' WHERE ';
            $sql .= '   count BETWEEN :from AND :to ';
        }

        if ($allChannels == 'true') {
            $channelBind = $this->createArrayBindParam('channels', [
                'channels' => $channels,
            ], $resultBindings);
            $sql .= ' ) ';
            $sql .= '   SELECT ';
            $sql .= '     date ';
            $sql .= '   , name ';
            $sql .= '   , product_name ';
            $sql .= '   , all_ch.display_name ';
            $sql .= '   , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(total_cnt, 0) ELSE 0 END) total_cnt';
            $sql .= '   , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(total_duration, 0) ELSE 0 END) total_duration ';

            if (count($divCodes) > 0) {
                foreach ($divCodes as $key => $value) {
                    $divCode = $divisionKey . $value;

                    if ($isConditionCross && $value === 'condition_cross') {
                        $divCode = 'condition_cross';
                    }
                    $code = sprintf('%s%02d', $sampleCodePrefix, $key);

                    foreach ($dataTypes as $type) {
                        if (!in_array($type, $dataType)) {
                            continue;
                        }

                        switch ($type) {
                            case $rtType:
                                $sql .= "    , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(rt_${divCode},0) ELSE 0 END) rt_${divCode} ";
                                break;
                            case $tsType:
                                $sql .= "    , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(ts_${divCode},0) ELSE 0 END) ts_${divCode} ";
                                break;
                            case $grossType:
                                $sql .= "    , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(gross_${divCode},0) ELSE 0 END) gross_${divCode} ";
                                break;
                            case $totalType:
                                $sql .= "    , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(total_${divCode},0) ELSE 0 END) total_${divCode} ";
                                break;
                            case $rtTotalType:
                                $sql .= "    , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(rt_total_${divCode},0) ELSE 0 END) rt_total_${divCode} ";
                                break;
                        }
                    }
                }
            }

            $sql .= '   , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(rt_personal_viewing_grp, 0) ELSE 0 END)  rt_personal_viewing_grp ';
            $sql .= '   , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(ts_personal_viewing_grp, 0) ELSE 0 END)  ts_personal_viewing_grp ';
            $sql .= '   , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(total_personal_viewing_grp, 0) ELSE 0 END)  total_personal_viewing_grp ';
            $sql .= '   , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(gross_personal_viewing_grp, 0) ELSE 0 END)  gross_personal_viewing_grp ';
            $sql .= '   , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(rt_total_personal_viewing_grp, 0) ELSE 0 END)  rt_total_personal_viewing_grp ';

            $sql .= '   , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(rt_household_viewing_grp, 0) ELSE 0 END)  rt_household_viewing_grp ';
            $sql .= '   , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(ts_household_viewing_grp, 0) ELSE 0 END)  ts_household_viewing_grp ';
            $sql .= '   , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(total_household_viewing_grp, 0) ELSE 0 END)  total_household_viewing_grp ';
            $sql .= '   , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(gross_household_viewing_grp, 0) ELSE 0 END)  gross_household_viewing_grp ';

            $sql .= '   FROM ';
            $sql .= '     result ';
            $sql .= '   CROSS JOIN ';
            $sql .= '   ( ';
            $sql .= '     SELECT ';
            $sql .= '       id, ';
            $sql .= '       display_name ';
            $sql .= '     FROM ';
            $sql .= '       channels ch  ';
            $sql .= '     WHERE ';
            $sql .= '       id IN (' . implode(',', $channelBind) . ')  ';
            $sql .= '   ) all_ch ';
            $sql .= ' GROUP BY ';
            $sql .= '   date ';
            $sql .= '   , company_id ';
            $sql .= '   , product_id ';
            $sql .= '   , name ';
            $sql .= '   , product_name ';
            $sql .= '   , all_ch.id ';
            $sql .= '   , all_ch.display_name ';
            $sql .= ' ORDER BY ';
            $sql .= '   date ';
            $sql .= '   , company_id ';
            $sql .= '   , product_id ';
            $sql .= '   , all_ch.id ';
        } else {
            $sql .= ' ORDER BY ';
            $sql .= '   date ';
            $sql .= '   , company_id ';
            $sql .= '   , product_id ';
            $sql .= '   , channel_id ';
        }
        $result = $this->select($sql, $resultBindings);

        return $result;
    }

    public function createCommonTempTables(bool $isConditionCross, String $startDate, String $endDate, int $regionId, String $division, String $divisionKey, ?array $codes, array $codeList, array $conditionCross, ?array $companyIds, ?array $companyBind, array $rsTimeBoxIds, array $dataType, string $sampleCodePrefix, array $dataTypeFlags, string $sampleCodeNumberPrefix, string $selectedPersonalName, int $codeNumber): void
    {
        $divCodes = [];

        if ($isConditionCross) {
            $divCodes[] = 'condition_cross';
        } else {
            $divCodes = array_filter($codes, function ($v, $k) {
                return $v != 'personal' && $v != 'household';
            }, ARRAY_FILTER_USE_BOTH);
        }

        // リアルタイムのtime_box_idによるwhere句
        $timeBoxIdWhere = '';
        $sampleBindings = [];

        if (count($rsTimeBoxIds) > 0) {
            $bindTimeBoxIds = $this->createArrayBindParam('time_box_ids', ['time_box_ids' => $rsTimeBoxIds], $sampleBindings);
            $timeBoxIdWhere .= ' tbp.time_box_id IN (' . implode(',', $bindTimeBoxIds) . ') ';
        }

        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = array_values($dataTypeFlags);

        $unionViewerBindings = [];
        $unionViewerBindings[':startDate'] = $startDate;
        $unionViewerBindings[':endDate'] = $endDate;
        $progStart = (new Carbon($startDate))->subDay();
        $progEnd = (new Carbon($endDate))->addDay();
        $unionViewerBindings[':progStartDate'] = $progStart->format('Ymd') . '0000000000';
        $unionViewerBindings[':progEndDate'] = $progEnd->format('Ymd') . '9999999999';

        if (count($companyIds) > 0) {
            $companyBind = $this->createArrayBindParam('companyIds', [
                'companyIds' => $companyIds,
            ], $unionViewerBindings);
        }

        if ($isRt) {
            $this->createRtSampleTempTable($isConditionCross, $conditionCross, $division, $divCodes, $rsTimeBoxIds, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName, true, $codeNumber);
        }

        if ($isTs || $isGross || $isTotal || $isRtTotal) {
            $this->createTsSampleTempTable($isConditionCross, $conditionCross, $division, $divCodes, $rsTimeBoxIds, $regionId, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName, true, $codeNumber);
        }

        if ($isTs || $isGross || $isTotal || $isRtTotal) {
            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE ts_cv_list ( ';
            $sql .= '   cm_id VARCHAR(32) ';
            $sql .= '   , prog_id VARCHAR(32) ';
            $sql .= '   , started_at datetime ';
            $sql .= '   , time_box_id int ';
            $sql .= '   , paneler_id int ';
            $sql .= '   , views int ';
            $sql .= ' )DISTSTYLE ALL SORTKEY(time_box_id, paneler_id); ';
            $this->select($sql);

            $sql = '';
            $sql .= ' INSERT INTO ts_cv_list SELECT ';
            $sql .= '   c.cm_id ';
            $sql .= '   , c.prog_id ';
            $sql .= '   , c.started_at ';
            $sql .= '   , c.time_box_id ';
            $sql .= '   , cv.paneler_id ';
            $sql .= '   , cv.views ';
            $sql .= ' FROM ';
            $sql .= '   cm_list c ';
            $sql .= ' INNER JOIN ';
            $sql .= '   ts_cm_viewers cv  ';
            $sql .= ' ON ';
            $sql .= '   c.cm_id = cv.cm_id AND c.started_at = cv.started_at AND c.prog_id = cv.prog_id  ';
            $sql .= ' WHERE ';
            $sql .= '   cv.date BETWEEN :startDate AND :endDate ';

            if (count($companyIds) > 0) {
                $sql .= ' AND cv.company_id IN (' . implode(',', $companyBind) . ')';
            }
            $sql .= '      AND cv.prog_id BETWEEN :progStartDate AND :progEndDate ';
            $sql .= '      AND cv.c_index = 7';
            $sql .= ';';
            $this->insertTemporaryTable($sql, $unionViewerBindings);

            $sql = '';
            $sql .= ' ANALYZE ts_cv_list; ';
            $this->select($sql);
        }

        if ($isRt || $isGross || $isRtTotal) {
            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE cv_list ( ';
            $sql .= '   cm_id VARCHAR(32) ';
            $sql .= '   , prog_id VARCHAR(32) ';
            $sql .= '   , started_at datetime ';
            $sql .= '   , time_box_id int ';
            $sql .= '   , paneler_id int ';
            $sql .= ' )DISTSTYLE ALL SORTKEY(paneler_id); ';
            $this->select($sql);

            $sql = '';
            $sql .= ' INSERT INTO cv_list SELECT ';
            $sql .= '   c.cm_id ';
            $sql .= '   , c.prog_id ';
            $sql .= '   , c.started_at ';
            $sql .= '   , c.time_box_id ';
            $sql .= '   , cv.paneler_id ';
            $sql .= ' FROM ';
            $sql .= '   cm_list c ';
            $sql .= ' LEFT JOIN ';
            $sql .= '   cm_viewers cv  ';
            $sql .= ' ON ';
            $sql .= '   c.cm_id = cv.cm_id AND c.started_at = cv.started_at AND c.prog_id = cv.prog_id  ';
            $sql .= ' WHERE ';
            $sql .= '        cv.date BETWEEN :startDate AND :endDate ';

            if (count($companyIds) > 0) {
                $sql .= ' AND cv.company_id IN (' . implode(',', $companyBind) . ')';
            }
            $sql .= '      AND cv.prog_id BETWEEN :progStartDate AND :progEndDate ';
            $sql .= ';';
            $this->insertTemporaryTable($sql, $unionViewerBindings);

            $sql = '';
            $sql .= ' ANALYZE cv_list; ';
            $this->select($sql);
        }

        if ($isGross || $isRtTotal) {
            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE rt_total_cv_list ( ';
            $sql .= '   cm_id VARCHAR(32) ';
            $sql .= '   , prog_id VARCHAR(32) ';
            $sql .= '   , started_at datetime ';
            $sql .= '   , time_box_id int ';
            $sql .= '   , paneler_id int ';
            $sql .= '   , views int ';
            $sql .= ' )DISTSTYLE ALL SORTKEY(time_box_id, paneler_id); ';
            $this->select($sql);

            $sql = '';
            $sql .= ' INSERT INTO rt_total_cv_list ';
            $sql .= '   SELECT ';
            $sql .= '     cv.cm_id ';
            $sql .= '     , cv.prog_id ';
            $sql .= '     , cv.started_at ';
            $sql .= '     , cv.time_box_id ';
            $sql .= '     , cv.paneler_id ';
            $sql .= '     , 1 views ';
            $sql .= '   FROM ';
            $sql .= '     cv_list cv ';
            $sql .= '   UNION ALL ';
            $sql .= '   SELECT ';
            $sql .= '     cv.cm_id ';
            $sql .= '     , cv.prog_id ';
            $sql .= '     , cv.started_at ';
            $sql .= '     , cv.time_box_id ';
            $sql .= '     , cv.paneler_id ';
            $sql .= '     , cv.views ';
            $sql .= '   FROM ';
            $sql .= '     ts_cv_list cv; ';
            $this->insertTemporaryTable($sql);

            $sql = '';
            $sql .= ' ANALYZE rt_total_cv_list; ';
            $this->select($sql);
        }

        // union
        $unionSqlArr = [];

        if ($isRt) {
            $selectedPersonals = [];

            for ($i = 0; $i < count($divCodes); $i++) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                $unionSql = '';
                $unionSql .= " SELECT cm_id, prog_id, started_at, 'rt' data_type, '${code}' code, COUNT(paneler_id) viewing_number ";
                $unionSql .= ' FROM cv_list cl ';
                $unionSql .= " WHERE EXISTS(SELECT 1 FROM samples s WHERE s.paneler_id = cl.paneler_id AND s.time_box_id = cl.time_box_id AND ${code} = 1) ";
                $unionSql .= ' GROUP BY cm_id, prog_id, started_at ';
                $unionSqlArr[] = $unionSql;
                $selectedPersonals[] = $unionSql;
            }
        }

        if ($isTs) {
            $selectedPersonals = [];

            for ($i = 0; $i < count($divCodes); $i++) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                $unionSql = '';
                $unionSql .= " SELECT cm_id, prog_id, started_at, 'ts' data_type, '${code}' code, COUNT(paneler_id) viewing_number ";
                $unionSql .= ' FROM ts_cv_list cl ';
                $unionSql .= " WHERE EXISTS(SELECT 1 FROM ts_samples s WHERE s.paneler_id = cl.paneler_id AND s.time_box_id = cl.time_box_id AND ${code} = 1) ";
                $unionSql .= ' GROUP BY cm_id, prog_id, started_at ';
                $unionSqlArr[] = $unionSql;
                $selectedPersonals[] = $unionSql;
            }
        }

        if ($isTotal) {
            $selectedPersonals = [];

            for ($i = 0; $i < count($divCodes); $i++) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                $unionSql = '';
                $unionSql .= " SELECT cm_id, prog_id, started_at, 'total' data_type, '${code}' code, SUM(views) viewing_number ";
                $unionSql .= ' FROM ts_cv_list cl ';
                $unionSql .= " WHERE EXISTS(SELECT 1 FROM ts_samples s WHERE s.paneler_id = cl.paneler_id AND s.time_box_id = cl.time_box_id AND ${code} = 1) ";
                $unionSql .= ' GROUP BY cm_id, prog_id, started_at ';
                $unionSqlArr[] = $unionSql;
                $selectedPersonals[] = $unionSql;
            }
        }

        if ($isGross) {
            $selectedPersonals = [];

            for ($i = 0; $i < count($divCodes); $i++) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                $unionSql = '';
                $unionSql .= " SELECT cm_id, prog_id, started_at, 'gross' data_type, '${code}' code, COUNT(DISTINCT paneler_id) viewing_number ";
                $unionSql .= ' FROM rt_total_cv_list cl ';
                $unionSql .= " WHERE EXISTS(SELECT * FROM ts_samples s WHERE s.paneler_id = cl.paneler_id AND s.time_box_id = cl.time_box_id AND ${code} = 1) ";
                $unionSql .= ' GROUP BY cm_id, prog_id, started_at';
                $unionSqlArr[] = $unionSql;
                $selectedPersonals[] = $unionSql;
            }
        }

        if ($isRtTotal) {
            $selectedPersonals = [];

            for ($i = 0; $i < count($divCodes); $i++) {
                $code = sprintf('%s%02d', $sampleCodePrefix, $i);
                $unionSql = '';
                $unionSql .= " SELECT cm_id, prog_id, started_at, 'rt_total' data_type, '${code}' code, SUM(views) viewing_number ";
                $unionSql .= ' FROM rt_total_cv_list cl ';
                $unionSql .= " WHERE EXISTS(SELECT * FROM ts_samples s WHERE s.paneler_id = cl.paneler_id AND s.time_box_id = cl.time_box_id AND ${code} = 1) ";
                $unionSql .= ' GROUP BY cm_id, prog_id, started_at ';
                $unionSqlArr[] = $unionSql;
                $selectedPersonals[] = $unionSql;
            }
        }

        $sql = '';
        $sql .= ' CREATE TEMPORARY TABLE union_cv_list ( ';
        $sql .= '   cm_id VARCHAR(32) ';
        $sql .= '   , prog_id VARCHAR(32) ';
        $sql .= '   , started_at datetime ';
        $sql .= '   , data_type VARCHAR(32) ';
        $sql .= '   , code varchar(255) ';
        $sql .= '   , viewing_number int ';
        $sql .= ' ) DISTSTYLE ALL SORTKEY (cm_id); ';
        $this->select($sql);

        if (count($unionSqlArr)) {
            $sql = '';
            $sql .= ' INSERT INTO union_cv_list ';
            $sql .= implode(' UNION ALL ', $unionSqlArr);
            $this->insertTemporaryTable($sql);

            $sql = '';
            $sql .= ' ANALYZE union_cv_list; ';
            $this->select($sql);
        }
    }

    /**
     * CMリスト検索の共通WHERE句.
     *
     * @param unknown $bindings
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param string $cmType
     * @param string $cmSeconds
     * @param array $progIds
     * @param int $regionId
     * @param string $division
     * @param array $codes
     * @param array $conditionCross
     * @param array $companyIds
     * @param array $productIds
     * @param array $cmIds
     * @param array $channels
     * @param bool $conv_15_sec_flag
     * @param bool $straddlingFlg
     * @param bool $isConditionCross
     * @param ?bool $conv15SecFlag
     * @return array[]|string[]
     */
    private function createListWhere(array &$bindings, bool $isConditionCross, String $startDate, String $endDate, String $startTime, String $endTime, ?String $cmType, ?String $cmSeconds, ?array $progIds, int $regionId, String $division, ?array $codes, ?array $conditionCross, ?array $companyIds, ?array $productIds, ?array $cmIds, array $channels, ?bool $conv15SecFlag, bool $straddlingFlg): array
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
            // TODO - konno: dead code
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

        // 番組名
        $programBind = $this->createArrayBindParam('progIds', [
            'progIds' => $progIds,
        ], $bindings);

        // 地域コード
        $bindings[':region_id'] = $regionId;

        // 企業ID
        $companyBind = $this->createArrayBindParam('companyIds', [
            'companyIds' => $companyIds,
        ], $bindings);

        // 商品ID
        $productIdsBind = $this->createArrayBindParam('productIds', [
            'productIds' => $productIds,
        ], $bindings);

        // 素材
        $cmIdsBind = $this->createArrayBindParam('cmIds', [
            'cmIds' => $cmIds,
        ], $bindings);

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

        // 番組名
        if (count($programBind) > 0) {
            $sql .= ' AND c.program_title IN (SELECT p.title FROM programs p WHERE p.prog_id IN (' . implode(',', $programBind) . ') )';
        }

        // 企業ID
        if (count($companyBind)) {
            $sql .= ' AND c.company_id IN (' . implode(',', $companyBind) . ')';
        }

        // 商品ID
        if (count($productIdsBind)) {
            $sql .= ' AND c.product_id IN (' . implode(',', $productIdsBind) . ')';
        }
        // CM素材オプション
        if (count($cmIdsBind)) {
            $sql .= ' AND c.cm_id IN (' . implode(',', $cmIdsBind) . ')';
        }

        $sql .= ' AND c.region_id = :region_id ';

        // 広告種別
        if (isset($cmType) && $cmType == '1') {
            $sql .= ' AND c.cm_type = 2';
        } elseif (isset($cmType) && $cmType == '2') {
            $sql .= ' AND c.cm_type IN (0, 1)';
        }
        // CM秒数（1は全CMなので、条件に含めない）
        if ($cmSeconds == '2') {
            $sql .= ' AND c.duration = 15';
        } elseif ($cmSeconds == '3') {
            $sql .= ' AND c.duration > 15';
        }

        return [
            $sql,
            $codeBind,
            $channelBind,
            $programBind,
            $companyBind,
            $productIdsBind,
            $cmIdsBind,
        ];
    }
}
