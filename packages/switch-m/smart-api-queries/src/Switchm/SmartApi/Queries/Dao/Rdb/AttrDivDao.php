<?php

namespace Switchm\SmartApi\Queries\Dao\Rdb;

class AttrDivDao extends Dao
{
    // 属性区分情報取得
    public function getAttrDiv(
        String $division,
        String $code
    ): array {
        $bindings = [];

        // 区分
        $bindings[':division'] = $division;
        // コード
        $bindings[':code'] = $code;

        $where = '';
        $where .= 'division = :division ';
        $where .= 'AND code = :code ';

        $query = sprintf(
            'SELECT ad.display_order, ad.code FROM attr_divs ad WHERE %s;',
            $where
        );

        $results = $this->select($query, $bindings);

        return [
            'list' => $results,
        ];
    }

    // 属性区分情報取得（並び順のみ）
    public function getDisplayOrder(
        String $division
    ): array {
        $bindings = [];

        // 区分
        $bindings[':division'] = $division;

        $where = '';
        $where .= 'division = :division ';
        $where .= "AND code <> '_def' ";

        $orderBy = 'display_order DESC ';

        $query = sprintf(
            'SELECT ad.display_order FROM attr_divs ad WHERE %s ORDER BY %s;',
            $where,
            $orderBy
        );

        $results = $this->select($query, $bindings);

        return [
            'list' => $results,
        ];
    }

    // コード情報取得（code <> "_def"指定）
    public function getCode(
        String $division
    ): array {
        $bindings = [];

        // 区分
        $bindings[':division'] = $division;

        $select = '';
        $select .= 'ad.code, ';
        $select .= 'ad.name ';

        $where = '';
        $where .= 'division = :division ';
        $where .= "AND code <> '_def' ";

        $orderBy = '';
        $orderBy .= 'display_order ASC ';

        $query = sprintf(
            'SELECT %s FROM attr_divs ad WHERE %s ORDER BY %s;',
            $select,
            $where,
            $orderBy
        );

        $results = $this->select($query, $bindings);

        return [
                'list' => $results,
            ];
    }
}
