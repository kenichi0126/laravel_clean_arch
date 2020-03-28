<?php

namespace Tests\Unit\App\Http\UserInterfaces\CommercialGrp\Get;

use App\Http\UserInterfaces\CommercialGrp\Get\PresenterTrait;
use Tests\TestCase;

class PresenterTraitTest extends TestCase
{
    use PresenterTrait;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     * @dataProvider getTypePrefixDataProvider
     * @param mixed $type
     * @param mixed $expected
     */
    public function getTypePrefixTest($type, $expected): void
    {
        $actual = $this->getTypePrefix($type);

        $this->assertSame($expected, $actual);
    }

    public function getTypePrefixDataProvider()
    {
        return [
            [0, 'rt_'],
            [1, 'ts_'],
            [2, 'gross_'],
            [3, 'total_'],
            [4, 'rt_total_'],
            [5, ''],
        ];
    }

    /**
     * @test
     * @dataProvider getTmpDataDataProvider
     * @param mixed $hasPersonal
     * @param mixed $dispSelectedPersonal
     * @param mixed $isConditionCross
     * @param mixed $hasHousehold
     * @param mixed $expected
     */
    public function getTmpDataTest($hasPersonal, $dispSelectedPersonal, $isConditionCross, $hasHousehold, $expected): void
    {
        $tmp = [];
        $row = [
            'rt_personal_viewing_grp' => 1.0,
            'rt_total_viewing_grp' => 1.0,
            'rt_condition_cross' => 1.0,
            'rt_household_viewing_grp' => 1.0,
            'rt_ga8_f1' => 1.0,
        ];
        $division = 'ga8';
        $divCodes = ['f1'];
        $divisionKey = 'ga8_';
        $prefix = 'rt_';

        $actual = $this->getTmpData(
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

        $this->assertSame($expected, $actual);
    }

    public function getTmpDataDataProvider()
    {
        return [
            [false, false, false, false, ['rt_ga8_f1' => 1.0]],
            [true, true, true, true, ['rt_personal_viewing_grp' => 1.0, 'rt_condition_cross' => 1.0, 'rt_ga8_f1' => 1.0, 'rt_household_viewing_grp' => 1.0]],
            [false, true, false, false, ['rt_total_viewing_grp' => 1.0, 'rt_ga8_f1' => 1.0]],
        ];
    }

    /**
     * @test
     * @dataProvider sumRowDataProvider
     * @param mixed $list
     * @param mixed $index
     * @param mixed $productName
     * @param mixed $division
     * @param mixed $codes
     * @param mixed $codeList
     * @param mixed $dataType
     * @param mixed $company
     * @param mixed $expected
     */
    public function sumRowTest($list, $index, $productName, $division, $codes, $codeList, $dataType, $company, $expected): void
    {
        $actual = $this->sumRow($list, $index, $productName, $division, $codes, $codeList, $dataType, $company);

        $this->assertSame($expected, $actual);
    }

    public function sumRowDataProvider()
    {
        return [
            [
                [],
                0,
                '',
                'ga8',
                ['personal', 'household', 'ft'],
                [],
                [],
                '',
                [],
            ],
            [
                [['product_name' => 'iPad']],
                0,
                'iPhone',
                'ga8',
                ['personal', 'household', 'ft'],
                [],
                [],
                '',
                [],
            ],
            [
                [['product_name' => 'iPhone', 'name' => 'Apple', 'total_cnt' => 100, 'total_duration' => 100]],
                0,
                'iPhone',
                'ga8',
                ['personal', 'household', 'ft'],
                [],
                [],
                'Apple',
                ['total_count' => 100.0, 'total_duration' => 100.0],
            ],
            [
                [['product_name' => 'iPhone', 'name' => 'Apple', 'total_cnt' => 100, 'total_duration' => 100], ['product_name' => 'iPhone', 'name' => 'Apple', 'total_cnt' => 100, 'total_duration' => 100]],
                0,
                'iPhone',
                'ga8',
                [],
                [],
                [0],
                'Apple',
                ['total_count' => 200.0, 'total_duration' => 200.0],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider convertTableDataDataProvider
     * @param mixed $list
     * @param mixed $codes
     * @param mixed $dataType
     * @param mixed $expected
     */
    public function convertTableDataTest($list, $codes, $dataType, $expected): void
    {
        $period = 'period';
        $division = 'ga8';
        $codeList = ['f1', 'personal'];

        $actual = $this->convertTableData($list, $division, $codes, $period, $codeList, $dataType);

        $this->assertSame($expected, $actual);
    }

    public function convertTableDataDataProvider()
    {
        return [
            [
                [['name' => 'test', 'product_name' => 'iPhone', 'total_cnt' => 1, 'total_duration' => 1, 'display_name' => 'disp']],
                ['f1'],
                [],
                [
                    ['date' => 'period', 'company_name' => 'test', 'product_name' => 'iPhone 計', 'total_count' => 1.0, 'total_duration' => 1.0],
                    ['company_name' => '＜局別内訳＞', 'date' => '', 'product_name' => 'disp', 'total_count' => 1, 'total_duration' => 1],
                    ['company_name' => 'test', 'date' => '', 'product_name' => '企業合計', 'total_count' => 1.0, 'total_duration' => 1.0], ],
            ],
            [
                [
                    ['name' => 'test', 'product_name' => '', 'total_cnt' => 1, 'total_duration' => 1, 'display_name' => 'disp', 'rt_ga8_f1' => 1],
                    ['name' => 'test2', 'product_name' => 'iPhone', 'total_cnt' => 1, 'total_duration' => 1, 'display_name' => 'disp', 'rt_ga8_f1' => 1],
                ],
                ['f1'],
                [0],
                [
                    ['date' => 'period', 'company_name' => '', 'product_name' => 'disp', 'total_count' => 1, 'total_duration' => 1, 'rt_ga8_f1' => 1.0],
                    ['date' => '', 'company_name' => 'test', 'product_name' => '企業合計'],
                    ['date' => '', 'company_name' => 'test2', 'product_name' => 'iPhone 計', 'total_count' => 1.0, 'total_duration' => 1.0, 'rt_ga8_f1' => 1.0],
                    ['company_name' => '＜局別内訳＞', 'date' => '',  'product_name' => 'disp', 'total_count' => 1, 'total_duration' => 1, 'rt_ga8_f1' => 1.0],
                    ['company_name' => 'test2', 'date' => '',  'product_name' => '企業合計', 'total_count' => 1.0, 'total_duration' => 1.0, 'rt_ga8_f1' => 1.0],
                ],
            ],
            [
                [
                    ['name' => 'test', 'product_name' => '', 'total_cnt' => 1, 'total_duration' => 1, 'display_name' => 'disp', 'rt_ga8_f1' => 1],
                    ['name' => 'test', 'product_name' => 'iPhone', 'total_cnt' => 1, 'total_duration' => 1, 'display_name' => 'disp', 'rt_ga8_f1' => 1],
                    ['name' => 'test', 'product_name' => 'iPad', 'total_cnt' => 1, 'total_duration' => 1, 'display_name' => 'disp', 'rt_ga8_f1' => 1],
                ],
                ['f1'],
                [0],
                [
                    ['date' => 'period', 'company_name' => '', 'product_name' => 'disp', 'total_count' => 1, 'total_duration' => 1, 'rt_ga8_f1' => 1.0],
                    ['date' => '', 'company_name' => 'test', 'product_name' => 'iPhone 計', 'total_count' => 1.0, 'total_duration' => 1.0, 'rt_ga8_f1' => 1.0],
                    ['company_name' => '＜局別内訳＞', 'date' => '',  'product_name' => 'disp', 'total_count' => 1, 'total_duration' => 1, 'rt_ga8_f1' => 1.0],
                    ['date' => '',  'company_name' => 'test',  'product_name' => 'iPad 計', 'total_count' => 1.0, 'total_duration' => 1.0, 'rt_ga8_f1' => 1.0],
                    ['company_name' => '＜局別内訳＞', 'date' => '',   'product_name' => 'disp', 'total_count' => 1, 'total_duration' => 1, 'rt_ga8_f1' => 1.0],
                    ['company_name' => 'test', 'date' => '', 'product_name' => '企業合計', 'total_count' => 2.0, 'total_duration' => 2.0, 'rt_ga8_f1' => 2.0],
                ],
            ],
            [
                [
                    ['name' => 'test1', 'product_name' => '', 'total_cnt' => 1, 'total_duration' => 1, 'display_name' => 'disp', 'rt_ga8_f1' => 1],
                    ['name' => 'test2', 'product_name' => 'iPhone', 'total_cnt' => 1, 'total_duration' => 1, 'display_name' => 'disp', 'rt_ga8_f1' => 1],
                    ['name' => 'test3', 'product_name' => 'iPad', 'total_cnt' => 1, 'total_duration' => 1, 'display_name' => 'disp', 'rt_ga8_f1' => 1],
                ],
                ['f1'],
                [0],
                [
                    ['date' => 'period', 'company_name' => '', 'product_name' => 'disp', 'total_count' => 1, 'total_duration' => 1, 'rt_ga8_f1' => 1.0],
                    ['date' => '', 'company_name' => 'test1', 'product_name' => '企業合計'],
                    ['date' => '', 'company_name' => 'test2', 'product_name' => 'iPhone 計', 'total_count' => 1.0, 'total_duration' => 1.0, 'rt_ga8_f1' => 1.0],
                    ['company_name' => '＜局別内訳＞', 'date' => '',  'product_name' => 'disp', 'total_count' => 1, 'total_duration' => 1, 'rt_ga8_f1' => 1.0],
                    ['date' => '',  'company_name' => 'test2',  'product_name' => '企業合計', 'total_count' => 1.0, 'total_duration' => 1.0, 'rt_ga8_f1' => 1.0],
                    ['date' => '', 'company_name' => 'test3', 'product_name' => 'iPad 計', 'total_count' => 1.0, 'total_duration' => 1.0, 'rt_ga8_f1' => 1.0],
                    ['company_name' => '＜局別内訳＞', 'date' => '',   'product_name' => 'disp', 'total_count' => 1, 'total_duration' => 1, 'rt_ga8_f1' => 1.0],
                    ['company_name' => 'test3', 'date' => '', 'product_name' => '企業合計', 'total_count' => 1.0, 'total_duration' => 1.0, 'rt_ga8_f1' => 1.0],
                ],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider convertCsvDataDataProvider
     * @param mixed $tableData
     * @param mixed $division
     * @param mixed $codes
     * @param mixed $codeList
     * @param mixed $dataType
     * @param mixed $expected
     */
    public function convertCsvDataTest($tableData, $division, $codes, $codeList, $dataType, $expected): void
    {
        $period = 'period';

        $actual = $this->convertCsvData($tableData, $division, $codes, $codeList, $period, $dataType);

        $this->assertSame($expected, $actual);
    }

    public function convertCsvDataDataProvider()
    {
        return [
            [
                [],
                'ga8',
                [],
                [],
                [],
                [],
            ],
            [
                [['date' => '', 'company_name' => '', 'product_name' => '', 'total_count' => 1, 'total_duration' => 1]],
                'ga8',
                ['f1', 'personal', 'household'],
                [],
                [],
                [['', '', '', 1, 1]],
            ],
            [
                [['date' => '', 'company_name' => '', 'product_name' => '', 'total_count' => 1, 'total_duration' => 1, 'rt_personal_viewing_grp' => 1, 'rt_total_viewing_grp' => 1, 'rt_household_viewing_grp' => 1],
                    ['date' => '', 'company_name' => '', 'product_name' => '', 'total_count' => 1, 'total_duration' => 1, 'rt_personal_viewing_grp' => 1, 'rt_total_viewing_grp' => 1, 'rt_household_viewing_grp' => 1, 'rt_ga8_f2' => 1], ],
                'ga8',
                ['f1', 'f2', 'personal', 'household'],
                ['f1', 'f2', 'f3', 'personal', 'household'],
                [0],
                [['', '', '', 1, 1, 1, 1, 1], ['', '', '', 1, 1, 1, 1, 1.0, 1]],
            ],
            [
                [['date' => '', 'company_name' => '', 'product_name' => '', 'total_count' => 1, 'total_duration' => 1, 'rt_condition_cross' => 1, 'rt_household_viewing_grp' => 1, 'ts_condition_cross' => 1, 'ts_household_viewing_grp' => 1]],
                'condition_cross',
                ['f1'],
                ['f1'],
                [0, 1],
                [['', '', '', 1, 1, 1, 1, 1, 1]],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider convertPeriodTableDataDataProvider
     * @param mixed $division
     * @param mixed $expected
     */
    public function convertPeriodTableDataTest($division, $expected): void
    {
        $list = [['date' => 'date', 'name' => 'name', 'product_name' => 'product_name', 'total_cnt' => 1, 'total_duration' => 1, 'display_name' => 'display_name']];
        $codes = [];
        $codeList = [];
        $dataType = [];

        $actual = $this->convertPeriodTableData($list, $division, $codes, $codeList, $dataType);

        $this->assertSame($expected, $actual);
    }

    public function convertPeriodTableDataDataProvider()
    {
        return [
            [
                'ga8',
                [
                    ['date' => 'date', 'company_name' => 'name', 'product_name' => 'product_name 計', 'total_count' => 1.0,  'total_duration' => 1.0],
                    ['company_name' => '＜局別内訳＞', 'date' => '', 'product_name' => 'display_name', 'total_count' => 1, 'total_duration' => 1],
                    ['company_name' => 'name', 'date' => '', 'product_name' => '企業合計', 'total_count' => 1.0, 'total_duration' => 1.0],
                ],
            ],
            [
                'condition_cross',
                [
                    ['date' => 'date', 'company_name' => 'name', 'product_name' => 'product_name 計', 'total_count' => 1.0,  'total_duration' => 1.0],
                    ['company_name' => '＜局別内訳＞', 'date' => '', 'product_name' => 'display_name', 'total_count' => 1, 'total_duration' => 1],
                    ['company_name' => 'name', 'date' => '', 'product_name' => '企業合計', 'total_count' => 1.0, 'total_duration' => 1.0],
                ],
            ],
        ];
    }
}
