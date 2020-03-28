<?php

namespace Switchm\SmartApi\Queries\Dao\Rdb;

use Carbon\Carbon;

class CalcRatingDao extends Dao
{
    private static $singleton;

    public static function getInstance(): self
    {
        if (!isset(self::$singleton)) {
            self::$singleton = new self();
        }
        return self::$singleton;
    }

    /**
     * かけ合わせ条件作成SQL.
     *
     * @param unknown $params
     * @param & $bindings
     * @param mixed $alias
     * @return string
     */
    public function createConditionCrossSql($params, &$bindings, $alias = 'tbp'): String
    {
        $conditionArr = $this->createConditionCross($params, $bindings, $alias);

        return empty($conditionArr) ? '' : ' AND ' . implode(' AND ', $conditionArr);
    }

    /**
     * 配列から、SQLバインド用のパラメータを作成する.
     *
     * @param string $keyName
     *                        キー名
     * @param array $valArr
     *                      値の配列
     * @param array $bindArray
     *                         バインド用の配列（参照渡し）
     * @param array $params
     * @param array& $bindings
     * @return array バインドキー名の配列
     */
    public function createArrayBindParam(string $keyName, array $params, array &$bindings)
    {
        // 不正な値は空の配列を返す
        if (!isset($params[$keyName]) || !is_array($params[$keyName])) {
            return [];
        }
        // コロンを削除
        $newKeyName = implode('', explode(':', $keyName));
        $keyArr = [];
        $valArr = [];

        foreach ($params[$keyName] as $k => $v) {
            $key = ":${newKeyName}${k}";
            $bindings[$key] = $v;
            array_push($keyArr, $key);
        }

        return $keyArr;
    }

    /**
     * TimeBoxのCASE文作成.
     *
     * @param string $startDate
     * @param string $endDate
     * @param int $regionId
     * @param string $target
     * @return string
     */
    public function createTimeBoxCaseClause(String $startDate, String $endDate, int $regionId, String $target): String
    {
        $query = "SELECT id, started_at, ended_at FROM time_boxes WHERE (:startDate <= ended_at - interval '5 hours' AND started_at - interval '5 hours' <= :endDate ) AND region_id = :regionId";
        $bindings = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'regionId' => $regionId,
        ];
        $list = $this->select($query, $bindings);

        $result = 'CASE ';
        $template = "WHEN ${target} >= '@startDate@' AND ${target} < '@endDate@' THEN @timeBoxId@ ";

        foreach ($list as $row) {
            $result .= str_replace([
                '@startDate@',
                '@endDate@',
                '@timeBoxId@',
            ], [
                $row->started_at,
                $row->ended_at,
                $row->id,
            ], $template);
        }

        $result .= 'END ';

        // 1つもタイムボックスIDが取れない場合は、偽を返す
        if (empty($list)) {
            $result = ' 1=2 ';
        }

        return $result;
    }

    /**
     * オリジナル、拡張属性の時に使用.
     *
     * @param string $division
     * @param array $codes
     * @param array $bidings
     * @param string $alias
     * @param array& $bindings
     * @param ?bool $personalFlag
     * @param ?bool $householdFlag
     * @return string
     */
    public function createCrossJoinWhereClause(String $division, array $codes, array &$bindings, ?bool $personalFlag = false, ?bool $householdFlag = false)
    {
        if (count($codes) === 0) {
            return ' 1 = 2 '; // コードが空の場合は引っかからないようにする
        }

        // 対象のdivision, code 値から Where$句を作成
        $innerBindings = [];
        $innerBindings[':division'] = $division;
        $keyArr = $this->createArrayBindParam('cross_join_codes', [
            'cross_join_codes' => $codes,
        ], $innerBindings);
        $query = ' SELECT code, definition FROM attr_divs WHERE division = :division AND code IN (' . implode(',', $keyArr) . ' ) ';
        $list = $this->select($query, $innerBindings);

        $result = [];

        foreach ($list as $row) {
            $key = ':cross_' . $division . $row->code;
            $bindings[$key] = $row->code;
            $tmpSql = '';
            $tmpSql .= '(';
            $tmpSql .= 'codes.code = ' . $key;
            $tmpSql .= ' AND ' . $this->createConditionOriginalDivSql($division, $row->code, $row->definition, $bindings);
            $tmpSql .= ')';
            array_push($result, $tmpSql);
        }

        if (in_array('personal', $codes)) {
            $key = ':cross_' . $division . 'personal';
            $bindings[$key] = 'personal';
            $tmpSql = '';
            $tmpSql .= '(';

            if ($personalFlag) {
                $tmpSql .= "codes.code = ${key} ";
            } else {
                $tmpSql .= "codes.code = ${key} AND 1 = 2 ";
            }
            $tmpSql .= ')';
            array_push($result, $tmpSql);
        }

        if (in_array('household', $codes)) {
            $key = ':cross_' . $division . 'household';
            $bindings[$key] = 'household';
            $tmpSql = '';
            $tmpSql .= '(';

            if ($householdFlag) {
                $tmpSql .= "codes.code = ${key} ";
            } else {
                $tmpSql .= "codes.code = ${key} AND 1 = 2 ";
            }
            $tmpSql .= ')';
            array_push($result, $tmpSql);
        }

        return implode(' OR ', $result);
    }

    public function createCrossJoinArray(String $division, array $codes, array &$bindings): array
    {
        if (count($codes) === 0) {
            return []; // コードが空の場合は引っかからないようにする
        }

        // 対象のdivision, code 値から Where$句を作成
        $innerBindings = [];
        $innerBindings[':division'] = $division;

        // division name取得
        $query = " SELECT name FROM attr_divs WHERE division = :division AND code = '_def'";
        $divData = $this->selectOne($query, $innerBindings);

        $keyArr = $this->createArrayBindParam('cross_join_codes', [
            'cross_join_codes' => $codes,
        ], $innerBindings);
        $query = ' SELECT code, name, definition FROM attr_divs WHERE division = :division AND code IN (' . implode(',', $keyArr) . ' ) order by display_order';
        $list = $this->select($query, $innerBindings);

        $result = [];

        foreach ($list as $row) {
            $name = $division . '_' . $row->code;
            $condition = '';
            $condition .= $this->createConditionOriginalDivSql($division, $row->code, $row->definition, $bindings);
            $result[] = ['name' => $name, 'divisionName' => $divData->name, 'codeName' => $row->name, 'condition' => $condition];
        }

        return $result;
    }

    public function createConditionCrossArray(array $params, array &$bindings, ?string $alias = 'tbp'): array
    {
        $conditionArr = $this->createConditionCross($params, $bindings, $alias);

        if (empty($conditionArr)) {
            return [];
        }
        $result = [];
        $result[] = ['name' => 'condition_cross', 'divisionName' => '掛け合わせ条件', 'codeName' => '', 'condition' => implode(' AND ', $conditionArr)];
        return $result;
    }

    /**
     * オリジナル、拡張属性の時に使用.
     *
     * @param string $division
     * @param string $code
     * @param string $definition
     * @param array $bindings
     * @param string $alias
     * @return string
     */
    public function createConditionOriginalDivSql(String $division, String $code, String $definition, array &$bindings, String $alias = 'tbp')
    {
        $result = [];
        $condArr = explode(':', $definition);
        $tmpStr = '';

        foreach ($condArr as $cond) {
            $colArr = explode('=', $cond);
            $col = $colArr[0];
            $val = $colArr[1];
            $key = ':original' . $division . $code . $col;

            if (strpos($val, ',') !== false) {
                // カンマ区切り
                $explode = explode(',', $val);

                if ($col === 'paneler_id') {
                    $tmpArr = [];

                    foreach ($explode as $val) {
                        $tmpArr[] = "'" . $val . "'";
                    }
                    $tmpStr = $col . ' IN ( ' . implode(',', $tmpArr) . ') ';
                } else {
                    $keyArr = $this->createArrayBindParam($key, [
                        $key => $explode,
                    ], $bindings);
                    $tmpStr = $col . ' IN ( ' . implode(',', $keyArr) . ') ';
                }
            } elseif (strpos($val, '-') !== false) {
                // ハイフン区切り
                $explode = explode('-', $val);

                if ($col === 'age' && $explode[1] === '99') {
                    unset($explode[1]);
                }

                if (!empty($explode[0]) && !empty($explode[1])) {
                    $tmpStr = $col . ' BETWEEN ' . $key . 'from AND ' . $key . 'to ';
                    $bindings[$key . 'from'] = $explode[0];
                    $bindings[$key . 'to'] = $explode[1];
                } elseif (empty($explode[0]) && !empty($explode[1])) {
                    $tmpStr = $col . ' <= ' . $key;
                    $bindings[$key] = $explode[1];
                } elseif (!empty($explode[0]) && empty($explode[1])) {
                    $tmpStr = $col . ' >= ' . $key;
                    $bindings[$key] = $explode[0];
                }
            } elseif (strpos($val, '_') !== false) {
                // アンダースコア区切り
                $explode = explode('_', $val);

                $condF = [];
                $condM = [];
                $condAll = [];

                for ($i = $explode[0]; $i <= $explode[1]; $i++) {
                    $target = str_pad($i, 2, 0, STR_PAD_LEFT);

                    if ($col === 'childage') {
                        $condF[] = "${alias}.${col}_f LIKE ${key}${target}";
                        $condM[] = "${alias}.${col}_m LIKE ${key}${target}";
                    } else {
                        $condAll[] = "${alias}.${col} LIKE ${key}${target}";
                    }
                    $bindings[$key . $target] = "%${target}%";
                }

                if ($col === 'childage') {
                    $condFStr = '(' . implode(' OR ', $condF) . ')';
                    $condMStr = '(' . implode(' OR ', $condM) . ')';
                    $tmpStr = "(${condFStr} OR ${condMStr})";
                } else {
                    $tmpStr = '(' . implode(' OR ', $condAll) . ')';
                }
            } else {
                // その他（イコール）
                $tmpStr = $col . ' = ' . $key;
                $bindings[$key] = $val;
            }

            if (strpos($val, '_') !== false) {
                array_push($result, $tmpStr);
            } else {
                array_push($result, $alias . '.' . $tmpStr);
            }
        }

        return implode(' AND ', $result);
    }

    protected function createCommercialListWhere(String $startDate, String $endDate, ?String $cmType, ?String $cmSeconds, ?array $progIds, int $regionId, String $division, ?array $codes, ?array $conditionCross, ?array $companyIds, ?array $productIds, ?array $cmIds, array $channels, bool $straddlingFlg)
    {
        $sql = '';

        $bindings = [];

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
        $bindings[':regionId'] = $regionId;

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
        $sql .= " 	c.date BETWEEN TO_DATE(:startDate,'YYYY-MM-DD') - interval '1 days' AND TO_DATE(:endDate,'YYYY-MM-DD') + interval '1 days' ";

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
        // regionId
        $sql .= ' AND c.region_id = :regionId ';
        $sql .= ' AND c.time_box_id IN (SELECT id FROM time_boxes WHERE region_id = :regionId) ';

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

        $query = " SELECT time_box_id, ''''||cm_id||'''' as cm_id FROM commercials c " . $sql;

        $list = $this->select($query, $bindings);

        return [
            array_unique(array_column($list, 'time_box_id')),
            array_unique(array_column($list, 'cm_id')),
            $this->createPanelersWhere($startDate, $endDate, $regionId, $division, $codes, $conditionCross),
        ];
    }

    protected function createProgramListWhere(
        string $startDate,
        string $endDate,
        array $channels,
        ?array $genres,
        ?array $progIds,
        string $division,
        ?array $conditionCross,
        ?array $codes,
        int $regionId,
        ?bool $bsFlg
    ) {
        $sd = new Carbon($startDate);
        $ed = new Carbon($endDate);

        $bindings = [];

        $bindings[':startDate'] = $sd->format('Y-m-d');
        $bindings[':endDate'] = $ed->format('Y-m-d');

        $channelBindArr = $this->createArrayBindParam('channels', ['channels' => $channels], $bindings);

        // コード種別の頭部分
        $divisionKey = "{$division}_";

        // BS指定時はテーブル名の頭にBSを付ける
        $bsKey = '';

        if ($bsFlg) {
            $bsKey = 'bs_';
        }

        $query = '';
        $query .= 'WITH program_data_converted_time AS ';
        $query .= ' ( ';
        $query .= '  SELECT ';
        $query .= '   p.date, ';
        $query .= "   (greatest(p.real_started_at, tb.started_at) - interval ' 5 hours ') as shift_start_time, ";
        $query .= "   (least(p.real_ended_at, tb.ended_at) - interval ' 5 hours 1 seconds') as shift_end_time, ";
        $query .= '   (greatest(p.real_started_at, tb.started_at)) as real_started_at, ';
        $query .= '   (least(p.real_ended_at, tb.ended_at)) as real_ended_at, ';
        $query .= '   p.title, ';
        $query .= '   p.prog_id, ';
        $query .= '   p.time_box_id, ';
        $query .= '   p.genre_id, ';
        $query .= '   p.prepared, ';
        $query .= '   c.id channel_id ';
        $query .= '  FROM ';
        $query .= '   ' . $bsKey . 'programs p ';
        $query .= '  INNER JOIN ';
        $query .= '    time_boxes tb ';
        $query .= '  ON ';
        $query .= '    p.time_box_id = tb.id ';
        $query .= '  INNER JOIN ';
        $query .= '   channels c ';
        $query .= '    ON ';
        $query .= '     c.id = p.channel_id ';

        if ($bsFlg) {
            $query .= "     AND c.type = 'bs' ";
        } else {
            $query .= "     AND c.type = 'dt' ";
        }
        $query .= ') ';
        $query .= '  SELECT ';
        $query .= "   ''''||p.prog_id||'''' prog_id, ";
        $query .= '   p.time_box_id ';
        $query .= '  FROM ';
        $query .= '   program_data_converted_time p ';
        $query .= '  WHERE ';
        $query .= '   p.prepared = 1 ';

        // 日付
        $query .= '   AND p.date BETWEEN :startDate AND :endDate ';

        // 放送
        $query .= '   AND p.channel_id IN (' . implode(',', $channelBindArr) . ') ';

        // ジャンル
        if (!empty($genres)) {
            $genresBindArr = $this->createArrayBindParam('genres', ['genres' => $genres], $bindings);
            $query .= '   AND p.genre_id IN (' . implode(',', $genresBindArr) . ') ';
        }
        $query .= "   AND p.genre_id <> '20014' ";

        // 番組名
        if (!empty($progIds)) {
            $progIdsBindArr = $this->createArrayBindParam('progIds', ['progIds' => $progIds], $bindings);
            $query .= '   AND p.title IN (SELECT title From ' . $bsKey . 'programs WHERE prog_id IN (' . implode(',', $progIdsBindArr) . ')) ';
        }

        $list = $this->select($query, $bindings);

        return [
            array_unique(array_column($list, 'time_box_id')),
            array_unique(array_column($list, 'prog_id')),
            $this->createPanelersWhere($startDate, $endDate, $regionId, $division, $codes, $conditionCross),
        ];
    }

    protected function createPanelersWhere(String $startDate, String $endDate, int $regionId, String $division, ?array $codes, ?array $conditionCross)
    {
        $bindings = [];
        $divisionKey = $division . '_';
        $divCodes = [];

        if (is_array($codes) && count($codes) > 0) {
            $divCodes = array_filter($codes, function ($v, $k) {
                return $v != 'personal' && $v != 'household';
            }, ARRAY_FILTER_USE_BOTH);
        }
        // 日付
        $bindings[':startDate'] = $startDate;
        $bindings[':endDate'] = $endDate;
        $bindings[':regionId'] = $regionId;

        $query = '';
        $query .= ' SELECT DISTINCT ';
        $query .= ' paneler_id ';
        $query .= ' FROM ';
        $query .= '   time_box_panelers tbp ';
        $query .= ' CROSS JOIN ';

        if ($division === 'condition_cross') {
            $bindings[':condition_cross_code'] = 'condition_cross';
            $query .= ' ( ';
            $query .= ' (SELECT :condition_cross_code ::varchar(255) as code) ';
            $query .= ' ) codes ';
        } else {
            $tmpArr = [];

            foreach ($divCodes as $code) {
                $key = ':union_' . $divisionKey . $code;
                $bindings[$key] = $code;
                $tmpSql = ' ( ';
                $tmpSql .= ' SELECT ' . $key . '::varchar(255) as code ';
                $tmpSql .= ' ) ';
                array_push($tmpArr, $tmpSql);
            }
            $query .= count($tmpArr) > 0 ? ' ( ' . implode(' UNION ', $tmpArr) . ' ) AS codes ' : ' ( SELECT 1 code ) AS codes ';
        }
        $query .= ' WHERE ';
        $query .= '   time_box_id IN ( ';
        $query .= '   SELECT ';
        $query .= '     id ';
        $query .= '   FROM ';
        $query .= '     time_boxes ';
        $query .= '   WHERE ';
        $query .= '     ( ';
        $query .= "       :startDate <= ended_at - interval '5 hours' ";
        $query .= "       AND started_at - interval '5 hours' <= :endDate ";
        $query .= '       AND region_id = :regionId ';
        $query .= '     ) ';
        $query .= '   ) ';

        if ($division === 'condition_cross') {
            $query .= $this->createConditionCrossSql($conditionCross, $bindings);
        } else {
            $query .= ' AND ( ' . $this->createCrossJoinWhereClause($division, $divCodes, $bindings) . ' ) ';
        }

        $list = $this->select($query, $bindings);

        return array_unique(array_column($list, 'paneler_id'));
    }

    /**
     * TimeBoxの取得.
     *
     * @param string $startDate
     * @param string $endDate
     * @param array $regionId
     * @return string
     */
    protected function createTimeBoxListWhere(String $startDate, String $endDate, int $regionId): array
    {
        $query = "SELECT id, started_at, ended_at FROM time_boxes WHERE (:startDate <= ended_at - interval '5 hours' AND started_at - interval '5 hours' <= :endDate ) AND region_id = :regionId ORDER BY id";
        $bindings = [
            ':startDate' => $startDate,
            ':endDate' => $endDate,
            ':regionId' => $regionId,
        ];
        $list = $this->select($query, $bindings);
        return array_unique(array_column($list, 'id'));
    }

    protected function getPerHourlyLatestDateTime(int $regionId, string $intervalHourly, string $intervalMinutes): String
    {
        $result = $this->getRatingPointsLatestDateTime($regionId, $intervalHourly, $intervalMinutes);

        return $result->per_hourly_datetime;
    }

    protected function getPerMinutesLatestDateTime(int $regionId, string $intervalHourly, string $intervalMinutes): String
    {
        $result = $this->getRatingPointsLatestDateTime($regionId, $intervalHourly, $intervalMinutes);

        return $result->per_minutes_datetime;
    }

    private function getRatingPointsLatestDateTime(int $regionId, string $intervalHourly, string $intervalMinutes): \stdClass
    {
        $bindings = [
            ':regionid' => $regionId,
        ];
        $perHourly = $this->quote($intervalHourly);
        $perMinutes = $this->quote($intervalMinutes);
        $query = '';
        $query .= ' SELECT ';
        $query .= '   datetime - interval ' . $perHourly . ' AS per_hourly_datetime, ';
        $query .= '   datetime - interval ' . $perMinutes . ' AS per_minutes_datetime ';
        $query .= ' FROM ';
        $query .= '   time_keepers ';
        $query .= ' WHERE ';
        $query .= "   name = 'TimeReportsPrepared' ";
        $query .= '   AND region_id = :regionid ';
        return $this->selectOne($query, $bindings);
    }

    /**
     * かけ合わせ条件作成.
     *
     * @param array $params
     * @param array & $bindings
     * @param mixed $alias
     * @return array
     */
    private function createConditionCross(array $params, array &$bindings, string $alias): array
    {
        $conditionArr = [];

        // 性別 1つめの配列が空の場合は、ALL
        if (!empty($params['gender']) && !empty($params['gender'][0])) {
            $keys = $this->createArrayBindParam('gender', $params, $bindings);
            array_push($conditionArr, " ${alias}.gender IN (" . implode(',', $keys) . ')');
        }

        // 年齢
        if (!empty($params['age']['from'])) {
            $bindings[':age_from'] = $params['age']['from'];
            array_push($conditionArr, " ${alias}.age >= :age_from ");
        }

        // 年齢
        if (!empty($params['age']['to']) && $params['age']['to'] !== 99) {
            $bindings[':age_to'] = $params['age']['to'];
            array_push($conditionArr, " ${alias}.age <= :age_to ");
        }

        // 職業
        if (!empty($params['occupation']) && !empty($params['occupation'][0])) {
            $keys = $this->createArrayBindParam('occupation', $params, $bindings);
            array_push($conditionArr, " ${alias}.occupation IN (" . implode(',', $keys) . ')');
        }

        // 既未婚
        if (!empty($params['married']) && !empty($params['married'][0])) {
            $keys = $this->createArrayBindParam('married', $params, $bindings);
            array_push($conditionArr, " ${alias}.married IN (" . implode(',', $keys) . ')');
        }

        // 子供条件
        if (isset($params['child']) && $params['child']['enabled'] && $params['child']['enabled'] !== 'false') {
            $childAgeFrom = $params['child']['age']['from'];
            $childAgeTo = $params['child']['age']['to'];

            if (isset($childAgeFrom, $childAgeTo)) {
                $childGender = $params['child']['gender'];
                $condF = [];
                $condM = [];

                for ($i = $childAgeFrom; $i <= $childAgeTo; $i++) {
                    $target = str_pad($i, 2, 0, STR_PAD_LEFT);
                    $condF[] = "${alias}.childage_f LIKE :childage_${target}";
                    $condM[] = "${alias}.childage_m LIKE :childage_${target}";
                    $bindings[":childage_${target}"] = "%${target}%";
                }

                if (!empty($childGender)) {
                    if (empty($childGender[0])) {
                        $childGender = ['f', 'm'];
                    }
                    $condArr = [];

                    if (in_array('f', $childGender, true)) {
                        $condArr[] = '(' . implode(' OR ', $condF) . ')';
                    }

                    if (in_array('m', $childGender, true)) {
                        $condArr[] = '(' . implode(' OR ', $condM) . ')';
                    }
                    array_push($conditionArr, '(' . implode(' OR ', $condArr) . ')');
                }
            }
        }

        return $conditionArr;
    }
}
