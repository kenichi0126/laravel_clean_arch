<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Rdb;

use Carbon\Carbon;
use Mockery;
use Switchm\SmartApi\Queries\Dao\Rdb\ProgramDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class ProgramDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = Mockery::mock(ProgramDao::class, [])->makePartial();
    }

    /**
     * @test
     * @dataProvider searchDataProvider
     * @param mixed $startDate
     * @param mixed $endDate
     * @param mixed $startTime
     * @param mixed $endTime
     * @param mixed $wdays
     * @param mixed $holiday
     * @param mixed $channels
     * @param mixed $genres
     * @param mixed $programNames
     * @param mixed $division
     * @param mixed $conditionCross
     * @param mixed $codes
     * @param mixed $order
     * @param mixed $length
     * @param mixed $regionId
     * @param mixed $page
     * @param mixed $straddlingFlg
     * @param mixed $bsFlg
     * @param mixed $csvFlag
     * @param mixed $programListExtensionFlag
     * @param mixed $dataType
     * @param mixed $dataTypeConst
     */
    public function search($startDate, $endDate, $startTime, $endTime, $wdays, $holiday, $channels, $genres, $programNames, $division, $conditionCross, $codes, $order, $length, $regionId, $page, $straddlingFlg, $bsFlg, $csvFlag, $programListExtensionFlag, $dataType, $dataTypeConst): void
    {
        $expected = [
            'list' => ['data'],
            'cnt' => 111,
        ];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('quote')
            ->andReturn("'quoted'");
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn(['data'])
            ->once();
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->andReturn((object) ['cnt' => 111])
            ->once();

        $actual = $this->target->search($startDate, $endDate, $startTime, $endTime, $wdays, $holiday, $channels, $genres, $programNames, $division, $conditionCross, $codes, $order, $length, $regionId, $page, $straddlingFlg, $bsFlg, $csvFlag, $programListExtensionFlag, $dataType, $dataTypeConst);

        $this->assertEquals($expected, $actual);
    }

    public function searchDataProvider()
    {
        // テストマトリクス
        $cases = ['case1', 'case2', 'case3', 'case4'];
        $startDate = ['19900614', '19900614', '19900614', '19900614'];
        $endDate = ['19900614', '19900614', '19900614', '19900614'];
        $startTime = ['', '000000', '100000', '050000'];
        $endTime = ['', '235959', '050000', '100000'];
        $wdays = [[0, 1, 2, 3, 4, 5, 6], [0, 1, 2, 3, 4, 5, 6], [0, 1, 2, 3, 4, 5, 6], [0, 1, 2, 3, 4, 5, 6]];
        $holiday = [false, true, false, true];
        $channels = [[], [1, 2, 3, 4, 5, 6, 7, 8], [], [1, 2, 3, 4, 5, 6, 7, 8]];
        $genres = [[], [10, 11, 12, 13, 14], [], [10, 11, 12, 13, 14]];
        $programNames = [[], ['progname'], [], ['progname']];
        $division = ['ga8', 'ga8', 'ga8', 'ga8'];
        $conditionCross = [[], [], [], []];
        $codes = [['personal', 'c1', 'household'], ['personal', 'c1', 'household'], ['personal', 'c1', 'household'], ['c1']];
        $order = [[['column' => 'date', 'dir' => 'desc'], ['column' => 'started_at', 'dir' => 'asc']], [], [['column' => 'date', 'dir' => 'desc'], ['column' => 'started_at', 'dir' => 'asc']], []];
        $length = [30, 30, 30, 30];
        $regionId = [1, 2, 1, 2];
        $page = [1, 2, 3, 4];
        $straddlingFlg = [true, false, true, false];
        $bsFlg = [true, true, false, false];
        $csvFlag = ['0', '0', '1', '1'];
        $programListExtensionFlag = [true, false, true, false];
        $dataType = [[1], [1], [1, 2, 3, 4], [1, 2, 3, 4]];
        $dataTypeConst = [['rt' => 1, 'ts' => 2, 'total' => 3, 'gross' => 4], ['rt' => 1, 'ts' => 2, 'total' => 3, 'gross' => 4], ['rt' => 1, 'ts' => 2, 'total' => 3, 'gross' => 4], ['rt' => 1, 'ts' => 2, 'total' => 3, 'gross' => 4]];

        foreach ($cases as $i => $val) {
            yield
            $val => [
                $startDate[$i],
                $endDate[$i],
                $startTime[$i],
                $endTime[$i],
                $wdays[$i],
                $holiday[$i],
                $channels[$i],
                $genres[$i],
                $programNames[$i],
                $division[$i],
                $conditionCross[$i],
                $codes[$i],
                $order[$i],
                $length[$i],
                $regionId[$i],
                $page[$i],
                $straddlingFlg[$i],
                $bsFlg[$i],
                $csvFlag[$i],
                $programListExtensionFlag[$i],
                $dataType[$i],
                $dataTypeConst[$i],
            ];
        }
    }

    /**
     * @test
     * @dataProvider findProgramDataProvider
     * @param string $progId
     * @param string $timeBoxId
     * @param array $expectBinds
     */
    public function findProgram(string $progId, string $timeBoxId, array $expectBinds): void
    {
        $expect = (object) [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->with(Mockery::any(), $expectBinds)
            ->andReturn($expect)
            ->once();

        $actual = $this->target->findProgram($progId, $timeBoxId);

        $this->assertEquals($expect, $actual);
    }

    public function findProgramDataProvider()
    {
        // テストマトリクス
        $cases = ['case1'];
        $progId = ['progId'];
        $timeBoxId = ['timeBoxId'];
        $expectBinds = [[':prog_id' => 'progId', ':time_box_id' => 'timeBoxId']];

        foreach ($cases as $i => $val) {
            yield
            $val => [
                $progId[$i],
                $timeBoxId[$i],
                $expectBinds[$i],
            ];
        }
    }

    /**
     * @test
     * @dataProvider tableDataProvider
     * @param string $startDateTime
     * @param string $endDateTime
     * @param string $startTime
     * @param string $endTime
     * @param ?array $channels
     * @param string $division
     * @param ?array $conditionCross
     * @param ?array $codes
     * @param bool $bsFlg
     * @param int $regionId
     * @param mixed $expectBinds
     */
    public function table(string $startDateTime, string $endDateTime, string $startTime, string $endTime, ?array $channels, string $division, ?array $conditionCross, ?array $codes, bool $bsFlg, int $regionId, $expectBinds): void
    {
        $expect = ['list' => ['rows']];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($expectBinds)
            ->andReturn(['rows'])
            ->once();

        $actual = $this->target->table($startDateTime, $endDateTime, $startTime, $endTime, $channels, $division, $conditionCross, $codes, $bsFlg, $regionId);

        $this->assertEquals($expect, $actual);
    }

    public function tableDataProvider()
    {
        // テストマトリクス
        $cases = ['case1', 'case2', 'case3', 'case4', 'case5'];
        $startDateTime = ['1990-06-14 10:00:00', '1990-06-14 10:00:00', '1990-06-14 05:00:00', '1990-06-15 04:00:00', '1990-06-15 04:00:00'];
        $endDateTime = ['1990-06-15 04:59:59', '1990-06-15 04:59:59', '1990-06-15 04:59:59', '1990-06-14 07:59:59', '1990-06-14 07:59:59'];
        $startTime = ['', '050000', '000000', '230000', '230000'];
        $endTime = ['', '235959', '235959', '025959', '025959'];
        $channels = [[1, 2, 3], [1, 2, 3, 4, 5, 6, 7], [1, 2, 3], [1, 2, 3, 4, 5, 6, 7], [1, 2, 3, 4, 5, 6, 7]];
        $division = ['', 'household', '', 'household', 'ga8'];
        $conditionCross = [[], [], [], [], []];
        $codes = [['personal'], ['household'], ['personal'], ['household'], ['c1']];
        $bsFlg = [true, false, true, false, false];
        $regionId = [1, 2, 1, 2, 2];
        $expectBinds = [
            function ($query, $bindings) { // case 1
                $expect = [
                    ':region_id' => 1,
                    ':digit' => 2,
                    ':division' => '',
                    ':startDateTime' => '1990-06-14 10:00:00',
                    ':endDateTime' => '1990-06-15 04:59:59',
                    ':channels0' => 1,
                    ':channels1' => 2,
                    ':channels2' => 3,
                    ':startTime' => '',
                    ':endTime' => '',
                    'toBoundary' => new Carbon('1990-06-15 04:59:59.000000'),
                ];
                $this->assertCount(count($expect), $bindings);

                foreach ($expect as $key => $val) {
                    $this->assertEquals($val, $bindings[$key]);
                }
                return true;
            },
            function ($query, $bindings) { // case 2
                $expect = [
                    ':region_id' => 2,
                    ':digit' => 1,
                    ':division' => 'household',
                    ':startDateTime' => '1990-06-14 10:00:00',
                    ':endDateTime' => '1990-06-15 04:59:59',
                    ':channels0' => 1,
                    ':channels1' => 2,
                    ':channels2' => 3,
                    ':channels3' => 4,
                    ':channels4' => 5,
                    ':channels5' => 6,
                    ':channels6' => 7,
                    ':startTime' => '050000',
                    ':endTime' => '235959',
                    'toBoundary' => new Carbon('1990-06-15 04:59:59.000000'),
                ];
                $this->assertCount(count($expect), $bindings);

                foreach ($expect as $key => $val) {
                    $this->assertEquals($val, $bindings[$key]);
                }
                return true;
            },
            function ($query, $bindings) { // case 3
                $expect = [
                    ':region_id' => 1,
                    ':digit' => 2,
                    ':division' => '',
                    ':startDateTime' => '1990-06-14 05:00:00',
                    ':endDateTime' => '1990-06-15 04:59:59',
                    ':channels0' => 1,
                    ':channels1' => 2,
                    ':channels2' => 3,
                    'toBoundary' => new Carbon('1990-06-15 04:59:59.000000'),
                ];
                $this->assertCount(count($expect), $bindings);

                foreach ($expect as $key => $val) {
                    $this->assertEquals($val, $bindings[$key]);
                }
                return true;
            },
            function ($query, $bindings) { // case 4
                $expect = [
                    ':region_id' => 2,
                    ':digit' => 1,
                    ':division' => 'household',
                    ':startDateTime' => '1990-06-15 04:00:00',
                    ':endDateTime' => '1990-06-14 07:59:59',
                    ':channels0' => 1,
                    ':channels1' => 2,
                    ':channels2' => 3,
                    ':channels3' => 4,
                    ':channels4' => 5,
                    ':channels5' => 6,
                    ':channels6' => 7,
                    ':startTime' => '230000',
                    ':endTime' => '025959',
                    'toBoundary' => new Carbon('1990-06-15 07:59:59.000000'),
                ];
                $this->assertCount(count($expect), $bindings);

                foreach ($expect as $key => $val) {
                    $this->assertEquals($val, $bindings[$key]);
                }
                return true;
            },
            function ($query, $bindings) { // case 5
                $expect = [
                    ':region_id' => 2,
                    ':digit' => 1,
                    ':division' => 'ga8',
                    ':ga8_0' => 'c1',
                    ':startDateTime' => '1990-06-15 04:00:00',
                    ':endDateTime' => '1990-06-14 07:59:59',
                    ':channels0' => 1,
                    ':channels1' => 2,
                    ':channels2' => 3,
                    ':channels3' => 4,
                    ':channels4' => 5,
                    ':channels5' => 6,
                    ':channels6' => 7,
                    ':startTime' => '230000',
                    ':endTime' => '025959',
                    'toBoundary' => new Carbon('1990-06-15 07:59:59.000000'),
                ];
                $this->assertCount(count($expect), $bindings);

                foreach ($expect as $key => $val) {
                    $this->assertEquals($val, $bindings[$key]);
                }
                return true;
            },
        ];

        foreach ($cases as $i => $val) {
            yield
            $val => [
                $startDateTime[$i],
                $endDateTime[$i],
                $startTime[$i],
                $endTime[$i],
                $channels[$i],
                $division[$i],
                $conditionCross[$i],
                $codes[$i],
                $bsFlg[$i],
                $regionId[$i],
                $expectBinds[$i],
            ];
        }
    }

    /**
     * @test
     * @dataProvider tableOriginalCustomDataProvider
     * @param string $startDateTime
     * @param string $endDateTime
     * @param string $startTime
     * @param string $endTime
     * @param ?array $channels
     * @param string $division
     * @param ?array $conditionCross
     * @param ?array $codes
     * @param bool $bsFlg
     * @param int $regionId
     * @param mixed $expectBinds
     */
    public function tableOriginalCustom(string $startDateTime, string $endDateTime, string $startTime, string $endTime, ?array $channels, string $division, ?array $conditionCross, ?array $codes, bool $bsFlg, int $regionId, $expectBinds): void
    {
        $expect = ['list' => ['rows']];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($expectBinds)
            ->andReturn(['rows'])
            ->once();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createCrossJoinWhereClause')
            ->with($division, $codes, Mockery::any())
            ->andReturn('')
            ->once();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldNotReceive('createCrossJoinWhereClause');

        $actual = $this->target->tableOriginal($startDateTime, $endDateTime, $startTime, $endTime, $channels, $division, $conditionCross, $codes, $bsFlg, $regionId);

        $this->assertEquals($expect, $actual);
    }

    public function tableOriginalCustomDataProvider()
    {
        // テストマトリクス
        $cases = ['case1', 'case2', 'case3', 'case4'];
        $startDateTime = ['1990-06-14 10:00:00', '1990-06-14 10:00:00', '1990-06-14 05:00:00', '1990-06-15 04:00:00'];
        $endDateTime = ['1990-06-15 04:59:59', '1990-06-15 04:59:59', '1990-06-15 04:59:59', '1990-06-14 07:59:59'];
        $startTime = ['', '050000', '000000', '230000'];
        $endTime = ['', '235959', '235959', '025959'];
        $channels = [[1, 2, 3], [1, 2, 3, 4, 5, 6, 7], [1, 2, 3], [1, 2, 3, 4, 5, 6, 7]];
        $division = ['custom', 'custom', 'custom', 'custom'];
        $conditionCross = [[], [], [], []];
        $codes = [['code1'], ['code2'], ['code1'], ['code2']];
        $bsFlg = [true, false, true, false];
        $regionId = [1, 2, 1, 2];
        $expectBinds = [
            function ($query, $bindings) { // case 1
                $expect = [
                    ':region_id' => 1,
                    'digit' => 2,
                    ':startDateTime' => '1990-06-14 10:00:00',
                    ':endDateTime' => '1990-06-15 04:59:59',
                    ':channels0' => 1,
                    ':channels1' => 2,
                    ':channels2' => 3,
                    ':union_custom_code1' => 'code1',
                    ':vertical_custom_code1' => 'code1',
                    ':startTime' => '',
                    ':endTime' => '',
                    'toBoundary' => new Carbon('1990-06-15 04:59:59.000000'),
                ];
                $this->assertCount(count($expect), $bindings);

                foreach ($expect as $key => $val) {
                    $this->assertEquals($val, $bindings[$key]);
                }
                return true;
            },
            function ($query, $bindings) { // case 2
                $expect = [
                    ':region_id' => 2,
                    'digit' => 1,
                    ':startDateTime' => '1990-06-14 10:00:00',
                    ':endDateTime' => '1990-06-15 04:59:59',
                    ':channels0' => 1,
                    ':channels1' => 2,
                    ':channels2' => 3,
                    ':channels3' => 4,
                    ':channels4' => 5,
                    ':channels5' => 6,
                    ':channels6' => 7,
                    ':union_custom_code2' => 'code2',
                    ':vertical_custom_code2' => 'code2',
                    ':startTime' => '050000',
                    ':endTime' => '235959',
                    'toBoundary' => new Carbon('1990-06-15 04:59:59.000000'),
                ];
                $this->assertCount(count($expect), $bindings);

                foreach ($expect as $key => $val) {
                    $this->assertEquals($val, $bindings[$key]);
                }
                return true;
            },
            function ($query, $bindings) { // case 3
                $expect = [
                    ':region_id' => 1,
                    'digit' => 2,
                    ':startDateTime' => '1990-06-14 05:00:00',
                    ':endDateTime' => '1990-06-15 04:59:59',
                    ':channels0' => 1,
                    ':channels1' => 2,
                    ':channels2' => 3,
                    ':union_custom_code1' => 'code1',
                    ':vertical_custom_code1' => 'code1',
                    'toBoundary' => new Carbon('1990-06-15 04:59:59.000000'),
                ];
                $this->assertCount(count($expect), $bindings);

                foreach ($expect as $key => $val) {
                    $this->assertEquals($val, $bindings[$key]);
                }
                return true;
            },
            function ($query, $bindings) { // case 4
                $expect = [
                    ':region_id' => 2,
                    'digit' => 1,
                    ':startDateTime' => '1990-06-15 04:00:00',
                    ':endDateTime' => '1990-06-14 07:59:59',
                    ':channels0' => 1,
                    ':channels1' => 2,
                    ':channels2' => 3,
                    ':channels3' => 4,
                    ':channels4' => 5,
                    ':channels5' => 6,
                    ':channels6' => 7,
                    ':union_custom_code2' => 'code2',
                    ':vertical_custom_code2' => 'code2',
                    ':startTime' => '230000',
                    ':endTime' => '025959',
                    'toBoundary' => new Carbon('1990-06-15 07:59:59.000000'),
                ];
                $this->assertCount(count($expect), $bindings);

                foreach ($expect as $key => $val) {
                    $this->assertEquals($val, $bindings[$key]);
                }
                return true;
            },
        ];

        foreach ($cases as $i => $val) {
            yield
            $val => [
                $startDateTime[$i],
                $endDateTime[$i],
                $startTime[$i],
                $endTime[$i],
                $channels[$i],
                $division[$i],
                $conditionCross[$i],
                $codes[$i],
                $bsFlg[$i],
                $regionId[$i],
                $expectBinds[$i],
            ];
        }
    }

    /**
     * @test
     * @dataProvider tableOriginalConditionCrossDataProvider
     * @param string $startDateTime
     * @param string $endDateTime
     * @param string $startTime
     * @param string $endTime
     * @param ?array $channels
     * @param string $division
     * @param ?array $conditionCross
     * @param ?array $codes
     * @param bool $bsFlg
     * @param int $regionId
     * @param mixed $expectBinds
     */
    public function tableOriginalConditionCross(string $startDateTime, string $endDateTime, string $startTime, string $endTime, ?array $channels, string $division, ?array $conditionCross, ?array $codes, bool $bsFlg, int $regionId, $expectBinds): void
    {
        $expect = ['list' => ['rows']];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($expectBinds)
            ->andReturn(['rows'])
            ->once();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldNotReceive('createCrossJoinWhereClause');

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createConditionCrossSql')
            ->with($conditionCross, Mockery::any())
            ->andReturn('')
            ->once();

        $actual = $this->target->tableOriginal($startDateTime, $endDateTime, $startTime, $endTime, $channels, $division, $conditionCross, $codes, $bsFlg, $regionId);

        $this->assertEquals($expect, $actual);
    }

    public function tableOriginalConditionCrossDataProvider()
    {
        // テストマトリクス
        $cases = ['case1', 'case2', 'case3', 'case4'];
        $startDateTime = ['1990-06-14 10:00:00', '1990-06-14 10:00:00', '1990-06-14 05:00:00', '1990-06-15 04:00:00'];
        $endDateTime = ['1990-06-15 04:59:59', '1990-06-15 04:59:59', '1990-06-15 04:59:59', '1990-06-14 07:59:59'];
        $startTime = ['', '050000', '000000', '230000'];
        $endTime = ['', '235959', '235959', '025959'];
        $channels = [[1, 2, 3], [1, 2, 3, 4, 5, 6, 7], [1, 2, 3], [1, 2, 3, 4, 5, 6, 7]];
        $division = ['condition_cross', 'condition_cross', 'condition_cross', 'condition_cross'];
        $conditionCross = [['condition'], ['condition'], ['condition'], ['condition']];
        $codes = [[], [], [], []];
        $bsFlg = [true, false, true, false];
        $regionId = [1, 2, 1, 2];
        $expectBinds = [
            function ($query, $bindings) { // case 1
                $expect = [
                    ':region_id' => 1,
                    'digit' => 2,
                    ':startDateTime' => '1990-06-14 10:00:00',
                    ':endDateTime' => '1990-06-15 04:59:59',
                    ':channels0' => 1,
                    ':channels1' => 2,
                    ':channels2' => 3,
                    ':condition_cross_code' => 'condition_cross',
                    ':startTime' => '',
                    ':endTime' => '',
                    'toBoundary' => new Carbon('1990-06-15 04:59:59.000000'),
                ];
                $this->assertCount(count($expect), $bindings);

                foreach ($expect as $key => $val) {
                    $this->assertEquals($val, $bindings[$key]);
                }
                return true;
            },
            function ($query, $bindings) { // case 2
                $expect = [
                    ':region_id' => 2,
                    'digit' => 1,
                    ':startDateTime' => '1990-06-14 10:00:00',
                    ':endDateTime' => '1990-06-15 04:59:59',
                    ':channels0' => 1,
                    ':channels1' => 2,
                    ':channels2' => 3,
                    ':channels3' => 4,
                    ':channels4' => 5,
                    ':channels5' => 6,
                    ':channels6' => 7,
                    ':condition_cross_code' => 'condition_cross',
                    ':startTime' => '050000',
                    ':endTime' => '235959',
                    'toBoundary' => new Carbon('1990-06-15 04:59:59.000000'),
                ];
                $this->assertCount(count($expect), $bindings);

                foreach ($expect as $key => $val) {
                    $this->assertEquals($val, $bindings[$key]);
                }
                return true;
            },
            function ($query, $bindings) { // case 3
                $expect = [
                    ':region_id' => 1,
                    'digit' => 2,
                    ':startDateTime' => '1990-06-14 05:00:00',
                    ':endDateTime' => '1990-06-15 04:59:59',
                    ':channels0' => 1,
                    ':channels1' => 2,
                    ':channels2' => 3,
                    ':condition_cross_code' => 'condition_cross',
                    'toBoundary' => new Carbon('1990-06-15 04:59:59.000000'),
                ];
                $this->assertCount(count($expect), $bindings);

                foreach ($expect as $key => $val) {
                    $this->assertEquals($val, $bindings[$key]);
                }
                return true;
            },
            function ($query, $bindings) { // case 4
                $expect = [
                    ':region_id' => 2,
                    'digit' => 1,
                    ':startDateTime' => '1990-06-15 04:00:00',
                    ':endDateTime' => '1990-06-14 07:59:59',
                    ':channels0' => 1,
                    ':channels1' => 2,
                    ':channels2' => 3,
                    ':channels3' => 4,
                    ':channels4' => 5,
                    ':channels5' => 6,
                    ':channels6' => 7,
                    ':condition_cross_code' => 'condition_cross',
                    ':startTime' => '230000',
                    ':endTime' => '025959',
                    'toBoundary' => new Carbon('1990-06-15 07:59:59.000000'),
                ];
                $this->assertCount(count($expect), $bindings);

                foreach ($expect as $key => $val) {
                    $this->assertEquals($val, $bindings[$key]);
                }
                return true;
            },
        ];

        foreach ($cases as $i => $val) {
            yield
            $val => [
                $startDateTime[$i],
                $endDateTime[$i],
                $startTime[$i],
                $endTime[$i],
                $channels[$i],
                $division[$i],
                $conditionCross[$i],
                $codes[$i],
                $bsFlg[$i],
                $regionId[$i],
                $expectBinds[$i],
            ];
        }
    }
}
