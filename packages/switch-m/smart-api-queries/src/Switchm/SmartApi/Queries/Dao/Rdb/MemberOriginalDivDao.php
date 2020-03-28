<?php

namespace Switchm\SmartApi\Queries\Dao\Rdb;

class MemberOriginalDivDao extends Dao
{
    /**
     * @param string $memberId
     * @param string $menu
     * @param int $regionId
     * @return array
     */
    public function selectWithMenu(String $memberId, String $menu, int $regionId): array
    {
        $bindings = [];
        $bindings[':memberId'] = $memberId;
        $bindings[':menu'] = $menu;
        $bindings[':regionId'] = $regionId;

        $query = '';
        $query .= ' SELECT  ';
        $query .= '   member_id, ';
        $query .= '   menu, ';
        $query .= '   LOWER(division) division, ';
        $query .= '   target_date_from, ';
        $query .= '   target_date_to, ';
        $query .= '   display_order, ';
        $query .= '   original_div_edit_flag ';
        $query .= ' FROM ';
        $query .= '   member_original_divs ';
        $query .= ' WHERE  ';
        $query .= '   member_id = :memberId AND  ';
        $query .= '   menu = :menu AND ';
        $query .= '   NOW() BEtweeN target_date_from AND target_date_to AND ';
        $query .= '   region_id = :regionId';
        $query .= ' ORDER BY ';
        $query .= '   display_order; ';

        return $this->select($query, $bindings);
    }

    public function selectDivisions(String $memberId, array $divisions, int $regionId): array
    {
        $bindings = [];
        $bindings[':memberId'] = $memberId;
        $bindings[':regionId'] = $regionId;

        $bindDivisions = $this->createArrayBindParam('divisions', [
            'divisions' => $divisions,
        ], $bindings);

        $query = '';
        $query .= ' SELECT ';
        $query .= "     (SELECT tad.name FROM attr_divs tad WHERE LOWER(tad.division) = LOWER(mod.division) AND code = '_def') division_name, ";
        $query .= "     CASE WHEN mod.menu = 'cm' THEN 'CM' ";
        $query .= "          WHEN mod.menu = 'timezone' THEN '時間帯' ";
        $query .= "          WHEN mod.menu = 'program' THEN '番組' ";
        $query .= "          WHEN mod.menu = 'rnf' THEN 'R&F' END menu, ";
        $query .= "     mod.target_date_from || '～' || mod.target_date_to period, ";
        $query .= '     mod.original_div_edit_flag, ';
        $query .= '     LOWER(mod.division) division, ';
        $query .= "     CASE WHEN mod.menu = 'timezone' THEN 1 ";
        $query .= "          WHEN mod.menu = 'program' THEN 2 ";
        $query .= "          WHEN mod.menu = 'cm' THEN 3 ";
        $query .= "          WHEN mod.menu = 'rnf' THEN 4 END menu_order ";
        $query .= ' FROM ';
        $query .= '     member_original_divs mod ';
        $query .= ' WHERE ';
        $query .= '     mod.member_id = :memberId AND ';
        $query .= '     LOWER(mod.division) in ( ' . implode(',', $bindDivisions) . ' ) AND ';
        $query .= "     mod.menu in('timezone', 'program', 'cm', 'rnf') AND ";
        $query .= '     mod.region_id = :regionId ';
        $query .= ' ORDER BY ';
        $query .= '     mod.display_order, ';
        $query .= '     division_name, ';
        $query .= '     menu_order ';

        return $this->select($query, $bindings);
    }

    public function selectCodes(array $divisions): array
    {
        $bindings = [];

        $bindDivisions = $this->createArrayBindParam('divisions', [
            'divisions' => $divisions,
        ], $bindings);

        $query = '';
        $query .= ' SELECT ';
        $query .= '     LOWER(ad.division) division, ';
        $query .= '     LOWER(ad.code) code, ';
        $query .= '     ad.name, ';
        $query .= '     ad.definition, ';
        $query .= '     ad.restore_info, ';
        $query .= '     ad.restore_info_text ';
        $query .= ' FROM ';
        $query .= '     attr_divs ad ';
        $query .= ' WHERE ';
        $query .= '     LOWER(ad.division) in (' . implode(',', $bindDivisions) . ") AND code <> '_def'  ";
        $query .= ' ORDER BY ';
        $query .= '     division, ';
        $query .= '     display_order ';

        return $this->select($query, $bindings);
    }

    public function selectDefinitionText(array $defs): array
    {
        $bindings = [];

        $bindKeyIndex = 0;

        $defs = array_unique($defs);

        $query = '';
        $query .= ' WITH codes AS( ';
        $query .= '   SELECT ';
        $query .= '     *, ';
        $query .= "     (SELECT DISTINCT cc.name FROM codes cc WHERE c.division = cc.division AND cc.code = '_def') division_name ";
        $query .= '   FROM ';
        $query .= '     codes c ';
        $query .= '   WHERE ';
        $query .= "     c.code <> '_def' ";
        $query .= ' ) ';

        $tmpStrArr = [];

        foreach ($defs as $def) {
            $splited = explode('=', $def);

            if ($splited[0] === 'paneler_id') {
                continue;
            }

            $divisionKey = ':' . $bindKeyIndex++;
            $defKey = ':' . $bindKeyIndex++;
            $bindings[$divisionKey] = $splited[0];
            $bindings[$defKey] = $def;

            $tmpStr = ' SELECT ';
            $tmpStr .= "     ${defKey}::text def, ";
            $tmpStr .= '     COALESCE((SELECT ';
            $tmpStr .= "        division_name || '=' || STRING_AGG(name, '、') condition_text ";
            $tmpStr .= '      FROM ';
            $tmpStr .= '        codes c ';
            $tmpStr .= '      WHERE ';
            $tmpStr .= "        LOWER(c.division) = ${divisionKey}::text AND ( ";
            $tmpStr .= $this->createConditionDefinitionTextSql($bindKeyIndex++, $def, $bindings, 'c');
            $tmpStr .= '        )';
            $tmpStr .= '     GROUP BY ';
            $tmpStr .= '        c.division_name ';
            $tmpStr .= "     ), '') condition_text ";

            $tmpStrArr[] = $tmpStr;
        }

        if (count($tmpStrArr) < 1) {
            return [];
        }
        $query .= implode(' UNION ', $tmpStrArr);
        return $this->select($query, $bindings);
    }

    protected function createConditionDefinitionTextSql(String $code, String $definition, array &$bindings, String $alias = 'c'): String
    {
        $result = [];
        $condArr = explode(':', $definition);
        $tmpStr = '';

        foreach ($condArr as $cond) {
            $colArr = explode('=', $cond);

            $col = 'code';
            $division = $colArr[0];
            $val = $colArr[1];
            $key = ':original' . $code . $col;

            if (strpos($val, ',') !== false) {
                // カンマ区切り
                $explode = explode(',', $val);
                $keyArr = $this->createArrayBindParam($key, [$key => $explode], $bindings);
                $tmpStr = $col . ' IN ( ' . implode(',', $keyArr) . ') ';
            } elseif (strpos($val, '-') !== false) {
                // ハイフン区切り
                $explode = explode('-', $val);

                if (!empty($explode[0]) && !empty($explode[1])) {
                    $tmpStr = $col . '::numeric BETWEEN ' . $key . 'from AND ' . $key . 'to ';
                    $bindings[$key . 'from'] = $explode[0];
                    $bindings[$key . 'to'] = $explode[1];
                } elseif (empty($explode[0]) && !empty($explode[1])) {
                    $tmpStr = $col . '::numeric <= ' . $key;
                    $bindings[$key] = $explode[1];
                } elseif (!empty($explode[0]) && empty($explode[1])) {
                    $tmpStr = $col . '::numeric >= ' . $key;
                    $bindings[$key] = $explode[0];
                }
            } else {
                // その他（イコール）
                $tmpStr = $col . ' = ' . $key;
                $bindings[$key] = $val;
            }
            $tmpStr = " ( ${tmpStr} AND ${alias}.division = '${division}' )";

            $result[] = $tmpStr;
        }

        return implode(' AND ', $result);
    }
}
