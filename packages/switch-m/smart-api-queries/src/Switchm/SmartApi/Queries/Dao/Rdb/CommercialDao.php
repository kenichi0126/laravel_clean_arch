<?php

namespace Switchm\SmartApi\Queries\Dao\Rdb;

class CommercialDao extends Dao
{
    /**
     * CMリスト検索.
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param null|string $cmType
     * @param null|string $cmSeconds
     * @param null|array $progIds
     * @param int $regionId
     * @param string $division
     * @param null|array $codes
     * @param null|array $conditionCross
     * @param null|array $companyIds
     * @param null|array $productIds
     * @param null|array $cmIds
     * @param array $channels
     * @param null|array $order
     * @param int $page
     * @param null|int $length
     * @param null|bool $conv_15_sec_flag
     * @param bool $straddlingFlg
     * @param null|array $codeList
     * @param string $csvFlag
     * @param array $dataType
     * @param bool $cmMaterialFlag
     * @param bool $cmTypeFlag
     * @param string $codeNumber
     * @param string $sampleCodePrefix
     * @param string $sampleCodeNumberPrefix
     * @param string $selectedPersonalName
     * @param array $dataTypes
     * @param array $dataTypeFlags
     * @return array
     */
    public function searchList(String $startDate, String $endDate, String $startTime, String $endTime, ?String $cmType, ?String $cmSeconds, ?array $progIds, int $regionId, String $division, ?array $codes, ?array $conditionCross, ?array $companyIds, ?array $productIds, ?array $cmIds, array $channels, ?array $order, int $page, ?int $length, ?bool $conv_15_sec_flag, bool $straddlingFlg, ?array $codeList, string $csvFlag, array $dataType, bool $cmMaterialFlag, bool $cmTypeFlag, string $codeNumber, string $sampleCodePrefix, string $sampleCodeNumberPrefix, string $selectedPersonalName, array $dataTypes, array $dataTypeFlags): array
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
            ) = $this->createListWhere($bindings, $isConditionCross, $startDate, $endDate, $startTime, $endTime, $cmType, $cmSeconds, $progIds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $conv_15_sec_flag, $straddlingFlg);

        // 冗長的なWHERE句用
        list($rsTimeBoxIds, $rsCmIds, $rsPanelers) = $this->createCommercialListWhere($startDate, $endDate, $cmType, $cmSeconds, $progIds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $straddlingFlg);

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
                    array_push($orderArr, "   ${val['column']} ${val['dir']}");
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

        $with = '';
        $count = '';

        $count .= ' WITH commercial_data AS ( ';
        $count .= ' SELECT ';
        $count .= ' 	c.date, ';
        $count .= ' 	c.started_at, ';
        $count .= ' 	c.started_at org_started_at, ';
        $count .= ' 	c.time_box_id, ';
        $count .= ' 	c.duration, ';
        $count .= ' 	c.cm_type, ';
        $count .= ' 	c.cm_id, ';
        $count .= ' 	c.prog_id, ';
        $count .= ' 	c.program_title, ';
        $count .= ' 	c.channel_id, ';
        $count .= ' 	c.company_id, ';
        $count .= ' 	c.product_id, ';
        $count .= ' 	c.setting, ';
        $count .= ' 	c.talent, ';
        $count .= ' 	c.bgm, ';
        $count .= ' 	c.memo, ';

        $count .= ' 	ch.code_name channel_code_name, ';
        $count .= ' 	co.name company_name, ';
        $count .= ' 	p.name cm_name, ';
        $count .= ' 	h.holiday holiday, ';

        $count .= ' 	c.personal_viewing_rate * CASE WHEN :conv15SecFlag = 1 THEN c.duration:: numeric / 15 ELSE 1 END AS rt_personal_viewing_rate, ';
        $count .= ' 	c.ts_personal_viewing_rate * CASE WHEN :conv15SecFlag = 1 THEN c.duration:: numeric / 15 ELSE 1 END AS ts_personal_viewing_rate, ';
        $count .= ' 	c.ts_personal_total_viewing_rate * CASE WHEN :conv15SecFlag = 1 THEN c.duration:: numeric / 15 ELSE 1 END AS total_personal_viewing_rate, ';
        $count .= ' 	COALESCE(c.ts_personal_gross_viewing_rate, c.ts_samples_personal_viewing_rate) * CASE WHEN :conv15SecFlag = 1 THEN c.duration:: numeric / 15 ELSE 1 END AS gross_personal_viewing_rate, ';
        $count .= ' 	COALESCE(c.ts_personal_rt_total_viewing_rate, c.ts_samples_personal_viewing_rate) * CASE WHEN :conv15SecFlag = 1 THEN c.duration:: numeric / 15 ELSE 1 END AS rt_total_personal_viewing_rate, ';
        $count .= ' 	c.household_viewing_rate * CASE WHEN :conv15SecFlag = 1 THEN c.duration:: numeric / 15 ELSE 1 END AS rt_household_viewing_rate, ';
        $count .= ' 	c.ts_household_viewing_rate * CASE WHEN :conv15SecFlag = 1 THEN c.duration:: numeric / 15 ELSE 1 END AS ts_household_viewing_rate, ';
        $count .= ' 	c.ts_household_total_viewing_rate * CASE WHEN :conv15SecFlag = 1 THEN c.duration:: numeric / 15 ELSE 1 END AS total_household_viewing_rate, ';
        $count .= ' 	COALESCE(c.ts_household_gross_viewing_rate, c.ts_samples_household_viewing_rate) * CASE WHEN :conv15SecFlag = 1 THEN c.duration:: numeric / 15 ELSE 1 END AS gross_household_viewing_rate ';

        $count .= ' FROM ';
        $count .= ' 	commercials c ';
        $count .= ' LEFT JOIN channels ch ';
        $count .= " ON ch.region_id = :region_id AND ch.type = 'dt' AND ch.id = c.channel_id ";
        $count .= ' LEFT JOIN companies co ';
        $count .= ' ON c.company_id = co.id ';
        $count .= ' LEFT JOIN products p ';
        $count .= ' ON c.product_id = p.id ';
        $count .= ' LEFT JOIN holidays h ';
        $count .= ' ON c.date = h.holiday ';

        // 共通WHERE句の結合
        $count .= $withWhere;
        $count .= ')';

        $tmpBindings = $bindings;

        $with .= $count . ',';
        $with .= ' commercial_grouped AS( ';
        $with .= ' SELECT ';
        $with .= '  c.cm_id, ';
        $with .= '  c.date, ';
        $with .= '  c.started_at, ';
        $with .= '  c.time_box_id, ';
        $with .= '  c.duration, ';
        $with .= '  c.cm_type, ';
        $with .= '  c.program_title, ';
        $with .= '  c.channel_id, ';
        $with .= '  c.company_id, ';
        $with .= '  c.product_id, ';
        $with .= '  c.setting,   ';
        $with .= '  c.talent, ';
        $with .= '  c.bgm, ';
        $with .= '  c.memo, ';

        $with .= '  c.channel_code_name, ';
        $with .= '  c.company_name, ';
        $with .= '  c.cm_name, ';
        $with .= '  c.holiday, ';

        $with .= '  c.rt_personal_viewing_rate, ';
        $with .= '  c.ts_personal_viewing_rate, ';
        $with .= '  c.total_personal_viewing_rate, ';
        $with .= '  c.gross_personal_viewing_rate, ';
        $with .= '  c.rt_total_personal_viewing_rate, ';

        // コードのcase文作成
        foreach ($codes as $key => $val) {
            $with .= ' 	MAX(CASE WHEN ucr.code = :codes' . $key . ' THEN ucr.viewing_rate END * CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END) AS rt_' . $divisionKey . $val . ', ';
            $with .= ' 	MAX(CASE WHEN ucr.code = :codes' . $key . ' THEN ucr.ts_viewing_rate END * CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END) AS ts_' . $divisionKey . $val . ', ';
            $with .= ' 	MAX(CASE WHEN ucr.code = :codes' . $key . ' THEN ucr.total_viewing_rate END * CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END) AS total_' . $divisionKey . $val . ', ';
            $with .= ' 	MAX(CASE WHEN ucr.code = :codes' . $key . ' THEN ucr.gross_viewing_rate END * CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END) AS gross_' . $divisionKey . $val . ', ';
            $with .= ' 	MAX(CASE WHEN ucr.code = :codes' . $key . ' THEN ucr.rt_total_viewing_rate END * CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END) rt_total_' . $divisionKey . $val . ', ';
        }
        $with .= ' 	c.rt_household_viewing_rate, ';
        $with .= ' 	c.ts_household_viewing_rate, ';
        $with .= ' 	c.total_household_viewing_rate, ';
        $with .= ' 	c.gross_household_viewing_rate, ';

        $with .= ' 	SUM(ucr.viewing_number) rt_viewing_number, ';
        $with .= ' 	SUM(ucr.ts_viewing_number) ts_viewing_number, ';
        $with .= ' 	SUM(ucr.total_viewing_number) total_viewing_number, ';
        $with .= ' 	SUM(ucr.gross_viewing_number) gross_viewing_number, ';
        $with .= ' 	SUM(ucr.rt_total_viewing_number) rt_total_viewing_number ';
        $with .= ' FROM ';
        $with .= ' 	commercial_data c ';
        $with .= ' LEFT JOIN ';
        $with .= ' ( ';
        $with .= '   SELECT ';
        $with .= '     cm_id ';
        $with .= '     , prog_id ';
        $with .= '     , started_at ';
        $with .= '     , division ';
        $with .= '     , code ';
        $with .= '     , MAX(viewing_rate) viewing_rate ';
        $with .= '     , MAX(ts_viewing_rate) ts_viewing_rate ';
        $with .= '     , MAX(total_viewing_rate) total_viewing_rate ';
        $with .= '     , COALESCE(MAX(gross_viewing_rate), MAX(ts_samples_viewing_rate)) gross_viewing_rate ';
        $with .= '     , COALESCE(MAX(rt_total_viewing_rate), MAX(ts_samples_viewing_rate)) rt_total_viewing_rate ';
        $with .= '     , MAX(viewing_number) viewing_number ';
        $with .= '     , MAX(ts_viewing_number) ts_viewing_number ';
        $with .= '     , MAX(total_viewing_number) total_viewing_number ';
        $with .= '     , COALESCE(MAX(gross_viewing_number), MAX(ts_samples_viewing_number)) gross_viewing_number ';
        $with .= '     , COALESCE(MAX(rt_total_viewing_number), MAX(ts_samples_viewing_number)) rt_total_viewing_number ';
        $with .= '   FROM ( ';
        $with .= '     SELECT  ';
        $with .= '       cm_id  ';
        $with .= '       , prog_id  ';
        $with .= '       , started_at  ';
        $with .= '       , division  ';
        $with .= '       , code  ';
        $with .= '       , viewing_rate  ';
        $with .= '       , ts_samples_viewing_rate  ';
        $with .= '       , NULL ts_viewing_rate  ';
        $with .= '       , NULL total_viewing_rate  ';
        $with .= '       , NULL gross_viewing_rate  ';
        $with .= '       , NULL rt_total_viewing_rate  ';
        $with .= '       , viewing_number  ';
        $with .= '       , ts_samples_viewing_number  ';
        $with .= '       , NULL ts_viewing_number  ';
        $with .= '       , NULL total_viewing_number  ';
        $with .= '       , NULL gross_viewing_number  ';
        $with .= '       , NULL rt_total_viewing_number  ';
        $with .= '     FROM  ';
        $with .= '       cm_reports cr  ';
        $with .= '     WHERE  ';
        $with .= '       EXISTS(  ';
        $with .= '        SELECT 1 FROM commercial_data c  ';
        $with .= '          WHERE  ';
        $with .= '            c.cm_id = cr.cm_id  ';
        $with .= '            AND c.prog_id = cr.prog_id  ';
        $with .= '            AND c.started_at = cr.started_at  ';
        $with .= '            AND cr.division = :division AND cr.code IN (' . implode(',', $codeBind) . ')  '; // 属性・コード
        $with .= '       ) ';
        $with .= '     UNION ALL  ';
        $with .= '     SELECT  ';
        $with .= '       cm_id  ';
        $with .= '       , prog_id  ';
        $with .= '       , started_at  ';
        $with .= '       , division  ';
        $with .= '       , code  ';
        $with .= '       , NULL viewing_rate  ';
        $with .= '       , NULL ts_samples_viewing_rate  ';
        $with .= '       , viewing_rate ts_viewing_rate  ';
        $with .= '       , total_viewing_rate  ';
        $with .= '       , gross_viewing_rate  ';
        $with .= '       , rt_total_viewing_rate  ';
        $with .= '       , NULL viewing_number  ';
        $with .= '       , NULL ts_samples_viewing_number  ';
        $with .= '       , viewing_number ts_viewing_number  ';
        $with .= '       , total_viewing_number  ';
        $with .= '       , gross_viewing_number  ';
        $with .= '       , rt_total_viewing_number  ';
        $with .= '     FROM  ';
        $with .= '       ts_cm_reports tcr  ';
        $with .= '     WHERE  ';
        $with .= '       EXISTS(  ';
        $with .= '         SELECT 1 FROM commercial_data c  ';
        $with .= '         WHERE  ';
        $with .= '           c.cm_id = tcr.cm_id  ';
        $with .= '           AND c.prog_id = tcr.prog_id  ';
        $with .= '           AND c.started_at = tcr.started_at  ';
        $with .= '           AND tcr.c_index = 7 AND tcr.division = :division AND tcr.code IN (' . implode(',', $codeBind) . ')  '; // 属性・コード
        $with .= '       )  ';
        $with .= '     ) ucr ';
        $with .= '     GROUP BY ';
        $with .= '       cm_id ';
        $with .= '       , prog_id ';
        $with .= '       , started_at ';
        $with .= '       , division ';
        $with .= '       , code ';
        $with .= ' ) ucr ';
        $with .= ' ON ';
        $with .= '   c.cm_id = ucr.cm_id ';
        $with .= '   AND c.prog_id = ucr.prog_id ';
        $with .= '   AND c.started_at = ucr.started_at ';
        $with .= ' GROUP BY ';
        $with .= '  c.cm_id, ';
        $with .= '  c.date, ';
        $with .= '  c.started_at, ';
        $with .= '  c.org_started_at, ';
        $with .= '  c.time_box_id, ';
        $with .= '  c.duration, ';
        $with .= '  c.cm_type, ';
        $with .= '  c.program_title, ';
        $with .= '  c.channel_id, ';
        $with .= '  c.company_id, ';
        $with .= '  c.product_id, ';
        $with .= '  c.setting,   ';
        $with .= '  c.talent, ';
        $with .= '  c.bgm, ';
        $with .= '  c.memo, ';
        $with .= '  c.channel_code_name, ';
        $with .= '  c.company_name, ';
        $with .= '  c.cm_name, ';
        $with .= '  c.holiday, ';
        $with .= '  c.rt_personal_viewing_rate, ';
        $with .= '  c.ts_personal_viewing_rate, ';
        $with .= '  c.total_personal_viewing_rate, ';
        $with .= '  c.gross_personal_viewing_rate, ';
        $with .= '  c.rt_total_personal_viewing_rate, ';
        $with .= ' 	c.rt_household_viewing_rate, ';
        $with .= ' 	c.ts_household_viewing_rate, ';
        $with .= ' 	c.total_household_viewing_rate, ';
        $with .= ' 	c.gross_household_viewing_rate ';
        $with .= $orderBy . $limit;
        $with .= ' ), ';

        $with .= $this->createSqlForTsTimeBoxAttrNumbers($bindings, $isConditionCross, $regionId, $division, $divisionKey, $codes, $conditionCross, $rsTimeBoxIds);

        $select = '';
        $select .= ' SELECT ';
        $select .= '   date, ';

        if ($csvFlag != 1) {
            $select .= '   holiday, ';
        }
        $select .= '   CASE  ';
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
        $select .= ' dow, ';
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
            $select .= ' bgm, ';
            $select .= ' memo, ';
        }

        $divCodes = array_filter($codes, function ($v, $k) {
            return $v != 'personal' && $v != 'household';
        }, ARRAY_FILTER_USE_BOTH);

        $hasPersonal = $division != 'condition_cross' && in_array('personal', $codes);
        $hasHousehold = $division == 'condition_cross' || in_array('household', $codes);
        $dispSelectedPersonal = 1 < count($divCodes) && count($divCodes) < count($codeList);

        list($rtType, $tsType, $grossType, $totalType, $rtTotalType) = array_values($dataTypes);

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
            // 個人選択計
            if ($dispSelectedPersonal && !$isConditionCross) {
                $select .= " COALESCE(${prefix}personal_total_viewing_rate,0) ${prefix}personal_total_viewing_rate, ";
            }
            // コードのcase文作成
            foreach ($divCodes as $val) {
                $name = $prefix . $divisionKey . $val;
                $select .= "   COALESCE(${name},0) ${name}, ";
            }
            // 世帯
            if ($hasHousehold) {
                $select .= " COALESCE(${prefix}household_viewing_rate,0) ${prefix}household_viewing_rate, ";
            }
        }

        $select .= ' channel_code_name, ';
        $select .= ' program_title ';
        $from = '';
        $from .= ' FROM ';
        $from .= '   ( ';
        $from .= '   SELECT ';
        $from .= '     c.cm_id, ';
        $from .= '     c.date, ';
        $from .= "     DATE_PART('dow' ,c.date) d,";
        $from .= "     lpad(to_char(started_at - interval '5 hours', 'HH24')::numeric + 5 || to_char(started_at, ':MI:SS'),8,'0') as started_at, ";
        $from .= '     c.duration, ';
        $from .= "     CASE WHEN c.cm_type = 0 THEN 'PT' WHEN c.cm_type = 1 THEN 'SB' WHEN c.cm_type = 2 THEN 'TIME' END as cm_type , ";
        $from .= '     c.program_title, ';
        $from .= '     c.setting, ';
        $from .= '     c.talent, ';
        $from .= '     c.bgm, ';
        $from .= '     c.memo, ';
        $from .= '     c.started_at as org_started_at, ';
        $from .= '     ROUND(c.rt_personal_viewing_rate:: numeric,1) rt_personal_viewing_rate, ';
        $from .= '     ROUND(c.ts_personal_viewing_rate:: numeric,1) ts_personal_viewing_rate, ';
        $from .= '     ROUND(c.total_personal_viewing_rate:: numeric,1) total_personal_viewing_rate, ';
        $from .= '     ROUND(c.gross_personal_viewing_rate:: numeric,1) gross_personal_viewing_rate, ';
        $from .= '     ROUND(c.rt_total_personal_viewing_rate:: numeric,1) rt_total_personal_viewing_rate, ';
        // コードのcase文作成
        foreach ($codes as $key => $val) {
            $name = $divisionKey . $val;
            $from .= "     ROUND(c.rt_${name}:: numeric,1) as rt_${name}, ";
            $from .= "     ROUND(c.ts_${name}:: numeric,1) as ts_${name}, ";
            $from .= "     ROUND(c.total_${name}:: numeric,1) as total_${name}, ";
            $from .= "     ROUND(c.gross_${name}:: numeric,1) as gross_${name}, ";
            $from .= "     ROUND(c.rt_total_${name}:: numeric,1) as rt_total_${name}, ";
        }
        // 個人選択計 選択されている属性の視聴数 / 選択されている属性の人数
        if ($dispSelectedPersonal && !$isConditionCross) {
            $from .= '   ROUND( ((c.rt_viewing_number::numeric / (SELECT SUM(tban.number) FROM time_box_attr_numbers tban WHERE c.time_box_id = tban.time_box_id AND tban.division = :division AND tban.code IN (' . implode(',', $codeBind) . ') GROUP BY tban.time_box_id )::numeric * 100) * CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END):: numeric , 1) rt_personal_total_viewing_rate ,';
            $from .= '   ROUND( ((c.ts_viewing_number::numeric / (SELECT SUM(tban.number) FROM ts_time_box_attr_numbers tban WHERE c.time_box_id = tban.time_box_id AND tban.division = :division AND tban.code IN (' . implode(',', $codeBind) . ') GROUP BY tban.time_box_id )::numeric * 100)* CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END):: numeric , 1) ts_personal_total_viewing_rate ,';
            $from .= '   ROUND( ((c.total_viewing_number::numeric / (SELECT SUM(tban.number) FROM ts_time_box_attr_numbers tban WHERE c.time_box_id = tban.time_box_id AND tban.division = :division AND tban.code IN (' . implode(',', $codeBind) . ') GROUP BY tban.time_box_id )::numeric * 100)* CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END):: numeric , 1) total_personal_total_viewing_rate ,';
            $from .= '   ROUND( ((c.gross_viewing_number::numeric / (SELECT SUM(tban.number) FROM ts_time_box_attr_numbers tban WHERE c.time_box_id = tban.time_box_id AND tban.division = :division AND tban.code IN (' . implode(',', $codeBind) . ') GROUP BY tban.time_box_id )::numeric * 100)* CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END):: numeric , 1) gross_personal_total_viewing_rate ,';
            $from .= '   ROUND( ((c.rt_total_viewing_number::numeric / (SELECT SUM(tban.number) FROM ts_time_box_attr_numbers tban WHERE c.time_box_id = tban.time_box_id AND tban.division = :division AND tban.code IN (' . implode(',', $codeBind) . ') GROUP BY tban.time_box_id )::numeric * 100)* CASE WHEN :conv15SecFlag = 1 THEN c.duration::numeric / 15 ELSE 1 END):: numeric , 1) rt_total_personal_total_viewing_rate ,';
        }
        $from .= '   	ROUND(c.rt_household_viewing_rate:: numeric,1) rt_household_viewing_rate, ';
        $from .= '   	ROUND(c.ts_household_viewing_rate:: numeric,1) ts_household_viewing_rate, ';
        $from .= '   	ROUND(c.total_household_viewing_rate:: numeric,1) total_household_viewing_rate, ';
        $from .= '   	ROUND(c.gross_household_viewing_rate:: numeric,1) gross_household_viewing_rate, ';

        $from .= '   	 channel_code_name, ';
        $from .= '   	 company_name, ';
        $from .= '   	 cm_name, ';
        $from .= '   	 holiday ';
        $from .= '   FROM ';
        $from .= '     commercial_grouped c ';
        $from .= '    ) odr ';

        $query = $with . $select . $from . $orderBy;
        $result = $this->select($query, $bindings);

        // 件数取得
        $query = $count . ' SELECT COUNT(*) cnt FROM commercial_data ';
        unset($tmpBindings[':division']);

        foreach ($codes as $key => $val) {
            unset($tmpBindings[':codes' . $key]);
        }
        $bindings = $tmpBindings;
        $resultCnt = $this->selectOne($query, $bindings);
        return [
            'list' => $result,
            'cnt' => $resultCnt->cnt,
        ];
    }

    /**
     * ts_time_box_attr_numbers関連のwith句生成.
     * @param array $bindings
     * @param bool $isConditionCross
     * @param int $regionId
     * @param string $division
     * @param string $divisionKey
     * @param ?array $codes
     * @param array $conditionCross
     * @param array $rsTimeBoxIds
     * @return string
     */
    public function createSqlForTsTimeBoxAttrNumbers(array &$bindings, bool $isConditionCross, int $regionId, String $division, String $divisionKey, ?array $codes, array $conditionCross, array $rsTimeBoxIds): String
    {
        $crossJoin = '';
        $crossJoinWhere = '';
        $timeBoxIdWhere = '';
        $samplesOn = '';

        $with = '';
        $withTsPanelers = '';
        $withTsSamples = '';
        $withTsNumbers = '';

        // リアルタイムのtime_box_idによるwhere句
        $bindTimeBoxIds = [];

        if (count($rsTimeBoxIds) > 0) {
            $bindTimeBoxIds = $this->createArrayBindParam('time_box_ids', ['time_box_ids' => $rsTimeBoxIds], $bindings);
            $timeBoxIdWhere .= ' tbp.time_box_id IN (' . implode(',', $bindTimeBoxIds) . ') AND ';
        }

        // timebox with句
        $with .= 'timebox as ( ';
        $with .= '    SELECT';
        $with .= '      id';
        $with .= '      , FIRST_VALUE(id) OVER ( ';
        $with .= '        ORDER BY';
        $with .= '          id DESC ROWS BETWEEN 1 PRECEDING AND CURRENT ROW';
        $with .= '      ) next_id';
        $with .= '      , region_id';
        $with .= '    FROM';
        $with .= '      time_boxes ';
        $with .= '    WHERE';
        $with .= '    region_id = :region_id ';
        $with .= '),';

        // ts_panelrs　with句
        $with .= 'ts_panelers AS (';
        $with .= '    SELECT';
        $with .= '        tbp.time_box_id';
        $with .= '        ,tbp_next.time_box_id next_time_box_id ';
        $with .= '        ,tbp_next.paneler_id ';
        $with .= '        ,tbp_next.household_id ';
        $with .= '        ,tbp_next.gender ';
        $with .= '        ,tbp_next.birthday ';
        $with .= '        ,tbp_next.primal ';
        $with .= '        ,tbp_next.age ';
        $with .= '        ,tbp_next.married ';
        $with .= '        ,tbp_next.occupation ';
        $with .= '    FROM';
        $with .= '        time_box_panelers tbp';
        $with .= '    INNER JOIN';
        $with .= '        timebox t';
        $with .= '        ON';

        if (count($bindTimeBoxIds) > 0) {
            $with .= ' tbp.time_box_id IN (' . implode(',', $bindTimeBoxIds) . ') AND ';
        }
        $with .= '        tbp.time_box_id = t.id';
        $with .= '    INNER JOIN';
        $with .= '        time_box_panelers tbp_next';
        $with .= '        ON';
        $with .= '            tbp_next.time_box_id = t.next_id';
        $with .= '        AND';
        $with .= '            tbp_next.paneler_id = tbp.paneler_id';
        $with .= '),';

        // cross joinの中身
        $tmpArr = [];
        $divCodes = array_filter($codes, function ($v, $k) {
            return $v != 'personal' && $v != 'household';
        }, ARRAY_FILTER_USE_BOTH);

        if (count($divCodes) > 0) {
            foreach ($divCodes as $code) {
                $key = ':union_' . $divisionKey . $code;
                $bindings[$key] = $code;
                $tmpSql = ' ( ';
                $tmpSql .= ' SELECT ' . $key . '::varchar(255) as code ';
                $tmpSql .= ' ) ';
                array_push($tmpArr, $tmpSql);
            }
        } else {
            $tmpArr[] = '(SELECT 1 as code ) ';
        }
        $crossJoin .= ' ( ' . implode(' UNION ', $tmpArr) . ' ) AS codes ';

        // codeごとのwhere句
        $crossJoinWhere .= '   ( ';

        $crossJoinWhere .= $this->createCrossJoinWhereClause($division, $divCodes, $bindings);
        $crossJoinWhere .= '   ) ';

        // ts_samples with句
        $with .= '        ts_samples AS (';
        $with .= '          SELECT';
        $with .= '            tbp.paneler_id';
        $with .= '            , tbp.time_box_id';
        $with .= '            , tbp.next_time_box_id';
        $with .= '            , codes.code';
        $with .= '          FROM';
        $with .= '            ts_panelers tbp';
        $with .= '            CROSS JOIN ' . $crossJoin;
        $with .= '          WHERE ' . $crossJoinWhere;
        $with .= '        ),';

        // ts_numbers with句、タイムシフトのパネラー人数取得
        $with .= '      ts_time_box_attr_numbers AS (';
        $with .= '        SELECT';
        $with .= '        	time_box_id';
        $with .= '         , ' . $this->quote($division) . '::varchar(255) division';
        $with .= '        	, code';
        $with .= '        	, COUNT(paneler_id) number';
        $with .= '        FROM';
        $with .= '        	ts_samples';
        $with .= '        GROUP BY';
        $with .= '        	code';
        $with .= '        	,time_box_id';
        $with .= '      )';

        return $with;
    }

    /**
     * GRP.
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
     * @param int page
     * @param int length
     * @param string $csvFlag
     * @param array $dataType
     * @param string $period
     * @param ?string $allChannels
     * @param int $length
     * @param int $page
     * @param string $codeNumber
     * @param string $sampleCodePrefix
     * @param string $sampleCodeNumberPrefix
     * @param string $selectedPersonalName
     * @param array $dataTypes
     * @param array $dataTypeFlags
     * @return array
     */
    public function searchGrp(String $startDate, String $endDate, String $startTime, String $endTime, ?String $cmType, ?String $cmSeconds, ?array $progIds, int $regionId, String $division, ?array $codes, ?array $conditionCross, ?array $companyIds, ?array $productIds, ?array $cmIds, array $channels, ?bool $conv_15_sec_flag, string $period, ?string $allChannels, bool $straddlingFlg, int $length, int $page, string $csvFlag, array $dataType, string $codeNumber, string $sampleCodePrefix, string $sampleCodeNumberPrefix, string $selectedPersonalName, array $dataTypes, array $dataTypeFlags): array
    {
        $bindings = [];
        $isConditionCross = $division == 'condition_cross';
        $divisionKey = $division . '_';

        // CSV出力出ない場合、全曲表示はしない
        if ($csvFlag === '0') {
            $allChannels = '0';
        }

        // 冗長的なWHERE句用
        list($rsTimeBoxIds, $rsCmIds, $rsPanelers) = $this->createCommercialListWhere($startDate, $endDate, $cmType, $cmSeconds, $progIds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $straddlingFlg);
        $timeBoxIdWhere = '';

        if (count($rsTimeBoxIds) > 0) {
            $bindTimeBoxIds = $this->createArrayBindParam('time_box_ids', ['time_box_ids' => $rsTimeBoxIds], $bindings);
            $timeBoxIdWhere .= ' tban.time_box_id IN (' . implode(',', $bindTimeBoxIds) . ') AND ';
        }

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

        list(
            $withWhere,
            $codeBind,
            $channelBind,
            $programBind,
            $companyBind,
            $productIdsBind,
            $cmIdsBind
            ) = $this->createListWhere($bindings, $isConditionCross, $startDate, $endDate, $startTime, $endTime, $cmType, $cmSeconds, $progIds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $conv_15_sec_flag, $straddlingFlg);

        $with = '';
        $with .= ' WITH cm_list AS (  ';
        $with .= '   SELECT ';
        $with .= $dateSql;
        $with .= '     c.cm_id ';
        $with .= '     , c.prog_id ';
        $with .= '     , c.product_id ';
        $with .= '     , c.company_id ';
        $with .= '     , c.time_box_id ';
        $with .= '     , c.started_at ';
        $with .= '     , c.duration ';
        $with .= '     , c.channel_id ';
        $with .= "     , COUNT(*) OVER (PARTITION BY ${dateParititionSql} company_id, product_id, channel_id) total_cnt ";
        $with .= "     , SUM(duration) OVER (PARTITION BY ${dateParititionSql} company_id, product_id, channel_id) total_duration ";
        $with .= '     , SUM(  ';
        $with .= '       ROUND(  ';
        $with .= '         (c.personal_viewing_rate * CASE  ';
        $with .= '           WHEN :conv15SecFlag = 1  ';
        $with .= '             THEN duration::numeric / 15  ';
        $with .= '           ELSE 1  ';
        $with .= '           END)::numeric ';
        $with .= '         , 1 ';
        $with .= '       ) ';
        $with .= "     ) OVER (PARTITION BY ${dateParititionSql} company_id, product_id, channel_id) rt_personal_viewing_grp ";
        $with .= '     , SUM(  ';
        $with .= '       ROUND(  ';
        $with .= '         (c.ts_personal_viewing_rate * CASE  ';
        $with .= '           WHEN :conv15SecFlag = 1  ';
        $with .= '             THEN duration::numeric / 15  ';
        $with .= '           ELSE 1  ';
        $with .= '           END)::numeric ';
        $with .= '         , 1 ';
        $with .= '       ) ';
        $with .= "     ) OVER (PARTITION BY ${dateParititionSql} company_id, product_id, channel_id) ts_personal_viewing_grp ";
        $with .= '     , SUM(  ';
        $with .= '       ROUND(  ';
        $with .= '         (c.ts_personal_total_viewing_rate * CASE  ';
        $with .= '           WHEN :conv15SecFlag = 1  ';
        $with .= '             THEN duration::numeric / 15  ';
        $with .= '           ELSE 1  ';
        $with .= '           END)::numeric ';
        $with .= '         , 1 ';
        $with .= '       ) ';
        $with .= "     ) OVER (PARTITION BY ${dateParititionSql} company_id, product_id, channel_id) total_personal_viewing_grp ";
        $with .= '     , SUM(  ';
        $with .= '       ROUND(  ';
        $with .= '         (COALESCE(c.ts_personal_gross_viewing_rate, c.ts_samples_personal_viewing_rate) * CASE  ';
        $with .= '           WHEN :conv15SecFlag = 1  ';
        $with .= '             THEN duration::numeric / 15  ';
        $with .= '           ELSE 1  ';
        $with .= '           END)::numeric ';
        $with .= '         , 1 ';
        $with .= '       ) ';
        $with .= "     ) OVER (PARTITION BY ${dateParititionSql} company_id, product_id, channel_id) gross_personal_viewing_grp ";
        $with .= '     , SUM(  ';
        $with .= '       ROUND(  ';
        $with .= '         (COALESCE(c.ts_personal_rt_total_viewing_rate, c.ts_samples_personal_viewing_rate) * CASE  ';
        $with .= '           WHEN :conv15SecFlag = 1  ';
        $with .= '             THEN duration::numeric / 15  ';
        $with .= '           ELSE 1  ';
        $with .= '           END)::numeric ';
        $with .= '         , 1 ';
        $with .= '       ) ';
        $with .= "     ) OVER (PARTITION BY ${dateParititionSql} company_id, product_id, channel_id) rt_total_personal_viewing_grp ";
        $with .= '     , SUM(  ';
        $with .= '       ROUND(  ';
        $with .= '         (c.household_viewing_rate * CASE  ';
        $with .= '           WHEN :conv15SecFlag = 1  ';
        $with .= '             THEN duration::numeric / 15  ';
        $with .= '           ELSE 1  ';
        $with .= '           END)::numeric ';
        $with .= '         , 1 ';
        $with .= '       ) ';
        $with .= "     ) OVER (PARTITION BY ${dateParititionSql} company_id, product_id, channel_id) rt_household_viewing_grp  ";
        $with .= '     , SUM(  ';
        $with .= '       ROUND(  ';
        $with .= '         (c.ts_household_viewing_rate * CASE  ';
        $with .= '           WHEN :conv15SecFlag = 1  ';
        $with .= '             THEN duration::numeric / 15  ';
        $with .= '           ELSE 1  ';
        $with .= '           END)::numeric ';
        $with .= '         , 1 ';
        $with .= '       ) ';
        $with .= "     ) OVER (PARTITION BY ${dateParititionSql} company_id, product_id, channel_id) ts_household_viewing_grp  ";
        $with .= '     , SUM(  ';
        $with .= '       ROUND(  ';
        $with .= '         (c.ts_household_total_viewing_rate * CASE  ';
        $with .= '           WHEN :conv15SecFlag = 1  ';
        $with .= '             THEN duration::numeric / 15  ';
        $with .= '           ELSE 1  ';
        $with .= '           END)::numeric ';
        $with .= '         , 1 ';
        $with .= '       ) ';
        $with .= "     ) OVER (PARTITION BY ${dateParititionSql} company_id, product_id, channel_id) total_household_viewing_grp  ";
        $with .= '     , SUM(  ';
        $with .= '       ROUND(  ';
        $with .= '         (COALESCE(c.ts_household_gross_viewing_rate, c.ts_samples_household_viewing_rate) * CASE  ';
        $with .= '           WHEN :conv15SecFlag = 1  ';
        $with .= '             THEN duration::numeric / 15  ';
        $with .= '           ELSE 1  ';
        $with .= '           END)::numeric ';
        $with .= '         , 1 ';
        $with .= '       ) ';
        $with .= "     ) OVER (PARTITION BY ${dateParititionSql} company_id, product_id, channel_id) gross_household_viewing_grp  ";
        $with .= '   FROM ';
        $with .= '     commercials c  ';
        $with .= $withWhere;
        $with .= ' ) ';

        $with .= ' ,union_reports AS (  ';
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
        $with .= '     , COALESCE(MAX(gross_viewing_rate), MAX(ts_samples_viewing_rate)) gross_viewing_rate ';
        $with .= '     , COALESCE(MAX(gross_viewing_number), MAX(ts_samples_viewing_number)) gross_viewing_number ';
        $with .= '     , COALESCE(MAX(rt_total_viewing_rate), MAX(ts_samples_viewing_rate)) rt_total_viewing_rate ';
        $with .= '     , COALESCE(MAX(rt_total_viewing_number), MAX(ts_samples_viewing_number)) rt_total_viewing_number ';
        $with .= '   FROM ( ';
        $with .= '      ( ';
        $with .= '          SELECT ';
        $with .= '              cl.cm_id';
        $with .= '              , cl.prog_id ';
        $with .= '              , cl.started_at ';
        $with .= '              , division ';
        $with .= '              , code ';
        $with .= '              , viewing_rate rt_viewing_rate ';
        $with .= '              , viewing_number rt_viewing_number ';
        $with .= '              , ts_samples_viewing_rate ';
        $with .= '              , ts_samples_viewing_number ';
        $with .= '              , null ts_viewing_rate ';
        $with .= '              , null ts_viewing_number ';
        $with .= '              , null total_viewing_rate ';
        $with .= '              , null total_viewing_number ';
        $with .= '              , null gross_viewing_rate ';
        $with .= '              , null gross_viewing_number ';
        $with .= '              , null rt_total_viewing_rate ';
        $with .= '              , null rt_total_viewing_number ';
        $with .= '          FROM ';
        $with .= '              cm_reports cr ';
        $with .= '          INNER JOIN ';
        $with .= '              cm_list cl ';
        $with .= '          ON cr.division = :division AND cr.code IN (' . implode(',', $codeBind) . ') ';
        $with .= '          AND cr.cm_id = cl.cm_id  ';
        $with .= '          AND cr.started_at = cl.started_at  ';
        $with .= '          AND cr.prog_id = cl.prog_id  ';
        $with .= '          AND cl.time_box_id  = ';
        $with .= $this->createTimeBoxCaseClause($startDate, $endDate, $regionId, 'cr.started_at');
        $with .= '      ) ';
        $with .= '      UNION ';
        $with .= '      ( ';
        $with .= '          SELECT ';
        $with .= '              cl.cm_id';
        $with .= '              , cl.prog_id ';
        $with .= '              , cl.started_at ';
        $with .= '              , division ';
        $with .= '              , code ';
        $with .= '              , null rt_viewing_rate ';
        $with .= '              , null rt_viewing_number ';
        $with .= '              , null ts_samples_viewing_rate ';
        $with .= '              , null ts_samples_viewing_number ';
        $with .= '              , viewing_rate ts_viewing_rate ';
        $with .= '              , viewing_number ts_viewing_number ';
        $with .= '              , total_viewing_rate ';
        $with .= '              , total_viewing_number ';
        $with .= '              , gross_viewing_rate ';
        $with .= '              , gross_viewing_number ';
        $with .= '              , rt_total_viewing_rate ';
        $with .= '              , rt_total_viewing_number ';
        $with .= '          FROM ';
        $with .= '              ts_cm_reports tcr ';
        $with .= '          INNER JOIN ';
        $with .= '              cm_list cl ';
        $with .= '          ON tcr.division = :division AND tcr.code IN (' . implode(',', $codeBind) . ') ';
        $with .= '          AND tcr.cm_id = cl.cm_id  ';
        $with .= '          AND tcr.started_at = cl.started_at  ';
        $with .= '          AND tcr.prog_id = cl.prog_id  ';
        $with .= '          AND cl.time_box_id  = ';
        $with .= $this->createTimeBoxCaseClause($startDate, $endDate, $regionId, 'tcr.started_at');
        $with .= '      ) ';
        $with .= '   ) union_reports ';
        $with .= '   GROUP BY ';
        $with .= '      cm_id ';
        $with .= '      ,prog_id ';
        $with .= '      ,started_at ';
        $with .= '      ,division ';
        $with .= '      ,code ';
        $with .= ' ) ';

        $with .= ' ,cm_data AS (  ';
        $with .= '   SELECT ';
        $with .= '     cl.cm_id ';
        $with .= '     , cl.prog_id ';
        $with .= '     , cl.product_id ';
        $with .= '     , cl.company_id ';
        $with .= '     , cl.time_box_id ';
        $with .= '     , cl.started_at ';
        $with .= '     , cl.duration ';
        $with .= '     , cl.channel_id ';
        $with .= '     , total_cnt ';
        $with .= '     , total_duration ';
        $with .= '     , rt_personal_viewing_grp ';
        $with .= '     , ts_personal_viewing_grp ';
        $with .= '     , total_personal_viewing_grp ';
        $with .= '     , gross_personal_viewing_grp ';
        $with .= '     , rt_total_personal_viewing_grp ';
        $with .= '     , rt_household_viewing_grp  ';
        $with .= '     , ts_household_viewing_grp  ';
        $with .= '     , total_household_viewing_grp  ';
        $with .= '     , gross_household_viewing_grp  ';
        $with .= '     , cr.code ';
        $with .= '     , cr.division ';
        $with .= '     , cr.rt_viewing_rate';
        $with .= '     , cr.rt_viewing_number';
        $with .= '     , cr.ts_viewing_rate ';
        $with .= '     , cr.total_viewing_rate';
        $with .= '     , cr.gross_viewing_rate';
        $with .= '     , cr.rt_total_viewing_rate';
        $with .= '     , cr.ts_viewing_number ';
        $with .= '     , cr.total_viewing_number ';
        $with .= '     , cr.gross_viewing_number ';
        $with .= '     , cr.rt_total_viewing_number ';
        $with .= '     , date ';
        $with .= '   FROM ';
        $with .= '     cm_list cl  ';
        $with .= '   LEFT JOIN  ';
        $with .= '     union_reports cr ';
        $with .= '   ON cr.division = :division AND cr.code IN (' . implode(',', $codeBind) . ') ';
        $with .= '   AND cr.cm_id = cl.cm_id  ';
        $with .= '   AND cr.started_at = cl.started_at  ';
        $with .= '   AND cr.prog_id = cl.prog_id  ';
        $with .= '   AND cl.time_box_id  = ';
        $with .= $this->createTimeBoxCaseClause($startDate, $endDate, $regionId, 'cr.started_at');
        $with .= ' ) ';

        $with .= ',' . $this->createSqlForTsTimeBoxAttrNumbers($bindings, $isConditionCross, $regionId, $division, $divisionKey, $codes, $conditionCross, $rsTimeBoxIds);

        $with .= ' , grp_list AS (  ';
        $with .= '   SELECT ';
        $with .= '     SUM(  ';
        $with .= '       ROUND(  ';
        $with .= '         (cd.rt_viewing_rate ::real * CASE  ';
        $with .= '           WHEN :conv15SecFlag = 1  ';
        $with .= '             THEN duration::numeric / 15  ';
        $with .= '           ELSE 1  ';
        $with .= '           END)::numeric ';
        $with .= '         , 1 ';
        $with .= '       ) ';
        $with .= '     ) grp ';
        $with .= '     , SUM(  ';
        $with .= '       ROUND(  ';
        $with .= '         (cd.ts_viewing_rate ::real * CASE  ';
        $with .= '           WHEN :conv15SecFlag = 1  ';
        $with .= '             THEN duration::numeric / 15  ';
        $with .= '           ELSE 1  ';
        $with .= '           END)::numeric ';
        $with .= '         , 1 ';
        $with .= '       ) ';
        $with .= '     ) ts_grp ';
        $with .= '     , SUM(  ';
        $with .= '       ROUND(  ';
        $with .= '         (cd.total_viewing_rate ::real * CASE  ';
        $with .= '           WHEN :conv15SecFlag = 1  ';
        $with .= '             THEN duration::numeric / 15  ';
        $with .= '           ELSE 1  ';
        $with .= '           END)::numeric ';
        $with .= '         , 1 ';
        $with .= '       ) ';
        $with .= '     ) total_grp ';
        $with .= '     , SUM(  ';
        $with .= '       ROUND(  ';
        $with .= '         (cd.gross_viewing_rate ::real * CASE  ';
        $with .= '           WHEN :conv15SecFlag = 1  ';
        $with .= '             THEN duration::numeric / 15  ';
        $with .= '           ELSE 1  ';
        $with .= '           END)::numeric ';
        $with .= '         , 1 ';
        $with .= '       ) ';
        $with .= '     ) gross_grp ';
        $with .= '     , SUM(  ';
        $with .= '       ROUND(  ';
        $with .= '         (cd.rt_total_viewing_rate ::real * CASE  ';
        $with .= '           WHEN :conv15SecFlag = 1  ';
        $with .= '             THEN duration::numeric / 15  ';
        $with .= '           ELSE 1  ';
        $with .= '           END)::numeric ';
        $with .= '         , 1 ';
        $with .= '       ) ';
        $with .= '     ) rt_total_grp ';
        $with .= '     , SUM(  ';
        $with .= '       cd.rt_viewing_number ::numeric * CASE  ';
        $with .= '         WHEN :conv15SecFlag = 1  ';
        $with .= '           THEN duration::numeric / 15  ';
        $with .= '         ELSE 1  ';
        $with .= '         END ';
        $with .= '     ) rt_viewing_number ';
        $with .= '     , SUM(  ';
        $with .= '       cd.ts_viewing_number ::numeric * CASE  ';
        $with .= '         WHEN :conv15SecFlag = 1  ';
        $with .= '           THEN duration::numeric / 15  ';
        $with .= '         ELSE 1  ';
        $with .= '         END ';
        $with .= '     ) ts_viewing_number ';
        $with .= '     , SUM(  ';
        $with .= '       cd.total_viewing_number ::numeric * CASE  ';
        $with .= '         WHEN :conv15SecFlag = 1  ';
        $with .= '           THEN duration::numeric / 15  ';
        $with .= '         ELSE 1  ';
        $with .= '         END ';
        $with .= '     ) total_viewing_number ';
        $with .= '     , SUM(  ';
        $with .= '       cd.gross_viewing_number ::numeric * CASE  ';
        $with .= '         WHEN :conv15SecFlag = 1  ';
        $with .= '           THEN duration::numeric / 15  ';
        $with .= '         ELSE 1  ';
        $with .= '         END ';
        $with .= '     ) gross_viewing_number ';
        $with .= '     , SUM(  ';
        $with .= '       cd.rt_total_viewing_number ::numeric * CASE  ';
        $with .= '         WHEN :conv15SecFlag = 1  ';
        $with .= '           THEN duration::numeric / 15  ';
        $with .= '         ELSE 1  ';
        $with .= '         END ';
        $with .= '     ) rt_total_viewing_number ';
        $with .= '     , rt_personal_viewing_grp ';
        $with .= '     , ts_personal_viewing_grp ';
        $with .= '     , total_personal_viewing_grp ';
        $with .= '     , gross_personal_viewing_grp ';
        $with .= '     , rt_total_personal_viewing_grp ';
        $with .= '     , rt_household_viewing_grp ';
        $with .= '     , ts_household_viewing_grp ';
        $with .= '     , total_household_viewing_grp ';
        $with .= '     , gross_household_viewing_grp ';
        $with .= '     , total_duration ';
        $with .= '     , total_cnt ';
        $with .= '     , company_id ';
        $with .= '     , cd.code ';
        $with .= '     , channel_id ';
        $with .= '     , product_id  ';
        $with .= '     , date  ';
        $with .= '     , cd.time_box_id ';
        $with .= '     , rt_tban.number rt_number ';
        $with .= '     , ts_tban.number ts_number ';
        $with .= '     , rt_tban.all_number rt_all_number ';
        $with .= '     , ts_tban.all_number ts_all_number ';
        $with .= '   FROM ';
        $with .= '     cm_data cd  ';
        $with .= '   LEFT JOIN  ';
        $with .= '      ( ';
        $with .= '          SELECT ';
        $with .= '              tban.number ';
        $with .= '              , SUM(tban.number) OVER (PARTITION BY time_box_id, tban.division) AS all_number  ';
        $with .= '              , tban.division ';
        $with .= '              , tban.code ';
        $with .= '              , tban.time_box_id ';
        $with .= '          FROM ';
        $with .= '              time_box_attr_numbers tban  ';
        $with .= '          WHERE ';
        $with .= $timeBoxIdWhere;
        $with .= '               tban.division = :division AND tban.code IN (' . implode(',', $codeBind) . ') '; // 属性・コード
        $with .= '      ) rt_tban ';
        $with .= '      ON cd.division = rt_tban.division ';
        $with .= '      AND cd.code = rt_tban.code  ';
        $with .= '      AND cd.time_box_id = rt_tban.time_box_id ';
        $with .= '   LEFT JOIN  ';
        $with .= '      ( ';
        $with .= '          SELECT ';
        $with .= '              tban.number ';
        $with .= '              , SUM(tban.number) OVER (PARTITION BY time_box_id, tban.division) AS all_number  ';
        $with .= '              , tban.division ';
        $with .= '              , tban.code ';
        $with .= '              , tban.time_box_id ';
        $with .= '          FROM ';
        $with .= '              time_box_attr_numbers tban  ';
        $with .= '          WHERE ';
        $with .= $timeBoxIdWhere;
        $with .= '              tban.division = :division AND tban.code IN (' . implode(',', $codeBind) . ') '; // 属性・コード
        $with .= '      ) ts_tban ';
        $with .= '      ON cd.division = ts_tban.division ';
        $with .= '      AND cd.code = ts_tban.code  ';
        $with .= '      AND cd.time_box_id = ts_tban.time_box_id ';
        $with .= '   GROUP BY ';
        $with .= '     company_id ';
        $with .= '     , rt_personal_viewing_grp ';
        $with .= '     , ts_personal_viewing_grp ';
        $with .= '     , total_personal_viewing_grp ';
        $with .= '     , gross_personal_viewing_grp ';
        $with .= '     , rt_total_personal_viewing_grp ';
        $with .= '     , rt_household_viewing_grp ';
        $with .= '     , ts_household_viewing_grp ';
        $with .= '     , total_household_viewing_grp ';
        $with .= '     , gross_household_viewing_grp ';
        $with .= '     , total_cnt ';
        $with .= '     , total_duration ';
        $with .= '     , cd.code ';
        $with .= '     , channel_id ';
        $with .= '     , product_id ';
        $with .= '     , date  ';
        $with .= '     , cd.time_box_id  ';
        $with .= '     , rt_tban.number ';
        $with .= '     , ts_tban.number ';
        $with .= '     , rt_tban.all_number  ';
        $with .= '     , ts_tban.all_number  ';
        $with .= ' ), with_count AS (  ';

        $with .= ' SELECT ';
        $with .= '   DENSE_RANK() OVER (ORDER BY date, name, company_id, product_name) count ';
        $with .= '   , DENSE_RANK() OVER (ORDER BY date DESC, name DESC, company_id DESC, product_name DESC) + DENSE_RANK() OVER (ORDER BY date, name, company_id, product_name) - 1 rowcount ';
        $with .= '   , name ';
        $with .= '   , company_id ';
        $with .= '   , product_id ';
        $with .= '   , product_name ';
        $with .= '   , total_cnt ';
        $with .= '   , total_duration ';
        $with .= '   , display_name ';
        $with .= '   , channel_id ';
        $with .= '   , rt_personal_viewing_grp ';
        $with .= '   , ts_personal_viewing_grp ';
        $with .= '   , total_personal_viewing_grp ';
        $with .= '   , gross_personal_viewing_grp ';
        $with .= '   , rt_total_personal_viewing_grp ';
        $with .= '   , date  ';

        foreach ($codes as $key => $val) {
            $with .= ' , ROUND(SUM(rt_' . $divisionKey . $val . ')::numeric,1) AS rt_' . $divisionKey . $val;
            $with .= ' , ROUND(SUM(ts_' . $divisionKey . $val . ')::numeric,1) AS ts_' . $divisionKey . $val;
            $with .= ' , ROUND(SUM(total_' . $divisionKey . $val . ')::numeric,1) AS total_' . $divisionKey . $val;
            $with .= ' , ROUND(SUM(gross_' . $divisionKey . $val . ')::numeric,1) AS gross_' . $divisionKey . $val;
            $with .= ' , ROUND(SUM(rt_total_' . $divisionKey . $val . ')::numeric,1) AS rt_total_' . $divisionKey . $val;
        }
        $with .= '   , rt_household_viewing_grp ';
        $with .= '   , ts_household_viewing_grp ';
        $with .= '   , total_household_viewing_grp ';
        $with .= '   , gross_household_viewing_grp ';

        $with .= '   , ROUND(  ';
        $with .= '        SUM(  ';
        $with .= '           ROUND(  ';
        $with .= '             (rt_viewing_number::numeric / rt_all_number * 100)::numeric ';
        $with .= '             , 2 ';
        $with .= '           ) ';
        $with .= '         )  ';
        $with .= '         , 1 ';
        $with .= '     ) as rt_total_viewing_grp  ';

        $with .= '   , ROUND(  ';
        $with .= '         SUM(  ';
        $with .= '           ROUND(  ';
        $with .= '             (ts_viewing_number::numeric / ts_all_number * 100)::numeric ';
        $with .= '             , 2 ';
        $with .= '           ) ';
        $with .= '         )  ';
        $with .= '         , 1 ';
        $with .= '     ) as ts_total_viewing_grp  ';

        $with .= '   , ROUND(  ';
        $with .= '         SUM(  ';
        $with .= '           ROUND(  ';
        $with .= '             (total_viewing_number::numeric / ts_all_number * 100)::numeric ';
        $with .= '             , 2 ';
        $with .= '           ) ';
        $with .= '         )  ';
        $with .= '         , 1 ';
        $with .= '     ) as total_total_viewing_grp  ';

        $with .= '   , ROUND(  ';
        $with .= '         SUM(  ';
        $with .= '           ROUND(  ';
        $with .= '             (gross_viewing_number::numeric / ts_all_number * 100)::numeric ';
        $with .= '             , 2 ';
        $with .= '           ) ';
        $with .= '         )  ';
        $with .= '         , 1 ';
        $with .= '     ) as gross_total_viewing_grp  ';

        $with .= '   , ROUND(  ';
        $with .= '         SUM(  ';
        $with .= '           ROUND(  ';
        $with .= '             (rt_total_viewing_number::numeric / ts_all_number * 100)::numeric ';
        $with .= '             , 2 ';
        $with .= '           ) ';
        $with .= '         )  ';
        $with .= '         , 1 ';
        $with .= '     ) as rt_total_total_viewing_grp  ';

        $with .= ' FROM ';
        $with .= '   (  ';
        $with .= '     SELECT ';
        $with .= '       c.name ';
        $with .= '       , p.name product_name ';
        $with .= '       , gl.company_id ';
        $with .= '       , gl.product_id ';
        $with .= '       , gl.total_cnt ';
        $with .= '       , gl.total_duration ';
        $with .= '       , gl.channel_id ';
        $with .= '       , ch.display_name ';
        $with .= '       , gl.rt_personal_viewing_grp ';
        $with .= '       , gl.ts_personal_viewing_grp ';
        $with .= '       , gl.total_personal_viewing_grp ';
        $with .= '       , gl.gross_personal_viewing_grp ';
        $with .= '       , gl.rt_total_personal_viewing_grp ';
        $with .= '       , gl.rt_viewing_number ';
        $with .= '       , gl.ts_viewing_number ';
        $with .= '       , gl.total_viewing_number ';
        $with .= '       , gl.gross_viewing_number ';
        $with .= '       , gl.rt_total_viewing_number ';
        $with .= '       , date  ';
        $with .= '       , FIRST_VALUE(gl.rt_all_number) OVER (PARTITION BY date, gl.company_id, gl.channel_id, gl.product_id ORDER BY time_box_id DESC ROWS UNBOUNDED PRECEDING) rt_all_number ';
        $with .= '       , FIRST_VALUE(gl.ts_all_number) OVER (PARTITION BY date, gl.company_id, gl.channel_id, gl.product_id ORDER BY time_box_id DESC ROWS UNBOUNDED PRECEDING) ts_all_number ';

        foreach ($codes as $key => $val) {
            $with .= '   , CASE WHEN gl.code = :codes' . $key . ' THEN gl.grp END AS rt_' . $divisionKey . $val . ' ';
            $with .= '   , CASE WHEN gl.code = :codes' . $key . ' THEN gl.ts_grp END AS ts_' . $divisionKey . $val . ' ';
            $with .= '   , CASE WHEN gl.code = :codes' . $key . ' THEN gl.total_grp END AS total_' . $divisionKey . $val . ' ';
            $with .= '   , CASE WHEN gl.code = :codes' . $key . ' THEN gl.gross_grp END AS gross_' . $divisionKey . $val . ' ';
            $with .= '   , CASE WHEN gl.code = :codes' . $key . ' THEN gl.rt_total_grp END AS rt_total_' . $divisionKey . $val . ' ';
        }
        $with .= '       , gl.rt_household_viewing_grp ';
        $with .= '       , gl.ts_household_viewing_grp ';
        $with .= '       , gl.total_household_viewing_grp ';
        $with .= '       , gl.gross_household_viewing_grp ';
        $with .= '       , gl.code  ';
        $with .= '     FROM ';
        $with .= '       grp_list gl  ';
        $with .= '       INNER JOIN products p  ';
        $with .= '         ON gl.product_id = p.id  ';
        $with .= '       INNER JOIN companies c  ';
        $with .= '         ON gl.company_id = c.id ';
        $with .= '       INNER JOIN channels ch  ';
        $with .= '         ON gl.channel_id = ch.id ';
        $with .= '   ) vertical  ';
        $with .= ' GROUP BY ';
        $with .= '   product_name ';
        $with .= '   , company_id ';
        $with .= '   , product_id ';
        $with .= '   , name ';
        $with .= '   , display_name ';
        $with .= '   , channel_id ';
        $with .= '   , total_cnt ';
        $with .= '   , total_duration ';
        $with .= '   , rt_personal_viewing_grp ';
        $with .= '   , ts_personal_viewing_grp ';
        $with .= '   , total_personal_viewing_grp ';
        $with .= '   , gross_personal_viewing_grp ';
        $with .= '   , rt_total_personal_viewing_grp ';
        $with .= '   , rt_household_viewing_grp  ';
        $with .= '   , ts_household_viewing_grp  ';
        $with .= '   , total_household_viewing_grp  ';
        $with .= '   , gross_household_viewing_grp  ';
        $with .= '   , date  ';
        $with .= ' ORDER BY ';
        $with .= '    date  ';
        $with .= '   , company_id ';
        $with .= '   , product_id ';
        $with .= '   , channel_id  ';
        $with .= ' )  ';

        // 全局表示フラグ
        if ($allChannels == 'true') {
            $with .= ', result AS ( ';
        }
        $select = '';
        $select .= ' SELECT ';
        $select .= '     company_id ';
        $select .= '   , product_id ';
        $select .= '   , name ';
        $select .= '   , product_name ';
        $select .= '   , total_cnt ';
        $select .= '   , total_duration ';
        $select .= '   , display_name ';
        $select .= '   , channel_id ';
        $select .= '   , COALESCE(rt_personal_viewing_grp, 0) rt_personal_viewing_grp ';
        $select .= '   , COALESCE(ts_personal_viewing_grp, 0) ts_personal_viewing_grp ';
        $select .= '   , COALESCE(total_personal_viewing_grp, 0) total_personal_viewing_grp ';
        $select .= '   , COALESCE(gross_personal_viewing_grp, 0) gross_personal_viewing_grp ';
        $select .= '   , COALESCE(rt_total_personal_viewing_grp, 0) rt_total_personal_viewing_grp ';
        $select .= '   , date  ';

        foreach ($codes as $key => $val) {
            $select .= ', COALESCE(rt_' . $divisionKey . $val . ', 0) rt_' . $divisionKey . $val . ' ';
            $select .= ', COALESCE(ts_' . $divisionKey . $val . ', 0) ts_' . $divisionKey . $val . ' ';
            $select .= ', COALESCE(total_' . $divisionKey . $val . ', 0) total_' . $divisionKey . $val . ' ';
            $select .= ', COALESCE(gross_' . $divisionKey . $val . ', 0) gross_' . $divisionKey . $val . ' ';
            $select .= ', COALESCE(rt_total_' . $divisionKey . $val . ', 0) rt_total_' . $divisionKey . $val . ' ';
        }
        $select .= '   , COALESCE(rt_household_viewing_grp, 0) rt_household_viewing_grp ';
        $select .= '   , COALESCE(ts_household_viewing_grp, 0) ts_household_viewing_grp ';
        $select .= '   , COALESCE(total_household_viewing_grp, 0) total_household_viewing_grp ';
        $select .= '   , COALESCE(gross_household_viewing_grp, rt_household_viewing_grp, 0) gross_household_viewing_grp ';
        $select .= '   , COALESCE(rt_total_viewing_grp, 0) rt_total_viewing_grp ';
        $select .= '   , COALESCE(ts_total_viewing_grp, 0) ts_total_viewing_grp ';
        $select .= '   , COALESCE(total_total_viewing_grp, 0) total_total_viewing_grp ';
        $select .= '   , COALESCE(gross_total_viewing_grp, 0) gross_total_viewing_grp ';
        $select .= '   , COALESCE(rt_total_total_viewing_grp, 0) rt_total_total_viewing_grp ';

        if ($csvFlag === '0') {
            $select .= '   , rowcount  ';
        }
        $select .= ' FROM ';
        $select .= '   with_count ';

        if ($csvFlag === '0') {
            $bindings[':from'] = ($page - 1) * $length + 1;
            $bindings[':to'] = $page * $length;
            $select .= ' WHERE ';
            $select .= '   count BETWEEN :from AND :to ';
        }

        $query = $with . $select;

        if ($allChannels == 'true') {
            $query .= ' ) ';
            $query .= '   SELECT ';
            $query .= '     date ';
            $query .= '   , name ';
            $query .= '   , product_name ';
            $query .= '   , all_ch.display_name ';
            $query .= '   , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(total_cnt, 0) ELSE 0 END) total_cnt';
            $query .= '   , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(total_duration, 0) ELSE 0 END) total_duration ';

            foreach ($codes as $key => $val) {
                $query .= ' , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(rt_' . $divisionKey . $val . ',0) ELSE 0 END) AS rt_' . $divisionKey . $val;
                $query .= ' , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(ts_' . $divisionKey . $val . ',0) ELSE 0 END) AS ts_' . $divisionKey . $val;
                $query .= ' , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(total_' . $divisionKey . $val . ',0) ELSE 0 END) AS total_' . $divisionKey . $val;
                $query .= ' , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(gross_' . $divisionKey . $val . ',0) ELSE 0 END) AS gross_' . $divisionKey . $val;
                $query .= ' , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(rt_total_' . $divisionKey . $val . ',0) ELSE 0 END) AS rt_total_' . $divisionKey . $val;
            }
            $query .= '   , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(rt_personal_viewing_grp, 0) ELSE 0 END)  rt_personal_viewing_grp ';
            $query .= '   , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(ts_personal_viewing_grp, 0) ELSE 0 END)  ts_personal_viewing_grp ';
            $query .= '   , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(total_personal_viewing_grp, 0) ELSE 0 END)  total_personal_viewing_grp ';
            $query .= '   , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(gross_personal_viewing_grp, 0) ELSE 0 END)  gross_personal_viewing_grp ';
            $query .= '   , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(rt_total_personal_viewing_grp, 0) ELSE 0 END)  rt_total_personal_viewing_grp ';

            $query .= '   , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(rt_household_viewing_grp, 0) ELSE 0 END)  rt_household_viewing_grp ';
            $query .= '   , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(ts_household_viewing_grp, 0) ELSE 0 END)  ts_household_viewing_grp ';
            $query .= '   , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(total_household_viewing_grp, 0) ELSE 0 END)  total_household_viewing_grp ';
            $query .= '   , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(gross_household_viewing_grp, 0) ELSE 0 END)  gross_household_viewing_grp ';

            $query .= '   , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(rt_total_viewing_grp, 0) ELSE 0 END)  rt_total_viewing_grp ';
            $query .= '   , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(ts_total_viewing_grp, 0) ELSE 0 END)  ts_total_viewing_grp ';
            $query .= '   , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(total_total_viewing_grp, 0) ELSE 0 END)  total_total_viewing_grp ';
            $query .= '   , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(gross_total_viewing_grp, 0) ELSE 0 END)  gross_total_viewing_grp ';
            $query .= '   , MAX(CASE WHEN result.channel_id = all_ch.id THEN COALESCE(rt_total_total_viewing_grp, 0) ELSE 0 END)  rt_total_total_viewing_grp ';
            $query .= '   FROM ';
            $query .= '     result ';
            $query .= '   CROSS JOIN ';
            $query .= '   ( ';
            $query .= '     SELECT ';
            $query .= '       id, ';
            $query .= '       display_name ';
            $query .= '     FROM ';
            $query .= '       channels ch  ';
            $query .= '     WHERE ';
            $query .= '       id IN (' . implode(',', $channelBind) . ')  ';
            $query .= '   ) all_ch ';
            $query .= ' GROUP BY ';
            $query .= '   date ';
            $query .= '   , company_id ';
            $query .= '   , product_id ';
            $query .= '   , name ';
            $query .= '   , product_name ';
            $query .= '   , all_ch.id ';
            $query .= '   , all_ch.display_name ';
            $query .= ' ORDER BY ';
            $query .= '   date ';
            $query .= '   , company_id ';
            $query .= '   , product_id ';
            $query .= '   , name ';
            $query .= '   , product_name ';
            $query .= '   , all_ch.id ';
        }
        $result = $this->select($query, $bindings);
        return $result;
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
    private function createListWhere(array &$bindings, bool $isConditionCross, String $startDate, String $endDate, String $startTime, String $endTime, ?String $cmType, ?String $cmSeconds, ?array $progIds, int $regionId, String $division, ?array $codes, ?array $conditionCross, ?array $companyIds, ?array $productIds, ?array $cmIds, array $channels, ?bool $conv15SecFlag, bool $straddlingFlg)
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
