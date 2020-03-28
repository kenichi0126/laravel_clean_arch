<?php

namespace App\Http\UserInterfaces\CommercialGrp\Get;

trait PresenterTrait
{
    /**
     * @param array $list
     * @param string $division
     * @param null|array $codes
     * @param array $codeList
     * @param array $dataType
     * @return array
     */
    protected function convertPeriodTableData(array $list, string $division, ?array $codes, array $codeList, array $dataType): array
    {
        // dateごとに配列を分ける
        $hash = [];
        $result = [];

        foreach ($list as $row) {
            $hash[$row['date']][] = $row;
        }

        $isConditionCross = $division == 'condition_cross';

        if ($isConditionCross) {
            $codes = [];
        }

        foreach ($hash as $key => $rows) {
            $result = array_merge($result, $this->convertTableData($rows, $division, $codes, $key, $codeList, $dataType));
        }

        return $result;
    }

    /**
     * @param array $tableData
     * @param string $division
     * @param null|array $codes
     * @param array $codeList
     * @param string $period
     * @param array $dataType
     * @return array
     */
    protected function convertCsvData(array $tableData, string $division, ?array $codes, array $codeList, string $period, array $dataType): array
    {
        $divisionKey = $division . '_';
        $isConditionCross = $division == 'condition_cross';

        if ($isConditionCross) {
            $codes = [];
        }

        $divCodes = array_filter($codes, function ($v, $k) {
            return $v != 'personal' && $v != 'household';
        }, ARRAY_FILTER_USE_BOTH);

        $hasPersonal = !$isConditionCross && in_array('personal', $codes);
        $hasHousehold = $isConditionCross || in_array('household', $codes);
        $dispSelectedPersonal = 1 < count($divCodes) && count($divCodes) < count($codeList);

        $result = [];

        foreach ($tableData as $row) {
            $tmpRow = [];
            $tmpRow[] = $row['date'];
            $tmpRow[] = $row['company_name'];
            $tmpRow[] = $row['product_name'];
            $tmpRow[] = $row['total_count'];
            $tmpRow[] = $row['total_duration'];

            foreach (\Config::get('const.DATA_TYPE_NUMBER') as $type) {
                if (!in_array($type, $dataType)) {
                    continue;
                }
                $prefix = $this->getTypePrefix($type);

                // 個人全体
                if ($hasPersonal) {
                    $tmpRow[] = $row["${prefix}personal_viewing_grp"];
                }
                // 個人選択計
                if ($dispSelectedPersonal && !$isConditionCross && in_array($division, \Config::get('const.BASE_DIVISION'))) {
                    $tmpRow[] = $row["${prefix}total_viewing_grp"];
                }
                // 掛け合わせ条件
                if ($isConditionCross) {
                    $tmpRow[] = $row["${prefix}condition_cross"];
                }
                // codes
                if (is_array($divCodes)) {
                    foreach ($divCodes as $code) {
                        $name = $prefix . $divisionKey . $code;

                        if (isset($row[$name])) {
                            $tmpRow[] = round($row[$name], 1);
                        }
                    }
                }
                // 世帯
                if ($hasHousehold) {
                    $tmpRow[] = $row["${prefix}household_viewing_grp"];
                }
            }

            $result[] = $tmpRow;
        }

        return $result;
    }

    /**
     * @param array $list
     * @param string $division
     * @param null|array $codes
     * @param null|string $period
     * @param array $codeList
     * @param array $dataType
     * @return array
     */
    private function convertTableData(array $list, string $division, ?array $codes, ?string $period, array $codeList, array $dataType): array
    {
        $result = [];
        // 初期処理
        $company = $list[0]['name'];
        $productName = '';
        $secondRowFlag = false;
        $companySum = [];

        $divisionKey = $division . '_';
        $isConditionCross = $division == 'condition_cross';

        $divCodes = array_filter($codes, function ($v, $k) {
            return $v != 'personal' && $v != 'household';
        }, ARRAY_FILTER_USE_BOTH);

        $hasPersonal = !$isConditionCross && in_array('personal', $codes);
        $hasHousehold = $isConditionCross || in_array('household', $codes);
        $dispSelectedPersonal = 1 < count($divCodes) && count($divCodes) < count($codeList);

        foreach ($list as $index => $row) {
            $tmpRow = [];
            $tmpRow['date'] = '';

            // 1の場合は期間合計
            if ($index == 0) {
                $tmpRow['date'] = $period;
            }

            if ($row['name'] != $company) {
                $tmpRow['company_name'] = $company;
                $tmpRow['product_name'] = '企業合計';

                foreach ($companySum as $key => $val) {
                    $companySum[$key] = round($val, 1);
                }
                $tmpRow = array_merge($tmpRow, $companySum);

                array_push($result, $tmpRow);

                $tmpRow = [];
                $companySum = [];
                $tmpRow['date'] = '';

                $company = $row['name'];

                $companyChangeFlag = true;
            } else {
                $companyChangeFlag = false;
            }

            if ($row['product_name'] != $productName || $companyChangeFlag) {
                $productName = $row['product_name'];
                // 商品行の設定
                $tmpRow['company_name'] = $row['name'];
                $tmpRow['product_name'] = $row['product_name'] . ' 計';
                $sum = $this->sumRow($list, $index, $productName, $division, $codes, $codeList, $dataType, $company);
                $tmpRow = array_merge($tmpRow, $sum);

                if (empty($companySum)) {
                    $companySum = $sum;
                } else {
                    foreach ($sum as $key => $val) {
                        $companySum[$key] += round($val, 1);
                    }
                }

                array_push($result, $tmpRow);

                $tmpRow = [];

                $secondRowFlag = true;
            }

            if ($secondRowFlag) {
                $tmpRow['company_name'] = '＜局別内訳＞';
                $tmpRow['date'] = '';
                $secondRowFlag = false;
            } else {
                $tmpRow['company_name'] = '';
            }

            $tmpRow['product_name'] = $row['display_name'];
            $tmpRow['total_count'] = $row['total_cnt'];
            $tmpRow['total_duration'] = $row['total_duration'];

            foreach (\Config::get('const.DATA_TYPE_NUMBER') as $type) {
                if (!in_array($type, $dataType)) {
                    continue;
                }
                $prefix = $this->getTypePrefix($type);

                $tmpRow = $this->getTmpData(
                    $hasPersonal,
                    $dispSelectedPersonal,
                    $isConditionCross,
                    $hasHousehold,
                    $division,
                    $divCodes,
                    $divisionKey,
                    $tmpRow,
                    $row,
                    $prefix
                );
            }
            array_push($result, $tmpRow);
        }

        // 最終企業合計
        foreach ($companySum as $key => $val) {
            $companySum[$key] = round($val, 1);
        }
        $tmpRow['company_name'] = $company;
        $tmpRow['product_name'] = '企業合計';
        $tmpRow = array_merge($tmpRow, $companySum);

        array_push($result, $tmpRow);

        return $result;
    }

    /**
     * @param array $list
     * @param int $index
     * @param string $productName
     * @param string $division
     * @param null|array $codes
     * @param array $codeList
     * @param array $dataType
     * @param string $company
     * @return array
     */
    private function sumRow(array $list, int $index, string $productName, string $division, ?array $codes, array $codeList, array $dataType, string $company): array
    {
        $divisionKey = $division . '_';
        $isConditionCross = $division == 'condition_cross';

        $divCodes = array_filter($codes, function ($v, $k) {
            return $v != 'personal' && $v != 'household';
        }, ARRAY_FILTER_USE_BOTH);

        $hasPersonal = $division != 'condition_cross' && in_array('personal', $codes);
        $hasHousehold = $division == 'condition_cross' || in_array('household', $codes);
        $dispSelectedPersonal = 1 < count($divCodes) && count($divCodes) < count($codeList);

        $summary = [];

        for ($i = $index; $i < count($list); $i++) {
            $tmp = [];
            $row = $list[$i];

            if ($productName != $row['product_name'] || $company != $row['name']) {
                break;
            }

            $tmp['total_count'] = $row['total_cnt'];
            $tmp['total_duration'] = $row['total_duration'];

            foreach (\Config::get('const.DATA_TYPE_NUMBER') as $type) {
                if (!in_array($type, $dataType)) {
                    continue;
                }
                $prefix = $this->getTypePrefix($type);

                $tmp = $this->getTmpData(
                    $hasPersonal,
                    $dispSelectedPersonal,
                    $isConditionCross,
                    $hasHousehold,
                    $division,
                    $divCodes,
                    $divisionKey,
                    $tmp,
                    $row,
                    $prefix
                );
            }

            if (empty($summary)) {
                $summary = $tmp;
            } else {
                foreach ($tmp as $key => $val) {
                    $summary[$key] += $val;
                }
            }
        }

        foreach ($summary as $key => $val) {
            $summary[$key] = round($val, 1);
        }

        return $summary;
    }

    private function getTmpData(
        bool $hasPersonal,
        bool $dispSelectedPersonal,
        bool $isConditionCross,
        bool $hasHousehold,
        string $division,
        array $divCodes,
        string $divisionKey,
        array $tmp,
        array $row,
        string $prefix
    ) {
        // 個人全体
        if ($hasPersonal) {
            $tmp["${prefix}personal_viewing_grp"] = $row["${prefix}personal_viewing_grp"];
        }
        // 個人選択計
        if ($dispSelectedPersonal && !$isConditionCross && in_array($division, \Config::get('const.BASE_DIVISION'))) {
            $tmp["${prefix}total_viewing_grp"] = $row["${prefix}total_viewing_grp"];
        }
        // 掛け合わせ条件
        if ($isConditionCross) {
            $tmp["${prefix}condition_cross"] = $row["${prefix}condition_cross"];
        }
        // codes
        foreach ($divCodes as $val) {
            $name = $prefix . $divisionKey . $val;
            $tmp[$name] = round($row[$name], 1);
        }
        // 世帯
        if ($hasHousehold) {
            $tmp["${prefix}household_viewing_grp"] = $row["${prefix}household_viewing_grp"];
        }

        return $tmp;
    }

    private function getTypePrefix($type)
    {
        if ($type === \Config::get('const.DATA_TYPE_NUMBER.REALTIME')) {
            return 'rt_';
        }

        if ($type === \Config::get('const.DATA_TYPE_NUMBER.TIMESHIFT')) {
            return 'ts_';
        }

        if ($type === \Config::get('const.DATA_TYPE_NUMBER.GROSS')) {
            return 'gross_';
        }

        if ($type === \Config::get('const.DATA_TYPE_NUMBER.TOTAL')) {
            return 'total_';
        }

        if ($type === \Config::get('const.DATA_TYPE_NUMBER.RT_TOTAL')) {
            return 'rt_total_';
        }

        return '';
    }
}
