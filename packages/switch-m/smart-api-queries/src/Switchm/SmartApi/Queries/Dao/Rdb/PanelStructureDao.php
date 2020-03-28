<?php

namespace Switchm\SmartApi\Queries\Dao\Rdb;

class PanelStructureDao extends Dao
{
    private $crossJoinUnions = [];

    private $conditions = [];

    /**
     * パネル構成データ取得.
     * @param array $atterDivs パネル構成用に加工したatter_divのデータ
     * @param int $regionId
     * @return array
     */
    public function getPanelData(array $atterDivs, int $regionId): array
    {
        $bindings = [];
        $regionBind = $this->putBindings($bindings, 'regionId', $regionId);
        list($unionQueries, $whereQueries) = $this->convertAtterDivsToQueryData($atterDivs, $bindings);

        $with = '';
        $with .= ' WITH div_codes AS ( ';
        $with .= '   SELECT ';
        $with .= '     division ';
        $with .= '     , code ';
        $with .= '   FROM ';
        $with .= '     ( ';
        $with .= implode(' UNION ', $unionQueries);
        $with .= '     ) dc ';
        $with .= ' ) ';

        $with .= ' , panel_structure AS ( ';
        $with .= '   SELECT ';
        $with .= '     division ';
        $with .= '     , code ';
        $with .= '     , ARRAY_AGG(paneler_id) as paneler_id';
        $with .= "     , CASE WHEN code = 'household' THEN COUNT(DISTINCT tbp.household_id) ELSE COUNT(tbp.paneler_id) END number";
        $with .= '   FROM ';
        $with .= '     time_box_panelers tbp ';
        $with .= '   CROSS JOIN ';
        $with .= '     div_codes codes ';
        $with .= '   WHERE ';
        $with .= "     tbp.time_box_id = (SELECT MAX(id) FROM time_boxes WHERE region_id = ${regionBind}) ";
        $with .= sprintf('AND (%s)', implode(' OR ', $whereQueries));
        $with .= '   GROUP BY codes.division, codes.code';
        $with .= ' ) ';

        $select = '';
        $select .= ' SELECT ';
        $select .= '   codes.division ';
        $select .= '   , codes.code ';
        $select .= '   , ps.paneler_id ';
        $select .= '   , COALESCE(ps.number, 0) number ';
        $select .= ' FROM ';
        $select .= '   div_codes codes ';
        $select .= ' LEFT JOIN ';
        $select .= '   panel_structure ps ';
        $select .= ' ON ';
        $select .= '   codes.division = ps.division ';
        $select .= ' AND ';
        $select .= '   codes.code = ps.code ';
        $query = $with . $select;

        $result = $this->select($query, $bindings);
        return $result;
    }

    /**
     * $nameを元に$bindingNameを生成して、bindings[$bindingName]に$valueを追加する。最後に$bindingNameの名前を返す。
     * @param array $bindings [description]
     * @param string $name [description]
     * @param string $value [description]
     * @return string
     */
    public function putBindings(array &$bindings, String $name, String $value): String
    {
        $bindingName;
        $key;

        $count = 0;
        // 念のため10000未満を条件にして無限ループ回避
        while ($count < 10000) {
            $bindingName = ':' . $name . $count;

            if (isset($bindings[$bindingName])) {
                if ($bindings[$bindingName] === $value) {
                    break;
                }
                $count++;
            } else {
                $bindings[$bindingName] = $value;
                break;
            }
        }

        return $bindingName;
    }

    /**
     * atterDivsをquery用のデータに変換.
     * @param array $atterDivs [description]
     * @param array $bindings [description]
     * @return array            [description]
     */
    private function convertAtterDivsToQueryData(array $atterDivs, array &$bindings): array
    {
        $unionQueries = [];
        $whereQueries = [];

        $baseDivision = '';

        foreach ($atterDivs['base'] as $key => $value) {
            $baseDivision = $key;
        }

        $unionQueryFormat = '(SELECT %s as division, %s as code)';
        $codesWhereQueryFormat = 'codes.division = %s AND codes.code = %s';

        // 集計区分個人全体
        $divisionBindingName = $this->putBindings($bindings, 'division', $baseDivision);
        $codeBindingName = $this->putBindings($bindings, 'code', 'personal');

        //集計区分個人全体union
        $appendUnionQuery = sprintf($unionQueryFormat, $divisionBindingName, $codeBindingName);
        array_push($unionQueries, $appendUnionQuery);
        // 集計区分個人全体where
        $appendWhereQuery = '(';
        $appendWhereQuery .= sprintf($codesWhereQueryFormat, $divisionBindingName, $codeBindingName);
        $appendWhereQuery .= ')';
        array_push($whereQueries, $appendWhereQuery);

        // 集計区分世帯全体
        $divisionBindingName = $this->putBindings($bindings, 'division', $baseDivision);
        $codeBindingName = $this->putBindings($bindings, 'code', 'household');

        //集計区分世帯全体union
        $appendUnionQuery = sprintf($unionQueryFormat, $divisionBindingName, $codeBindingName);
        array_push($unionQueries, $appendUnionQuery);
        // 集計区分世帯全体where
        $appendWhereQuery = '(';
        $appendWhereQuery .= sprintf($codesWhereQueryFormat, $divisionBindingName, $codeBindingName);
        $appendWhereQuery .= ')';
        array_push($whereQueries, $appendWhereQuery);

        foreach ($atterDivs['base'][$baseDivision] as $base) {
            // 選択されている集計区分のcodeごとのデータ
            $divisionBindingName = $this->putBindings($bindings, 'division', $baseDivision);
            $codeBindingName = $this->putBindings($bindings, 'code', $base['code']);

            // union追加
            $appendUnionQuery = sprintf($unionQueryFormat, $divisionBindingName, $codeBindingName);
            array_push($unionQueries, $appendUnionQuery);
            //where追加
            $appendWhereQuery = '(';
            $appendWhereQuery .= sprintf($codesWhereQueryFormat, $divisionBindingName, $codeBindingName);
            $appendWhereQuery .= ' AND ' . $this->createConditionOriginalDivSql($baseDivision, $base['code'], $base['definition'], $bindings);
            $appendWhereQuery .= ')';
            array_push($whereQueries, $appendWhereQuery);
        }

        if (empty($atterDivs['custom'])) {
            return [$unionQueries, $whereQueries];
        }

        // カスタム区分
        foreach ($atterDivs['custom'] as $customs) {
            foreach ($customs as $custom) {
                $divisionBindingName = $this->putBindings($bindings, 'division', $custom['division']);
                $codeBindingName = $this->putBindings($bindings, 'code', $custom['code']);

                // union追加
                $appendUnionQuery = sprintf($unionQueryFormat, $divisionBindingName, $codeBindingName);
                array_push($unionQueries, $appendUnionQuery);
                //where追加
                $appendWhereQuery = '(';
                $appendWhereQuery .= sprintf($codesWhereQueryFormat, $divisionBindingName, $codeBindingName);
                $appendWhereQuery .= ' AND ' . $this->createConditionOriginalDivSql($custom['division'], $custom['code'], $custom['definition'], $bindings);
                $appendWhereQuery .= ')';
                array_push($whereQueries, $appendWhereQuery);
            }
        }

        return [$unionQueries, $whereQueries];
    }
}
