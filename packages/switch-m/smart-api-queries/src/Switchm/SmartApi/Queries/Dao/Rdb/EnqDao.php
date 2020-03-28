<?php

namespace Switchm\SmartApi\Queries\Dao\Rdb;

class EnqDao extends Dao
{
    // カテゴリー（大項目・中項目）情報取得
    public function getCategory(): array
    {
        // 大項目
        $select = '';
        $select .= 'q_group, ';
        $select .= 'MIN(column_position) AS column_position ';

        $where = '';
        $where .= "q_group <> '' ";

        $groupBy = 'q_group ';

        $orderBy = 'column_position ';

        $query = sprintf(
            'SELECT %s FROM enq_questions WHERE %s GROUP BY %s ORDER BY %s;',
            $select,
            $where,
            $groupBy,
            $orderBy
        );

        $largeCategories = $this->select($query);
        array_unshift($largeCategories, ['q_group' => '全選択']);

        // 中項目
        $select = '';
        $select .= 'tag, ';
        $select .= 'q_group, ';
        $select .= 'MIN(column_position) AS column_position ';

        $where = '';
        $where .= "tag <> '' ";

        $groupBy = '';
        $groupBy .= 'tag, ';
        $groupBy .= 'q_group ';

        // ソート順は大項目と同様

        $query = sprintf(
            'SELECT %s FROM enq_questions WHERE %s GROUP BY %s ORDER BY %s;',
            $select,
            $where,
            $groupBy,
            $orderBy
        );

        $middleCategories = $this->select($query);
        array_unshift($middleCategories, ['q_group' => '', 'tag' => '全選択']);

        return [
            'largeCategories' => $largeCategories,
            'middleCategories' => $middleCategories,
        ];
    }

    // アンケート質問項目情報取得
    public function getQuestion(
        ?String $keyword,
        ?String $qGroup,
        ?String $tag
    ): array {
        $bindings = [];

        $where = '';

        if ($qGroup != '全選択') {
            $bindings[':q_group'] = $qGroup;
            $where = $where . ' and q_group = :q_group ';
        }

        if ($tag != '全選択') {
            $bindings[':tag'] = $tag;
            $where = $where . ' and tag = :tag ';
        }

        if (strlen($keyword) > 0) {
            // TODO キーワードの変換方針は未定
            $bindings[':keyword'] = '%' . $keyword . '%';

            $where = $where . ' AND (';
            $where = $where . ' question ILIKE :keyword OR ';
            $where = $where . ' option ILIKE :keyword ';
            $where = $where . ' )';
        }

        $q_no_item_where = '';
        $q_no_item_where .= " (q_no, coalesce(item,'')) in( ";
        $q_no_item_where .= '  SELECT ';
        $q_no_item_where .= "   q_no, coalesce(item,'') ";
        $q_no_item_where .= '  FROM';
        $q_no_item_where .= '   enq_questions ';
        $q_no_item_where .= '  WHERE ';
        $q_no_item_where .= "   ((a_type = 'SA' and q_type = 'MTS') or (a_type = 'MA' and q_type = 'MTM'))";
        $q_no_item_where .= "   AND q_group <> '' ";
        $q_no_item_where .= $where;
        $q_no_item_where .= '  GROUP BY ';
        $q_no_item_where .= '   q_no, ';
        $q_no_item_where .= '   item ';
        $q_no_item_where .= ' ) ';

        $q_no_where = '';
        $q_no_where .= ' (q_no) in( ';
        $q_no_where .= '  SELECT ';
        $q_no_where .= '   q_no ';
        $q_no_where .= '  FROM ';
        $q_no_where .= '   enq_questions ';
        $q_no_where .= '  WHERE ';
        $q_no_where .= "   ((a_type = 'SA' and q_type = 'SAR') or (a_type = 'MA' and q_type = 'MAC')) ";
        $q_no_where .= "   AND q_group <> '' ";
        $q_no_where .= $where;
        $q_no_where .= '  GROUP BY ';
        $q_no_where .= '   q_no ';
        $q_no_where .= '  ) ';

        $order_by = '';
        $order_by .= ' column_position, ';
        $order_by .= ' option_no ';

        $query = sprintf(
            'SELECT * FROM enq_questions WHERE %s OR %s ORDER BY %s;',
            $q_no_item_where,
            $q_no_where,
            $order_by
        );

        $results = $this->select($query, $bindings);

        // アンケート内容出力用に情報をまとめる
        $questionAll = [];
        $question = [];
        $optionAll = [];
        $option = [];

        $no = 0;
        $last = count($results) - 1;
        $index = 1;

        foreach ($results as $key => $result) {
            // オプション情報を設定する
            $option['name'] = $result->option;

            if ($result->a_type === 'SA') {
                $option['val'] = $result->option_no;
            } else {
                $option['val'] = $result->answer_column;
            }

            $option['answer_column'] = $result->answer_column;
            $option['q_type'] = $result->q_type;
            $option['a_type'] = $result->a_type;
            $option['index'] = $index++;
            array_push($optionAll, $option);

            // オプション情報初期化
            $option = [];

            // 最終ループではない
            if ($no++ != $last) {
                // 次レコード項目を見て質問の区切りを判断する
                $qType = $result->q_type;

                if ($qType === 'SAR' || $qType === 'MAC') {
                    if ($results[$key + 1]->q_no === $result->q_no) {
                        continue;
                    }
                } else {
                    if ($results[$key + 1]->item === $result->item) {
                        continue;
                    }
                }
            }

            // 質問情報を設定する
            $questionText = $result->question;

            if ($result->a_type === 'MA') {
                $questionText .= ' [複数回答可]';
            }

            if ($qType === 'SAR' || $qType === 'MAC') {
                $question['q_no'] = $result->q_no;
            } else {
                $question['q_no'] = $result->item;
            }
            $question['q_type'] = $result->q_type;
            $question['a_type'] = $result->a_type;
            $question['question'] = $questionText;
            $question['q_group'] = $result->q_group;
            $question['options'] = $optionAll;
            $question['tag'] = $result->tag;

            // 1質問の情報をセットする
            array_push($questionAll, $question);

            // 初期化
            $index = 1;
            $question = [];
            $optionAll = [];
        }

        return $questionAll;
    }

    // サンプル数取得
    public function getSampleCount(
        ?array $info,
        array $conditionCross,
        int $regionId
    ): array {
        $bindings = [];

        // 地域コード
        $bindings[':regionId'] = $regionId;

        $where = '';
        $where .= ' time_box_id = ( ';
        $where .= '  SELECT ';
        $where .= '   tb.id ';
        $where .= '  FROM ';
        $where .= '   time_boxes tb ';
        $where .= '  WHERE ';
        $where .= '   tb.region_id = :regionId ';
        $where .= '  ORDER BY ';
        $where .= '   started_at DESC ';
        $where .= '  LIMIT 1 ';
        $where .= ' ) ';

        // 掛け合わせ条件
        $conditionCrossSql = $this->createConditionCrossSql($conditionCross, $bindings);
        $where .= $conditionCrossSql;

        $with = '';
        $with .= ' WITH answer_panelers AS ( ';
        $with .= '   SELECT ';
        $with .= "     CASE WHEN a_type = 'SA' THEN option_no::varchar(50) ELSE eq.answer_column END AS val, ";
        $with .= '     eq.answer_column, ';
        $with .= '     paneler_id ';
        $with .= '   FROM ';
        $with .= '     enq_questions eq ';
        $with .= '     INNER JOIN enq_answers ea ';
        $with .= '     ON eq.answer_column = ea.answer_column AND ';
        $with .= "         ((eq.a_type = 'MA' AND ea.answer != 0) OR (eq.a_type = 'SA' AND ea.answer = eq.option_no)) ";
        $with .= ' ) ';

        if (count($info) != 0) {
            $infoQuery = '';

            foreach ($info as $groupId => $group) {
                $valueConditions = [];

                foreach ($group['values'] as $selectId => $value) {
                    $valBindName = ":${groupId}_${selectId}_val";
                    $answerColumnBindName = ":${groupId}_${selectId}_answer_column";

                    $bindings[$valBindName] = $value['val'];
                    $bindings[$answerColumnBindName] = $value['answer_column'];

                    $valueConditions[] = "(${valBindName}, ${answerColumnBindName})";
                }

                $groupQuery = '';
                $groupQuery .= ' ( ';
                $groupQuery .= 'SELECT paneler_id FROM answer_panelers ';
                $groupQuery .= '  WHERE (val, answer_column) IN ';
                $groupQuery .= '    (' . implode(',', $valueConditions) . ')';
                $groupQuery .= '  GROUP BY paneler_id ';

                if ($group['innerLinkingType'] === 'AND') {
                    $andCountBindName = ":${groupId}_value_count";
                    $bindings[$andCountBindName] = count($group['values']);

                    $groupQuery .= " HAVING COUNT(*) = ${andCountBindName}";
                }
                $groupQuery .= ' ) ';

                if (!empty($group['connectorLinkingType'])) {
                    if ($group['connectorLinkingType'] === 'AND') {
                        $groupQuery .= ' intersect ';
                    } else {
                        $groupQuery .= ' union ';
                    }
                }
                $infoQuery .= $groupQuery;
            }

            $where .= ' AND paneler_id in ( ';
            $where .= $infoQuery;
            $where .= ' ) ';
        }

        $query = sprintf(
            $with . 'SELECT COUNT(*) AS cnt FROM time_box_panelers AS tbp WHERE %s;',
            $where
        );

        $recordCount = $this->selectOne($query, $bindings);

        return [
            'cnt' => $recordCount->cnt,
        ];
    }

    // パネラーID取得
    // TODO - fujisaki: $infoが空の時やSQL結果が0件で中身が空の時にどうするのかを相談しつつリファクタリングが必要
    public function getPanelerIds(
        array $info,
        String $regionId
    ): array {
        $bindings = [];

        // 地域コード
        $bindings[':regionId'] = $regionId;

        $where = '';
        $where .= ' time_box_id IN ( ';
        $where .= '  SELECT ';
        $where .= '   tb.id ';
        $where .= '  FROM ';
        $where .= '   time_boxes tb ';
        $where .= '  WHERE ';
        $where .= '   tb.region_id = :regionId ';
        $where .= ' ) ';

        $with = '';
        $with .= ' WITH answer_panelers AS ( ';
        $with .= '   SELECT ';
        $with .= "     CASE WHEN a_type = 'SA' THEN option_no::varchar(50) ELSE eq.answer_column END AS val, ";
        $with .= '     eq.answer_column, ';
        $with .= '     paneler_id ';
        $with .= '   FROM ';
        $with .= '     enq_questions eq ';
        $with .= '     INNER JOIN enq_answers ea ';
        $with .= '     ON eq.answer_column = ea.answer_column AND ';
        $with .= "         ((eq.a_type = 'MA' AND ea.answer != 0) OR (eq.a_type = 'SA' AND ea.answer = eq.option_no)) ";
        $with .= ' ) ';

        $infoQuery = '';

        foreach ($info as $groupId => $group) {
            $valueConditions = [];

            foreach ($group['values'] as $selectId => $value) {
                $valBindName = ":${groupId}_${selectId}_val";
                $answerColumnBindName = ":${groupId}_${selectId}_answer_column";

                $bindings[$valBindName] = $value['val'];
                $bindings[$answerColumnBindName] = $value['answer_column'];

                $valueConditions[] = "(${valBindName}, ${answerColumnBindName})";
            }

            $groupQuery = '';
            $groupQuery .= ' ( ';
            $groupQuery .= 'SELECT paneler_id FROM answer_panelers ';
            $groupQuery .= '  WHERE (val, answer_column) IN ';
            $groupQuery .= '    (' . implode(',', $valueConditions) . ')';
            $groupQuery .= '  GROUP BY paneler_id ';

            if ($group['innerLinkingType'] === 'AND') {
                $andCountBindName = ":${groupId}_value_count";
                $bindings[$andCountBindName] = count($group['values']);

                $groupQuery .= " HAVING COUNT(*) = ${andCountBindName}";
            }
            $groupQuery .= ' ) ';

            if (!empty($group['connectorLinkingType'])) {
                if ($group['connectorLinkingType'] === 'AND') {
                    $groupQuery .= ' intersect ';
                } else {
                    $groupQuery .= ' union ';
                }
            }
            $infoQuery .= $groupQuery;
        }

        $where .= ' AND paneler_id in ( ';
        $where .= $infoQuery;
        $where .= ' ) ';

        $query = sprintf(
            $with . 'SELECT DISTINCT tbp.paneler_id FROM time_box_panelers AS tbp WHERE %s ORDER BY tbp.paneler_id ASC;',
            $where
        );
        $results = $this->select($query, $bindings);

        return [
                'list' => $results,
            ];
    }
}
