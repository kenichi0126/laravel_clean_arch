<?php

namespace Switchm\SmartApi\Queries\Dao\Rdb;

use Carbon\Carbon;

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
     * @return array
     */
    public function search(string $startDate, string $endDate, string $startTime, string $endTime, ?array $wdays, bool $holiday, ?array $channels, ?array $genres, ?array $programNames, string $division, ?array $conditionCross, ?array $codes, $order, ?int $length, int $regionId, ?int $page, bool $straddlingFlg, bool $bsFlg, string $csvFlag, bool $programListExtensionFlag, array $dataType, array $dataTypeConst): array
    {
        $escapes = [
            'startDate' => $this->quote($startDate),
            'endDate' => $this->quote($endDate),
            'startTime' => $this->quote($startTime),
            'endTime' => $this->quote($endTime),
            'regionId' => $this->quote($regionId),
            'wdays' => array_map(function ($val) {
                return $this->quote($val);
            }, $wdays),
            'channels' => array_map(function ($val) {
                return $this->quote($val);
            }, $channels),
            'genres' => empty($genres) ? [] : array_map(function ($val) {
                return $this->quote($val);
            }, $genres),
            'programNames' => empty($programNames) ? [] : array_map(function ($val) {
                return $this->quote($val);
            }, $programNames),
            'division' => $this->quote($division),
            'codes' => empty($codes) ? [] : array_map(function ($val) {
                return $this->quote($val);
            }, $codes),
        ];

        $digit = 1;

        if ($bsFlg) {
            $digit = 2;
        }
        $escapes['digit'] = $this->quote($digit);

        $count = '';

        $limit = '';
        $offset = '';

        $from = ' program_grouped p ';

        if ($csvFlag != 1) {
            $orderBy = '';

            if (count($order) > 0) {
                $orderArr = [];

                foreach ($order as $key => $val) {
                    if ($val['column'] === 'date') {
                        $val['column'] = 'started_at';
                    }

                    if ($val['dir'] === 'desc') {
                        $val['dir'] = $val['dir'] . ' NULLS LAST ';
                    } else {
                        $val['dir'] = $val['dir'] . ' NULLS FIRST ';
                    }
                    array_push($orderArr, "   ${val['column']} ${val['dir']}");
                }
                $orderBy .= implode(',', $orderArr);
                $orderBy .= ' , p.channel_code_name asc ';
                $orderBy .= ' , p.started_at asc ';
            } else {
                $orderBy .= ' p.channel_code_name asc ';
                $orderBy .= ' , p.started_at asc ';
            }

            $limit = " LIMIT {$length} ";

            $offsetNum = $length * ($page - 1);
            $offset = " OFFSET {$offsetNum} ";
        } else {
            $orderBy = ' p.channel_code_name asc ';
            $orderBy .= ' , p.started_at asc ';
            $from = ' program_grouped p ';

            if (!$bsFlg && $csvFlag == 1 && $programListExtensionFlag) {
                $from .= ' LEFT JOIN cm_rate_horizontal c ON p.prog_id = c.prog_id AND p.time_box_id = c.time_box_id ';
            }
        }

        // コード種別の頭部分
        $divisionKey = "{$division}_";

        // BS指定時はテーブル名の頭にBSを付ける
        $bsKey = '';

        if ($bsFlg) {
            $bsKey = 'bs_';
        }

        $with = '';
        $count = '';
        $count .= 'WITH program_data_converted_time AS ';
        $count .= ' ( ';
        $count .= '  SELECT ';
        $count .= '   p.date, ';
        $count .= "   TO_CHAR(greatest(p.real_started_at, tb.started_at) - interval '5:00:00', 'HH24MISS') as shift_start_time, ";
        $count .= "   TO_CHAR(least(p.real_ended_at, tb.ended_at) - interval '5:00:01', 'HH24MISS') as shift_end_time, ";
        $count .= '   (greatest(p.real_started_at, tb.started_at)) as real_started_at, ';
        $count .= '   (least(p.real_ended_at, tb.ended_at)) as real_ended_at, ';
        $count .= '   c.code_name AS channel_code_name, ';
        $count .= '   p.title, ';
        $count .= '   p.prog_id, ';
        $count .= '   p.time_box_id, ';
        $count .= '   p.genre_id, ';
        $count .= '   p.started_at, ';
        $count .= '   p.personal_viewing_rate rt_personal_viewing_rate, ';
        $count .= '   p.household_viewing_rate rt_household_viewing_rate, ';

        if (!$bsFlg) {
            $count .= '   p.ts_personal_viewing_rate, ';
            $count .= '   p.ts_household_viewing_rate, ';
            $count .= '   p.ts_personal_total_viewing_rate, ';
            $count .= '   p.ts_household_total_viewing_rate, ';
            $count .= '   COALESCE(p.ts_personal_gross_viewing_rate, p.ts_samples_personal_viewing_rate) gross_personal_viewing_rate, ';
            $count .= '   COALESCE(p.ts_household_gross_viewing_rate, p.ts_samples_household_viewing_rate) gross_household_viewing_rate, ';
        }
        $count .= '   p.household_end_viewing_rate, ';
        $count .= '   p.household_viewing_share, ';
        $count .= '   p.prepared, ';
        $count .= '   c.id channel_id ';
        $count .= '  FROM ';
        $count .= '   ' . $bsKey . 'programs p ';
        $count .= '  INNER JOIN ';
        $count .= '    time_boxes tb ';
        $count .= '  ON ';
        $count .= '    p.time_box_id = tb.id ';
        $count .= '    AND tb.region_id = ' . $escapes['regionId'];
        $count .= '  INNER JOIN ';
        $count .= '   channels c ';
        $count .= '    ON ';
        $count .= '     c.id = p.channel_id ';

        if ($bsFlg) {
            $count .= "     AND c.type = 'bs' ";
        } else {
            $count .= "     AND c.type = 'dt' ";
        }
        $count .= '  WHERE ';
        $count .= '   p.prepared = 1 ';
        $count .= '   AND p.date BETWEEN ' . $escapes['startDate'] . ' AND ' . $escapes['endDate'] . ' ';

        $count .= '   AND (EXTRACT(DOW FROM p.date) IN (' . implode(',', $escapes['wdays']) . ')) ';

        if (!$holiday) {
            $count .= '   AND NOT EXISTS(SELECT 1 FROM holidays h where p.date = h.holiday) ';
        }

        if (!empty($channels)) {
            $count .= '   AND p.channel_id IN (' . implode(',', $escapes['channels']) . ') ';
        }

        if (!empty($genres)) {
            $count .= '   AND p.genre_id IN (' . implode(',', $escapes['genres']) . ') ';
        }
        $count .= "   AND p.genre_id <> '20014' ";

        if (!empty($programNames)) {
            $count .= '   AND p.title IN (SELECT title From ' . $bsKey . 'programs WHERE prog_id IN (' . implode(',', $escapes['programNames']) . ')) ';
        }

        $count .= '), program_data AS ';
        $count .= ' ( ';
        $count .= '  SELECT ';

        if ($holiday) {
            $count .= '   h.holiday, ';
        }
        $count .= '   p.date, ';
        $count .= '   p.real_started_at, ';
        $count .= '   p.real_ended_at, ';
        $count .= '   p.real_ended_at - p.real_started_at, ';
        $count .= '   p.channel_id, ';
        $count .= '   p.channel_code_name, ';
        $count .= '   p.title, ';
        $count .= '   p.prog_id, ';
        $count .= '   p.time_box_id, ';
        $count .= '   p.genre_id, ';
        $count .= '   p.started_at, ';

        $count .= '   p.rt_personal_viewing_rate, ';
        $count .= '   p.rt_household_viewing_rate, ';

        if (!$bsFlg) {
            $count .= '   p.ts_personal_viewing_rate, ';
            $count .= '   p.ts_household_viewing_rate, ';
            $count .= '   p.ts_personal_total_viewing_rate, ';
            $count .= '   p.ts_household_total_viewing_rate, ';
            $count .= '   p.gross_personal_viewing_rate, ';
            $count .= '   p.gross_household_viewing_rate, ';
        } else {
            // bs用ダミー
            $count .= '   0 AS ts_personal_viewing_rate, ';
            $count .= '   0 AS ts_household_viewing_rate, ';
            $count .= '   0 AS ts_personal_total_viewing_rate, ';
            $count .= '   0 AS ts_household_total_viewing_rate, ';
            $count .= '   0 AS gross_personal_viewing_rate, ';
            $count .= '   0 AS gross_household_viewing_rate, ';
        }
        $count .= '   p.household_end_viewing_rate, ';
        $count .= '   p.household_viewing_share ';
        $count .= '  FROM ';
        $count .= '   program_data_converted_time p ';

        if ($holiday) {
            $count .= '  LEFT JOIN holidays h ';
            $count .= '   ON p.date = h.holiday ';
        }
        $count .= '  WHERE ';

        if (isset($startDate, $endDate, $startTime, $endTime)) {
            // 時間帯
            if ($escapes['startTime'] === '000000' && $escapes['endTime'] === '235959') {
                // 全選択の場合は検索条件に含めない
            } else {
                if ($endTime >= $startTime) {
                    $count .= '  (( ';
                    $count .= '   p.shift_end_time <  p.shift_start_time ';
                    $count .= '   AND ( ';
                    $count .= '    p.shift_start_time <= ' . $escapes['endTime'] . ' ';
                    $count .= '    OR p.shift_end_time > ' . $escapes['startTime'] . ' ';
                    $count .= '   ) ';
                    $count .= '  ) ';
                    $count .= '  OR ( ';
                    $count .= '   p.shift_end_time >= p.shift_start_time ';
                    $count .= '   AND p.shift_start_time <= ' . $escapes['endTime'] . ' ';
                    $count .= '   AND p.shift_end_time > ' . $escapes['startTime'] . ' ';
                    $count .= '  )) ';
                } else {
                    $count .= ' (( ';
                    $count .= '   p.shift_end_time < p.shift_start_time ';
                    $count .= '  ) ';
                    $count .= '  OR ( ';
                    $count .= '   p.shift_end_time >=  p.shift_start_time ';
                    $count .= '   AND ( ';
                    $count .= '    p.shift_start_time <= ' . $escapes['endTime'] . ' ';
                    $count .= '    OR p.shift_end_time > ' . $escapes['startTime'] . ' ';
                    $count .= '   ) ';
                    $count .= '  )) ';
                }
            }
        }
        $count .= ' ) ';
        $with .= $count;
        $with .= ', program_grouped AS ';
        $with .= ' ( ';
        $with .= '  SELECT ';

        if ($holiday) {
            $with .= '   p.holiday, ';
        }
        $with .= "   to_char(p.date, 'yyyy/mm/dd') AS date, ";
        $with .= "   DATE_PART('dow' ,p.date) d, ";
        $with .= "   lpad(to_char(p.real_started_at - interval '5 hours', 'HH24')::numeric + 5 || to_char(p.real_started_at, ':MI:SS'),8,'0') AS real_started_at, ";
        $with .= "   lpad(to_char(p.real_ended_at - interval '5 hours 1 seconds', 'HH24')::numeric + 5 || to_char(p.real_ended_at - interval '1 seconds', ':MI:SS'),8,'0') AS real_ended_at, ";
        $with .= '   trunc(EXTRACT(EPOCH FROM p.real_ended_at - p.real_started_at)/60) AS fraction, ';
        $with .= '   p.channel_id, ';
        $with .= '   p.channel_code_name, ';
        $with .= '   p.title, ';
        $with .= '   p.prog_id, ';
        $with .= '   p.time_box_id, ';
        $with .= '   p.genre_id, ';
        $with .= '   p.started_at, ';

        $with .= '   ROUND(p.rt_personal_viewing_rate:: numeric,' . $escapes['digit'] . ') AS rt_personal_viewing_rate, ';
        $with .= '   ROUND(p.ts_personal_viewing_rate:: numeric,' . $escapes['digit'] . ') AS ts_personal_viewing_rate, ';
        $with .= '   ROUND(p.ts_personal_total_viewing_rate:: numeric,' . $escapes['digit'] . ') AS ts_personal_total_viewing_rate, ';
        $with .= '   ROUND(p.gross_personal_viewing_rate:: numeric,' . $escapes['digit'] . ') AS gross_personal_viewing_rate, ';
        $with .= "   ROUND(MAX(CASE WHEN pr.division = 'personal' and pr.code = '1' THEN pr.end_viewing_rate END)::numeric, " . $escapes['digit'] . ') AS end_personal_viewing_rate, ';
        // コードのcase文作成
        foreach ($dataType as $type) {
            switch ($type) {
                case $dataTypeConst['rt']:
                    foreach ($codes as $val) {
                        $name = 'rt_' . $divisionKey . $val;
                        $with .= '   ROUND(MAX(CASE WHEN pr.division = ' . $escapes['division'] . ' AND pr.code = ' . $this->getEscapeValueFromArray($val, $escapes['codes']) . ' THEN pr.viewing_rate END)::numeric, ' . $escapes['digit'] . ') AS ' . $name . ', ';
                        $with .= '   ROUND(MAX(CASE WHEN pr.division = ' . $escapes['division'] . ' AND pr.code = ' . $this->getEscapeValueFromArray($val, $escapes['codes']) . ' THEN pr.end_viewing_rate END)::numeric, ' . $escapes['digit'] . ') AS end_' . $name . ', ';
                    }
                    break;
                case $dataTypeConst['ts']:
                    foreach ($codes as $val) {
                        $name = 'ts_' . $divisionKey . $val;
                        $with .= '   ROUND(MAX(CASE WHEN tspr.division = ' . $escapes['division'] . ' AND tspr.code = ' . $this->getEscapeValueFromArray($val, $escapes['codes']) . ' THEN tspr.viewing_rate END)::numeric, ' . $escapes['digit'] . ') AS ' . $name . ', ';
                    }
                    break;
                case $dataTypeConst['total']:
                    foreach ($codes as $val) {
                        $name = 'total_' . $divisionKey . $val;
                        $with .= '   ROUND(MAX(CASE WHEN tspr.division = ' . $escapes['division'] . ' AND tspr.code = ' . $this->getEscapeValueFromArray($val, $escapes['codes']) . ' THEN tspr.total_viewing_rate END)::numeric, ' . $escapes['digit'] . ') AS ' . $name . ', ';
                    }
                    break;
                case $dataTypeConst['gross']:
                    foreach ($codes as $val) {
                        $name = 'gross_' . $divisionKey . $val;
                        $with .= '   ROUND(MAX(CASE WHEN tspr.division = ' . $escapes['division'] . ' AND tspr.code = ' . $this->getEscapeValueFromArray($val, $escapes['codes']) . ' THEN tspr.gross_viewing_rate END)::numeric, ' . $escapes['digit'] . ') AS ' . $name . ', ';
                        $name = 'ts_samples_' . $divisionKey . $val;
                        $with .= '   ROUND(MAX(CASE WHEN pr.division = ' . $escapes['division'] . ' AND pr.code = ' . $this->getEscapeValueFromArray($val, $escapes['codes']) . ' THEN pr.ts_samples_viewing_rate END)::numeric, ' . $escapes['digit'] . ') AS ' . $name . ', ';
                    }
                    break;
            }
        }

        $with .= '   ROUND(p.rt_household_viewing_rate:: numeric,' . $escapes['digit'] . ') AS rt_household_viewing_rate, ';
        $with .= '   ROUND(p.ts_household_viewing_rate:: numeric,' . $escapes['digit'] . ') AS ts_household_viewing_rate, ';
        $with .= '   ROUND(p.ts_household_total_viewing_rate:: numeric,' . $escapes['digit'] . ') AS ts_household_total_viewing_rate, ';
        $with .= '   ROUND(p.gross_household_viewing_rate:: numeric,' . $escapes['digit'] . ') AS gross_household_viewing_rate, ';
        $with .= '   ROUND(p.household_end_viewing_rate:: numeric,' . $escapes['digit'] . ') AS household_end_viewing_rate, ';
        $with .= '   ROUND(p.household_viewing_share:: numeric, ' . $escapes['digit'] . ') AS household_viewing_share';
        $with .= '  FROM ';
        $with .= '   program_data p ';
        $with .= '  LEFT JOIN ';
        $with .= '   ' . $bsKey . 'program_reports pr ';
        $with .= '    ON ';
        $with .= '     p.prog_id = pr.prog_id ';
        $with .= '     AND p.time_box_id = pr.time_box_id ';
        $with .= '     AND ( ';
        $with .= '             (pr.division = ' . $escapes['division'] . ' AND pr.code IN (' . implode(',', $escapes['codes']) . ') ) OR ';
        $with .= "             (pr.division = 'personal' AND pr.code = '1' ) ";
        $with .= '         ) ';
        $with .= '  LEFT JOIN ';
        $with .= '   ts_program_reports tspr ';
        $with .= '    ON ';
        $with .= '     p.prog_id = tspr.prog_id ';
        $with .= '     AND p.time_box_id = tspr.time_box_id ';
        $with .= '     AND ( ';
        $with .= '             (tspr.division = ' . $escapes['division'] . ' AND tspr.code IN (' . implode(',', $escapes['codes']) . ') ) OR ';
        $with .= "             (tspr.division = 'personal' AND pr.code = '1' ) ";
        $with .= '         ) ';
        $with .= '     AND tspr.c_index = 7 ';

        $with .= '  GROUP BY ';

        if ($holiday) {
            $with .= '   p.holiday, ';
        }
        $with .= '   p.date, ';
        $with .= '   p.real_started_at, ';
        $with .= '   p.real_ended_at, ';
        $with .= '   p.real_ended_at - p.real_started_at, ';
        $with .= '   p.channel_id, ';
        $with .= '   p.channel_code_name, ';
        $with .= '   p.title, ';
        $with .= '   p.prog_id, ';
        $with .= '   p.time_box_id, ';
        $with .= '   p.genre_id, ';
        $with .= '   p.started_at, ';

        $with .= '   p.rt_personal_viewing_rate, ';
        $with .= '   p.rt_household_viewing_rate, ';
        $with .= '   p.ts_personal_viewing_rate, ';
        $with .= '   p.ts_household_viewing_rate, ';
        $with .= '   p.ts_personal_total_viewing_rate, ';
        $with .= '   p.ts_household_total_viewing_rate, ';
        $with .= '   p.gross_personal_viewing_rate, ';
        $with .= '   p.gross_household_viewing_rate, ';
        $with .= '   p.household_end_viewing_rate, ';
        $with .= '   p.household_viewing_share ';

        $with .= ' ORDER BY ' . $orderBy . $limit . $offset . ' ';
        $with .= ' ) ';

        $select = '';
        $select .= ' date ';

        if ($csvFlag != 1) {
            $select .= ' ,prog_id ';
            $select .= ' ,time_box_id ';

            if ($holiday) {
                $select .= ' ,holiday ';
            }
        }
        $select .= " ,CASE WHEN d = 0 THEN '日' ";
        $select .= " WHEN d = 1 THEN '月' ";
        $select .= " WHEN d = 2 THEN '火' ";
        $select .= " WHEN d = 3 THEN '水' ";
        $select .= " WHEN d = 4 THEN '木' ";
        $select .= " WHEN d = 5 THEN '金' ";
        $select .= " WHEN d = 6 THEN '土' END ";

        if ($csvFlag === '1' && $holiday) {
            $select .= "  || CASE WHEN holiday IS NOT NULL THEN  '(祝)' ELSE '' END ";
        }
        $select .= '  dow ';
        $select .= ' ,real_started_at ';
        $select .= ' ,real_ended_at ';
        $select .= ' ,fraction ';

        if ($csvFlag !== '1') {
            $select .= '   , p.channel_id ';
        }
        $select .= ' ,channel_code_name ';
        $select .= " ,CASE WHEN genre_id = '20001' THEN 'その他'  ";
        $select .= "      WHEN genre_id = '20002' THEN 'ニュース/報道'  ";
        $select .= "      WHEN genre_id = '20003' THEN '情報/ワイドショー'  ";
        $select .= "      WHEN genre_id = '20004' THEN '音楽'  ";
        $select .= "      WHEN genre_id = '20005' THEN 'バラエティー'  ";
        $select .= "      WHEN genre_id = '20006' THEN 'ドラマ'  ";
        $select .= "      WHEN genre_id = '20007' THEN 'アニメ/特撮'  ";
        $select .= "      WHEN genre_id = '20008' THEN '映画'  ";
        $select .= "      WHEN genre_id = '20009' THEN 'スポーツ'  ";
        $select .= "      WHEN genre_id = '20010' THEN 'ドキュメンタリー'  ";
        $select .= "      WHEN genre_id = '20011' THEN '趣味/教育'  ";
        $select .= "      WHEN genre_id = '20012' THEN '演劇/公演'  ";
        $select .= "      WHEN genre_id = '20013' THEN '福祉'  ";
        $select .= "      WHEN genre_id = '20014' THEN '放送休止' END genre ";
        $select .= ' ,title ';

        $divCodes = array_filter($codes, function ($v, $k) {
            return $v != 'personal' && $v != 'household';
        }, ARRAY_FILTER_USE_BOTH);

        $hasPersonal = in_array('personal', $codes);
        $hasHousehold = in_array('household', $codes) || $division == 'condition_cross';

        // Rating ----------------------------------------
        if (in_array($dataTypeConst['rt'], $dataType)) {
            if ($hasPersonal) {
                $select .= ' ,COALESCE(rt_personal_viewing_rate,0) rt_personal_viewing_rate ';
            }
            // コードのcase文作成
            foreach ($divCodes as $val) {
                $name = 'rt_' . $divisionKey . $val;
                $select .= "   ,COALESCE(${name},0) ${name} ";
            }

            if ($hasHousehold) {
                $select .= ' ,COALESCE(rt_household_viewing_rate,0) rt_household_viewing_rate ';
            }
        }

        if (in_array($dataTypeConst['ts'], $dataType)) {
            // Rating ----------------------------------------
            if ($hasPersonal) {
                $select .= ' ,COALESCE(ts_personal_viewing_rate,0) ts_personal_viewing_rate ';
            }
            // コードのcase文作成
            foreach ($divCodes as $val) {
                $name = 'ts_' . $divisionKey . $val;
                $select .= "   ,COALESCE(${name},0) ${name} ";
            }

            if ($hasHousehold) {
                $select .= ' ,COALESCE(ts_household_viewing_rate,0) ts_household_viewing_rate ';
            }
        }

        if (in_array($dataTypeConst['total'], $dataType)) {
            // Rating ----------------------------------------
            if ($hasPersonal) {
                $select .= ' ,COALESCE(ts_personal_total_viewing_rate,0) total_personal_viewing_rate ';
            }
            // コードのcase文作成
            foreach ($divCodes as $val) {
                $name = 'total_' . $divisionKey . $val;
                $select .= "   ,COALESCE(${name},0) ${name} ";
            }

            if ($hasHousehold) {
                $select .= ' ,COALESCE(ts_household_total_viewing_rate,0) total_household_viewing_rate ';
            }
        }

        if (in_array($dataTypeConst['gross'], $dataType)) {
            // Rating ----------------------------------------
            if ($hasPersonal) {
                $select .= ' ,COALESCE(gross_personal_viewing_rate,0) gross_personal_viewing_rate ';
            }
            // コードのcase文作成
            foreach ($divCodes as $val) {
                $name = 'gross_' . $divisionKey . $val;
                $realtimeName = 'ts_samples_' . $divisionKey . $val;
                $select .= "   ,COALESCE(${name},${realtimeName},0) ${name} ";
            }

            if ($hasHousehold) {
                $select .= ' ,COALESCE(gross_household_viewing_rate, 0) gross_household_viewing_rate ';
            }
        }

        // End Rating ----------------------------------------
        if ($hasHousehold && !$bsFlg) {
            $select .= ' ,COALESCE(household_end_viewing_rate,0) household_end_viewing_rate ';
        }

        if ($hasHousehold && !$bsFlg) {
            $select .= '   ,COALESCE(p.household_viewing_share,0) household_viewing_share ';
        }

        $query = sprintf('%s SELECT %s FROM %s ORDER BY %s ;', $with, $select, $from, $orderBy);
        $records = $this->select($query);

        // 件数取得
        $query = sprintf('%s SELECT COUNT(*) cnt FROM program_data p;', $count);
        $recordCount = $this->selectOne($query);

        return [
            'list' => $records,
            'cnt' => $recordCount->cnt,
        ];
    }

    /**
     * 番組情報取得.
     * @param string $progId
     * @param string $timeBoxId
     * @return null|\stdClass
     */
    public function findProgram(String $progId, String $timeBoxId): ?\stdClass
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
        $where .= 'p.prog_id = :prog_id ';
        $query = sprintf('SELECT %s FROM %s WHERE %s;', $select, $from, $where);

        return $this->selectOne($query, $bindings);
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
        $bindings = [];

        $digit = 1;

        if ($bsFlg) {
            $digit = 2;
        }

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

        $withName = '';

        $withName = 'program_data';

        $with = '';
        $with .= 'WITH ' . $withName . ' AS ';
        $with .= ' ( ';
        $with .= '  SELECT ';
        $with .= '   p.date, ';
        $with .= '   p.channel_id, ';
        $with .= '   p.real_started_at, ';
        $with .= '   p.real_ended_at, ';
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
            $with .= '   LEFT JOIN per_minute_reports pmr ';
            $with .= '    ON ';
            $with .= '     pmr.time_box_id = p.time_box_id ';
            $with .= "     AND pmr.datetime = p.real_ended_at - interval '1 minute' ";
            $with .= '     AND pmr.channel_id = p.channel_id ';
            $with .= "     AND pmr.division = 'personal' ";
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

        $with .= ' ), ';

        $with .= ' program_grouped AS ';
        $with .= ' ( ';
        $with .= '  SELECT ';
        $with .= '   p.date as org_date, ';
        $with .= '   p.channel_id, ';
        $with .= '   p.real_started_at, ';
        $with .= '   p.real_ended_at, ';
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
            $with .= '   COALESCE(ROUND(p.personal_viewing_rate::numeric,:digit), 0) AS rate, ';
            $with .= '   COALESCE(ROUND(p.personald_end_viewing_rate::numeric, :digit), 0) AS end_rate, ';
        }

        foreach ($codeBind as $val) {
            $name = $divisionKey . $codes[preg_replace('!:' . $divisionKey . '!', '', $val, 1)];
            $with .= "   COALESCE(ROUND(MAX(p.${name})::numeric,:digit), 0) AS rate, ";
            $with .= "   COALESCE(ROUND(MAX(p.${name}_end)::numeric,:digit), 0) AS end_rate, ";
        }

        if ($householdFlg) {
            $with .= '   COALESCE(ROUND(p.household_viewing_rate::numeric,:digit), 0) AS rate, ';
            $with .= '   COALESCE(ROUND(p.household_end_viewing_rate::numeric,:digit), 0) AS end_rate, ';
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
            if (!($startTime === '000000' && $endTime === '235959')) {
                $bindings[':startTime'] = $startTime;
                $bindings[':endTime'] = $endTime;

                $with .= '  AND ( ';
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
                $with .= ') ';
            }
        }

        $with .= '  GROUP BY ';
        $with .= '   p.date, ';
        $with .= '   p.channel_id, ';
        $with .= '   p.real_started_at, ';
        $with .= '   p.real_ended_at, ';
        $with .= '   p.channel_code_name, ';
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
        $orderBy .= ', p.real_started_at asc ';

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
        $bindings = [];

        // 地域コード
        $bindings[':region_id'] = $regionId;

        // コード種別の頭部分
        $divisionKey = "{$division}_";

        $isConditionCross = $division == 'condition_cross';

        // BS指定時はテーブル名の頭にBSを付ける
        $bsKey = '';

        $digit = 1;

        if ($bsFlg) {
            $digit = 2;
            $bsKey = 'bs_';
        }

        $bindings['digit'] = $digit;

        $with = '';
        $with .= 'WITH program_data AS ';
        $with .= ' ( ';
        $with .= '  SELECT ';
        $with .= '   p.date, ';
        $with .= '   p.channel_id, ';
        $with .= '   p.real_started_at, ';
        $with .= '   p.real_ended_at, ';
        $with .= "   to_char(p.real_started_at - interval '5 hours', 'HH24MISS') AS shift_start_time, ";
        $with .= "   to_char(p.real_ended_at - interval '5 hours', 'HH24MISS') AS shift_end_time, ";
        $with .= '   c.code_name channel_code_name, ';
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
        if (!empty($channels)) {
            $channelsBind = $this->createArrayBindParam('channels', [
                'channels' => $channels,
            ], $bindings);

            $with .= '   AND p.channel_id IN (' . implode(',', $channelsBind) . ') ';
        }

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
        $with .= ' WHERE  ';
        $with .= '   ( pv.prog_id, pv.time_box_id ) IN (SELECT p.prog_id, p.time_box_id FROM program_data p ) ';
        $with .= ' GROUP BY ';
        $with .= '   pv.prog_id, ';
        $with .= '   pv.time_box_id, ';
        $with .= '   s.code, ';
        $with .= '   s.number ';

        $with .= ' ), vertical AS( ';
        $with .= ' SELECT  ';
        $with .= '   p.date, ';
        $with .= '   p.channel_id, ';
        $with .= '   p.real_started_at, ';
        $with .= '   p.real_ended_at, ';
        $with .= '   p.shift_start_time, ';
        $with .= '   p.shift_end_time, ';
        $with .= '   p.channel_code_name, ';
        $with .= '   p.title, ';

        if ($isConditionCross) {
            $with .= '   ROUND( ( CASE WHEN pr.code = :condition_cross_code THEN pr.viewing_seconds END / ( EXTRACT(EPOCH FROM (p.real_ended_at - p.real_started_at )) * pr.number) * 100 )::numeric, :digit) AS rate, ';
            $with .= "   '----'::varchar(255) AS end_rate, ";
        } else {
            foreach ($codes as $key => $val) {
                $key = ':vertical_' . $divisionKey . $code;
                $bindings[$key] = $code;
                $with .= "   ROUND( ( CASE WHEN pr.code = ${key} THEN pr.viewing_seconds END / ( EXTRACT(EPOCH FROM (p.real_ended_at - p.real_started_at )) * pr.number) * 100)::numeric, :digit) AS rate, ";
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
        $with .= '  p.date as org_date, ';
        $with .= '  p.channel_id, ';
        $with .= '  p.real_started_at, ';
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

        if (!($startTime === '000000' && $endTime === '235959')) {
            $bindings[':startTime'] = $startTime;
            $bindings[':endTime'] = $endTime;

            $with .= '  AND ( ';
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
            $with .= ') ';
        }

        $with .= ' GROUP BY ';
        $with .= '   p.date, ';
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
        $orderBy .= ', p.real_started_at asc ';

        $query = sprintf('%s SELECT %s FROM with_master p ORDER BY %s;', $with, $select, $orderBy);
        $records = $this->select($query, $bindings);

        return [
            'list' => $records,
        ];
    }
}
