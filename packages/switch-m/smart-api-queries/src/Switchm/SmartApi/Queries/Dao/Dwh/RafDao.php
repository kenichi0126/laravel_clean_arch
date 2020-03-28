<?php

namespace Switchm\SmartApi\Queries\Dao\Dwh;

use Carbon\Carbon;

class RafDao extends Dao
{
    public function selectTempResultsForCsv(int $limit, int $offset): array
    {
        $sql = '';
        $sql .= ' SELECT * FROM results order by row_number ';
        $sql .= " LIMIT ${limit} ";
        $sql .= " OFFSET ${offset}; ";
        return $this->select($sql);
    }

    public function createCsvTempTable(String $startDate, String $endDate, String $startTime, String $endTime, String $cmType, String $cmSeconds, int $regionId, String $division, ?array $codes, array $conditionCross, array $companyIds, ?array $productIds, ?array $cmIds, array $channels, ?bool $conv15SecFlag, ?array $progIds, bool $straddlingFlg, array $dataType, string $axisType, ?string $channelAxis, string $period, array $dataTypeFlags, string $axisTypeProduct, string $axisTypeCompany): void
    {
        $bindings = [];
        $divisionKey = $division . '_';

        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = array_values($dataTypeFlags);

        $isCompanyAxis = false;
        $isProductAxis = false;
        $isChannelAxis = false;
        $axisArr = [];

        if ($axisType === $axisTypeCompany || $axisType === $axisTypeProduct) {
            $isCompanyAxis = true;
            $axisArr[] = 'company_id';
        }

        if ($axisType === $axisTypeProduct) {
            $isProductAxis = true;
            $axisArr[] = 'product_id';
        }

        if ($channelAxis === '1') {
            $isChannelAxis = true;
            $axisArr[] = 'channel_id';
        }

        $isConditionCross = $division == 'condition_cross';

        $isHousehold = false;

        if (in_array('household', $codes) || $isConditionCross) {
            $isHousehold = true;
        }

        if ($conv15SecFlag === null) {
        } elseif ($conv15SecFlag == 1) {
            // する
            $bindings[':conv15SecFlag'] = 1;
        } else {
            // しない
            $bindings[':conv15SecFlag'] = 15;
        }

        // create fq_list
        $sql = '';
        $sql .= ' CREATE TEMPORARY TABLE fq_list ( ';
        $sql .= '   cm_id VARCHAR(32), ';
        $sql .= '   date datetime, ';
        $sql .= '   started_at datetime, ';
        $sql .= '   time_group varchar(255), ';
        $sql .= '   prog_id VARCHAR(32), ';
        $sql .= '   duration int, ';
        $sql .= '   paneler_id int, ';
        $sql .= '   household_id int, ';
        $sql .= '   company_id int, ';
        $sql .= '   product_id int, ';
        $sql .= '   channel_id int, ';
        $sql .= '   total_reach int, ';
        $sql .= '   prev_total_reach int, ';
        $sql .= '   single_reach int, ';
        $sql .= '   hh_total_reach int, ';
        $sql .= '   hh_prev_total_reach int, ';
        $sql .= '   hh_single_reach int ';
        $sql .= ' ) DISTKEY (cm_id) SORTKEY (paneler_id); ';
        $result = $this->select($sql);

        $cmOn = '';
        $fqBindings = [];
        $companyBind = $this->createArrayBindParam('companyIds', [
            'companyIds' => $companyIds,
        ], $fqBindings);
        $fqBindings[':startDate'] = $startDate;
        $fqBindings[':endDate'] = $endDate;
        $progStart = (new Carbon($startDate))->subDay();
        $progEnd = (new Carbon($endDate))->addDay();
        $fqBindings[':progStartDate'] = $progStart->format('Ymd') . '0000000000';
        $fqBindings[':progEndDate'] = $progEnd->format('Ymd') . '9999999999';
        $cmOn .= '        ON cv.cm_id = cl.cm_id ';
        $cmOn .= '        AND cv.started_at = cl.started_at ';
        $cmOn .= '        AND cv.prog_id BETWEEN :progStartDate AND :progEndDate ';
        $cmOn .= '        AND cv.prog_id = cl.prog_id ';
        $cmOn .= '        AND cv.date BETWEEN :startDate  AND :endDate ';

        if (count($companyIds) > 0) {
            $cmOn .= ' AND cv.company_id IN (' . implode(',', $companyBind) . ')';
        }

        if ($isGross || $isRtTotal) {
            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE rt_total_cm_viewers AS ';
            $sql .= '   SELECT ';
            $sql .= '     cm_id ';
            $sql .= '     , started_at ';
            $sql .= '     , prog_id ';
            $sql .= '     , date ';
            $sql .= '     , company_id ';
            $sql .= '     , paneler_id ';
            $sql .= '     , SUM(views) views ';
            $sql .= '   FROM ( ';
            $sql .= '   SELECT ';
            $sql .= '     cl.cm_id ';
            $sql .= '     , cl.started_at ';
            $sql .= '     , cl.prog_id ';
            $sql .= '     , cv.date ';
            $sql .= '     , cv.company_id ';
            $sql .= '     , cv.paneler_id ';
            $sql .= '     , 1 views ';
            $sql .= '   FROM ';
            $sql .= '     cm_list cl ';
            $sql .= '   INNER JOIN ';
            $sql .= '     cm_viewers cv ';
            $sql .= $cmOn;

            $sql .= '   UNION ALL ';
            $sql .= '   SELECT ';
            $sql .= '     cl.cm_id ';
            $sql .= '     , cl.started_at ';
            $sql .= '     , cl.prog_id ';
            $sql .= '     , cv.date ';
            $sql .= '     , cv.company_id ';
            $sql .= '     , cv.paneler_id ';
            $sql .= '     , cv.views ';
            $sql .= '   FROM ';
            $sql .= '     cm_list cl ';
            $sql .= '   INNER JOIN ';
            $sql .= '     ts_cm_viewers cv ';
            $sql .= $cmOn;
            $sql .= '   AND cv.c_index = 7';
            $sql .= '   ) cv ';
            $sql .= '   GROUP BY ';
            $sql .= '     cm_id ';
            $sql .= '     , started_at ';
            $sql .= '     , prog_id ';
            $sql .= '     , date ';
            $sql .= '     , company_id ';
            $sql .= '     , paneler_id ';
            $result = $this->select($sql, $fqBindings);
        }

        // insert fq_list
        $sql = '';
        $sql .= ' INSERT INTO fq_list  ';
        $sql .= '  SELECT ';
        $sql .= '    cl.cm_id ';
        $sql .= '    , cl.date ';
        $sql .= '    , cl.started_at ';
        $sql .= '    , cl.time_group ';
        $sql .= '    , cl.prog_id ';
        $sql .= '    , cl.duration ';
        $sql .= '    , cv.paneler_id ';
        $sql .= '    , household_id ';
        $sql .= '    , cl.company_id ';
        $sql .= '    , cl.product_id ';
        $sql .= '    , cl.channel_id ';

        if ($isRtTotal) {
            $sql .= '    , SUM(SUM(cv.views)) OVER ( ';
            $sql .= '      PARTITION BY cv.paneler_id ';
            $sql .= $isCompanyAxis ? ', cl.company_id' : '';
            $sql .= $isProductAxis ? ', cl.product_id' : '';
            $sql .= $isChannelAxis ? ', cl.channel_id' : '';
            $sql .= '    ORDER BY cl.started_at ROWS UNBOUNDED PRECEDING ';
            $sql .= '    ) total_reach ';

            $sql .= '    , COALESCE(SUM(SUM(cv.views)) OVER ( ';
            $sql .= '      PARTITION BY cv.paneler_id ';
            $sql .= $isCompanyAxis ? ', cl.company_id' : '';
            $sql .= $isProductAxis ? ', cl.product_id' : '';
            $sql .= $isChannelAxis ? ', cl.channel_id' : '';
            $sql .= '    ORDER BY cl.started_at ROWS BETWEEN UNBOUNDED PRECEDING AND 1 PRECEDING ';
            $sql .= '    ), 0) prev_total_reach ';

            $sql .= '    , SUM(cv.views) single_reach ';

            $sql .= '    , CASE WHEN household_id IS NULL THEN 0 ELSE DENSE_RANK() OVER (';
            $sql .= '      PARTITION BY s.household_id ';
            $sql .= $isCompanyAxis ? ', cl.company_id' : '';
            $sql .= $isProductAxis ? ', cl.product_id' : '';
            $sql .= $isChannelAxis ? ', cl.channel_id' : '';
            $sql .= '        ORDER BY cl.started_at ';
            $sql .= '    ) END hh_total_reach ';
            $sql .= '    , CASE WHEN household_id IS NULL THEN 0 ELSE hh_total_reach - 1 END hh_prev_total_reach ';
            $sql .= '    , COUNT(DISTINCT s.household_id) hh_single_reach ';
        } else {
            $sql .= '    , SUM(COUNT(cv.paneler_id)) OVER ( ';
            $sql .= '      PARTITION BY cv.paneler_id ';
            $sql .= $isCompanyAxis ? ', cl.company_id' : '';
            $sql .= $isProductAxis ? ', cl.product_id' : '';
            $sql .= $isChannelAxis ? ', cl.channel_id' : '';
            $sql .= '    ORDER BY cl.started_at ROWS UNBOUNDED PRECEDING ';
            $sql .= '    ) total_reach ';

            $sql .= '    , COALESCE(SUM(COUNT(cv.paneler_id)) OVER ( ';
            $sql .= '      PARTITION BY cv.paneler_id ';
            $sql .= $isCompanyAxis ? ', cl.company_id' : '';
            $sql .= $isProductAxis ? ', cl.product_id' : '';
            $sql .= $isChannelAxis ? ', cl.channel_id' : '';
            $sql .= '    ORDER BY cl.started_at ROWS BETWEEN UNBOUNDED PRECEDING AND 1 PRECEDING ';
            $sql .= '    ), 0) prev_total_reach ';

            $sql .= '    , COUNT(cv.paneler_id) single_reach ';

            $sql .= '    , CASE WHEN household_id IS NULL THEN 0 ELSE DENSE_RANK() OVER (';
            $sql .= '      PARTITION BY s.household_id ';
            $sql .= $isCompanyAxis ? ', cl.company_id' : '';
            $sql .= $isProductAxis ? ', cl.product_id' : '';
            $sql .= $isChannelAxis ? ', cl.channel_id' : '';
            $sql .= '        ORDER BY cl.started_at ';
            $sql .= '    ) END hh_total_reach ';
            $sql .= '    , CASE WHEN household_id IS NULL THEN 0 ELSE hh_total_reach - 1 END hh_prev_total_reach ';
            $sql .= '    , COUNT(DISTINCT s.household_id) hh_single_reach ';
        }

        $sql .= '  FROM ';
        $sql .= '    cm_list cl ';

        if ($isRt) {
            $sql .= '  LEFT JOIN cm_viewers cv ';
            $sql .= $cmOn;
        } elseif ($isTs) {
            $sql .= '  LEFT JOIN ts_cm_viewers cv ';
            $sql .= $cmOn;
            $sql .= '    AND cv.c_index = 7 ';
        } elseif ($isGross || $isRtTotal) {
            $sql .= '  LEFT JOIN rt_total_cm_viewers cv ';
            $sql .= $cmOn;
        }
        $sql .= '  LEFT JOIN ';

        if ($isRt) {
            $sql .= '   samples s ';
        } else {
            $sql .= '   ts_samples s ';
        }
        $sql .= '  ON ';
        $sql .= "    s.code = 'household' AND s.paneler_id = cv.paneler_id ";
        $sql .= '  GROUP BY ';
        $sql .= '      cv.paneler_id ';
        $sql .= '    , s.household_id ';
        $sql .= '    , cl.time_group ';
        $sql .= '    , cl.company_id ';
        $sql .= '    , cl.product_id ';
        $sql .= '    , cl.channel_id ';
        $sql .= '    , cl.cm_id ';
        $sql .= '    , cl.date ';
        $sql .= '    , cl.started_at ';
        $sql .= '    , cl.prog_id ';
        $sql .= '    , cl.duration ;';
        $this->insertTemporaryTable($sql, $fqBindings);

        $sql = '';
        $sql .= ' ANALYZE fq_list; ';
        $this->select($sql);

        $partition = '';
        $hPartition = '';

        if (count($axisArr) > 0) {
            $partition = ' PARTITION BY cl.' . implode(',cl.', $axisArr);
            $hPartition = ' PARTITION BY ' . implode(',', $axisArr);
        }

        // category_sql
        $sql = '';
        $sql .= ' SELECT ';
        $sql .= '   @replace@ ::varchar(255) code ';

        if ($isCompanyAxis) {
            $sql .= ' ,cl.company_id ';
            $sql .= ' ,cl.company_name ';
        }

        if ($isProductAxis) {
            $sql .= ' ,cl.product_id ';
            $sql .= ' ,cl.product_name ';
        }

        if ($isChannelAxis) {
            $sql .= ' ,cl.channel_id ';
            $sql .= ' ,cl.channel_name ';
        }
        $sql .= '   , cl.date ';
        $sql .= '   , COALESCE( ROUND( SUM(SUM(single_reach * (CASE WHEN :conv15SecFlag = 1 THEN cl.duration::numeric / 15 ELSE 1 END))) OVER (' . $partition . ' ORDER BY cl.date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) ::numeric / rt_number * 100, 1), 0) as grp_summary ';
        $sql .= "   , COALESCE( ROUND( SUM(SUM(CASE WHEN cl.time_group = '05:00～07:59' THEN single_reach ELSE 0 END * (CASE WHEN :conv15SecFlag = 1 THEN cl.duration::numeric / 15 ELSE 1 END))) OVER (" . $partition . ' ORDER BY cl.date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) ::numeric / rt_number * 100, 1), 0) time_group_1_grp ';
        $sql .= "   , COALESCE( ROUND( SUM(SUM(CASE WHEN cl.time_group = '08:00～11:59' THEN single_reach ELSE 0 END * (CASE WHEN :conv15SecFlag = 1 THEN cl.duration::numeric / 15 ELSE 1 END))) OVER (" . $partition . ' ORDER BY cl.date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) ::numeric / rt_number * 100, 1), 0) time_group_2_grp ';
        $sql .= "   , COALESCE( ROUND( SUM(SUM(CASE WHEN cl.time_group = '12:00～17:59' THEN single_reach ELSE 0 END * (CASE WHEN :conv15SecFlag = 1 THEN cl.duration::numeric / 15 ELSE 1 END))) OVER (" . $partition . ' ORDER BY cl.date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) ::numeric / rt_number * 100, 1), 0) time_group_3_grp ';
        $sql .= "   , COALESCE( ROUND( SUM(SUM(CASE WHEN cl.time_group = '18:00～22:59' THEN single_reach ELSE 0 END * (CASE WHEN :conv15SecFlag = 1 THEN cl.duration::numeric / 15 ELSE 1 END))) OVER (" . $partition . ' ORDER BY cl.date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) ::numeric / rt_number * 100, 1), 0) time_group_4_grp ';
        $sql .= "   , COALESCE( ROUND( SUM(SUM(CASE WHEN cl.time_group = '23:00～23:59' THEN single_reach ELSE 0 END * (CASE WHEN :conv15SecFlag = 1 THEN cl.duration::numeric / 15 ELSE 1 END))) OVER (" . $partition . ' ORDER BY cl.date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) ::numeric / rt_number * 100, 1), 0) time_group_5_grp ';
        $sql .= "   , COALESCE( ROUND( SUM(SUM(CASE WHEN cl.time_group = '24:00～28:59' THEN single_reach ELSE 0 END * (CASE WHEN :conv15SecFlag = 1 THEN cl.duration::numeric / 15 ELSE 1 END))) OVER (" . $partition . ' ORDER BY cl.date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) ::numeric / rt_number * 100, 1), 0) time_group_6_grp ';

        $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 1 AND prev_total_reach < 1 THEN 1 ELSE 0 END)) OVER (' . $partition . ' ORDER BY cl.date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq01 ';
        $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 2 AND prev_total_reach < 2 THEN 1 ELSE 0 END)) OVER (' . $partition . ' ORDER BY cl.date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq02 ';
        $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 3 AND prev_total_reach < 3 THEN  1 ELSE 0 END)) OVER (' . $partition . ' ORDER BY cl.date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq03 ';
        $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 4 AND prev_total_reach < 4 THEN  1 ELSE 0 END)) OVER (' . $partition . ' ORDER BY cl.date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq04 ';
        $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 5 AND prev_total_reach < 5 THEN  1 ELSE 0 END)) OVER (' . $partition . ' ORDER BY cl.date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq05 ';
        $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 6 AND prev_total_reach < 6 THEN  1 ELSE 0 END)) OVER (' . $partition . ' ORDER BY cl.date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq06 ';
        $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 7 AND prev_total_reach < 7 THEN  1 ELSE 0 END)) OVER (' . $partition . ' ORDER BY cl.date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq07 ';
        $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 8 AND prev_total_reach < 8 THEN  1 ELSE 0 END)) OVER (' . $partition . ' ORDER BY cl.date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq08 ';
        $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 9 AND prev_total_reach < 9 THEN  1 ELSE 0 END)) OVER (' . $partition . ' ORDER BY cl.date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq09 ';
        $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 10 AND prev_total_reach < 10 THEN  1 ELSE 0 END)) OVER (' . $partition . ' ORDER BY cl.date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq10 ';
        $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 11 AND prev_total_reach < 11 THEN  1 ELSE 0 END)) OVER (' . $partition . ' ORDER BY cl.date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq11 ';
        $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 12 AND prev_total_reach < 12 THEN  1 ELSE 0 END)) OVER (' . $partition . ' ORDER BY cl.date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq12 ';
        $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 13 AND prev_total_reach < 13 THEN  1 ELSE 0 END)) OVER (' . $partition . ' ORDER BY cl.date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq13 ';
        $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 14 AND prev_total_reach < 14 THEN  1 ELSE 0 END)) OVER (' . $partition . ' ORDER BY cl.date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq14 ';
        $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 15 AND prev_total_reach < 15 THEN  1 ELSE 0 END)) OVER (' . $partition . ' ORDER BY cl.date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq15 ';
        $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 16 AND prev_total_reach < 16 THEN  1 ELSE 0 END)) OVER (' . $partition . ' ORDER BY cl.date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq16 ';
        $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 17 AND prev_total_reach < 17 THEN  1 ELSE 0 END)) OVER (' . $partition . ' ORDER BY cl.date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq17 ';
        $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 18 AND prev_total_reach < 18 THEN  1 ELSE 0 END)) OVER (' . $partition . ' ORDER BY cl.date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq18 ';
        $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 19 AND prev_total_reach < 19 THEN  1 ELSE 0 END)) OVER (' . $partition . ' ORDER BY cl.date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq19 ';
        $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 20 AND prev_total_reach < 20 THEN  1 ELSE 0 END)) OVER (' . $partition . ' ORDER BY cl.date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq20 ';
        $sql .= '   , COALESCE( ROUND(SUM(SUM(single_reach)) OVER (' . $partition . ' ORDER BY cl.date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) ::numeric / t_fq01, 1), 0) reach_avg ';
        $sql .= '   , t_fq01 - t_fq02 s_fq01 ';
        $sql .= '   , t_fq02 - t_fq03 s_fq02 ';
        $sql .= '   , t_fq03 - t_fq04 s_fq03 ';
        $sql .= '   , t_fq04 - t_fq05 s_fq04 ';
        $sql .= '   , t_fq05 - t_fq06 s_fq05 ';
        $sql .= '   , t_fq06 - t_fq07 s_fq06 ';
        $sql .= '   , t_fq07 - t_fq08 s_fq07 ';
        $sql .= '   , t_fq08 - t_fq09 s_fq08 ';
        $sql .= '   , t_fq09 - t_fq10 s_fq09 ';
        $sql .= '   , t_fq10 - t_fq11 s_fq10 ';
        $sql .= '   , t_fq11 - t_fq12 s_fq11 ';
        $sql .= '   , t_fq12 - t_fq13 s_fq12 ';
        $sql .= '   , t_fq13 - t_fq14 s_fq13 ';
        $sql .= '   , t_fq14 - t_fq15 s_fq14 ';
        $sql .= '   , t_fq15 - t_fq16 s_fq15 ';
        $sql .= '   , t_fq16 - t_fq17 s_fq16 ';
        $sql .= '   , t_fq17 - t_fq18 s_fq17 ';
        $sql .= '   , t_fq18 - t_fq19 s_fq18 ';
        $sql .= '   , t_fq19 - t_fq20 s_fq19 ';
        $sql .= '   , rt_number ';
        $sql .= ' FROM ';

        if ($period === 'cm') {
            $sql .= '    cm_list cl ';
        } else {
            $sql .= '    adjust_cm_list cl ';
        }
        $sql .= ' LEFT JOIN ';
        $sql .= '   fq_list rl ';
        $sql .= ' ON ';
        $sql .= '   cl.cm_id = rl.cm_id AND cl.started_at = rl.started_at AND cl.prog_id = rl.prog_id ';

        if ($isRt) {
            $sql .= ' AND rl.paneler_id IN (SELECT paneler_id FROM samples s WHERE s.code = @replace@) ';
        } else {
            $sql .= ' AND rl.paneler_id IN (SELECT paneler_id FROM ts_samples s WHERE s.code = @replace@) ';
        }

        if ($isRt) {
            $sql .= ' CROSS JOIN ( SELECT number rt_number FROM rt_numbers WHERE code = @replace@) codes ';
        } else {
            $sql .= ' CROSS JOIN ( SELECT number rt_number FROM ts_numbers WHERE code = @replace@) codes ';
        }
        $sql .= ' GROUP BY ';
        $sql .= '   cl.date ';

        if ($isCompanyAxis) {
            $sql .= ' ,cl.company_id ';
            $sql .= ' ,cl.company_name ';
        }

        if ($isProductAxis) {
            $sql .= ' ,cl.product_id ';
            $sql .= ' ,cl.product_name ';
        }

        if ($isChannelAxis) {
            $sql .= ' ,cl.channel_id ';
            $sql .= ' ,cl.channel_name ';
        }
        $sql .= '   , rt_number     ';

        $unionSql = [];
        $divCodes = [];

        if ($isConditionCross) {
            $bindings[':condition_cross_code_tac'] = 'condition_cross';
            $unionSql[] = str_replace('@replace@', ':condition_cross_code_tac', $sql);
        } else {
            $tmpArr = [];
            $divCodes = array_filter($codes, function ($v, $k) {
                return $v != 'household';
            }, ARRAY_FILTER_USE_BOTH);

            if (count($divCodes) > 0) {
                foreach ($divCodes as $code) {
                    $bindings[':' . $code] = $code;
                    $unionSql[] = str_replace('@replace@', ':' . $code, $sql);
                }
            }
        }

        if ($isHousehold) {
            $sql = '';
            $sql .= ' SELECT ';
            $sql .= "   'household'::varchar(255) code ";

            if ($isCompanyAxis) {
                $sql .= ' ,company_id ';
                $sql .= ' ,company_name ';
            }

            if ($isProductAxis) {
                $sql .= ' ,product_id ';
                $sql .= ' ,product_name ';
            }

            if ($isChannelAxis) {
                $sql .= ' ,channel_id ';
                $sql .= ' ,channel_name ';
            }
            $sql .= '   , date ';
            $sql .= '   , COALESCE( ROUND( SUM(SUM(single_reach * (CASE WHEN :conv15SecFlag = 1 THEN duration::numeric / 15 ELSE 1 END))) OVER (' . $hPartition . ' ORDER BY date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) ::numeric / rt_number * 100, 1), 0) as grp_summary ';
            $sql .= "   , COALESCE( ROUND( SUM(SUM(CASE WHEN time_group = '05:00～07:59' THEN single_reach ELSE 0 END * (CASE WHEN :conv15SecFlag = 1 THEN duration::numeric / 15 ELSE 1 END))) OVER (" . $hPartition . ' ORDER BY date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) ::numeric / rt_number * 100, 1), 0) time_group_1_grp ';
            $sql .= "   , COALESCE( ROUND( SUM(SUM(CASE WHEN time_group = '08:00～11:59' THEN single_reach ELSE 0 END * (CASE WHEN :conv15SecFlag = 1 THEN duration::numeric / 15 ELSE 1 END))) OVER (" . $hPartition . ' ORDER BY date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) ::numeric / rt_number * 100, 1), 0) time_group_2_grp ';
            $sql .= "   , COALESCE( ROUND( SUM(SUM(CASE WHEN time_group = '12:00～17:59' THEN single_reach ELSE 0 END * (CASE WHEN :conv15SecFlag = 1 THEN duration::numeric / 15 ELSE 1 END))) OVER (" . $hPartition . ' ORDER BY date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) ::numeric / rt_number * 100, 1), 0) time_group_3_grp ';
            $sql .= "   , COALESCE( ROUND( SUM(SUM(CASE WHEN time_group = '18:00～22:59' THEN single_reach ELSE 0 END * (CASE WHEN :conv15SecFlag = 1 THEN duration::numeric / 15 ELSE 1 END))) OVER (" . $hPartition . ' ORDER BY date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) ::numeric / rt_number * 100, 1), 0) time_group_4_grp ';
            $sql .= "   , COALESCE( ROUND( SUM(SUM(CASE WHEN time_group = '23:00～23:59' THEN single_reach ELSE 0 END * (CASE WHEN :conv15SecFlag = 1 THEN duration::numeric / 15 ELSE 1 END))) OVER (" . $hPartition . ' ORDER BY date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) ::numeric / rt_number * 100, 1), 0) time_group_5_grp ';
            $sql .= "   , COALESCE( ROUND( SUM(SUM(CASE WHEN time_group = '24:00～28:59' THEN single_reach ELSE 0 END * (CASE WHEN :conv15SecFlag = 1 THEN duration::numeric / 15 ELSE 1 END))) OVER (" . $hPartition . ' ORDER BY date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) ::numeric / rt_number * 100, 1), 0) time_group_6_grp ';
            $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 1 AND prev_total_reach < 1 THEN  1 ELSE 0 END)) OVER (' . $hPartition . ' ORDER BY date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq01 ';
            $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 2 AND prev_total_reach < 2 THEN  1 ELSE 0 END)) OVER (' . $hPartition . ' ORDER BY date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq02 ';
            $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 3 AND prev_total_reach < 3 THEN  1 ELSE 0 END)) OVER (' . $hPartition . ' ORDER BY date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq03 ';
            $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 4 AND prev_total_reach < 4 THEN  1 ELSE 0 END)) OVER (' . $hPartition . ' ORDER BY date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq04 ';
            $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 5 AND prev_total_reach < 5 THEN  1 ELSE 0 END)) OVER (' . $hPartition . ' ORDER BY date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq05 ';
            $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 6 AND prev_total_reach < 6 THEN  1 ELSE 0 END)) OVER (' . $hPartition . ' ORDER BY date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq06 ';
            $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 7 AND prev_total_reach < 7 THEN  1 ELSE 0 END)) OVER (' . $hPartition . ' ORDER BY date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq07 ';
            $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 8 AND prev_total_reach < 8 THEN  1 ELSE 0 END)) OVER (' . $hPartition . ' ORDER BY date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq08 ';
            $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 9 AND prev_total_reach < 9 THEN  1 ELSE 0 END)) OVER (' . $hPartition . ' ORDER BY date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq09 ';
            $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 10 AND prev_total_reach < 10 THEN  1 ELSE 0 END)) OVER (' . $hPartition . ' ORDER BY date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq10 ';
            $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 11 AND prev_total_reach < 11 THEN  1 ELSE 0 END)) OVER (' . $hPartition . ' ORDER BY date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq11 ';
            $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 12 AND prev_total_reach < 12 THEN  1 ELSE 0 END)) OVER (' . $hPartition . ' ORDER BY date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq12 ';
            $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 13 AND prev_total_reach < 13 THEN  1 ELSE 0 END)) OVER (' . $hPartition . ' ORDER BY date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq13 ';
            $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 14 AND prev_total_reach < 14 THEN  1 ELSE 0 END)) OVER (' . $hPartition . ' ORDER BY date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq14 ';
            $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 15 AND prev_total_reach < 15 THEN  1 ELSE 0 END)) OVER (' . $hPartition . ' ORDER BY date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq15 ';
            $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 16 AND prev_total_reach < 16 THEN  1 ELSE 0 END)) OVER (' . $hPartition . ' ORDER BY date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq16 ';
            $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 17 AND prev_total_reach < 17 THEN  1 ELSE 0 END)) OVER (' . $hPartition . ' ORDER BY date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq17 ';
            $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 18 AND prev_total_reach < 18 THEN  1 ELSE 0 END)) OVER (' . $hPartition . ' ORDER BY date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq18 ';
            $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 19 AND prev_total_reach < 19 THEN  1 ELSE 0 END)) OVER (' . $hPartition . ' ORDER BY date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq19 ';
            $sql .= '   , COALESCE( SUM (SUM(CASE WHEN total_reach >= 20 AND prev_total_reach < 20 THEN  1 ELSE 0 END)) OVER (' . $hPartition . ' ORDER BY date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW), 0) ::numeric  as t_fq20 ';
            $sql .= '   , COALESCE( ROUND( SUM(SUM(single_reach)) OVER (' . $hPartition . ' ORDER BY date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) ::numeric / CASE WHEN t_fq01 = 0 THEN 1 ELSE t_fq01  END, 1), 0) reach_avg ';
            $sql .= '   , t_fq01 - t_fq02 s_fq01 ';
            $sql .= '   , t_fq02 - t_fq03 s_fq02 ';
            $sql .= '   , t_fq03 - t_fq04 s_fq03 ';
            $sql .= '   , t_fq04 - t_fq05 s_fq04 ';
            $sql .= '   , t_fq05 - t_fq06 s_fq05 ';
            $sql .= '   , t_fq06 - t_fq07 s_fq06 ';
            $sql .= '   , t_fq07 - t_fq08 s_fq07 ';
            $sql .= '   , t_fq08 - t_fq09 s_fq08 ';
            $sql .= '   , t_fq09 - t_fq10 s_fq09 ';
            $sql .= '   , t_fq10 - t_fq11 s_fq10 ';
            $sql .= '   , t_fq11 - t_fq12 s_fq11 ';
            $sql .= '   , t_fq12 - t_fq13 s_fq12 ';
            $sql .= '   , t_fq13 - t_fq14 s_fq13 ';
            $sql .= '   , t_fq14 - t_fq15 s_fq14 ';
            $sql .= '   , t_fq15 - t_fq16 s_fq15 ';
            $sql .= '   , t_fq16 - t_fq17 s_fq16 ';
            $sql .= '   , t_fq17 - t_fq18 s_fq17 ';
            $sql .= '   , t_fq18 - t_fq19 s_fq18 ';
            $sql .= '   , t_fq19 - t_fq20 s_fq19 ';
            $sql .= '   , rt_number ';
            $sql .= ' FROM ';
            $sql .= '   (SELECT DISTINCT ';
            $sql .= '       cl.cm_id ';
            $sql .= '     , cl.date ';
            $sql .= '     , cl.time_group ';
            $sql .= '     , cl.started_at ';
            $sql .= '     , cl.prog_id ';
            $sql .= '     , rl.household_id ';
            $sql .= '     , cl.company_id ';
            $sql .= '     , cl.product_id ';
            $sql .= '     , cl.channel_id ';
            $sql .= '     , cl.company_name ';
            $sql .= '     , cl.product_name ';
            $sql .= '     , cl.channel_name ';
            $sql .= '     , rl.hh_prev_total_reach prev_total_reach ';
            $sql .= '     , rl.hh_total_reach total_reach ';
            $sql .= '     , rl.hh_single_reach single_reach ';
            $sql .= '     , cl.duration ';
            $sql .= '   FROM ';

            if ($period === 'cm') {
                $sql .= '    cm_list cl ';
            } else {
                $sql .= '    adjust_cm_list cl ';
            }
            $sql .= ' LEFT JOIN ';
            $sql .= '   fq_list rl ';
            $sql .= ' ON ';
            $sql .= '   cl.cm_id = rl.cm_id AND cl.started_at = rl.started_at AND cl.prog_id = rl.prog_id ';

            if ($isRt) {
                $sql .= " AND rl.paneler_id IN (SELECT paneler_id FROM samples s WHERE s.code = 'household') ";
            } else {
                $sql .= " AND rl.paneler_id IN (SELECT paneler_id FROM ts_samples s WHERE s.code = 'household') ";
            }

            $sql .= '  ) rl  ';

            if ($isRt) {
                $sql .= " CROSS JOIN ( SELECT number rt_number FROM rt_numbers WHERE code = 'household') ";
            } else {
                $sql .= " CROSS JOIN ( SELECT number rt_number FROM ts_numbers WHERE code = 'household') ";
            }
            $sql .= ' GROUP BY ';
            $sql .= '   date ';

            if ($isCompanyAxis) {
                $sql .= ' ,company_id ';
                $sql .= ' ,company_name ';
            }

            if ($isProductAxis) {
                $sql .= ' ,product_id ';
                $sql .= ' ,product_name ';
            }

            if ($isChannelAxis) {
                $sql .= ' ,channel_id ';
                $sql .= ' ,channel_name ';
            }
            $sql .= '   , rt_number     ';
            $unionSql[] = $sql;
        }

        $sql = '';
        $sql .= ' CREATE TEMPORARY TABLE results ( ';
        $sql .= '    row_number int ';
        $sql .= '    , date datetime ';
        $sql .= '    , company_id int ';
        $sql .= '    , company_name varchar(1000) ';
        $sql .= '    , product_id int ';
        $sql .= '    , product_name varchar(1000) ';
        $sql .= '    , channel_id int ';
        $sql .= '    , channel_name varchar(255) ';
        $sql .= '    , code varchar(255) ';
        $sql .= '    , grp_summary real ';
        $sql .= '    , time_group_1_grp real ';
        $sql .= '    , time_group_2_grp real ';
        $sql .= '    , time_group_3_grp real ';
        $sql .= '    , time_group_4_grp real ';
        $sql .= '    , time_group_5_grp real ';
        $sql .= '    , time_group_6_grp real ';

        $sql .= '    , freq_1 real ';
        $sql .= '    , freq_2 real ';
        $sql .= '    , freq_3 real ';
        $sql .= '    , freq_4 real ';
        $sql .= '    , freq_5 real ';
        $sql .= '    , freq_6 real ';
        $sql .= '    , freq_7 real ';
        $sql .= '    , freq_8 real ';
        $sql .= '    , freq_9 real ';
        $sql .= '    , freq_10 real ';
        $sql .= '    , freq_11 real ';
        $sql .= '    , freq_12 real ';
        $sql .= '    , freq_13 real ';
        $sql .= '    , freq_14 real ';
        $sql .= '    , freq_15 real ';
        $sql .= '    , freq_16 real ';
        $sql .= '    , freq_17 real ';
        $sql .= '    , freq_18 real ';
        $sql .= '    , freq_19 real ';
        $sql .= '    , freq_20 real ';
        $sql .= '    , reach_1 real ';
        $sql .= '    , reach_2 real ';
        $sql .= '    , reach_3 real ';
        $sql .= '    , reach_4 real ';
        $sql .= '    , reach_5 real ';
        $sql .= '    , reach_6 real ';
        $sql .= '    , reach_7 real ';
        $sql .= '    , reach_8 real ';
        $sql .= '    , reach_9 real ';
        $sql .= '    , reach_10 real ';
        $sql .= '    , reach_avg real ';
        $sql .= '    , rt_number int ';
        $sql .= '    , next varchar(255) ';
        $sql .= ' ) SORTKEY (row_number); ';
        $result = $this->select($sql);

        $sql = '';
        $sql .= ' INSERT INTO results  ';
        $sql .= 'WITH list AS ( ' . implode(' UNION ALL ', $unionSql) . ' ) ';
        $sql .= ' SELECT ';
        $sql .= '    ROW_NUMBER() OVER (ORDER BY ';

        if (count($axisArr) > 0) {
            $orderBy = [];

            if ($isCompanyAxis) {
                $orderBy[] = 'company_id';
            }

            if ($isProductAxis) {
                $orderBy[] = 'product_name';
            }

            if ($isChannelAxis) {
                $orderBy[] = 'channel_id';
            }
            $sql .= implode(',', $orderBy) . ', ';
        }
        $sql .= ' code, date ';
        $sql .= ' ) row_number ';
        $sql .= '    , date ';

        if ($isCompanyAxis) {
            $sql .= ' ,company_id ';
            $sql .= ' ,company_name ';
        } else {
            $sql .= ' ,0 AS company_id ';
            $sql .= " ,'' company_name ";
        }

        if ($isProductAxis) {
            $sql .= ' ,product_id ';
            $sql .= ' ,product_name ';
        } else {
            $sql .= ' ,0 AS product_id ';
            $sql .= " ,'' AS product_name ";
        }

        if ($isChannelAxis) {
            $sql .= ' ,channel_id ';
            $sql .= ' ,channel_name ';
        } else {
            $sql .= ' ,0 AS channel_id ';
            $sql .= " ,'' AS channel_name ";
        }
        $sql .= '    , l.code ';
        $sql .= '    , grp_summary ';
        $sql .= '    , time_group_1_grp ';
        $sql .= '    , time_group_2_grp ';
        $sql .= '    , time_group_3_grp ';
        $sql .= '    , time_group_4_grp ';
        $sql .= '    , time_group_5_grp ';
        $sql .= '    , time_group_6_grp ';
        $sql .= '    , ROUND(s_fq01 / rt_number * 100 ,1) freq_1  ';
        $sql .= '    , ROUND(s_fq02 / rt_number * 100 ,1) freq_2  ';
        $sql .= '    , ROUND(s_fq03 / rt_number * 100 ,1) freq_3  ';
        $sql .= '    , ROUND(s_fq04 / rt_number * 100 ,1) freq_4  ';
        $sql .= '    , ROUND(s_fq05 / rt_number * 100 ,1) freq_5  ';
        $sql .= '    , ROUND(s_fq06 / rt_number * 100 ,1) freq_6  ';
        $sql .= '    , ROUND(s_fq07 / rt_number * 100 ,1) freq_7  ';
        $sql .= '    , ROUND(s_fq08 / rt_number * 100 ,1) freq_8  ';
        $sql .= '    , ROUND(s_fq09 / rt_number * 100 ,1) freq_9  ';
        $sql .= '    , ROUND(s_fq10 / rt_number * 100 ,1) freq_10  ';
        $sql .= '    , ROUND(s_fq11 / rt_number * 100 ,1) freq_11  ';
        $sql .= '    , ROUND(s_fq12 / rt_number * 100 ,1) freq_12  ';
        $sql .= '    , ROUND(s_fq13 / rt_number * 100 ,1) freq_13  ';
        $sql .= '    , ROUND(s_fq14 / rt_number * 100 ,1) freq_14  ';
        $sql .= '    , ROUND(s_fq15 / rt_number * 100 ,1) freq_15  ';
        $sql .= '    , ROUND(s_fq16 / rt_number * 100 ,1) freq_16  ';
        $sql .= '    , ROUND(s_fq17 / rt_number * 100 ,1) freq_17  ';
        $sql .= '    , ROUND(s_fq18 / rt_number * 100 ,1) freq_18  ';
        $sql .= '    , ROUND(s_fq19 / rt_number * 100 ,1) freq_19  ';
        $sql .= '    , ROUND(t_fq20 / rt_number * 100 ,1) freq_20  ';
        $sql .= '    , ROUND(t_fq01 / rt_number * 100 ,1) reach_1  ';
        $sql .= '    , ROUND(t_fq02 / rt_number * 100 ,1) reach_2  ';
        $sql .= '    , ROUND(t_fq03 / rt_number * 100 ,1) reach_3  ';
        $sql .= '    , ROUND(t_fq04 / rt_number * 100 ,1) reach_4  ';
        $sql .= '    , ROUND(t_fq05 / rt_number * 100 ,1) reach_5  ';
        $sql .= '    , ROUND(t_fq06 / rt_number * 100 ,1) reach_6  ';
        $sql .= '    , ROUND(t_fq07 / rt_number * 100 ,1) reach_7  ';
        $sql .= '    , ROUND(t_fq08 / rt_number * 100 ,1) reach_8  ';
        $sql .= '    , ROUND(t_fq09 / rt_number * 100 ,1) reach_9  ';
        $sql .= '    , ROUND(t_fq10 / rt_number * 100 ,1) reach_10  ';

        $sql .= '    , reach_avg ';
        $sql .= '    , rt_number ';
        $next = 'code';

        if (!$isConditionCross && count($codes) === 1) {
            if ($isCompanyAxis) {
                $next = 'company_id';
            }

            if ($isProductAxis) {
                $next = 'product_id';
            }

            if ($isChannelAxis) {
                $next = 'channel_id';
            }
        }
        $sql .= "    , LEAD(${next}) OVER (ORDER BY ";

        if (count($axisArr) > 0) {
            $sql .= implode(',', $axisArr) . ', code, date';
        } else {
            $sql .= ' code, date ';
        }
        $sql .= '    ) as next ';
        $sql .= '  FROM ';
        $sql .= '   list l; ';

        $results = $this->insertTemporaryTable($sql, $bindings);

        $sql = ' ANALYZE results; ';

        $this->select($sql);
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param string $cmType
     * @param string $cmSeconds
     * @param int $regionId
     * @param string $division
     * @param null|array $codes
     * @param array $conditionCross
     * @param array $companyIds
     * @param null|array $productIds
     * @param null|array $cmIds
     * @param array $channels
     * @param null|bool $conv15SecFlag
     * @param null|array $progIds
     * @param bool $straddlingFlg
     * @param array $dataType
     * @param string $axisType
     * @param null|string $channelAxis
     * @param string $period
     * @param array $dataTypeFlags
     * @param string $axisTypeProduct
     * @param string $axisTypeCompany
     */
    public function commonCreateTempTables(String $startDate, String $endDate, String $startTime, String $endTime, String $cmType, String $cmSeconds, int $regionId, String $division, ?array $codes, array $conditionCross, array $companyIds, ?array $productIds, ?array $cmIds, array $channels, ?bool $conv15SecFlag, ?array $progIds, bool $straddlingFlg, array $dataType, string $axisType, ?string $channelAxis, string $period, array $dataTypeFlags, string $axisTypeProduct, string $axisTypeCompany): void
    {
        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = array_values($dataTypeFlags);

        $bindings = [];
        $divisionKey = $division . '_';
        $isCompanyAxis = false;
        $isProductAxis = false;
        $isChannelAxis = false;

        if ($axisType === $axisTypeCompany || $axisType === $axisTypeProduct) {
            $isCompanyAxis = true;
        }

        if ($axisType === $axisTypeProduct) {
            $isProductAxis = true;
        }

        if ($channelAxis === '1') {
            $isChannelAxis = true;
        }
        $cs = new Carbon($startDate);
        $ce = new Carbon($endDate);
        $hashDateArr = [];

        if ($period !== 'cm') {
            $tmpDate = '';

            for ($i = 0; $ce->gte($cs); $i++) {
                $tmpCs = $cs->copy();

                if ($period === 'day') {
                    $hashDateArr[] = "       SELECT TO_TIMESTAMP('" . $tmpCs->format('Y-m-d 00:00:00') . "', 'YYYY-MM-DD HH24:MI:SS') date ";
                } elseif ($period === 'week') {
                    $tmpCs->startOfWeek();

                    if ($tmpDate !== $tmpCs->format('Y-m-d')) {
                        $hashDateArr[] = "       SELECT TO_TIMESTAMP('" . $tmpCs->format('Y-m-d 00:00:00') . "', 'YYYY-MM-DD HH24:MI:SS') date ";
                        $tmpDate = $tmpCs->format('Y-m-d');
                    }
                } elseif ($period === 'month') {
                    if ($tmpDate !== $tmpCs->format('Y-m')) {
                        $hashDateArr[] = "       SELECT TO_TIMESTAMP('" . $tmpCs->format('Y-m-01 00:00:00') . "', 'YYYY-MM-DD HH24:MI:SS') date ";
                        $tmpDate = $tmpCs->format('Y-m');
                    }
                }
                $cs->addDay();
            }
        }

        switch ($period) {
            case 'cm':
                $dateSql = 'started_at date ';
                break;
            case 'day':
                $dateSql = " TO_TIMESTAMP(TO_CHAR(date, 'YYYY-MM-dd 00:00:00'), 'YYYY-MM-DD HH24:MI:SS') date ";
                break;
            case 'week':
                $dateSql = " TO_TIMESTAMP(TO_CHAR(DATE_TRUNC('week', date ),'YYYY-MM-dd 00:00:00'), 'YYYY-MM-DD HH24:MI:SS') date ";
                break;
            case 'month':
                $dateSql = " TO_TIMESTAMP(TO_CHAR(DATE_TRUNC('month', date),'YYYY-MM-01 00:00:00'), 'YYYY-MM-DD HH24:MI:SS') date ";
                break;
        }
        $isConditionCross = $division == 'condition_cross';
        $isHousehold = false;

        if (in_array('household', $codes) || $isConditionCross) {
            $isHousehold = true;
        }
        list($withWwhere, $codeBind, $channelBind, $companyBind, $productIdsBind, $cmIdsBind, $progIdsBind) = $this->createListWhere($bindings, $startDate, $endDate, $startTime, $endTime, $cmType, $cmSeconds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $progIds, $channels, $straddlingFlg, $conv15SecFlag);
        unset($bindings[':conv15SecFlag']);
        // cm_list
        $sql = '';
        $sql .= ' CREATE TEMPORARY TABLE cm_list ( ';
        $sql .= '   cm_id VARCHAR(32), ';
        $sql .= '   time_box_id int, ';
        $sql .= '   date datetime, ';
        $sql .= '   started_at datetime, ';
        $sql .= '   prog_id VARCHAR(32), ';
        $sql .= '   duration int, ';
        $sql .= '   company_id int, ';
        $sql .= '   product_id int, ';
        $sql .= '   channel_id int, ';
        $sql .= '   time_group varchar(255), ';
        $sql .= '   company_name varchar(1000), ';
        $sql .= '   product_name varchar(1000), ';
        $sql .= '   channel_name varchar(255) ';
        $sql .= ' ) DISTKEY (cm_id); ';
        $result = $this->select($sql);

        $sql = '';
        $sql .= ' ANALYZE cm_list; ';
        $this->select($sql);
        $sql = '';
        $sql .= ' INSERT INTO cm_list WITH cm_list AS ( ';
        $sql .= '   SELECT ';
        $sql .= '      c.cm_id ';
        $sql .= '     , c.time_box_id ';
        $sql .= "     , ${dateSql} ";
        $sql .= '     , c.started_at ';
        $sql .= '     , c.prog_id ';
        $sql .= '     , c.duration ';
        $sql .= '     , company_id ';
        $sql .= '     , product_id ';
        $sql .= '     , channel_id ';
        $sql .= "    , CASE WHEN TO_CHAR(c.started_at, 'HH24MI') BETWEEN '0500' AND '0759' THEN '05:00～07:59' ";
        $sql .= "           WHEN TO_CHAR(c.started_at, 'HH24MI') BETWEEN '0800' AND '1159' THEN '08:00～11:59' ";
        $sql .= "           WHEN TO_CHAR(c.started_at, 'HH24MI') BETWEEN '1200' AND '1759' THEN '12:00～17:59' ";
        $sql .= "           WHEN TO_CHAR(c.started_at, 'HH24MI') BETWEEN '1800' AND '2259' THEN '18:00～22:59' ";
        $sql .= "           WHEN TO_CHAR(c.started_at, 'HH24MI') BETWEEN '2300' AND '2359' THEN '23:00～23:59' ";
        $sql .= "           WHEN TO_CHAR(c.started_at, 'HH24MI') BETWEEN '0000' AND '0459' THEN '24:00～28:59' ELSE 'ELSE' END time_group ";
        $sql .= '   FROM ';
        $sql .= '     commercials c  ';
        $sql .= $withWwhere;
        $sql .= '   AND region_id = :regionId ';
        $sql .= ' ) ';
        $sql .= ' SELECT c.*, cp.name company_name, pr.name product_name, ch.display_name channel_name FROM cm_list c ';
        $sql .= '   INNER JOIN ';
        $sql .= '     companies cp ';
        $sql .= '   ON cp.id =  c.company_id ';
        $sql .= '   INNER JOIN ';
        $sql .= '     products pr ';
        $sql .= '   ON pr.id =  c.product_id ';
        $sql .= '   INNER JOIN ';
        $sql .= '     channels ch ';
        $sql .= '   ON ch.id =  c.channel_id; ';
        $result = $this->insertTemporaryTable($sql, $bindings);

        if ($period !== 'cm') {
            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE date_list AS ';
            $sql .= '   SELECT ';
            $sql .= '     date ';
            $sql .= '   FROM ';
            $sql .= '     (' . implode(' UNION ', $hashDateArr) . ') dates; ';
            $result = $this->select($sql);
            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE adjust_cm_list AS ';
            $sql .= ' WITH cm_master AS (  ';
            $sql .= '   SELECT ';
            $sql .= '     company_id ';
            $sql .= '     , cm_id ';
            $sql .= '     , product_id ';
            $sql .= '     , channel_id ';
            $sql .= '     , company_name ';
            $sql .= '     , product_name ';
            $sql .= '     , channel_name ';
            $sql .= '     , (SELECT MIN(date) FROM date_list) min_date ';
            $sql .= '     , (SELECT MAX(date) FROM date_list) max_date  ';
            $sql .= '   FROM ';
            $sql .= '     cm_list  ';
            $sql .= '   GROUP BY ';
            $sql .= '     company_id ';
            $sql .= '     , product_id ';
            $sql .= '     , cm_id ';
            $sql .= '     , channel_id ';
            $sql .= '     , company_name ';
            $sql .= '     , product_name ';
            $sql .= '     , channel_name ';
            $sql .= ' )  ';
            $sql .= ' , adjust_cm_list AS (  ';
            $sql .= '   SELECT ';
            $sql .= '     *  ';
            $sql .= '   FROM ';
            $sql .= '     cm_master cm  ';
            $sql .= '     INNER JOIN date_list dl  ';
            $sql .= '       ON dl.date BETWEEN cm.min_date AND cm.max_date ';
            $sql .= ' )  ';
            $sql .= ' SELECT ';
            $sql .= '   *  ';
            $sql .= ' FROM ';
            $sql .= '   cm_list  ';
            $sql .= ' UNION ALL  ';
            $sql .= ' SELECT ';
            $sql .= '   acl.cm_id ';
            $sql .= '   , - 1 time_box_id ';
            $sql .= '   , acl.date ';
            $sql .= '   , acl.date started_at ';
            $sql .= "   , 'acl_dummy' prog_id ";
            $sql .= '   , 15 duration ';
            $sql .= '   , acl.company_id ';
            $sql .= '   , product_id ';
            $sql .= '   , channel_id ';
            $sql .= "   , 'else' time_group ";
            $sql .= '   , company_name ';
            $sql .= '   , product_name ';
            $sql .= '   , channel_name ';
            $sql .= ' FROM ';
            $sql .= '   adjust_cm_list acl  ';
            $sql .= ' WHERE ';
            $sql .= '   NOT EXISTS (  ';
            $sql .= '     SELECT ';
            $sql .= '       *  ';
            $sql .= '     FROM ';
            $sql .= '       cm_list cl  ';
            $sql .= '     WHERE ';
            $sql .= '       acl.cm_id = cl.cm_id  ';
            $sql .= '       AND acl.date = cl.date ';
            $sql .= '       AND acl.company_id = cl.company_id ';
            $sql .= '       AND acl.product_id = cl.product_id ';
            $sql .= '       AND acl.channel_id = cl.channel_id ';
            $sql .= '   )  ';
            $sql .= ' ORDER BY ';
            $sql .= '   company_id ';
            $sql .= '   , cm_id ';
            $sql .= '   , date;  ';
            $result = $this->select($sql);
        }

        $crossJoin = '';
        $crossJoinWhere = '';
        $sampleBindings = [];
        // cross joinの中身
        $divCodes = [];

        if ($isConditionCross) {
            $sampleBindings[':condition_cross_code'] = 'condition_cross';
            $crossJoin .= ' ( ';
            $crossJoin .= ' (SELECT :condition_cross_code ::varchar(255) as code) ';
            $crossJoin .= ' ) codes ';
        } else {
            $tmpArr = [];
            $divCodes = array_filter($codes, function ($v, $k) {
                return $v != 'household';
            }, ARRAY_FILTER_USE_BOTH);

            if (count($divCodes) > 0) {
                foreach ($divCodes as $code) {
                    $key = ':union_' . $divisionKey . $code;
                    $sampleBindings[$key] = $code;
                    $tmpSql = ' ( ';
                    $tmpSql .= ' SELECT ' . $key . '::varchar(255) as code ';
                    $tmpSql .= ' ) ';
                    array_push($tmpArr, $tmpSql);
                }
            } else {
                $tmpArr[] = '(SELECT 1::VARCHAR(255) as code ) ';
            }
            $crossJoin .= ' ( ' . implode(' UNION ', $tmpArr) . ' ) AS codes ';
        }
        // codeごとのwhere句
        $crossJoinWhere .= '   ( ';

        if ($isConditionCross) {
            $crossJoinWhere .= " codes.code = 'condition_cross' ";
            $crossJoinWhere .= $this->createConditionCrossSql($conditionCross, $sampleBindings);
        } else {
            $crossJoinWhere .= $this->createCrossJoinWhereClause($division, $divCodes, $sampleBindings, true);
        }
        $crossJoinWhere .= '   ) ';

        // samples
        if ($isRt || $isGross || $isRtTotal) {
            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE  samples AS WITH time_box_ids AS (SELECT DISTINCT time_box_id FROM cm_list)  ';
            $sql .= '  , household_ids AS (SELECT household_id FROM time_box_households tbh WHERE time_box_id IN (SELECT time_box_id FROM time_box_ids) GROUP BY household_id HAVING COUNT(household_id) = (SELECT COUNT(time_box_id) FROM time_box_ids) ) ';
            $sql .= '  , paneler_ids AS ( SELECT paneler_id FROM time_box_panelers WHERE time_box_id IN (SELECT time_box_id FROM time_box_ids) GROUP BY paneler_id HAVING COUNT(paneler_id) = (SELECT COUNT(time_box_id) FROM time_box_ids))  ';
            $sql .= '  , panelers AS ( SELECT tbp.* FROM time_box_panelers tbp WHERE tbp.time_box_id IN (SELECT time_box_id FROM time_box_ids) AND tbp.paneler_id IN (SELECT paneler_id FROM paneler_ids))  ';
            $sql .= '  , samples AS ( ';
            $sql .= '    SELECT tbp.paneler_id, tbp.household_id, codes.code ';
            $sql .= '    FROM panelers tbp ';
            $sql .= '      CROSS JOIN ' . $crossJoin;
            $sql .= '    WHERE ';
            $sql .= '      tbp.time_box_id = (SELECT MAX(time_box_id) FROM time_box_ids) ';
            $sql .= '      AND ' . $crossJoinWhere;
            $sql .= '   UNION ALL  ';
            $sql .= '   SELECT  ';
            $sql .= '     tbp.paneler_id  ';
            $sql .= '     , tbp.household_id  ';
            $sql .= '     , codes.code  ';
            $sql .= '   FROM  ';
            $sql .= '     time_box_panelers tbp  ';
            $sql .= "   CROSS JOIN ((SELECT 'household' ::VARCHAR (255) AS code)) codes  ";
            $sql .= '   WHERE  ';
            $sql .= '     tbp.time_box_id IN (SELECT time_box_id FROM time_box_ids)  ';
            $sql .= '     AND tbp.household_id IN (SELECT household_id FROM household_ids)  ';
            $sql .= '   GROUP BY  ';
            $sql .= '     tbp.paneler_id  ';
            $sql .= '     , tbp.household_id  ';
            $sql .= '     , codes.code  ';
            $sql .= '  ) ';
            $sql .= ' SELECT * FROM samples; ';
            $result = $this->select($sql, $sampleBindings);

            // rt_numbers
            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE  rt_numbers AS WITH rt_numbers AS (  ';
            $sql .= ' SELECT ';
            $sql .= "   CASE WHEN code = 'household' THEN (COUNT(DISTINCT household_id)) ";
            $sql .= "        WHEN code != 'household' THEN COUNT(paneler_id) END number  ";
            $sql .= '   , code  ';
            $sql .= ' FROM  ';
            $sql .= '   samples  ';
            $sql .= ' GROUP BY  ';
            $sql .= '   code  ';
            $sql .= ' )  ';
            $sql .= ' SELECT * FROM rt_numbers; ';
            $result = $this->select($sql);
        }

        if ($isTs || $isGross || $isRtTotal) {
            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE  ts_samples AS WITH time_box_ids AS (SELECT DISTINCT time_box_id FROM cm_list)  ';
            $sql .= '  , household_ids AS (SELECT household_id FROM ts_time_box_households tbh WHERE time_box_id IN (SELECT time_box_id FROM time_box_ids) GROUP BY household_id HAVING COUNT(household_id) = (SELECT COUNT(time_box_id) FROM time_box_ids) ) ';
            $sql .= '  , paneler_ids AS ( SELECT paneler_id FROM ts_time_box_panelers WHERE time_box_id IN (SELECT time_box_id FROM time_box_ids) GROUP BY paneler_id HAVING COUNT(paneler_id) = (SELECT COUNT(time_box_id) FROM time_box_ids))  ';
            $sql .= '  , panelers AS ( SELECT tbp.* FROM ts_time_box_panelers tbp WHERE tbp.time_box_id IN (SELECT time_box_id FROM time_box_ids) AND tbp.paneler_id IN (SELECT paneler_id FROM paneler_ids))  ';
            $sql .= '  , ts_samples AS ( ';
            $sql .= '    SELECT tbp.paneler_id, tbp.household_id, codes.code ';
            $sql .= '    FROM panelers tbp ';
            $sql .= '      CROSS JOIN ' . $crossJoin;
            $sql .= '    WHERE ';
            $sql .= '      tbp.time_box_id = (SELECT MAX(time_box_id) FROM time_box_ids) ';
            $sql .= '      AND ' . $crossJoinWhere;
            $sql .= '   UNION ALL  ';
            $sql .= '   SELECT  ';
            $sql .= '     tbp.paneler_id  ';
            $sql .= '     , tbp.household_id  ';
            $sql .= '     , codes.code  ';
            $sql .= '   FROM  ';
            $sql .= '     ts_time_box_panelers tbp  ';
            $sql .= "   CROSS JOIN ((SELECT 'household' ::VARCHAR (255) AS code)) codes  ";
            $sql .= '   WHERE  ';
            $sql .= '     tbp.time_box_id IN (SELECT time_box_id FROM time_box_ids)  ';
            $sql .= '     AND tbp.household_id IN (SELECT household_id FROM household_ids)  ';
            $sql .= '   GROUP BY  ';
            $sql .= '     tbp.paneler_id  ';
            $sql .= '     , tbp.household_id  ';
            $sql .= '     , codes.code  ';
            $sql .= '  ) ';
            $sql .= ' SELECT * FROM ts_samples; ';
            $result = $this->select($sql, $sampleBindings);

            // rt_numbers
            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE  ts_numbers AS WITH ts_numbers AS (  ';
            $sql .= ' SELECT ';
            $sql .= "   CASE WHEN code = 'household' THEN (COUNT(DISTINCT household_id)) ";
            $sql .= "        WHEN code != 'household' THEN COUNT(paneler_id) END number  ";
            $sql .= '   , code  ';
            $sql .= ' FROM  ';
            $sql .= '   ts_samples  ';
            $sql .= ' GROUP BY  ';
            $sql .= '   code  ';
            $sql .= ' )  ';
            $sql .= ' SELECT * FROM ts_numbers; ';
            $result = $this->select($sql);
        }
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param string $cmType
     * @param string $cmSeconds
     * @param int $regionId
     * @param string $division
     * @param array $codes
     * @param array $conditionCross
     * @param array $companyIds
     * @param array $productIds
     * @param array $cmIds
     * @param array $channels
     * @param bool $conv15SecFlag
     * @param array $progIds
     * @param bool $straddlingFlg
     * @param array $dataType
     * @param array $reachAndFrequencyGroupingUnit
     * @param array $dataTypeFlags
     * @return array
     */
    public function getChartResults(String $startDate, String $endDate, String $startTime, String $endTime, String $cmType, String $cmSeconds, int $regionId, String $division, array $codes, array $conditionCross, array $companyIds, array $productIds, array $cmIds, array $channels, bool $conv15SecFlag, array $progIds, bool $straddlingFlg, array $dataType, array $reachAndFrequencyGroupingUnit, array $dataTypeFlags): array
    {
        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = array_values($dataTypeFlags);

        $bindings = [];
        $divisionKey = $division . '_';
        $isConditionCross = $division == 'condition_cross';
        $units = [];
        $before = 0;

        foreach ($reachAndFrequencyGroupingUnit as $n) {
            $units[] = [$before + 1, $n];
            $before = $n;
        }
        $units[] = $before + 1;
        $isHousehold = false;

        if (in_array('household', $codes) || $isConditionCross) {
            $isHousehold = true;
        }
        $fqBindings = [];

        if ($conv15SecFlag === null) {
        } elseif ($conv15SecFlag == 1) {
            // する
            $fqBindings[':conv15SecFlag'] = 1;
        } else {
            // しない
            $fqBindings[':conv15SecFlag'] = 15;
        }

        $cvBindings = [];
        $cmOn = '';
        $companyBind = $this->createArrayBindParam('companyIds', [
            'companyIds' => $companyIds,
        ], $cvBindings);
        $cvBindings[':startDate'] = $startDate;
        $cvBindings[':endDate'] = $endDate;
        $progStart = (new Carbon($startDate))->subDay();
        $progEnd = (new Carbon($endDate))->addDay();
        $cvBindings[':progStartDate'] = $progStart->format('Ymd') . '0000000000';
        $cvBindings[':progEndDate'] = $progEnd->format('Ymd') . '9999999999';
        $cmOn .= '        ON cv.cm_id = cl.cm_id ';
        $cmOn .= '        AND cv.started_at = cl.started_at ';
        $cmOn .= '        AND cv.prog_id BETWEEN :progStartDate AND :progEndDate ';
        $cmOn .= '        AND cv.prog_id = cl.prog_id ';
        $cmOn .= '        AND cv.date BETWEEN :startDate  AND :endDate ';

        if (count($companyIds) > 0) {
            $cmOn .= ' AND cv.company_id IN (' . implode(',', $companyBind) . ')';
        }

        if ($isRt || $isGross || $isRtTotal) {
            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE cv_list ( ';
            $sql .= '   cm_id VARCHAR(32) ';
            $sql .= '   , prog_id VARCHAR(32) ';
            $sql .= '   , started_at datetime ';
            $sql .= '   , duration int ';
            $sql .= '   , household_id int ';
            $sql .= '   , paneler_id int ';
            $sql .= '   , views int ';
            $sql .= ' )DISTSTYLE ALL SORTKEY(paneler_id); ';
            $this->select($sql);

            $sql = '';
            $sql .= ' INSERT INTO cv_list SELECT ';
            $sql .= '   cl.cm_id ';
            $sql .= '   , cl.prog_id ';
            $sql .= '   , cl.started_at ';
            $sql .= '   , cl.duration ';
            $sql .= '   , s.household_id ';
            $sql .= '   , cv.paneler_id ';
            $sql .= '   , 1 views ';
            $sql .= ' FROM ';
            $sql .= '   cm_list cl ';
            $sql .= ' INNER JOIN ';
            $sql .= '   cm_viewers cv ';
            $sql .= $cmOn;
            $sql .= ' INNER JOIN ';
            $sql .= '   samples s ';
            $sql .= "   ON s.code = 'household' ";
            $sql .= '   AND s.paneler_id = cv.paneler_id ';
            $sql .= ' GROUP BY ';
            $sql .= '   cl.cm_id ';
            $sql .= '   , cl.prog_id ';
            $sql .= '   , cl.started_at ';
            $sql .= '   , cl.duration ';
            $sql .= '   , s.household_id ';
            $sql .= '   , cv.paneler_id ';
            $sql .= ';';
            $this->insertTemporaryTable($sql, $cvBindings);

            $sql = '';
            $sql .= ' ANALYZE cv_list; ';
            $this->select($sql);
        }

        if ($isTs || $isGross || $isRtTotal) {
            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE ts_cv_list ( ';
            $sql .= '   cm_id VARCHAR(32) ';
            $sql .= '   , prog_id VARCHAR(32) ';
            $sql .= '   , started_at datetime ';
            $sql .= '   , duration int ';
            $sql .= '   , household_id int ';
            $sql .= '   , paneler_id int ';
            $sql .= '   , views int ';
            $sql .= ' )DISTSTYLE ALL SORTKEY(paneler_id); ';
            $this->select($sql);

            $sql = '';
            $sql .= ' INSERT INTO ts_cv_list SELECT ';
            $sql .= '   cl.cm_id ';
            $sql .= '   , cl.prog_id ';
            $sql .= '   , cl.started_at ';
            $sql .= '   , cl.duration ';
            $sql .= '   , s.household_id ';
            $sql .= '   , cv.paneler_id ';
            $sql .= '   , cv.views ';
            $sql .= ' FROM ';
            $sql .= '   cm_list cl ';
            $sql .= ' INNER JOIN ';
            $sql .= '   ts_cm_viewers cv  ';
            $sql .= $cmOn;
            $sql .= '   AND cv.c_index = 7 ';
            $sql .= ' INNER JOIN ';
            $sql .= '   ts_samples s ';
            $sql .= "   ON s.code = 'household' ";
            $sql .= '   AND s.paneler_id = cv.paneler_id ';
            $sql .= ' GROUP BY ';
            $sql .= '   cl.cm_id ';
            $sql .= '   , cl.prog_id ';
            $sql .= '   , cl.started_at ';
            $sql .= '   , cl.duration ';
            $sql .= '   , s.household_id ';
            $sql .= '   , cv.paneler_id ';
            $sql .= '   , cv.views ';
            $sql .= ';';
            $this->insertTemporaryTable($sql, $cvBindings);

            $sql = '';
            $sql .= ' ANALYZE ts_cv_list; ';
            $this->select($sql);
        }

        if ($isGross || $isRtTotal) {
            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE rt_total_cv_list ( ';
            $sql .= '   cm_id VARCHAR(32) ';
            $sql .= '   , prog_id VARCHAR(32) ';
            $sql .= '   , started_at datetime ';
            $sql .= '   , duration int ';
            $sql .= '   , household_id int ';
            $sql .= '   , paneler_id int ';
            $sql .= '   , views int ';
            $sql .= ' )DISTSTYLE ALL SORTKEY(paneler_id); ';
            $this->select($sql);

            $sql = '';
            $sql .= ' INSERT INTO rt_total_cv_list ';
            $sql .= '   SELECT ';
            $sql .= '     cm_id ';
            $sql .= '     , prog_id ';
            $sql .= '     , started_at ';
            $sql .= '     , duration ';
            $sql .= '     , household_id ';
            $sql .= '     , paneler_id ';
            $sql .= '     , SUM(views) views ';
            $sql .= '   FROM ';
            $sql .= '     ( ';
            $sql .= '       SELECT ';
            $sql .= '         cm_id ';
            $sql .= '         , prog_id ';
            $sql .= '         , started_at ';
            $sql .= '         , duration ';
            $sql .= '         , household_id ';
            $sql .= '         , paneler_id ';
            $sql .= '         , views ';
            $sql .= '       FROM ';
            $sql .= '         cv_list cv ';
            $sql .= "       WHERE EXISTS(SELECT 1 FROM ts_samples ts WHERE ts.code = 'household' AND cv.paneler_id = ts.paneler_id )";
            $sql .= '       UNION ALL ';
            $sql .= '       SELECT ';
            $sql .= '         cm_id ';
            $sql .= '         , prog_id ';
            $sql .= '         , started_at ';
            $sql .= '         , duration ';
            $sql .= '         , household_id ';
            $sql .= '         , paneler_id ';
            $sql .= '         , views ';
            $sql .= '       FROM ';
            $sql .= '         ts_cv_list cv ';
            $sql .= '     ) cv ';
            $sql .= '   GROUP BY ';
            $sql .= '     cm_id ';
            $sql .= '     , prog_id ';
            $sql .= '     , started_at ';
            $sql .= '     , duration ';
            $sql .= '     , household_id ';
            $sql .= '     , paneler_id ';
            $sql .= ';';
            $this->insertTemporaryTable($sql);

            $sql = '';
            $sql .= ' ANALYZE rt_total_cv_list; ';
            $this->select($sql);
        }

        $viewBindings = [];

        if ($conv15SecFlag === null) {
        } elseif ($conv15SecFlag == 1) {
            // する
            $viewBindings[':conv15SecFlag'] = 1;
        } else {
            // しない
            $viewBindings[':conv15SecFlag'] = 15;
        }

        if ($isRt || $isTs || $isGross) {
            $sampleViewSql = '';
            $sampleViewSql .= ' CREATE TEMPORARY TABLE %spersonal_viewers AS WITH personal_viewers AS (  ';
            $sampleViewSql .= '   select ';
            $sampleViewSql .= '     paneler_id ';
            $sampleViewSql .= '     , COUNT(paneler_id) view_numbers ';
            $sampleViewSql .= '     , duration  ';
            $sampleViewSql .= '   FROM ';
            $sampleViewSql .= '     %scv_list cv  ';
            $sampleViewSql .= '   GROUP BY ';
            $sampleViewSql .= '     paneler_id ';
            $sampleViewSql .= '     , duration ';
            $sampleViewSql .= ' )  ';
            $sampleViewSql .= ' select ';
            $sampleViewSql .= '   paneler_id ';
            $sampleViewSql .= '   , SUM(view_numbers) view_numbers ';
            $sampleViewSql .= '   , SUM(view_numbers * (CASE WHEN :conv15SecFlag = 1 THEN duration::numeric / 15 ELSE 1 END)) duration_view_numbers  ';
            $sampleViewSql .= ' FROM ';
            $sampleViewSql .= '   personal_viewers  ';
            $sampleViewSql .= ' group by ';
            $sampleViewSql .= '   paneler_id;  ';

            $householdViewSql = '';
            $householdViewSql .= ' CREATE TEMPORARY TABLE %shousehold_viewers AS WITH household_viewers AS ( ';
            $householdViewSql .= '   SELECT ';
            $householdViewSql .= '     household_id ';
            $householdViewSql .= '     , COUNT(DISTINCT household_id) view_numbers  ';
            $householdViewSql .= '     , duration ';
            $householdViewSql .= '   FROM ';
            $householdViewSql .= '     %scv_list cv  ';
            $householdViewSql .= '   GROUP BY ';
            $householdViewSql .= '     household_id ';
            $householdViewSql .= '     , cm_id ';
            $householdViewSql .= '     , started_at ';
            $householdViewSql .= '     , prog_id ';
            $householdViewSql .= '     , duration ';
            $householdViewSql .= ' )  ';
            $householdViewSql .= ' select ';
            $householdViewSql .= '   household_id ';
            $householdViewSql .= '   , SUM(view_numbers) view_numbers ';
            $householdViewSql .= '   , SUM(view_numbers * (CASE WHEN :conv15SecFlag = 1 THEN duration::numeric / 15 ELSE 1 END)) duration_view_numbers  ';
            $householdViewSql .= ' from ';
            $householdViewSql .= '   household_viewers  ';
            $householdViewSql .= ' group by ';
            $householdViewSql .= '   household_id;  ';

            if ($isRt) {
                $sql = sprintf($sampleViewSql, '', '');
                $this->select($sql, $viewBindings);
                $sql = sprintf($householdViewSql, '', '');
                $this->select($sql, $viewBindings);
            }

            if ($isTs) {
                $sql = sprintf($sampleViewSql, 'ts_', 'ts_');
                $this->select($sql, $viewBindings);
                $sql = sprintf($householdViewSql, 'ts_', 'ts_');
                $this->select($sql, $viewBindings);
            }

            if ($isGross) {
                $sql = sprintf($sampleViewSql, 'gross_', 'rt_total_');
                $this->select($sql, $viewBindings);
                $sql = sprintf($householdViewSql, 'gross_', 'rt_total_');
                $this->select($sql, $viewBindings);
            }
        }

        if ($isRtTotal) {
            $sql = '';
            $sql .= ' CREATE TEMPORARY TABLE rt_total_personal_viewers AS WITH personal_viewers AS (  ';
            $sql .= '   select ';
            $sql .= '     paneler_id ';
            $sql .= '     , SUM(views) view_numbers ';
            $sql .= '     , duration  ';
            $sql .= '   FROM ';
            $sql .= '     rt_total_cv_list cv  ';
            $sql .= '   GROUP BY ';
            $sql .= '     paneler_id ';
            $sql .= '     , duration ';
            $sql .= ' )  ';
            $sql .= ' select ';
            $sql .= '   paneler_id ';
            $sql .= '   , SUM(view_numbers) view_numbers ';
            $sql .= '   , SUM(view_numbers * (CASE WHEN :conv15SecFlag = 1 THEN duration::numeric / 15 ELSE 1 END)) duration_view_numbers  ';
            $sql .= ' FROM ';
            $sql .= '   personal_viewers  ';
            $sql .= ' group by ';
            $sql .= '   paneler_id;  ';
            $this->select($sql, $viewBindings);
        }

        $columns = [];

        foreach ($units as $i => $unit) {
            if (is_array($unit)) {
                $condition = sprintf('BETWEEN %d AND %d', $unit[0], $unit[1]);
                $columns[] = "COALESCE(ROUND(SUM(CASE WHEN view_numbers ${condition} THEN 1 ELSE 0 END) ::numeric / number * 100, 1), 0, 0) freq_" . ($i + 1);
            } else {
                $condition = ' >= ' . $unit;
                $columns[] = "COALESCE(ROUND(SUM(CASE WHEN view_numbers ${condition} THEN 1 ELSE 0 END) ::numeric / number * 100, 1), 0, 0) freq_" . ($i + 1);
            }
        }

        // category_sql
        $sql = '';
        $sql .= ' SELECT ';
        $sql .= "   '%s' ::varchar(40) data_type ";
        $sql .= '   , @replace@ ::varchar(255) code ';
        $sql .= '   , COALESCE( ROUND( SUM(duration_view_numbers) ::numeric / number * 100, 1), 0) as grp ';
        $sql .= '   , COALESCE( SUM(CASE WHEN view_numbers >= 1 THEN 1 ELSE 0 END), 0) ::numeric  as t_fq1 ';
        $sql .= '   , COALESCE( ROUND(SUM(view_numbers) ::numeric / CASE WHEN t_fq1 = 0 THEN 1 ELSE t_fq1 END, 1), 0) average ';
        $sql .= '   , ' . implode(',', $columns);
        $sql .= '   , number ';
        $sql .= ' FROM ';
        $sql .= '   %s rl ';
        $sql .= ' CROSS JOIN ( SELECT number FROM %s WHERE code = @replace@) codes ';
        $sql .= ' WHERE rl.paneler_id IN (SELECT paneler_id FROM %s s WHERE s.code = @replace@) ';
        $sql .= ' GROUP BY ';
        $sql .= '   number     ';

        $unionSql = [];
        $divCodes = [];

        if ($isConditionCross) {
            $bindings[':condition_cross_code_tac'] = 'condition_cross';
            $codeSql = str_replace('@replace@', ':condition_cross_code_tac', $sql);

            if ($isRt) {
                $unionSql[] = sprintf($codeSql, 'rt', 'personal_viewers', 'rt_numbers', 'samples');
            }

            if ($isTs) {
                $unionSql[] = sprintf($codeSql, 'ts', 'ts_personal_viewers', 'ts_numbers', 'ts_samples');
            }

            if ($isGross) {
                $unionSql[] = sprintf($codeSql, 'gross', 'gross_personal_viewers', 'ts_numbers', 'ts_samples');
            }

            if ($isRtTotal) {
                $unionSql[] = sprintf($codeSql, 'rt_total', 'rt_total_personal_viewers', 'ts_numbers', 'ts_samples');
            }
        } else {
            $tmpArr = [];
            $divCodes = array_filter($codes, function ($v, $k) {
                return $v != 'household';
            }, ARRAY_FILTER_USE_BOTH);

            if (count($divCodes) > 0) {
                foreach ($divCodes as $code) {
                    $bindings[':' . $code] = $code;
                    $codeSql = str_replace('@replace@', ':' . $code, $sql);

                    if ($isRt) {
                        $unionSql[] = sprintf($codeSql, 'rt', 'personal_viewers', 'rt_numbers', 'samples');
                    }

                    if ($isTs) {
                        $unionSql[] = sprintf($codeSql, 'ts', 'ts_personal_viewers', 'ts_numbers', 'ts_samples');
                    }

                    if ($isGross) {
                        $unionSql[] = sprintf($codeSql, 'gross', 'gross_personal_viewers', 'ts_numbers', 'ts_samples');
                    }

                    if ($isRtTotal) {
                        $unionSql[] = sprintf($codeSql, 'rt_total', 'rt_total_personal_viewers', 'ts_numbers', 'ts_samples');
                    }
                }
            }
        }

        if ($isHousehold) {
            $sql = '';
            $sql .= ' SELECT ';
            $sql .= "   '%s' ::varchar(40) data_type ";
            $sql .= "   , 'household'::varchar(255) code ";
            $sql .= '   , COALESCE( ROUND( SUM(duration_view_numbers) ::numeric / number * 100, 1), 0) as grp ';
            $sql .= '   , COALESCE( SUM(CASE WHEN view_numbers >= 1 THEN 1 ELSE 0 END), 0) ::numeric  as t_fq1 ';
            $sql .= '   , COALESCE( ROUND(SUM(view_numbers) ::numeric / CASE WHEN t_fq1 = 0 THEN 1 ELSE t_fq1 END, 1), 0) average ';
            $sql .= '   , ' . implode(',', $columns);
            $sql .= '   , number ';
            $sql .= ' FROM ';
            $sql .= '   %s rl ';
            $sql .= " CROSS JOIN ( SELECT number FROM %s WHERE code = 'household') ";
            $sql .= ' GROUP BY ';
            $sql .= '   number ';

            if ($isRt) {
                $unionSql[] = sprintf($sql, 'rt', 'household_viewers', 'rt_numbers');
            }

            if ($isTs) {
                $unionSql[] = sprintf($sql, 'ts', 'ts_household_viewers', 'ts_numbers');
            }

            if ($isGross) {
                $unionSql[] = sprintf($sql, 'gross', 'gross_household_viewers', 'ts_numbers');
            }

            if ($isRtTotal) {
                $unionSql[] = sprintf($sql, 'rt_total', 'rt_total_household_viewers', 'ts_numbers');
            }
        }

        $sql = '';
        $sql .= ' WITH list AS ( ' . implode(' UNION ALL ', $unionSql) . ' ) ';
        $sql .= ' SELECT ';
        $sql .= '    n.code ';
        $sql .= '    , COALESCE(grp, 0) grp ';

        foreach ($units as $i => $unit) {
            $sql .= '    , COALESCE(freq_' . ($i + 1) . ', 0) freq_' . ($i + 1);
        }
        $sql .= '    , COALESCE(average, 0) average ';
        $sql .= '    , COALESCE(ROUND(t_fq1 / n.number * 100 ,1), 0) over_one ';

        if ($isRt && ($isTs || $isGross || $isRtTotal)) {
            $sql .= "    , CASE WHEN data_type = 'rt' THEN rt_number ELSE ts_number END number";
        } else {
            $sql .= '    , n.number ';
        }

        $sql .= '  FROM ';
        $sql .= '    list l ';
        $sql .= '  RIGHT JOIN ';

        if ($isRt && ($isTs || $isGross || $isRtTotal)) {
            $sql .= '    (SELECT rn.number rt_number, tn.number ts_number, code FROM rt_numbers rn INNER JOIN ts_numbers tn ON rn.code = tn.code) n ';
        } elseif ($isRt) {
            $sql .= '    rt_numbers n ';
        } else {
            $sql .= '    ts_numbers n ';
        }
        $sql .= '  ON n.code = l.code; ';

        $results = $this->select($sql, $bindings);
        return $results;
    }

    public function getProductNames(string $companyId, array $productIds): array
    {
        $bindings = [];
        $bindArr = $this->createArrayBindParam('productIds', [
            'productIds' => $productIds,
        ], $bindings);
        $bindings[':companyId'] = $companyId;
        $query = '';
        $query .= ' SELECT ';
        $query .= ' 	p.name ';
        $query .= ' FROM ';
        $query .= ' 	products p ';
        $query .= ' INNER JOIN ';
        $query .= '     companies c ';
        $query .= ' ON c.id = p.company_id ';
        $query .= ' WHERE ';
        $query .= ' 	p.id in (' . implode(',', $bindArr) . ')  ';
        $query .= ' AND ';
        $query .= '     p.company_id = :companyId ';
        $query .= ' ORDER BY ';
        $query .= ' 	p.id; ';
        $result = $this->select($query, $bindings);
        return $result;
    }

    public function getCsvButtonInfo(String $startDate, String $endDate, String $startTime, String $endTime, String $cmType, String $cmSeconds, int $regionId, String $division, ?array $codes, array $conditionCross, array $companyIds, ?array $productIds, ?array $cmIds, array $channels, ?bool $conv15SecFlag, ?array $progIds, bool $straddlingFlg, array $dataType, string $axisType, ?string $channelAxis, string $axisTypeProduct, string $axisTypeCompany): array
    {
        $bindings = [];

        $isCompanyAxis = false;
        $isProductAxis = false;
        $isChannelAxis = false;
        $axisSelectArr = [];

        if ($axisType === $axisTypeCompany || $axisType === $axisTypeProduct) {
            $isCompanyAxis = true;
            $axisSelectArr[] = 'company_id';
        }

        if ($axisType === $axisTypeProduct) {
            $isProductAxis = true;
            $axisSelectArr[] = 'product_id';
        }

        if ($channelAxis === '1') {
            $isChannelAxis = true;
            $axisSelectArr[] = 'channel_id';
        }

        if (!$isCompanyAxis && !$isProductAxis && !$isChannelAxis) {
            return [];
        }

        list($withWwhere, $codeBind, $channelBind, $companyBind, $productIdsBind, $cmIdsBind, $progIdsBind) = $this->createListWhere($bindings, $startDate, $endDate, $startTime, $endTime, $cmType, $cmSeconds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $progIds, $channels, $straddlingFlg);

        $query = '';
        $query .= ' WITH axis_cm AS ( ';
        $query .= '   SELECT ';
        $query .= implode(', ', $axisSelectArr);
        $query .= '     , COUNT(*) total_cnt ';
        $query .= '     , SUM(duration) total_duration ';
        $query .= '   FROM ';
        $query .= '     commercials c ';
        $query .= $withWwhere;
        $query .= '   AND region_id = :regionId ';
        $query .= '   GROUP BY ';
        $query .= implode(', ', $axisSelectArr);
        $query .= ' ) ';

        if ($isCompanyAxis) {
            $query .= ' , axis_companies AS ( ';
            $query .= '   SELECT ';
            $query .= '     id ';
            $query .= '     , name ';
            $query .= '   FROM ';
            $query .= '     companies ';
            $query .= '   WHERE ';
            $query .= '     id IN (' . implode(',', $companyBind) . ') ';
            $query .= ' ) ';
        }

        if ($isProductAxis && count($productIdsBind) > 0) {
            $query .= ' , axis_products AS ( ';
            $query .= '   SELECT ';
            $query .= '     id ';
            $query .= '     , company_id ';
            $query .= '     , name ';
            $query .= '   FROM ';
            $query .= '     products ';
            $query .= '   WHERE ';
            $query .= '     id IN (' . implode(',', $productIdsBind) . ') ';
            $query .= ' ) ';
        } elseif ($isProductAxis) {
            $query .= ' , axis_products AS ( ';
            $query .= '   SELECT ';
            $query .= '     p.id ';
            $query .= '     , p.company_id ';
            $query .= '     , p.name ';
            $query .= '   FROM ';
            $query .= '     axis_cm ac ';
            $query .= '   LEFT JOIN ';
            $query .= '     products p ';
            $query .= '   ON ';
            $query .= '     ac.product_id = p.id ';
            $query .= ' ) ';
        }

        if ($isChannelAxis) {
            $query .= ' , axis_channels AS ( ';
            $query .= '   SELECT ';
            $query .= '     id ';
            $query .= '     , display_name ';
            $query .= '   FROM ';
            $query .= '     channels ';
            $query .= '   WHERE ';
            $query .= '     id IN (' . implode(',', $channelBind) . ') ';
            $query .= ' ) ';
        }

        $query .= ' SELECT ';
        $query .= '  a.*  ';
        $query .= '  , COALESCE(ac.total_cnt, 0) total_cnt';
        $query .= '  , COALESCE(ac.total_duration, 0) total_duration';

        if ($isCompanyAxis) {
            $query .= '  , CASE WHEN ac.company_id IS NOT NULL THEN 1 ELSE 0 END has_advertising ';
        } else {
            $query .= '  , CASE WHEN ac.channel_id IS NOT NULL THEN 1 ELSE 0 END has_advertising ';
        }

        $query .= ' FROM ';
        $query .= '   axis_cm ac ';
        $query .= ' RIGHT JOIN ';
        $query .= '   ( ';

        if ($isProductAxis) {
            $query .= ' SELECT ';
            $query .= '   p.id product_id ';
            $query .= '   , p.company_id ';
            $query .= '   , p.name product_name ';
            $query .= '   , c.name company_name ';

            if ($isChannelAxis) {
                $query .= '   , ch.id channel_id ';
                $query .= '   , ch.display_name channel_name ';
            }
            $query .= ' FROM ';
            $query .= '   axis_products p ';
            $query .= ' LEFT JOIN ';
            $query .= '   axis_companies c ';
            $query .= ' ON ';
            $query .= '   p.company_id = c.id ';

            if ($isChannelAxis) {
                $query .= ' CROSS JOIN ';
                $query .= '   axis_channels ch ';
            }
        } elseif ($isCompanyAxis) {
            $query .= ' SELECT ';
            $query .= '   c.id company_id ';
            $query .= '   , c.name company_name ';

            if ($isChannelAxis) {
                $query .= '   , ch.id channel_id ';
                $query .= '   , ch.display_name channel_name ';
            }
            $query .= ' FROM ';
            $query .= '   axis_companies c ';

            if ($isChannelAxis) {
                $query .= ' CROSS JOIN ';
                $query .= '   axis_channels ch ';
            }
        } else {
            $query .= ' SELECT ';
            $query .= '   ch.id channel_id ';
            $query .= '   , ch.display_name channel_name ';
            $query .= ' FROM ';
            $query .= '   axis_channels ch ';
        }
        $query .= '   ) a ';
        $query .= ' ON ';
        $axisOn = [];

        if ($isProductAxis) {
            $axisOn[] = ' ac.product_id = a.product_id ';
        }

        if ($isCompanyAxis) {
            $axisOn[] = ' ac.company_id = a.company_id ';
        }

        if ($isChannelAxis) {
            $axisOn[] = ' ac.channel_id = a.channel_id ';
        }
        $query .= implode(' AND ', $axisOn);
        $query .= ' GROUP BY ';
        $groupArr = [];
        $groupArr[] = 'ac.total_cnt';
        $groupArr[] = 'ac.total_duration';

        if ($isCompanyAxis) {
            $groupArr[] = 'a.company_id';
            $groupArr[] = 'a.company_name';
        }

        if ($isProductAxis) {
            $groupArr[] = 'a.product_id';
            $groupArr[] = 'a.product_name';
        }

        if ($isChannelAxis) {
            $groupArr[] = 'a.channel_id';
            $groupArr[] = 'a.channel_name';
        }

        if ($isCompanyAxis) {
            $groupArr[] = 'ac.company_id';
        } else {
            $groupArr[] = 'ac.channel_id';
        }
        $query .= implode(', ', $groupArr);

        $query .= ' ORDER BY ';
        $orderArr = [];

        if ($isCompanyAxis) {
            $orderArr[] = 'a.company_id';
        }

        if ($isProductAxis) {
            $orderArr[] = 'a.product_name';
        }

        if ($isChannelAxis) {
            $orderArr[] = 'a.channel_id';
        }
        $query .= implode(', ', $orderArr);

        $result = $this->select($query, $bindings);

        return $result;
    }

    /**
     * @param array $bindings
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param string $cmType
     * @param string $cmSeconds
     * @param int $regionId
     * @param string $division
     * @param array $codes
     * @param array $conditionCross
     * @param array $companyIds
     * @param array $productIds
     * @param array $cmIds
     * @param array $progIds
     * @param array $channels
     * @param bool $straddlingFlg
     * @param bool $conv_15_sec_flag
     * @param null|bool $conv15SecFlag
     * @return array[]|string[]
     */
    private function createListWhere(array &$bindings, String $startDate, String $endDate, String $startTime, String $endTime, String $cmType, String $cmSeconds, int $regionId, String $division, ?array $codes, array $conditionCross, array $companyIds, ?array $productIds, ?array $cmIds, ?array $progIds, array $channels, bool $straddlingFlg, bool $conv15SecFlag = null)
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

        // 日付
        $bindings[':startDate'] = $startDate;
        $bindings[':endDate'] = $endDate;

        // 放送局
        $channelBind = $this->createArrayBindParam('channels', [
            'channels' => $channels,
        ], $bindings);

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

        // 番組ID
        $progIdsBind = $this->createArrayBindParam('progIds', [
            'progIds' => $progIds,
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

        // 番組名
        if (count($progIdsBind)) {
            $sql .= '   AND c.program_title IN (SELECT title From programs WHERE prog_id IN (' . implode(',', $progIdsBind) . ')) ';
        }
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

        // 地域コード
        $bindings[':regionId'] = $regionId;

        return [
            $sql,
            $codeBind,
            $channelBind,
            $companyBind,
            $productIdsBind,
            $cmIdsBind,
            $progIdsBind,
        ];
    }
}
