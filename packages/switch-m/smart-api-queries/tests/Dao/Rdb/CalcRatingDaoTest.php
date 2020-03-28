<?php

namespace Smart2\QueryModel\Dao\ReadRdb;

use Mockery;
use ReflectionException;
use Switchm\SmartApi\Queries\Dao\Rdb\CalcRatingDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class CalcRatingDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = CalcRatingDao::getInstance();
    }

    /**
     * @test
     * @dataProvider createConditionCrossDataProvider
     * @param $params
     * @param $expectedArr
     * @param $expectBinds
     * @throws ReflectionException
     */
    public function createConditionCross($params, $expectedArr, $expectBinds): void
    {
        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('createConditionCross');
        $method->setAccessible(true);

        $actualBind = [];
        $actualArr = $method->invokeArgs($this->target, [$params, &$actualBind, 'tbp']);

        $this->assertEquals($expectedArr, $actualArr);
        $this->assertEquals($expectBinds, $actualBind);
    }

    /**
     * @return array
     */
    public function createConditionCrossDataProvider()
    {
        $cases = ['childage_all', 'childage_f', 'childage_m', 'all'];
        $params = [
            [
                'gender' => [''],
                'age' => ['from' => 4, 'to' => 99],
                'occupation' => [''],
                'married' => [''],
                'child' => [
                    'enabled' => true,
                    'gender' => [''],
                    'age' => ['from' => 4, 'to' => 6],
                ],
            ],
            [
                'gender' => [''],
                'age' => ['from' => 4, 'to' => 99],
                'occupation' => [''],
                'married' => [''],
                'child' => [
                    'enabled' => true,
                    'gender' => ['f'],
                    'age' => ['from' => 7, 'to' => 10],
                ],
            ],
            [
                'gender' => [''],
                'age' => ['from' => 4, 'to' => 99],
                'occupation' => [''],
                'married' => [''],
                'child' => [
                    'enabled' => true,
                    'gender' => ['m'],
                    'age' => ['from' => 2, 'to' => 5],
                ],
            ],
            [
                'gender' => ['1'],
                'age' => ['from' => 4, 'to' => 19],
                'occupation' => ['1'],
                'married' => ['1'],
                'child' => [
                    'enabled' => false,
                    'gender' => [],
                    'age' => [],
                ],
            ],
        ];
        $expectedArr = [
            [
                ' tbp.age >= :age_from ',
                '((tbp.childage_f LIKE :childage_04 OR tbp.childage_f LIKE :childage_05 OR tbp.childage_f LIKE :childage_06) OR (tbp.childage_m LIKE :childage_04 OR tbp.childage_m LIKE :childage_05 OR tbp.childage_m LIKE :childage_06))',
            ],
            [
                ' tbp.age >= :age_from ',
                '((tbp.childage_f LIKE :childage_07 OR tbp.childage_f LIKE :childage_08 OR tbp.childage_f LIKE :childage_09 OR tbp.childage_f LIKE :childage_10))',
            ],
            [
                ' tbp.age >= :age_from ',
                '((tbp.childage_m LIKE :childage_02 OR tbp.childage_m LIKE :childage_03 OR tbp.childage_m LIKE :childage_04 OR tbp.childage_m LIKE :childage_05))',
            ],
            [
                ' tbp.gender IN (:gender0)',
                ' tbp.age >= :age_from ',
                ' tbp.age <= :age_to ',
                ' tbp.occupation IN (:occupation0)',
                ' tbp.married IN (:married0)',
            ],
        ];
        $expectBinds = [
            [
                ':age_from' => 4,
                ':childage_04' => '%04%',
                ':childage_05' => '%05%',
                ':childage_06' => '%06%',
            ],
            [
                ':age_from' => 4,
                ':childage_07' => '%07%',
                ':childage_08' => '%08%',
                ':childage_09' => '%09%',
                ':childage_10' => '%10%',
            ],
            [
                ':age_from' => 4,
                ':childage_02' => '%02%',
                ':childage_03' => '%03%',
                ':childage_04' => '%04%',
                ':childage_05' => '%05%',
            ],
            [
                ':gender0' => '1',
                ':age_from' => 4,
                ':age_to' => 19,
                ':occupation0' => '1',
                ':married0' => '1',
            ],
        ];

        foreach ($cases as $i => $case) {
            yield
            $case => [
                $params[$i],
                $expectedArr[$i],
                $expectBinds[$i],
            ];
        }
    }

    /**
     * @test
     */
    public function createArrayBindParam_invalid(): void
    {
        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('createArrayBindParam');
        $method->setAccessible(true);
        $keyName = 'invalid';
        $params = [
            'valid' => 1,
        ];
        $bindings = [];

        $actualBind = [];
        $actual = $method->invokeArgs($this->target, [$keyName, $params, &$bindings]);

        $this->assertEquals([], $actual);
        $this->assertEquals([], $bindings);
    }

    /**
     * @test
     * @dataProvider createTimeBoxDataProvider
     * @param $list
     * @param $expected
     */
    public function createTimeBoxCaseClause(array $list, string $expected): void
    {
        $this->target = Mockery::mock(CalcRatingDao::class, [])->makePartial();
        $startDate = 'startDate';
        $endDate = 'endDate';
        $regionId = 1;
        $target = 'tbp';

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($list);

        $actual = $this->target->createTimeBoxCaseClause($startDate, $endDate, $regionId, $target);

        $this->assertEquals($expected, $actual);
    }

    public function createTimeBoxDataProvider()
    {
        return
            [
                [
                    [
                        (object) ['started_at' => '1started_at', 'ended_at' => '1ended_at', 'id' => '1id'],
                        (object) ['started_at' => '2started_at', 'ended_at' => '2ended_at', 'id' => '2id'],
                    ],
                    "CASE WHEN tbp >= '1started_at' AND tbp < '1ended_at' THEN 1id WHEN tbp >= '2started_at' AND tbp < '2ended_at' THEN 2id END ",
                ],
                [
                    [
                    ],
                    ' 1=2 ',
                ],
            ];
    }

    /**
     * @test
     * @dataProvider createCrossJoinWhereClauseDataProvider
     * @param $list
     * @param $expected
     * @param string $division
     * @param array $codes
     * @param bool $hasPerosnal
     * @param bool $hasHousehold
     */
    public function createCrossJoinWhereClause(string $division, array $codes, bool $hasPerosnal, bool $hasHousehold, array $list, string $expected): void
    {
        $this->target = Mockery::mock(CalcRatingDao::class, [])->makePartial();
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($list);

        $bindings = [];

        $actual = $this->target->createCrossJoinWhereClause($division, $codes, $bindings, $hasPerosnal, $hasHousehold);

        $this->assertEquals($expected, $actual);
    }

    public function createCrossJoinWhereClauseDataProvider()
    {
        return
            [
                'empty' => ['division', [], false, false, [], ' 1 = 2 '],
                'normal' => ['division', ['codes'], false, false, [(object) ['code' => 'code', 'definition' => 'age=4-']], '(codes.code = :cross_divisioncode AND tbp.age >= :originaldivisioncodeage)'],
                'personal' => ['division', ['personal'], true, false, [], '(codes.code = :cross_divisionpersonal )'],
                'household' => ['division', ['household'], false, true, [], '(codes.code = :cross_divisionhousehold )'],
                'personal2' => ['division', ['personal'], false, false, [], '(codes.code = :cross_divisionpersonal AND 1 = 2 )'],
                'household2' => ['division', ['household'], false, false, [], '(codes.code = :cross_divisionhousehold AND 1 = 2 )'],
            ];
    }

    /**
     * @test
     * @dataProvider createCrossJoinArrayDataProvider
     * @param $list
     * @param $expected
     * @param string $division
     * @param array $codes
     * @param object $divData
     */
    public function createCrossJoinArray(string $division, array $codes, object $divData, array $list, array $expected): void
    {
        $this->target = Mockery::mock(CalcRatingDao::class, [])->makePartial();
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->andReturn($divData);
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($list);

        $bindings = [];

        $actual = $this->target->createCrossJoinArray($division, $codes, $bindings);

        $this->assertEquals($expected, $actual);
    }

    public function createCrossJoinArrayDataProvider()
    {
        return
            [
                'empty' => ['division', [], (object) ['divisionName' => 'd_name'], [], []],
                'normal' => ['division', ['codes'], (object) ['divisionName' => 'd_name'], [(object) ['code' => 'code', 'definition' => 'defs', 'name' => 'name', 'codeName' => 'codeName', 'condition' => 'condition']], [
                    [
                        'name' => 'division_code',
                        'divisionName' => null,
                        'codeName' => 'name',
                        'condition' => 'tbp.defs = :originaldivisioncodedefs',
                    ],
                ]],
            ];
    }

    /**
     * @test
     * @dataProvider createConditionCrossArrayDataProvider
     * @param $expected
     * @param array $params
     */
    public function createConditionCrossArray(array $params, $expected): void
    {
        $this->target = Mockery::mock(CalcRatingDao::class, [])->makePartial();

        $bindings = [];

        $actual = $this->target->createConditionCrossArray($params, $bindings, 'tbp');
        $this->assertEquals($expected, $actual);
    }

    public function createConditionCrossArrayDataProvider()
    {
        return
            [
                'empty' => [[], []],
                'normal' => [['gender' => '1'], [
                    [
                        'name' => 'condition_cross',
                        'divisionName' => '掛け合わせ条件',
                        'codeName' => '',
                        'condition' => ' tbp.gender IN ()',
                    ],
                ]],
            ];
    }

    /**
     * @test
     * @dataProvider createConditionOriginalDivSqlDataProvider
     * @param $expected
     * @param string $division
     * @param string $code
     * @param string $definition
     */
    public function createConditionOriginalDivSql(string $division, string $code, string $definition, string $expected): void
    {
        $this->target = Mockery::mock(CalcRatingDao::class, [])->makePartial();

        $bindings = [];

        $actual = $this->target->createConditionOriginalDivSql($division, $code, $definition, $bindings, 'tbp');
        $this->assertEquals($expected, $actual);
    }

    public function createConditionOriginalDivSqlDataProvider()
    {
        return
            [
                'empty' => ['division', 'code', 'definition', 'tbp.definition = :originaldivisioncodedefinition'],
                'normal' => ['division', 'code', 'gender=f:age=4-98:occupation=2,6,7:married=2:paneler_id=2,4,5,7,13', "tbp.gender = :originaldivisioncodegender AND tbp.age BETWEEN :originaldivisioncodeagefrom AND :originaldivisioncodeageto  AND tbp.occupation IN ( :originaldivisioncodeoccupation0,:originaldivisioncodeoccupation1,:originaldivisioncodeoccupation2)  AND tbp.married = :originaldivisioncodemarried AND tbp.paneler_id IN ( '2','4','5','7','13') "],
                'age99' => ['division', 'code', 'gender=f:age=4-99:occupation=2,6,7:married=2:paneler_id=2,4,5,7,13', "tbp.gender = :originaldivisioncodegender AND tbp.age >= :originaldivisioncodeage AND tbp.occupation IN ( :originaldivisioncodeoccupation0,:originaldivisioncodeoccupation1,:originaldivisioncodeoccupation2)  AND tbp.married = :originaldivisioncodemarried AND tbp.paneler_id IN ( '2','4','5','7','13') "],
                'ageonly99' => ['division', 'code', 'gender=f:age=-99', 'tbp.gender = :originaldivisioncodegender AND tbp.gender = :originaldivisioncodegender'],
                'ageonly98' => ['division', 'code', 'gender=f:age=-98', 'tbp.gender = :originaldivisioncodegender AND tbp.age <= :originaldivisioncodeage'],
                'child_age' => ['division', 'code', 'age=4-99:childage=5_6', 'tbp.age >= :originaldivisioncodeage AND ((tbp.childage_f LIKE :originaldivisioncodechildage05 OR tbp.childage_f LIKE :originaldivisioncodechildage06) OR (tbp.childage_m LIKE :originaldivisioncodechildage05 OR tbp.childage_m LIKE :originaldivisioncodechildage06))'],
                'childf_age' => ['division', 'code', 'age=4-99:occupation=16,17,18:childage_f=5_6', 'tbp.age >= :originaldivisioncodeage AND tbp.occupation IN ( :originaldivisioncodeoccupation0,:originaldivisioncodeoccupation1,:originaldivisioncodeoccupation2)  AND (tbp.childage_f LIKE :originaldivisioncodechildage_f05 OR tbp.childage_f LIKE :originaldivisioncodechildage_f06)'],
            ];
    }

    /**
     * @test
     * @dataProvider createCommercialListWhereDataProvider
     * @param $expected
     * @param string $startDate
     * @param string $endDate
     * @param ?String $cmType
     * @param ?String $cmSeconds
     * @param ?array $progIds
     * @param int $regionId
     * @param string $division
     * @param ?array $codes
     * @param ?array $conditionCross
     * @param ?array $companyIds
     * @param ?array $productIds
     * @param ?array $cmIds
     * @param array $channels
     * @param bool $straddlingFlg
     */
    public function createCommercialListWhere(String $startDate, String $endDate, ?String $cmType, ?String $cmSeconds, ?array $progIds, int $regionId, String $division, ?array $codes, ?array $conditionCross, ?array $companyIds, ?array $productIds, ?array $cmIds, array $channels, bool $straddlingFlg, array $expected): void
    {
        $this->target = Mockery::mock(CalcRatingDao::class, [])->makePartial();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected);

        list($a1, $a2, $a3) = $this->target->shouldAllowMockingProtectedMethods()->createCommercialListWhere($startDate, $endDate, $cmType, $cmSeconds, $progIds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $straddlingFlg);
        $this->assertEquals([1], $a1);
        $this->assertEquals([2], $a2);
        $this->assertEquals([], $a3);
    }

    public function createCommercialListWhereDataProvider()
    {
        $cases = ['case1', 'case2', 'case3'];
        $startDate = ['19900614', '19900614', '19900614'];
        $endDate = ['19900615', '19900615', '19900615'];
        $cmType = ['1', '2', ''];
        $cmSeconds = ['2', '3', ''];
        $progIds = [[10], [10], [10], [10]];
        $regionId = [1, 2, 1];
        $division = ['ga8', 'ga8', 'ga8'];
        $codes = [['personal'], ['personal', 'household'], ['household']];
        $conditionCross = [[], [], []];
        $companyIds = [[1, 2, 3], [1, 2, 3], [1, 2, 3]];
        $productIds = [[10, 11, 12], [], []];
        $cmIds = [['20', '21', '22'], [], []];
        $channels = [[1, 2, 3], [], []];
        $straddlingFlg = [true, true, false];
        $expected = [
            [['time_box_id' => 1, 'cm_id' => 2]],
            [['time_box_id' => 1, 'cm_id' => 2]],
            [['time_box_id' => 1, 'cm_id' => 2]],
        ];

        foreach ($cases as $i => $val) {
            yield $val => [
                $startDate[$i],
                $endDate[$i],
                $cmType[$i],
                $cmSeconds[$i],
                $progIds[$i],
                $regionId[$i],
                $division[$i],
                $codes[$i],
                $conditionCross[$i],
                $companyIds[$i],
                $productIds[$i],
                $cmIds[$i],
                $channels[$i],
                $straddlingFlg[$i],
                $expected[$i],
            ];
        }
    }

    /**
     * @test
     * @dataProvider createProgramListWhereDataProvider
     * @param $expected
     * @param string $startDate
     * @param string $endDate
     * @param array $channels
     * @param ?array $genres
     * @param ?array $programNames
     * @param string $division
     * @param ?array $conditionCross
     * @param ?array $codes
     * @param int $regionId
     * @param ?bool $bsFlg
     */
    public function createProgramListWhere(string $startDate, string $endDate, array $channels, ?array $genres, ?array $programNames, string $division, ?array $conditionCross, ?array $codes, int $regionId, ?bool $bsFlg, array $expected): void
    {
        $this->target = Mockery::mock(CalcRatingDao::class, [])->makePartial();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected);

        list($a1, $a2, $a3) = $this->target->shouldAllowMockingProtectedMethods()->createProgramListWhere($startDate, $endDate, $channels, $genres, $programNames, $division, $conditionCross, $codes, $regionId, $bsFlg);
        $this->assertEquals([1], $a1);
        $this->assertEquals([2], $a2);
        $this->assertEquals([], $a3);
    }

    public function createProgramListWhereDataProvider()
    {
        $cases = ['case1', 'case2', 'case3', 'case4'];
        $startDate = ['19900614', '19900614', '19900614', '19900614'];
        $endDate = ['19900614', '19900614', '19900614', '19900614'];
        $channels = [[], [1, 2, 3, 4, 5, 6, 7, 8], [], [1, 2, 3, 4, 5, 6, 7, 8]];
        $genres = [[], [10, 11, 12, 13, 14], [], [10, 11, 12, 13, 14]];
        $programNames = [[], ['progname'], [], ['progname']];
        $division = ['ga8', 'ga8', 'ga8', 'ga8'];
        $conditionCross = [[], [], [], []];
        $codes = [['personal', 'personal', 'household'], ['personal', 'household'], ['personal', 'household'], ['personal']];
        $regionId = [1, 2, 1, 2];
        $bsFlg = [true, true, false, false];
        $expected = [
            [['time_box_id' => 1, 'prog_id' => 2]],
            [['time_box_id' => 1, 'prog_id' => 2]],
            [['time_box_id' => 1, 'prog_id' => 2]],
            [['time_box_id' => 1, 'prog_id' => 2]],
        ];

        foreach ($cases as $i => $val) {
            yield $val => [
                $startDate[$i],
                $endDate[$i],
                $channels[$i],
                $genres[$i],
                $programNames[$i],
                $division[$i],
                $conditionCross[$i],
                $codes[$i],
                $regionId[$i],
                $bsFlg[$i],
                $expected[$i],
            ];
        }
    }

    /**
     * @test
     * @dataProvider createProgramListWhere_conditionCrossDataProvider
     * @param $expected
     * @param string $startDate
     * @param string $endDate
     * @param array $channels
     * @param ?array $genres
     * @param ?array $programNames
     * @param string $division
     * @param ?array $conditionCross
     * @param ?array $codes
     * @param int $regionId
     * @param ?bool $bsFlg
     */
    public function createProgramListWhere_conditionCross(string $startDate, string $endDate, array $channels, ?array $genres, ?array $programNames, string $division, ?array $conditionCross, ?array $codes, int $regionId, ?bool $bsFlg, array $expected): void
    {
        $this->target = Mockery::mock(CalcRatingDao::class, [])->makePartial();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected);

        list($a1, $a2, $a3) = $this->target->shouldAllowMockingProtectedMethods()->createProgramListWhere($startDate, $endDate, $channels, $genres, $programNames, $division, $conditionCross, $codes, $regionId, $bsFlg);
        $this->assertEquals([1], $a1);
        $this->assertEquals([2], $a2);
        $this->assertEquals([], $a3);
    }

    public function createProgramListWhere_conditionCrossDataProvider()
    {
        $cases = ['case1', 'case2', 'case3', 'case4'];
        $startDate = ['19900614', '19900614', '19900614', '19900614'];
        $endDate = ['19900614', '19900614', '19900614', '19900614'];
        $channels = [[], [1, 2, 3, 4, 5, 6, 7, 8], [], [1, 2, 3, 4, 5, 6, 7, 8]];
        $genres = [[], [10, 11, 12, 13, 14], [], [10, 11, 12, 13, 14]];
        $programNames = [[], ['progname'], [], ['progname']];
        $division = ['condition_cross', 'condition_cross', 'condition_cross', 'condition_cross'];
        $conditionCross = [[], [], [], []];
        $codes = [['condition_cross'], ['condition_cross'], ['condition_cross'], ['condition_cross']];
        $regionId = [1, 2, 1, 2];
        $bsFlg = [true, true, false, false];
        $expected = [
            [['time_box_id' => 1, 'prog_id' => 2]],
            [['time_box_id' => 1, 'prog_id' => 2]],
            [['time_box_id' => 1, 'prog_id' => 2]],
            [['time_box_id' => 1, 'prog_id' => 2]],
        ];

        foreach ($cases as $i => $val) {
            yield $val => [
                $startDate[$i],
                $endDate[$i],
                $channels[$i],
                $genres[$i],
                $programNames[$i],
                $division[$i],
                $conditionCross[$i],
                $codes[$i],
                $regionId[$i],
                $bsFlg[$i],
                $expected[$i],
            ];
        }
    }

    /**
     * @test
     * @dataProvider createProgramListWhere_multicodesDataProvider
     * @param $expected
     * @param string $startDate
     * @param string $endDate
     * @param array $channels
     * @param ?array $genres
     * @param ?array $programNames
     * @param string $division
     * @param ?array $conditionCross
     * @param ?array $codes
     * @param int $regionId
     * @param ?bool $bsFlg
     */
    public function createProgramListWhere_multicodes(string $startDate, string $endDate, array $channels, ?array $genres, ?array $programNames, string $division, ?array $conditionCross, ?array $codes, int $regionId, ?bool $bsFlg, array $expected): void
    {
        $this->target = Mockery::mock(CalcRatingDao::class, [])->makePartial();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn([])
            ->ordered('query');

        list($a1, $a2, $a3) = $this->target->shouldAllowMockingProtectedMethods()->createProgramListWhere($startDate, $endDate, $channels, $genres, $programNames, $division, $conditionCross, $codes, $regionId, $bsFlg);
        $this->assertEquals([], $a1);
        $this->assertEquals([], $a2);
        $this->assertEquals([], $a3);
    }

    public function createProgramListWhere_multicodesDataProvider()
    {
        $cases = ['case1', 'case2', 'case3', 'case4'];
        $startDate = ['19900614', '19900614', '19900614', '19900614'];
        $endDate = ['19900614', '19900614', '19900614', '19900614'];
        $channels = [[], [1, 2, 3, 4, 5, 6, 7, 8], [], [1, 2, 3, 4, 5, 6, 7, 8]];
        $genres = [[], [10, 11, 12, 13, 14], [], [10, 11, 12, 13, 14]];
        $programNames = [[], ['progname'], [], ['progname']];
        $division = ['ga8', 'ga8', 'ga8', 'ga8'];
        $conditionCross = [[], [], [], []];
        $codes = [['c'], ['c'], ['c'], ['c']];
        $regionId = [1, 2, 1, 2];
        $bsFlg = [true, true, false, false];
        $expected = [
            [['time_box_id' => 1, 'prog_id' => 2]],
            [['time_box_id' => 1, 'prog_id' => 2]],
            [['time_box_id' => 1, 'prog_id' => 2]],
            [['time_box_id' => 1, 'prog_id' => 2]],
        ];

        foreach ($cases as $i => $val) {
            yield $val => [
                $startDate[$i],
                $endDate[$i],
                $channels[$i],
                $genres[$i],
                $programNames[$i],
                $division[$i],
                $conditionCross[$i],
                $codes[$i],
                $regionId[$i],
                $bsFlg[$i],
                $expected[$i],
            ];
        }
    }

    /**
     * @test
     */
    public function createTimeBoxListWhere(): void
    {
        $this->target = Mockery::mock(CalcRatingDao::class, [])->makePartial();

        $startDate = '20190614';
        $endDate = '20190615';
        $regionId = 1;
        $bindings = [
            ':startDate' => $startDate,
            ':endDate' => $endDate,
            ':regionId' => $regionId,
        ];
        $expected = [1, 2];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any(), $bindings)
            ->andReturn([['id' => 1], ['id' => 2], ['id' => 2]])
            ->ordered('query');

        $actual = $this->target->shouldAllowMockingProtectedMethods()->createTimeBoxListWhere($startDate, $endDate, $regionId);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @param $expected
     * @dataProvider getPerHourlyLatestDateTimeDataProvider
     * @param string $regionId
     * @param string $intervalHourly
     * @param string $intervalMinutes
     * @param object $list
     * @param array $binds
     */
    public function getPerHourlyLatestDateTime(int $regionId, string $intervalHourly, string $intervalMinutes, object $list, array $binds, ?string $expected): void
    {
        $this->target = Mockery::mock(CalcRatingDao::class, [])->makePartial();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->with(Mockery::any(), $binds)
            ->andReturn($list);

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('quote')
            ->andReturn('');

        $actual = $this->target->shouldAllowMockingProtectedMethods()->getPerHourlyLatestDateTime($regionId, $intervalHourly, $intervalMinutes);
        $this->assertEquals($expected, $actual);
    }

    public function getPerHourlyLatestDateTimeDataProvider()
    {
        return [
            [
                1,
                '100',
                '60',
                (object) ['per_hourly_datetime' => 'result'],
                [':regionid' => 1],
                'result',
            ],
        ];
    }

    /**
     * @test
     * @param $expected
     * @dataProvider getPerMinutesLatestDateTimeDataProvider
     * @param string $regionId
     * @param string $intervalHourly
     * @param string $intervalMinutes
     * @param object $list
     * @param array $binds
     */
    public function getPerMinutesLatestDateTime(int $regionId, string $intervalHourly, string $intervalMinutes, object $list, array $binds, ?string $expected): void
    {
        $this->target = Mockery::mock(CalcRatingDao::class, [])->makePartial();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->with(Mockery::any(), $binds)
            ->andReturn($list);

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('quote')
            ->andReturn('');

        $actual = $this->target->shouldAllowMockingProtectedMethods()->getPerMinutesLatestDateTime($regionId, $intervalHourly, $intervalMinutes);
        $this->assertEquals($expected, $actual);
    }

    public function getPerMinutesLatestDateTimeDataProvider()
    {
        return [
            [
                1,
                '100',
                '60',
                (object) ['per_minutes_datetime' => 'result'],
                [':regionid' => 1],
                'result',
            ],
        ];
    }
}
