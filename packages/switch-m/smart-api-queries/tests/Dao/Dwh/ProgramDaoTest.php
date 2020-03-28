<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Dwh;

use Carbon\Carbon;
use Mockery;
use Switchm\SmartApi\Queries\Dao\Dwh\ProgramDao;
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
     *
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
     * @param mixed $dataTypeFlags
     * @param mixed $dataTypeConst
     * @param mixed $prefixes
     * @param mixed $expectBinds
     * @param mixed $createProgramListTempTableBindings
     * @param mixed $programReportsPivotBindings
     * @param mixed $tsProgramReportsPivotBindings
     * @param mixed $divCodes
     * @param mixed $cmListBindings
     * @param mixed $cvListBindings
     * @param mixed $selectedPersonalName
     * @param mixed $codeNumber
     */
    public function search(
        $startDate,
        $endDate,
        $startTime,
        $endTime,
        $wdays,
        $holiday,
        $channels,
        $genres,
        $programNames,
        $division,
        $conditionCross,
        $codes,
        $order,
        $length,
        $regionId,
        $page,
        $straddlingFlg,
        $bsFlg,
        $csvFlag,
        $programListExtensionFlag,
        $dataType,
        $dataTypeFlags,
        $dataTypeConst,
        $prefixes,
        $selectedPersonalName,
        $codeNumber,
        $expectBinds,
        $createProgramListTempTableBindings,
        $programReportsPivotBindings,
        $tsProgramReportsPivotBindings,
        $divCodes,
        $cmListBindings,
        $cvListBindings
    ): void {
        $expected = [
            'list' => ['data'],
            'cnt' => 111,
        ];

        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createProgramListWhere')
            ->with($startDate, $endDate, $channels, $genres, $programNames, $division, $conditionCross, $codes, $regionId, $bsFlg)
            ->andReturns([[1], ['100', '200'], [3]])
            ->once()->ordered();

        // common
        // for createProgramListTempTable
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($createProgramListTempTableBindings, 'createProgram'))
            ->andReturn(['data'])
            ->once()->ordered();

        // only RT or GROSS
        // for program_reports_pivot
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($programReportsPivotBindings, 'ProgramReportsPivot'))
            ->andReturn(['data'])
            ->once()->ordered();

        // only TS or TOTAL
        // for ts_program_reports_pivot
        if (in_array(2, $dataType)) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->withArgs($this->bindAsserts($tsProgramReportsPivotBindings, 'tsProgramReportsPivotBindings'))
                ->andReturn(['data'])
                ->once()->ordered();
        }

        // only extensionFlag is true
        if ($programListExtensionFlag && !$bsFlg && $csvFlag == '1') {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createRtSampleTempTable')
                ->with($isConditionCross, $conditionCross, $division, $divCodes, [1], 'code', 'number', $selectedPersonalName, false, $codeNumber)
                ->andReturn()
                ->once()->ordered();

            // only extensionFlag is true
            // for cm_list
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->withArgs($this->bindAsserts($cmListBindings, 'cmListBindings'))
                ->andReturn(['data'])
                ->once()->ordered();

            // only extensionFlag is true
            // for cv_list
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('insertTemporaryTable')
                ->withArgs($this->bindAsserts($cvListBindings, 'cvListBindings'))
                ->andReturn(true)
                ->once()->ordered();

            // for analyze
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();

            // for cv_union
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();

            // for cv reprots pivot
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();

            // for cm_result
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();
        }

        // common
        // for result
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->andReturn(['data'])
            ->once()->ordered();

        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->with(Mockery::any())
            ->andReturn((object) ['cnt' => 111])
            ->once()->ordered();

        $actual = $this->target->search($startDate, $endDate, $startTime, $endTime, $wdays, $holiday, $channels, $genres, $programNames, $division, $conditionCross, $codes, $order, $length, $regionId, $page, $straddlingFlg, $bsFlg, $csvFlag, $programListExtensionFlag, $dataType, $dataTypeFlags, $dataTypeConst, $prefixes, $selectedPersonalName, $codeNumber);

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
        $dataTypeFlags = [['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => false], ['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => false], ['rt' => true, 'ts' => true, 'gross' => true, 'total' => true, 'rtTotal' => false], ['rt' => true, 'ts' => true, 'gross' => true, 'total' => true, 'rtTotal' => false]];
        $dataTypeConst = [['rt' => 1, 'ts' => 2, 'total' => 4, 'gross' => 3], ['rt' => 1, 'ts' => 2, 'total' => 4, 'gross' => 3], ['rt' => 1, 'ts' => 2, 'total' => 4, 'gross' => 3], ['rt' => 1, 'ts' => 2, 'total' => 4, 'gross' => 3]];
        $prefixes = [['code' => 'code', 'number' => 'number'], ['code' => 'code', 'number' => 'number'], ['code' => 'code', 'number' => 'number'], ['code' => 'code', 'number' => 'number']];
        $selectedPersonalName = ['selected_personal', 'selected_personal', 'selected_personal', 'selected_personal'];
        $codeNumber = [32, 32, 32, 32];
        // expects
        $expectBinds = [[], [], [], []];
        $createProgramListTempTableBindings = [
            [// case 1
                ':startDate' => '19900614',
                ':endDate' => '19900614',
                ':regionId' => 1,
                ':wdays0' => 0,
                ':wdays1' => 1,
                ':wdays2' => 2,
                ':wdays3' => 3,
                ':wdays4' => 4,
                ':wdays5' => 5,
                ':wdays6' => 6,
                ':startTime' => '',
                ':endTime' => '',
            ],
            [// case 2
                ':startDate' => '19900614',
                ':endDate' => '19900614',
                ':regionId' => 2,
                ':genres0' => 10,
                ':genres1' => 11,
                ':genres2' => 12,
                ':genres3' => 13,
                ':genres4' => 14,
                ':channels0' => 1,
                ':channels1' => 2,
                ':channels2' => 3,
                ':channels3' => 4,
                ':channels4' => 5,
                ':channels5' => 6,
                ':channels6' => 7,
                ':channels7' => 8,
                ':wdays0' => 0,
                ':wdays1' => 1,
                ':wdays2' => 2,
                ':wdays3' => 3,
                ':wdays4' => 4,
                ':wdays5' => 5,
                ':wdays6' => 6,
                ':programNames0' => 'progname',
                ':startTime' => '000000',
                ':endTime' => '235959',
            ],
            [// case 3
                ':startDate' => '19900614',
                ':endDate' => '19900614',
                ':regionId' => 1,
                ':wdays0' => 0,
                ':wdays1' => 1,
                ':wdays2' => 2,
                ':wdays3' => 3,
                ':wdays4' => 4,
                ':wdays5' => 5,
                ':wdays6' => 6,
                ':startTime' => '100000',
                ':endTime' => '050000',
            ],
            [ // case 4
                ':startDate' => '19900614',
                ':endDate' => '19900614',
                ':regionId' => 2,
                ':genres0' => 10,
                ':genres1' => 11,
                ':genres2' => 12,
                ':genres3' => 13,
                ':genres4' => 14,
                ':channels0' => 1,
                ':channels1' => 2,
                ':channels2' => 3,
                ':channels3' => 4,
                ':channels4' => 5,
                ':channels5' => 6,
                ':channels6' => 7,
                ':channels7' => 8,
                ':wdays0' => 0,
                ':wdays1' => 1,
                ':wdays2' => 2,
                ':wdays3' => 3,
                ':wdays4' => 4,
                ':wdays5' => 5,
                ':wdays6' => 6,
                ':programNames0' => 'progname',
                ':startTime' => '050000',
                ':endTime' => '100000',
            ],
        ];
        $programReportsPivotBindings = [
            [
                ':division' => 'ga8',
                ':codeName0' => 'c1',
            ],
            [
                ':division' => 'ga8',
                ':codeName0' => 'c1',
            ],
            [
                ':division' => 'ga8',
                ':codeName0' => 'c1',
            ],
            [
                ':division' => 'ga8',
                ':codeName0' => 'c1',
            ],
        ];
        $tsProgramReportsPivotBindings = [
            [],
            [],
            [
                ':codeName0' => 'c1',
                ':division' => 'ga8',
            ], [
                ':codeName0' => 'c1',
                ':division' => 'ga8',
            ],
        ];
        $divCodes = [['c1'], ['c1'], ['c1'], ['c1']];
        $cmListBindings = [
            [],
            [],
            [
                ':startDate' => '19900614',
                ':endDate' => '19900614',
                ':progStartDate' => '199006130000000000',
                ':progEndDate' => '199006159999999999',
            ],
            [],
        ];

        $cvListBindings = [
            [],
            [],
            [
                ':startDate' => '19900614',
                ':endDate' => '19900614',
                ':progStartDate' => '199006130000000000',
                ':progEndDate' => '199006159999999999',
                ':regionId' => 1,
            ],
            [],
        ];

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
                $dataTypeFlags[$i],
                $dataTypeConst[$i],
                $prefixes[$i],
                $selectedPersonalName[$i],
                $codeNumber[$i],
                $expectBinds[$i],
                $createProgramListTempTableBindings[$i],
                $programReportsPivotBindings[$i],
                $tsProgramReportsPivotBindings[$i],
                $divCodes[$i],
                $cmListBindings[$i],
                $cvListBindings[$i],
            ];
        }
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
     * @param mixed $dataTypeFlags
     * @param mixed $dataTypeConst
     * @param mixed $prefixes
     * @param mixed $expectBinds
     * @param mixed $createProgramListTempTableBindings
     * @param mixed $programReportsPivotBindings
     * @param mixed $tsProgramReportsPivotBindings
     * @param mixed $divCodes
     * @param mixed $cmListBindings
     * @param mixed $cvListBindings
     * @param mixed $selectedPersonalName
     * @param mixed $codeNumber
     */
    public function searchEmpty(
        $startDate,
        $endDate,
        $startTime,
        $endTime,
        $wdays,
        $holiday,
        $channels,
        $genres,
        $programNames,
        $division,
        $conditionCross,
        $codes,
        $order,
        $length,
        $regionId,
        $page,
        $straddlingFlg,
        $bsFlg,
        $csvFlag,
        $programListExtensionFlag,
        $dataType,
        $dataTypeFlags,
        $dataTypeConst,
        $prefixes,
        $selectedPersonalName,
        $codeNumber,
        $expectBinds,
        $createProgramListTempTableBindings,
        $programReportsPivotBindings,
        $tsProgramReportsPivotBindings,
        $divCodes,
        $cmListBindings,
        $cvListBindings
    ): void {
        $expected = [
            'list' => [],
            'cnt' => 0,
        ];

        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createProgramListWhere')
            ->with($startDate, $endDate, $channels, $genres, $programNames, $division, $conditionCross, $codes, $regionId, $bsFlg)
            ->andReturns([[], [], []])
            ->once()->ordered();

        $actual = $this->target->search($startDate, $endDate, $startTime, $endTime, $wdays, $holiday, $channels, $genres, $programNames, $division, $conditionCross, $codes, $order, $length, $regionId, $page, $straddlingFlg, $bsFlg, $csvFlag, $programListExtensionFlag, $dataType, $dataTypeFlags, $dataTypeConst, $prefixes, $selectedPersonalName, $codeNumber);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider searchOriginalDataProvider
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
     * @param mixed $dataTypeConst
     * @param mixed $prefixes
     * @param mixed $isConditionCross
     * @param mixed $expectBinds
     * @param mixed $createProgramListTempTableBindings
     * @param mixed $divCodes
     * @param mixed $cmListBindings
     * @param mixed $cvListBindings
     * @param mixed $selectedPersonalName
     * @param mixed $codeNumber
     */
    public function searchOriginalEmpty(
        $startDate,
        $endDate,
        $startTime,
        $endTime,
        $wdays,
        $holiday,
        $channels,
        $genres,
        $programNames,
        $division,
        $conditionCross,
        $codes,
        $order,
        $length,
        $regionId,
        $page,
        $straddlingFlg,
        $bsFlg,
        $csvFlag,
        $programListExtensionFlag,
        $dataTypeConst,
        $prefixes,
        $selectedPersonalName,
        $codeNumber,
        $isConditionCross,
        $expectBinds,
        $createProgramListTempTableBindings,
        $divCodes,
        $cmListBindings,
        $cvListBindings
    ): void {
        $expected = [
            'list' => [],
            'cnt' => 0,
        ];
        $dataType = [1];
        $dataTypeFlags = ['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => false];
        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createProgramListWhere')
            ->with($startDate, $endDate, $channels, $genres, $programNames, $division, $conditionCross, $codes, $regionId, $bsFlg)
            ->andReturns([[], [], []])
            ->once()->ordered();

        $actual = $this->target->searchOriginal($startDate, $endDate, $startTime, $endTime, $wdays, $holiday, $channels, $genres, $programNames, $division, $conditionCross, $codes, $order, $length, $regionId, $page, $straddlingFlg, $bsFlg, $csvFlag, $programListExtensionFlag, $dataTypeFlags, $dataType, $dataTypeConst, $prefixes, $selectedPersonalName, $codeNumber);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider searchOriginalDataProvider
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
     * @param mixed $dataTypeConst
     * @param mixed $prefixes
     * @param mixed $isConditionCross
     * @param mixed $expectBinds
     * @param mixed $createProgramListTempTableBindings
     * @param mixed $divCodes
     * @param mixed $cmListBindings
     * @param mixed $cvListBindings
     * @param mixed $selectedPersonalName
     * @param mixed $codeNumber
     */
    public function searchOriginalRt(
        $startDate,
        $endDate,
        $startTime,
        $endTime,
        $wdays,
        $holiday,
        $channels,
        $genres,
        $programNames,
        $division,
        $conditionCross,
        $codes,
        $order,
        $length,
        $regionId,
        $page,
        $straddlingFlg,
        $bsFlg,
        $csvFlag,
        $programListExtensionFlag,
        $dataTypeConst,
        $prefixes,
        $selectedPersonalName,
        $codeNumber,
        $isConditionCross,
        $expectBinds,
        $createProgramListTempTableBindings,
        $divCodes,
        $cmListBindings,
        $cvListBindings
    ): void {
        $expected = [
            'list' => ['data'],
            'cnt' => 111,
        ];
        $dataType = [1];
        $dataTypeFlags = ['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => false];

        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createProgramListWhere')
            ->with($startDate, $endDate, $channels, $genres, $programNames, $division, $conditionCross, $codes, $regionId, $bsFlg)
            ->andReturns([[1], ['100', '200'], [3]])
            ->once()->ordered();

        // common
        // for createProgramListTempTable
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($createProgramListTempTableBindings, 'searchOriginalRt - createProgram'))
            ->andReturn(['data'])
            ->once()->ordered();

        // for cv
        // for rt createPvUniondTempTables
        if (count($divCodes) > 0) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createRtSampleTempTable')
                ->with($isConditionCross, $conditionCross, $division, $divCodes, [1], 'code', 'number', $selectedPersonalName, false, $codeNumber)
                ->once()->ordered();
            // for rt_viewers
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();
            // for pv_unioned
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();
        }

        // for rt program_reports_pivot
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->andReturn(['data'])
            ->once()->ordered();

        // only RT extensionFlag is true
        if ($programListExtensionFlag && !$bsFlg && $csvFlag == '1') {
            // only extensionFlag is true
            // for cm_list
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->withArgs($this->bindAsserts($cmListBindings, 'searchOriginalRt - cmListBindings'))
                ->andReturn(['data'])
                ->once()->ordered();

            // only extensionFlag is true
            // for CREATE TEMPORARY TABLE cv_list
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();
            // for insertTemporaryTable
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('insertTemporaryTable')
                ->withArgs($this->bindAsserts($cvListBindings, 'searchOriginalRt - cvListBindings'))
                ->andReturn(true)
                ->once()->ordered();

            // for analyze
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();

            if (count($divCodes) > 0) {
                // for cv_union
                $this->target
                    ->shouldAllowMockingProtectedMethods()
                    ->shouldReceive('select')
                    ->with(Mockery::any())
                    ->andReturn(['data'])
                    ->once()->ordered();

                // for cv reprots pivot
                $this->target
                    ->shouldAllowMockingProtectedMethods()
                    ->shouldReceive('select')
                    ->with(Mockery::any())
                    ->andReturn(['data'])
                    ->once()->ordered();
            }

            // for cm_result
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();
        }

        // common
        // for result
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->andReturn(['data'])
            ->once()->ordered();

        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->with(Mockery::any())
            ->andReturn((object) ['cnt' => 111])
            ->once()->ordered();

        $actual = $this->target->searchOriginal($startDate, $endDate, $startTime, $endTime, $wdays, $holiday, $channels, $genres, $programNames, $division, $conditionCross, $codes, $order, $length, $regionId, $page, $straddlingFlg, $bsFlg, $csvFlag, $programListExtensionFlag, $dataTypeFlags, $dataType, $dataTypeConst, $prefixes, $selectedPersonalName, $codeNumber);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider searchOriginalDataProvider
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
     * @param mixed $dataTypeConst
     * @param mixed $prefixes
     * @param mixed $isConditionCross
     * @param mixed $expectBinds
     * @param mixed $createProgramListTempTableBindings
     * @param mixed $divCodes
     * @param mixed $cmListBindings
     * @param mixed $cvListBindings
     * @param mixed $selectedPersonalName
     * @param mixed $codeNumber
     */
    public function searchOriginalTs(
        $startDate,
        $endDate,
        $startTime,
        $endTime,
        $wdays,
        $holiday,
        $channels,
        $genres,
        $programNames,
        $division,
        $conditionCross,
        $codes,
        $order,
        $length,
        $regionId,
        $page,
        $straddlingFlg,
        $bsFlg,
        $csvFlag,
        $programListExtensionFlag,
        $dataTypeConst,
        $prefixes,
        $selectedPersonalName,
        $codeNumber,
        $isConditionCross,
        $expectBinds,
        $createProgramListTempTableBindings,
        $divCodes,
        $cmListBindings,
        $cvListBindings
    ): void {
        $expected = [
            'list' => ['data'],
            'cnt' => 111,
        ];
        $dataType = [2];
        $dataTypeFlags = ['rt' => false, 'ts' => true, 'gross' => false, 'total' => false, 'rtTotal' => false];

        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createProgramListWhere')
            ->with($startDate, $endDate, $channels, $genres, $programNames, $division, $conditionCross, $codes, $regionId, $bsFlg)
            ->andReturns([[1], ['100', '200'], [3]])
            ->once()->ordered();

        // common
        // for createProgramListTempTable
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($createProgramListTempTableBindings, 'searchOriginalts - createProgram'))
            ->andReturn(['data'])
            ->once()->ordered();

        // for cv
        // for rt createPvUniondTempTables
        if (count($divCodes) > 0) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createTsSampleTempTable')
                ->with($isConditionCross, $conditionCross, $division, $divCodes, [1], $regionId, 'code', 'number', $selectedPersonalName, false, $codeNumber)
                ->once()->ordered();
            // for ts_viewers
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();
            // for pv_unioned
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();
        }

        // for ts program_reports_pivot
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->andReturn(['data'])
            ->once()->ordered();

        // only RT extensionFlag is true
        if ($programListExtensionFlag && !$bsFlg && $csvFlag == '1') {
            // only extensionFlag is true
            // for cm_list
            if (count($divCodes) > 0) {
                // TODO - konno: 多分死にコード　timeshift なのに、 RtSample を作っている
                $this->target
                    ->shouldAllowMockingProtectedMethods()
                    ->shouldReceive('createRtSampleTempTable')
                    ->with($isConditionCross, $conditionCross, $division, $divCodes, [1], 'code', 'number', $selectedPersonalName, false, $codeNumber)
                    ->once()->ordered();
            }
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->withArgs($this->bindAsserts($cmListBindings, 'searchOriginalTs - cmListBindings'))
                ->andReturn(['data'])
                ->once()->ordered();

            // only extensionFlag is true
            // for CREATE TEMPORARY TABLE cv_list
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();
            // for insertTemporaryTable
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('insertTemporaryTable')
                ->withArgs($this->bindAsserts($cvListBindings, 'searchOriginalTs - cvListBindings'))
                ->andReturn(true)
                ->once()->ordered();

            // for analyze
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();

            if (count($divCodes) > 0) {
                // for cv_union
                $this->target
                    ->shouldAllowMockingProtectedMethods()
                    ->shouldReceive('select')
                    ->with(Mockery::any())
                    ->andReturn(['data'])
                    ->once()->ordered();
                // for cv reprots pivot
                $this->target
                    ->shouldAllowMockingProtectedMethods()
                    ->shouldReceive('select')
                    ->with(Mockery::any())
                    ->andReturn(['data'])
                    ->once()->ordered();
            }

            // for cm_result
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();
        }

        // common
        // for result
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->andReturn(['data'])
            ->once()->ordered();

        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->with(Mockery::any())
            ->andReturn((object) ['cnt' => 111])
            ->once()->ordered();

        $actual = $this->target->searchOriginal($startDate, $endDate, $startTime, $endTime, $wdays, $holiday, $channels, $genres, $programNames, $division, $conditionCross, $codes, $order, $length, $regionId, $page, $straddlingFlg, $bsFlg, $csvFlag, $programListExtensionFlag, $dataType, $dataTypeFlags, $dataTypeConst, $prefixes, $selectedPersonalName, $codeNumber);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider searchOriginalDataProvider
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
     * @param mixed $dataTypeConst
     * @param mixed $prefixes
     * @param mixed $isConditionCross
     * @param mixed $expectBinds
     * @param mixed $createProgramListTempTableBindings
     * @param mixed $divCodes
     * @param mixed $cmListBindings
     * @param mixed $cvListBindings
     * @param mixed $selectedPersonalName
     * @param mixed $codeNumber
     */
    public function searchOriginalGross(
        $startDate,
        $endDate,
        $startTime,
        $endTime,
        $wdays,
        $holiday,
        $channels,
        $genres,
        $programNames,
        $division,
        $conditionCross,
        $codes,
        $order,
        $length,
        $regionId,
        $page,
        $straddlingFlg,
        $bsFlg,
        $csvFlag,
        $programListExtensionFlag,
        $dataTypeConst,
        $prefixes,
        $selectedPersonalName,
        $codeNumber,
        $isConditionCross,
        $expectBinds,
        $createProgramListTempTableBindings,
        $divCodes,
        $cmListBindings,
        $cvListBindings
    ): void {
        $expected = [
            'list' => ['data'],
            'cnt' => 111,
        ];
        $dataType = [3];
        $dataTypeFlags = ['rt' => false, 'ts' => false, 'gross' => true, 'total' => false, 'rtTotal' => false];

        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createProgramListWhere')
            ->with($startDate, $endDate, $channels, $genres, $programNames, $division, $conditionCross, $codes, $regionId, $bsFlg)
            ->andReturns([[1], ['100', '200'], [3]])
            ->once()->ordered('');

        // common
        // for createProgramListTempTable
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($createProgramListTempTableBindings, 'searchOriginalts - createProgram'))
            ->andReturn(['data'])
            ->once()->ordered();

        // for cv
        // for rt createPvUniondTempTables
        if (count($divCodes) > 0) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createRtSampleTempTable')
                ->with($isConditionCross, $conditionCross, $division, $divCodes, [1], 'code', 'number', $selectedPersonalName, false, $codeNumber)
                ->once()->ordered();
            // for rt_viewers
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();
        }
        // for rt createPvUniondTempTables
        if (count($divCodes) > 0) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createTsSampleTempTable')
                ->with($isConditionCross, $conditionCross, $division, $divCodes, [1], $regionId, 'code', 'number', $selectedPersonalName, false, $codeNumber)
                ->once()->ordered();
            // for tv_viewers
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();
            // for gross_viewers
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();
            // for pv_unioned
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();
        }
        // for ts program_reports_pivot
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->andReturn(['data'])
            ->once()->ordered();

        // only RT extensionFlag is true
        if ($programListExtensionFlag && !$bsFlg && $csvFlag == '1') {
            // only extensionFlag is true
            // for cm_list
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->withArgs($this->bindAsserts($cmListBindings, 'searchOriginalTs - cmListBindings'))
                ->andReturn(['data'])
                ->once()->ordered();

            // only extensionFlag is true
            // for CREATE TEMPORARY TABLE cv_list
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();
            // for insertTemporaryTable
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('insertTemporaryTable')
                ->withArgs($this->bindAsserts($cvListBindings, 'searchOriginalTs - cvListBindings'))
                ->andReturn(true)
                ->once()->ordered();

            // for analyze
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();

            if (count($divCodes) > 0) {
                // for cv_union
                $this->target
                    ->shouldAllowMockingProtectedMethods()
                    ->shouldReceive('select')
                    ->with(Mockery::any())
                    ->andReturn(['data'])
                    ->once()->ordered();
                // for cv reprots pivot
                $this->target
                    ->shouldAllowMockingProtectedMethods()
                    ->shouldReceive('select')
                    ->with(Mockery::any())
                    ->andReturn(['data'])
                    ->once()->ordered();
            }

            // for cm_result
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();
        }

        // common
        // for result
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->andReturn(['data'])
            ->once()->ordered();

        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->with(Mockery::any())
            ->andReturn((object) ['cnt' => 111])
            ->once()->ordered();

        $actual = $this->target->searchOriginal($startDate, $endDate, $startTime, $endTime, $wdays, $holiday, $channels, $genres, $programNames, $division, $conditionCross, $codes, $order, $length, $regionId, $page, $straddlingFlg, $bsFlg, $csvFlag, $programListExtensionFlag, $dataType, $dataTypeFlags, $dataTypeConst, $prefixes, $selectedPersonalName, $codeNumber);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider searchOriginalDataProvider
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
     * @param mixed $dataTypeConst
     * @param mixed $prefixes
     * @param mixed $isConditionCross
     * @param mixed $expectBinds
     * @param mixed $createProgramListTempTableBindings
     * @param mixed $divCodes
     * @param mixed $cmListBindings
     * @param mixed $cvListBindings
     * @param mixed $selectedPersonalName
     * @param mixed $codeNumber
     */
    public function searchOriginalTotal(
        $startDate,
        $endDate,
        $startTime,
        $endTime,
        $wdays,
        $holiday,
        $channels,
        $genres,
        $programNames,
        $division,
        $conditionCross,
        $codes,
        $order,
        $length,
        $regionId,
        $page,
        $straddlingFlg,
        $bsFlg,
        $csvFlag,
        $programListExtensionFlag,
        $dataTypeConst,
        $prefixes,
        $selectedPersonalName,
        $codeNumber,
        $isConditionCross,
        $expectBinds,
        $createProgramListTempTableBindings,
        $divCodes,
        $cmListBindings,
        $cvListBindings
    ): void {
        $expected = [
            'list' => ['data'],
            'cnt' => 111,
        ];
        $dataType = [4];

        $dataTypeFlags = ['rt' => false, 'ts' => false, 'gross' => false, 'total' => true, 'rtTotal' => false];

        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createProgramListWhere')
            ->with($startDate, $endDate, $channels, $genres, $programNames, $division, $conditionCross, $codes, $regionId, $bsFlg)
            ->andReturns([[1], ['100', '200'], [3]])
            ->once()->ordered('');

        // common
        // for createProgramListTempTable
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($createProgramListTempTableBindings, 'searchOriginalts - createProgram'))
            ->andReturn(['data'])
            ->once()->ordered();

        // for cv
        if (count($divCodes) > 0) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createTsSampleTempTable')
                ->with($isConditionCross, $conditionCross, $division, $divCodes, [1], $regionId, 'code', 'number', $selectedPersonalName, false, $codeNumber)
                ->once()->ordered();
            // for tv_viewers
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();
        }

        // only RT extensionFlag is true
        if ($programListExtensionFlag && !$bsFlg && $csvFlag == '1') {
            // only extensionFlag is true
            if (count($divCodes) > 0) {
                // TODO - konno: 多分死にコード　timeshift なのに、 RtSample を作っている
                $this->target
                    ->shouldAllowMockingProtectedMethods()
                    ->shouldReceive('createRtSampleTempTable')
                    ->with($isConditionCross, $conditionCross, $division, $divCodes, [1], 'code', 'number', $selectedPersonalName, false, $codeNumber)
                    ->once()->ordered();
            }

            // for cm_list
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->withArgs($this->bindAsserts($cmListBindings, 'searchOriginalTs - cmListBindings'))
                ->andReturn(['data'])
                ->once()->ordered();

            // only extensionFlag is true
            // for CREATE TEMPORARY TABLE cv_list
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();
            // for insertTemporaryTable
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('insertTemporaryTable')
                ->withArgs($this->bindAsserts($cvListBindings, 'searchOriginalTs - cvListBindings'))
                ->andReturn(true)
                ->once()->ordered();

            // for analyze
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();

            if (count($divCodes) > 0) {
                // for cv_union
                $this->target
                    ->shouldAllowMockingProtectedMethods()
                    ->shouldReceive('select')
                    ->with(Mockery::any())
                    ->andReturn(['data'])
                    ->once()->ordered();
                // for cv reprots pivot
                $this->target
                    ->shouldAllowMockingProtectedMethods()
                    ->shouldReceive('select')
                    ->with(Mockery::any())
                    ->andReturn(['data'])
                    ->once()->ordered();
            }

            // for cm_result
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();
        }

        // common
        // for result
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->andReturn(['data'])
            ->once()->ordered();

        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->with(Mockery::any())
            ->andReturn((object) ['cnt' => 111])
            ->once()->ordered();

        $actual = $this->target->searchOriginal($startDate, $endDate, $startTime, $endTime, $wdays, $holiday, $channels, $genres, $programNames, $division, $conditionCross, $codes, $order, $length, $regionId, $page, $straddlingFlg, $bsFlg, $csvFlag, $programListExtensionFlag, $dataType, $dataTypeFlags, $dataTypeConst, $prefixes, $selectedPersonalName, $codeNumber);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider searchOriginalDataProvider
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
     * @param mixed $dataTypeConst
     * @param mixed $prefixes
     * @param mixed $isConditionCross
     * @param mixed $expectBinds
     * @param mixed $createProgramListTempTableBindings
     * @param mixed $divCodes
     * @param mixed $cmListBindings
     * @param mixed $cvListBindings
     * @param mixed $selectedPersonalName
     * @param mixed $codeNumber
     */
    public function searchOriginalRtTotal(
        $startDate,
        $endDate,
        $startTime,
        $endTime,
        $wdays,
        $holiday,
        $channels,
        $genres,
        $programNames,
        $division,
        $conditionCross,
        $codes,
        $order,
        $length,
        $regionId,
        $page,
        $straddlingFlg,
        $bsFlg,
        $csvFlag,
        $programListExtensionFlag,
        $dataTypeConst,
        $prefixes,
        $selectedPersonalName,
        $codeNumber,
        $isConditionCross,
        $expectBinds,
        $createProgramListTempTableBindings,
        $divCodes,
        $cmListBindings,
        $cvListBindings
    ): void {
        $expected = [
            'list' => ['data'],
            'cnt' => 111,
        ];
        $dataType = [5];
        $dataTypeFlags = ['rt' => false, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => true];

        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createProgramListWhere')
            ->with($startDate, $endDate, $channels, $genres, $programNames, $division, $conditionCross, $codes, $regionId, $bsFlg)
            ->andReturns([[1], ['100', '200'], [3]])
            ->once()->ordered('');

        // common
        // for createProgramListTempTable
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($createProgramListTempTableBindings, 'searchOriginalts - createProgram'))
            ->andReturn(['data'])
            ->once()->ordered();

        // for cv
        // for rt createPvUniondTempTables
        if (count($divCodes) > 0) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createRtSampleTempTable')
                ->with($isConditionCross, $conditionCross, $division, $divCodes, [1], 'code', 'number', $selectedPersonalName, false, $codeNumber)
                ->once()->ordered();
            // for rt_viewers
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();
        }
        // for rt createPvUniondTempTables
        if (count($divCodes) > 0) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createTsSampleTempTable')
                ->with($isConditionCross, $conditionCross, $division, $divCodes, [1], $regionId, 'code', 'number', $selectedPersonalName, false, $codeNumber)
                ->once()->ordered();
            // for tv_viewers
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();
            // for gross_viewers
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();
        }

        // only RT extensionFlag is true
        if ($programListExtensionFlag && !$bsFlg && $csvFlag == '1') {
            // only extensionFlag is true
            // for cm_list
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->withArgs($this->bindAsserts($cmListBindings, 'searchOriginalTs - cmListBindings'))
                ->andReturn(['data'])
                ->once()->ordered();

            // only extensionFlag is true
            // for CREATE TEMPORARY TABLE cv_list
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();
            // for insertTemporaryTable
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('insertTemporaryTable')
                ->withArgs($this->bindAsserts($cvListBindings, 'searchOriginalTs - cvListBindings'))
                ->andReturn(true)
                ->once()->ordered();

            // for analyze
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();

            if (count($divCodes) > 0) {
                // for cv_union
                $this->target
                    ->shouldAllowMockingProtectedMethods()
                    ->shouldReceive('select')
                    ->with(Mockery::any())
                    ->andReturn(['data'])
                    ->once()->ordered();
                // for cv reprots pivot
                $this->target
                    ->shouldAllowMockingProtectedMethods()
                    ->shouldReceive('select')
                    ->with(Mockery::any())
                    ->andReturn(['data'])
                    ->once()->ordered();
            }

            // for cm_result
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();
        }

        // common
        // for result
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->andReturn(['data'])
            ->once()->ordered();

        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->with(Mockery::any())
            ->andReturn((object) ['cnt' => 111])
            ->once()->ordered();

        $actual = $this->target->searchOriginal($startDate, $endDate, $startTime, $endTime, $wdays, $holiday, $channels, $genres, $programNames, $division, $conditionCross, $codes, $order, $length, $regionId, $page, $straddlingFlg, $bsFlg, $csvFlag, $programListExtensionFlag, $dataType, $dataTypeFlags, $dataTypeConst, $prefixes, $selectedPersonalName, $codeNumber);

        $this->assertEquals($expected, $actual);
    }

    public function searchOriginalDataProvider()
    {
        // テストマトリクス
        $cases = ['case1', 'case2', 'case3', 'case4', 'case5'];
        $startDate = ['19900614', '19900614', '19900614', '19900614', '19900614'];
        $endDate = ['19900614', '19900614', '19900614', '19900614', '19900614'];
        $startTime = ['', '000000', '100000', '050000', '050000'];
        $endTime = ['', '235959', '050000', '100000', '100000'];
        $wdays = [[0, 1, 2, 3, 4, 5, 6], [0, 1, 2, 3, 4, 5, 6], [0, 1, 2, 3, 4, 5, 6], [0, 1, 2, 3, 4, 5, 6], [0, 1, 2, 3, 4, 5, 6]];
        $holiday = [false, true, false, true, true];
        $channels = [[], [1, 2, 3, 4, 5, 6, 7, 8], [], [1, 2, 3, 4, 5, 6, 7, 8], [1, 2, 3, 4, 5, 6, 7, 8]];
        $genres = [[], [10, 11, 12, 13, 14], [], [10, 11, 12, 13, 14], [10, 11, 12, 13, 14]];
        $programNames = [[], ['progname'], [], ['progname'], ['progname']];
        $division = ['ga8', 'ga8', 'ga8', 'condition_cross', 'condition_cross'];
        $conditionCross = [[], [], [], [], []];
        $codes = [['personal', 'c1', 'household'], ['personal', 'c1', 'household'], ['personal', 'household'], [''], ['']];
        $order = [[['column' => 'date', 'dir' => 'desc'], ['column' => 'started_at', 'dir' => 'asc']], [], [['column' => 'date', 'dir' => 'desc'], ['column' => 'started_at', 'dir' => 'asc']], [], []];
        $length = [30, 30, 30, 30, 30];
        $regionId = [1, 2, 1, 2, 2];
        $page = [1, 2, 3, 4, 4];
        $straddlingFlg = [true, false, true, false, false];
        $bsFlg = [true, true, false, false, false];
        $csvFlag = ['0', '0', '1', '1', '1'];
        $programListExtensionFlag = [true, false, true, false, true];
        $dataTypeConst = [['rt' => 1, 'ts' => 2, 'total' => 4, 'gross' => 3, 'rtTotal' => 5], ['rt' => 1, 'ts' => 2, 'total' => 4, 'gross' => 3, 'rtTotal' => 5], ['rt' => 1, 'ts' => 2, 'total' => 4, 'gross' => 3, 'rtTotal' => 5], ['rt' => 1, 'ts' => 2, 'total' => 4, 'gross' => 3, 'rtTotal' => 5], ['rt' => 1, 'ts' => 2, 'total' => 4, 'gross' => 3, 'rtTotal' => 5]];
        $prefixes = [['code' => 'code', 'number' => 'number'], ['code' => 'code', 'number' => 'number'], ['code' => 'code', 'number' => 'number'], ['code' => 'code', 'number' => 'number'], ['code' => 'code', 'number' => 'number']];
        $selectedPersonalName = ['selected_personal', 'selected_personal', 'selected_personal', 'selected_personal', 'selected_personal'];
        $codeNumber = [32, 32, 32, 32, 32];
        $isConditionCross = [false, false, false, true, true];
        // expects
        $expectBinds = [[], [], [], []];
        $createProgramListTempTableBindings = [
            [// case 1
                ':startDate' => '19900614',
                ':endDate' => '19900614',
                ':regionId' => 1,
                ':wdays0' => 0,
                ':wdays1' => 1,
                ':wdays2' => 2,
                ':wdays3' => 3,
                ':wdays4' => 4,
                ':wdays5' => 5,
                ':wdays6' => 6,
                ':startTime' => '',
                ':endTime' => '',
            ],
            [// case 2
                ':startDate' => '19900614',
                ':endDate' => '19900614',
                ':regionId' => 2,
                ':genres0' => 10,
                ':genres1' => 11,
                ':genres2' => 12,
                ':genres3' => 13,
                ':genres4' => 14,
                ':channels0' => 1,
                ':channels1' => 2,
                ':channels2' => 3,
                ':channels3' => 4,
                ':channels4' => 5,
                ':channels5' => 6,
                ':channels6' => 7,
                ':channels7' => 8,
                ':wdays0' => 0,
                ':wdays1' => 1,
                ':wdays2' => 2,
                ':wdays3' => 3,
                ':wdays4' => 4,
                ':wdays5' => 5,
                ':wdays6' => 6,
                ':programNames0' => 'progname',
                ':startTime' => '000000',
                ':endTime' => '235959',
            ],
            [// case 3
                ':startDate' => '19900614',
                ':endDate' => '19900614',
                ':regionId' => 1,
                ':wdays0' => 0,
                ':wdays1' => 1,
                ':wdays2' => 2,
                ':wdays3' => 3,
                ':wdays4' => 4,
                ':wdays5' => 5,
                ':wdays6' => 6,
                ':startTime' => '100000',
                ':endTime' => '050000',
            ],
            [ // case 4
                ':startDate' => '19900614',
                ':endDate' => '19900614',
                ':regionId' => 2,
                ':genres0' => 10,
                ':genres1' => 11,
                ':genres2' => 12,
                ':genres3' => 13,
                ':genres4' => 14,
                ':channels0' => 1,
                ':channels1' => 2,
                ':channels2' => 3,
                ':channels3' => 4,
                ':channels4' => 5,
                ':channels5' => 6,
                ':channels6' => 7,
                ':channels7' => 8,
                ':wdays0' => 0,
                ':wdays1' => 1,
                ':wdays2' => 2,
                ':wdays3' => 3,
                ':wdays4' => 4,
                ':wdays5' => 5,
                ':wdays6' => 6,
                ':programNames0' => 'progname',
                ':startTime' => '050000',
                ':endTime' => '100000',
            ],
            [ // case 5
                ':startDate' => '19900614',
                ':endDate' => '19900614',
                ':regionId' => 2,
                ':genres0' => 10,
                ':genres1' => 11,
                ':genres2' => 12,
                ':genres3' => 13,
                ':genres4' => 14,
                ':channels0' => 1,
                ':channels1' => 2,
                ':channels2' => 3,
                ':channels3' => 4,
                ':channels4' => 5,
                ':channels5' => 6,
                ':channels6' => 7,
                ':channels7' => 8,
                ':wdays0' => 0,
                ':wdays1' => 1,
                ':wdays2' => 2,
                ':wdays3' => 3,
                ':wdays4' => 4,
                ':wdays5' => 5,
                ':wdays6' => 6,
                ':programNames0' => 'progname',
                ':startTime' => '050000',
                ':endTime' => '100000',
            ],
        ];
        $divCodes = [['c1'], ['c1'], [], ['condition_cross'], ['condition_cross']];
        $cmListBindings = [
            [],
            [],
            [
                ':startDate' => '19900614',
                ':endDate' => '19900614',
                ':progStartDate' => '199006130000000000',
                ':progEndDate' => '199006159999999999',
            ],
            [],
            [
                ':startDate' => '19900614',
                ':endDate' => '19900614',
                ':progStartDate' => '199006130000000000',
                ':progEndDate' => '199006159999999999',
            ],
        ];

        $cvListBindings = [
            [],
            [],
            [
                ':startDate' => '19900614',
                ':endDate' => '19900614',
                ':progStartDate' => '199006130000000000',
                ':progEndDate' => '199006159999999999',
                ':regionId' => 1,
            ],
            [],
            [
                ':startDate' => '19900614',
                ':endDate' => '19900614',
                ':progStartDate' => '199006130000000000',
                ':progEndDate' => '199006159999999999',
                ':regionId' => 2,
            ],
        ];

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
                $dataTypeConst[$i],
                $prefixes[$i],
                $selectedPersonalName[$i],
                $codeNumber[$i],
                $isConditionCross[$i],
                $expectBinds[$i],
                $createProgramListTempTableBindings[$i],
                $divCodes[$i],
                $cmListBindings[$i],
                $cvListBindings[$i],
            ];
        }
    }

    /**
     * @test
     * @dataProvider averageDataProvider
     * @param mixed $averageType
     * @param mixed $progIds
     * @param mixed $timeBoxIds
     * @param mixed $division
     * @param mixed $conditionCross
     * @param mixed $codes
     * @param mixed $bsFlg
     * @param mixed $regionId
     * @param mixed $dataTypeFlags
     * @param mixed $dataType
     * @param mixed $dataTypeConst
     * @param mixed $expectBinds
     * @param mixed $modeTimeBindings
     * @param mixed $expected
     * @param mixed $resultListAverage
     * @param mixed $modeAndTime
     */
    public function averageTest($averageType, $progIds, $timeBoxIds, $division, $conditionCross, $codes, $bsFlg, $regionId, $dataTypeFlags, $dataType, $dataTypeConst, $expectBinds, $modeTimeBindings, $expected, $resultListAverage, $modeAndTime): void
    {
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($expectBinds, 'expectBindings'))
            ->andReturn($resultListAverage)
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($modeTimeBindings, 'modeBindings'))
            ->andReturn($modeAndTime['mode'])
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($modeTimeBindings, 'timeBindings'))
            ->andReturn($modeAndTime['time'])
            ->once()->ordered();

        $actual = $this->target->average($averageType, $progIds, $timeBoxIds, $division, $conditionCross, $codes, $bsFlg, $regionId, $dataTypeFlags, $dataType, $dataTypeConst);

        $this->assertEquals($expected, $actual);
    }

    public function averageDataProvider()
    {
        // テストマトリクス
        $cases = ['case1', 'case2', 'case3', 'case4'];
        $averageType = ['weight', 'simple', 'weight', 'simple'];
        $progIds = [[10], [10], [10], [10]];
        $timeBoxIds = [[20], [20], [20], [20], 20];
        $division = ['ga8', 'ga8', 'ga8', 'ga8'];
        $conditionCross = [[], [], [], []];
        $codes = [['personal', 'c1', 'household'], ['personal', 'c1', 'household'], ['personal', 'household'], ['c1']];
        $bsFlg = [true, true, false, false];
        $regionId = [1, 2, 1, 2];
        $dataType = [[1, 2, 3, 4, 5], [1, 2, 3, 4, 5], [1, 2, 3, 4, 5], [1, 2, 3, 4, 5]];
        $dataTypeFlags = [['rt' => true, 'ts' => true, 'gross' => true, 'total' => true, 'rtTotal' => true], ['rt' => true, 'ts' => true, 'gross' => true, 'total' => true, 'rtTotal' => true], ['rt' => true, 'ts' => true, 'gross' => true, 'total' => true, 'rtTotal' => true], ['rt' => true, 'ts' => true, 'gross' => true, 'total' => true, 'rtTotal' => true]];
        $dataTypeConst = [['rt' => 1, 'ts' => 2, 'total' => 4, 'gross' => 3, 'rtTotal' => 5], ['rt' => 1, 'ts' => 2, 'total' => 4, 'gross' => 3, 'rtTotal' => 5], ['rt' => 1, 'ts' => 2, 'total' => 4, 'gross' => 3, 'rtTotal' => 5], ['rt' => 1, 'ts' => 2, 'total' => 4, 'gross' => 3, 'rtTotal' => 5]];
        // expects
        $expectBinds = [
            [
                ':ga8_1' => 'c1',
                ':digit' => 2,
                ':regionId' => 1,
                ':progId0' => 10,
                ':timeBoxId1' => 20,
                ':division' => 'ga8',
                ':timeBoxIds0' => 20,
            ],
            [
                ':ga8_1' => 'c1',
                ':digit' => 2,
                ':regionId' => 2,
                ':progId0' => 10,
                ':timeBoxId1' => 20,
                ':division' => 'ga8',
                ':timeBoxIds0' => 20,
            ],
            [
                ':digit' => 1,
                ':regionId' => 1,
                ':progId0' => 10,
                ':timeBoxId1' => 20,
            ],
            [
                ':ga8_0' => 'c1',
                ':digit' => 1,
                ':regionId' => 2,
                ':progId0' => 10,
                ':timeBoxId1' => 20,
                ':division' => 'ga8',
                ':timeBoxIds0' => 20,
            ],
        ];
        $modeTimeBindings = [
            [
                ':regionId' => 1,
                ':progId0' => 10,
                ':timeBoxId1' => 20,
            ],
            [
                ':regionId' => 2,
                ':progId0' => 10,
                ':timeBoxId1' => 20,
            ],
            [
                ':regionId' => 1,
                ':progId0' => 10,
                ':timeBoxId1' => 20,
            ],
            [
                ':regionId' => 2,
                ':progId0' => 10,
                ':timeBoxId1' => 20,
            ],
        ];
        $expected =
            [
                ['11:55:00', '13:44:59', '水', 'CX', 'バイキング など', 2, '110', '135', '1.8', null, '3.9', null, null, null, null, null, null, null, null, null],
                ['11:55:00', '13:44:59', '水', 'CX', 'バイキング など', 2, '110', '135', '1.8', '1', '3.9', '11.8', '11', '13.9', '31.8', '31', '33.9', '21.8', '21', '23.9'],
                ['11:55:00', '13:44:59', '水木', 'CX', 'バイキング', 2, '110', '135', '1.8', '3.9', null, null, null, null, null, null],
                ['11:55:00', '13:44:59', '水', 'CX', 'バイキング など', 2, '110', '135', '9', null, null, null],
            ];
        $resultListAverage =
            [
                [(object) ['rt_personal' => '1.8', 'rt_household' => '3.9', 'cnt' => 2, 'total_minute' => '135']],
                [(object) ['rt_personal' => '1.8', 'rt_ga8_1' => '1', 'rt_household' => '3.9', 'ts_personal' => '11.8', 'ts_ga8_1' => '11', 'ts_household' => '13.9', 'gross_personal' => '21.8', 'gross_ga8_1' => '21', 'gross_household' => '23.9', 'total_personal' => '31.8', 'total_ga8_1' => '31', 'total_household' => '33.9', 'cnt' => 2, 'total_minute' => '135']],
                [(object) ['rt_personal' => '1.8', 'rt_household' => '3.9', 'cnt' => 2, 'total_minute' => '135']],
                [(object) ['rt_ga8_0' => '9', 'cnt' => 2, 'total_minute' => '135']],
            ];
        $modeAndTime = [
            [ // case 1
                'mode' => [
                    (object) ['title' => 'バイキング', 'channel_code' => 'CX', 'cnt' => 1, 'dow' => '3'],
                    (object) ['title' => 'ＦＮＮＬｉｖｅＮｅｗｓｄａｙｓ', 'channel_code' => 'CX', 'cnt' => 1, 'dow' => '3'],
                ],
                'time' => [
                    (object) ['title' => 'ＦＮＮＬｉｖｅＮｅｗｓｄａｙｓ', 'channel_code' => 'CX', 'start_time' => '11:30:00', 'end_time' => '11:54:59', 'cnt' => 1, 'minute' => '25'],
                    (object) ['title' => 'バイキング', 'channel_code' => 'CX', 'start_time' => '11:55:00', 'end_time' => '13:44:59', 'cnt' => 1, 'minute' => '110'],
                ],
            ],
            [ // case 2
                'mode' => [
                    (object) ['title' => 'バイキング', 'channel_code' => 'CX', 'cnt' => 1, 'dow' => '3'],
                    (object) ['title' => 'ＦＮＮＬｉｖｅＮｅｗｓｄａｙｓ', 'channel_code' => 'CX', 'cnt' => 1, 'dow' => '3'],
                ],
                'time' => [
                    (object) ['title' => 'ＦＮＮＬｉｖｅＮｅｗｓｄａｙｓ', 'channel_code' => 'CX', 'start_time' => '11:30:00', 'end_time' => '11:54:59', 'cnt' => 1, 'minute' => '25'],
                    (object) ['title' => 'バイキング', 'channel_code' => 'CX', 'start_time' => '11:55:00', 'end_time' => '13:44:59', 'cnt' => 1, 'minute' => '110'],
                ],
            ],
            [ // case 3
                'mode' => [
                    (object) ['title' => 'バイキング', 'channel_code' => 'CX', 'cnt' => 1, 'dow' => '34'],
                ],
                'time' => [
                    (object) ['title' => 'バイキング', 'channel_code' => 'CX', 'start_time' => '11:55:00', 'end_time' => '13:44:59', 'cnt' => 1, 'minute' => '110'],
                    (object) ['title' => 'バイキング', 'channel_code' => 'CX', 'start_time' => '11:30:00', 'end_time' => '11:54:59', 'cnt' => 1, 'minute' => '25'],
                ],
            ],
            [ // case 4
                'mode' => [
                    (object) ['title' => 'バイキング', 'channel_code' => 'CX', 'cnt' => 1, 'dow' => '3'],
                    (object) ['title' => 'ＦＮＮＬｉｖｅＮｅｗｓｄａｙｓ', 'channel_code' => 'CX', 'cnt' => 1, 'dow' => '3'],
                ],
                'time' => [
                    (object) ['title' => 'ＦＮＮＬｉｖｅＮｅｗｓｄａｙｓ', 'channel_code' => 'CX', 'start_time' => '11:30:00', 'end_time' => '11:54:59', 'cnt' => 1, 'minute' => '25'],
                    (object) ['title' => 'バイキング', 'channel_code' => 'CX', 'start_time' => '11:55:00', 'end_time' => '13:44:59', 'cnt' => 1, 'minute' => '110'],
                ],
            ],
        ];

        foreach ($cases as $i => $val) {
            yield
            $val => [
                $averageType[$i],
                $progIds[$i],
                $timeBoxIds[$i],
                $division[$i],
                $conditionCross[$i],
                $codes[$i],
                $bsFlg[$i],
                $regionId[$i],
                $dataType[$i],
                $dataTypeFlags[$i],
                $dataTypeConst[$i],
                $expectBinds[$i],
                $modeTimeBindings[$i],
                $expected[$i],
                $resultListAverage[$i],
                $modeAndTime[$i],
            ];
        }
    }

    /**
     * @test
     * @dataProvider averageOriginalDataProvider
     * @param mixed $averageType
     * @param mixed $progIds
     * @param mixed $timeBoxIds
     * @param mixed $division
     * @param mixed $conditionCross
     * @param mixed $codes
     * @param mixed $divCodes
     * @param mixed $bsFlg
     * @param mixed $regionId
     * @param mixed $dataTypeConst
     * @param mixed $prefixes
     * @param mixed $programListBinds
     * @param mixed $isConditionCross
     * @param mixed $sampleBinds
     * @param mixed $pivotBinds
     * @param mixed $expectBinds
     * @param mixed $modeTimeBindings
     * @param mixed $expected
     * @param mixed $resultListAverage
     * @param mixed $resultListAverageTs
     * @param mixed $resultListAverageGross
     * @param mixed $resultListAverageTotal
     * @param mixed $modeAndTime
     * @param mixed $selectedPersonalName
     * @param mixed $codeNumber
     */
    public function periodAverageOriginalRtTest($averageType, $progIds, $timeBoxIds, $division, $conditionCross, $codes, $divCodes, $bsFlg, $regionId, $dataTypeConst, $prefixes, $selectedPersonalName, $codeNumber, $programListBinds, $isConditionCross, $sampleBinds, $pivotBinds, $expectBinds, $modeTimeBindings, $expected, $resultListAverage, $resultListAverageTs, $resultListAverageGross, $resultListAverageTotal, $modeAndTime): void
    {
        $dataType = [1];
        $dataTypeFlags = ['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => false];
        // for programlist
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($programListBinds, 'for programList'))
            ->andReturn(['data'])
            ->once()->ordered();

        if (count($divCodes) > 0) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createRtSampleTempTable')
                ->with($isConditionCross, $conditionCross, $division, $divCodes, $timeBoxIds, 'code', 'number', $selectedPersonalName, false, $codeNumber)
                ->andReturn(['data'])
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->withArgs($this->bindAsserts($sampleBinds, 'for sample'))
                ->andReturn(['data'])
                ->once()->ordered();

            // for pvunioned
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();
        }

        // for pivots
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($pivotBinds, 'pivots'))
            ->andReturn(['data'])
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($expectBinds, 'expectBindings'))
            ->andReturn($resultListAverage)
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($modeTimeBindings, 'modeBindings'))
            ->andReturn($modeAndTime['mode'])
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($modeTimeBindings, 'timeBindings'))
            ->andReturn($modeAndTime['time'])
            ->once()->ordered();

        $actual = $this->target->averageOriginal($averageType, $progIds, $timeBoxIds, $division, $conditionCross, $codes, $bsFlg, $regionId, $dataTypeFlags, $dataType, $dataTypeConst, $prefixes, $selectedPersonalName, $codeNumber);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider averageOriginalDataProvider
     * @param mixed $averageType
     * @param mixed $progIds
     * @param mixed $timeBoxIds
     * @param mixed $division
     * @param mixed $conditionCross
     * @param mixed $codes
     * @param mixed $divCodes
     * @param mixed $bsFlg
     * @param mixed $regionId
     * @param mixed $dataTypeConst
     * @param mixed $prefixes
     * @param mixed $programListBinds
     * @param mixed $isConditionCross
     * @param mixed $sampleBinds
     * @param mixed $pivotBinds
     * @param mixed $expectBinds
     * @param mixed $modeTimeBindings
     * @param mixed $expected
     * @param mixed $resultListAverage
     * @param mixed $resultListAverageTs
     * @param mixed $resultListAverageGross
     * @param mixed $resultListAverageTotal
     * @param mixed $modeAndTime
     * @param mixed $selectedPersonalName
     * @param mixed $codeNumber
     */
    public function periodAverageOriginalTsTest($averageType, $progIds, $timeBoxIds, $division, $conditionCross, $codes, $divCodes, $bsFlg, $regionId, $dataTypeConst, $prefixes, $selectedPersonalName, $codeNumber, $programListBinds, $isConditionCross, $sampleBinds, $pivotBinds, $expectBinds, $modeTimeBindings, $expected, $resultListAverage, $resultListAverageTs, $resultListAverageGross, $resultListAverageTotal, $modeAndTime): void
    {
        $dataType = [2];
        $dataTypeFlags = ['rt' => false, 'ts' => true, 'gross' => false, 'total' => false, 'rtTotal' => false];
        // for programlist

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($programListBinds, 'for programList'))
            ->andReturn(['data'])
            ->once()->ordered();

        if (count($divCodes) > 0) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createTsSampleTempTable')
                ->with($isConditionCross, $conditionCross, $division, $divCodes, $timeBoxIds, $regionId, 'code', 'number', $selectedPersonalName, false, $codeNumber)
                ->andReturn(['data'])
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->withArgs($this->bindAsserts($sampleBinds, 'for sample'))
                ->andReturn(['data'])
                ->once()->ordered();

            // for pvunioned
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();
        }

        // for pivots
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($pivotBinds, 'pivots'))
            ->andReturn(['data'])
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($expectBinds, 'expectBindings'))
            ->andReturn($resultListAverageTs)
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($modeTimeBindings, 'modeBindings'))
            ->andReturn($modeAndTime['mode'])
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($modeTimeBindings, 'timeBindings'))
            ->andReturn($modeAndTime['time'])
            ->once()->ordered();

        $actual = $this->target->averageOriginal($averageType, $progIds, $timeBoxIds, $division, $conditionCross, $codes, $bsFlg, $regionId, $dataTypeFlags, $dataType, $dataTypeConst, $prefixes, $selectedPersonalName, $codeNumber);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider averageOriginalDataProvider
     * @param mixed $averageType
     * @param mixed $progIds
     * @param mixed $timeBoxIds
     * @param mixed $division
     * @param mixed $conditionCross
     * @param mixed $codes
     * @param mixed $divCodes
     * @param mixed $bsFlg
     * @param mixed $regionId
     * @param mixed $dataTypeConst
     * @param mixed $prefixes
     * @param mixed $programListBinds
     * @param mixed $isConditionCross
     * @param mixed $sampleBinds
     * @param mixed $pivotBinds
     * @param mixed $expectBinds
     * @param mixed $modeTimeBindings
     * @param mixed $expected
     * @param mixed $resultListAverage
     * @param mixed $resultListAverageTs
     * @param mixed $resultListAverageGross
     * @param mixed $resultListAverageTotal
     * @param mixed $modeAndTime
     * @param mixed $selectedPersonalName
     * @param mixed $codeNumber
     */
    public function periodAverageOriginalGrossTest($averageType, $progIds, $timeBoxIds, $division, $conditionCross, $codes, $divCodes, $bsFlg, $regionId, $dataTypeConst, $prefixes, $selectedPersonalName, $codeNumber, $programListBinds, $isConditionCross, $sampleBinds, $pivotBinds, $expectBinds, $modeTimeBindings, $expected, $resultListAverage, $resultListAverageTs, $resultListAverageGross, $resultListAverageTotal, $modeAndTime): void
    {
        $dataType = [3];
        $dataTypeFlags = ['rt' => false, 'ts' => false, 'gross' => true, 'total' => false, 'rtTotal' => false];
        // for programlist

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($programListBinds, 'for programList'))
            ->andReturn(['data'])
            ->once()->ordered();

        if (count($divCodes) > 0) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createRtSampleTempTable')
                ->with($isConditionCross, $conditionCross, $division, $divCodes, $timeBoxIds, 'code', 'number', $selectedPersonalName, false, $codeNumber)
                ->andReturn(['data'])
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->withArgs($this->bindAsserts($sampleBinds, 'for sample'))
                ->andReturn(['data'])
                ->once()->ordered();
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createTsSampleTempTable')
                ->with($isConditionCross, $conditionCross, $division, $divCodes, $timeBoxIds, $regionId, 'code', 'number', $selectedPersonalName, false, $codeNumber)
                ->andReturn(['data'])
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->withArgs($this->bindAsserts($sampleBinds, 'for sample'))
                ->andReturn(['data'])
                ->once()->ordered();

            // for viewers
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();

            // for pvunioned
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();
        }

        // for pivots
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($pivotBinds, 'pivots'))
            ->andReturn(['data'])
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($expectBinds, 'expectBindings'))
            ->andReturn($resultListAverageGross)
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($modeTimeBindings, 'modeBindings'))
            ->andReturn($modeAndTime['mode'])
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($modeTimeBindings, 'timeBindings'))
            ->andReturn($modeAndTime['time'])
            ->once()->ordered();

        $actual = $this->target->averageOriginal($averageType, $progIds, $timeBoxIds, $division, $conditionCross, $codes, $bsFlg, $regionId, $dataTypeFlags, $dataType, $dataTypeConst, $prefixes, $selectedPersonalName, $codeNumber);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider averageOriginalDataProvider
     * @param mixed $averageType
     * @param mixed $progIds
     * @param mixed $timeBoxIds
     * @param mixed $division
     * @param mixed $conditionCross
     * @param mixed $codes
     * @param mixed $divCodes
     * @param mixed $bsFlg
     * @param mixed $regionId
     * @param mixed $dataTypeConst
     * @param mixed $prefixes
     * @param mixed $programListBinds
     * @param mixed $isConditionCross
     * @param mixed $sampleBinds
     * @param mixed $pivotBinds
     * @param mixed $expectBinds
     * @param mixed $modeTimeBindings
     * @param mixed $expected
     * @param mixed $resultListAverage
     * @param mixed $resultListAverageTs
     * @param mixed $resultListAverageGross
     * @param mixed $resultListAverageTotal
     * @param mixed $modeAndTime
     * @param mixed $selectedPersonalName
     * @param mixed $codeNumber
     */
    public function periodAverageOriginalTotalTest($averageType, $progIds, $timeBoxIds, $division, $conditionCross, $codes, $divCodes, $bsFlg, $regionId, $dataTypeConst, $prefixes, $selectedPersonalName, $codeNumber, $programListBinds, $isConditionCross, $sampleBinds, $pivotBinds, $expectBinds, $modeTimeBindings, $expected, $resultListAverage, $resultListAverageTs, $resultListAverageGross, $resultListAverageTotal, $modeAndTime): void
    {
        $dataType = [4];
        $dataTypeFlags = ['rt' => false, 'ts' => false, 'gross' => false, 'total' => true, 'rtTotal' => false];
        // for programlist

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($programListBinds, 'for programList'))
            ->andReturn(['data'])
            ->once()->ordered();

        if (count($divCodes) > 0) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createTsSampleTempTable')
                ->with($isConditionCross, $conditionCross, $division, $divCodes, $timeBoxIds, $regionId, 'code', 'number', $selectedPersonalName, false, $codeNumber)
                ->andReturn(['data'])
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->withArgs($this->bindAsserts($sampleBinds, 'for sample'))
                ->andReturn(['data'])
                ->once()->ordered();

            // for viewers
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();

            // for pvunioned
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn(['data'])
                ->once()->ordered();
        }

        // for pivots
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($pivotBinds, 'pivots'))
            ->andReturn(['data'])
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($expectBinds, 'expectBindings'))
            ->andReturn($resultListAverageTotal)
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($modeTimeBindings, 'modeBindings'))
            ->andReturn($modeAndTime['mode'])
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($modeTimeBindings, 'timeBindings'))
            ->andReturn($modeAndTime['time'])
            ->once()->ordered();

        $actual = $this->target->averageOriginal($averageType, $progIds, $timeBoxIds, $division, $conditionCross, $codes, $bsFlg, $regionId, $dataTypeFlags, $dataType, $dataTypeConst, $prefixes, $selectedPersonalName, $codeNumber);

        $this->assertEquals($expected, $actual);
    }

    public function averageOriginalDataProvider()
    {
        // テストマトリクス
        $cases = ['case1', 'case2', 'case3', 'case4', 'case5'];
        $averageType = ['weight', 'simple', 'simple', 'simple', 'weight'];
        $progIds = [[10], [10], [10], [10], [10]];
        $timeBoxIds = [[20], [20], [20], [20], [20]];
        $division = ['ga8', 'ga8', 'ga8', 'condition_cross', 'condition_cross'];
        $conditionCross = [[], [], [], [], []];
        $codes = [['personal', 'c1', 'household'], ['c1'], ['personal', 'household'], [], []];
        $divCodes = [['c1'], ['c1'], [], ['condition_cross'], ['condition_cross']];
        $bsFlg = [true, true, false, false, false];
        $regionId = [1, 2, 1, 2, 2];
        $dataTypeConst = [['rt' => 1, 'ts' => 2, 'total' => 4, 'gross' => 3, 'rtTotal' => 5], ['rt' => 1, 'ts' => 2, 'total' => 4, 'gross' => 3, 'rtTotal' => 5], ['rt' => 1, 'ts' => 2, 'total' => 4, 'gross' => 3, 'rtTotal' => 5], ['rt' => 1, 'ts' => 2, 'total' => 4, 'gross' => 3, 'rtTotal' => 5], ['rt' => 1, 'ts' => 2, 'total' => 4, 'gross' => 3, 'rtTotal' => 5]];
        $prefixes = [['code' => 'code', 'number' => 'number'], ['code' => 'code', 'number' => 'number'], ['code' => 'code', 'number' => 'number'], ['code' => 'code', 'number' => 'number'], ['code' => 'code', 'number' => 'number']];
        $selectedPersonalName = ['selected_personal', 'selected_personal', 'selected_personal', 'selected_personal', 'selected_personal'];
        $codeNumber = [32, 32, 32, 32, 32];
        // expects
        $programListBinds = [
            [
                'regionId' => 1,
                ':progId0' => '10',
                ':timeBoxId1' => 20,
            ],
            [
                'regionId' => 2,
                ':progId0' => '10',
                ':timeBoxId1' => 20,
            ],
            [
                'regionId' => 1,
                ':progId0' => '10',
                ':timeBoxId1' => 20,
            ],
            [
                'regionId' => 2,
                ':progId0' => '10',
                ':timeBoxId1' => 20,
            ], [
                'regionId' => 2,
                ':progId0' => '10',
                ':timeBoxId1' => 20,
            ],
        ];
        $isConditionCross = [false, false, false, true, true];
        $sampleBinds = [
            [
                ':progId0' => '10',
                ':timeBoxId1' => 20,
            ],
            [
                ':progId0' => '10',
                ':timeBoxId1' => 20,
            ],
            [],
            [
                ':progId0' => '10',
                ':timeBoxId1' => 20,
            ],
            [
                ':progId0' => '10',
                ':timeBoxId1' => 20,
            ],
            [
                ':progId0' => '10',
                ':timeBoxId1' => 20,
            ],
        ];
        $pivotBinds = [[], [], [], [], []];
        $expectBinds = [
            [
                ':digit' => 2,
            ],
            [
                ':digit' => 2,
            ],
            [
                ':digit' => 1,
            ],
            [
                ':digit' => 1,
            ],
            [
                ':digit' => 1,
            ],
        ];
        $modeTimeBindings = [
            [
                ':regionId' => 1,
                ':progId0' => 10,
                ':timeBoxId1' => 20,
            ],
            [
                ':regionId' => 2,
                ':progId0' => 10,
                ':timeBoxId1' => 20,
            ],
            [
                ':regionId' => 1,
                ':progId0' => 10,
                ':timeBoxId1' => 20,
            ],
            [
                ':regionId' => 2,
                ':progId0' => 10,
                ':timeBoxId1' => 20,
            ],
            [
                ':regionId' => 2,
                ':progId0' => 10,
                ':timeBoxId1' => 20,
            ],
        ];
        $expected =
            [
                ['11:55:00', '13:44:59', '水', 'CX', 'バイキング など', 2, '110', '135', '1.8', null, '3.9'],
                ['11:55:00', '13:44:59', '水', 'CX', 'バイキング など', 2, '110', '135', '1'],
                ['11:55:00', '13:44:59', '水木', 'CX', 'バイキング', 2, '110', '135', '1.8', '3.9'],
                ['11:55:00', '13:44:59', '水', 'CX', 'バイキング など', 2, '110', '135', '9', null],
                ['11:55:00', '13:44:59', '水', 'CX', 'バイキング など', 2, '110', '135', '9', null],
            ];
        $resultListAverage =
            [
                [(object) ['rt_personal' => '1.8', 'rt_household' => '3.9', 'cnt' => 2, 'total_minute' => '135']],
                [(object) ['rt_ga8_c1' => '1', 'cnt' => 2, 'total_minute' => '135']],
                [(object) ['rt_personal' => '1.8', 'rt_household' => '3.9', 'cnt' => 2, 'total_minute' => '135']],
                [(object) ['rt_condition_cross' => '9', 'cnt' => 2, 'total_minute' => '135']],
                [(object) ['rt_condition_cross' => '9', 'cnt' => 2, 'total_minute' => '135']],
            ];
        $resultListAverageTs =
            [
                [(object) ['ts_personal' => '1.8', 'ts_household' => '3.9', 'cnt' => 2, 'total_minute' => '135']],
                [(object) ['ts_ga8_c1' => '1', 'cnt' => 2, 'total_minute' => '135']],
                [(object) ['ts_personal' => '1.8', 'ts_household' => '3.9', 'cnt' => 2, 'total_minute' => '135']],
                [(object) ['ts_condition_cross' => '9', 'cnt' => 2, 'total_minute' => '135']],
                [(object) ['ts_condition_cross' => '9', 'cnt' => 2, 'total_minute' => '135']],
            ];
        $resultListAverageGross =
            [
                [(object) ['gross_personal' => '1.8', 'gross_household' => '3.9', 'cnt' => 2, 'total_minute' => '135']],
                [(object) ['gross_ga8_c1' => '1', 'cnt' => 2, 'total_minute' => '135']],
                [(object) ['gross_personal' => '1.8', 'gross_household' => '3.9', 'cnt' => 2, 'total_minute' => '135']],
                [(object) ['gross_condition_cross' => '9', 'cnt' => 2, 'total_minute' => '135']],
                [(object) ['gross_condition_cross' => '9', 'cnt' => 2, 'total_minute' => '135']],
            ];
        $resultListAverageTotal =
            [
                [(object) ['total_personal' => '1.8', 'total_household' => '3.9', 'cnt' => 2, 'total_minute' => '135']],
                [(object) ['total_ga8_c1' => '1', 'cnt' => 2, 'total_minute' => '135']],
                [(object) ['total_personal' => '1.8', 'total_household' => '3.9', 'cnt' => 2, 'total_minute' => '135']],
                [(object) ['total_condition_cross' => '9', 'cnt' => 2, 'total_minute' => '135']],
                [(object) ['total_condition_cross' => '9', 'cnt' => 2, 'total_minute' => '135']],
            ];
        $modeAndTime = [
            [ // case 1
                'mode' => [
                    (object) ['title' => 'バイキング', 'channel_code' => 'CX', 'cnt' => 1, 'dow' => '3'],
                    (object) ['title' => 'ＦＮＮＬｉｖｅＮｅｗｓｄａｙｓ', 'channel_code' => 'CX', 'cnt' => 1, 'dow' => '3'],
                ],
                'time' => [
                    (object) ['title' => 'ＦＮＮＬｉｖｅＮｅｗｓｄａｙｓ', 'channel_code' => 'CX', 'start_time' => '11:30:00', 'end_time' => '11:54:59', 'cnt' => 1, 'minute' => '25'],
                    (object) ['title' => 'バイキング', 'channel_code' => 'CX', 'start_time' => '11:55:00', 'end_time' => '13:44:59', 'cnt' => 1, 'minute' => '110'],
                ],
            ],
            [ // case 2
                'mode' => [
                    (object) ['title' => 'バイキング', 'channel_code' => 'CX', 'cnt' => 1, 'dow' => '3'],
                    (object) ['title' => 'ＦＮＮＬｉｖｅＮｅｗｓｄａｙｓ', 'channel_code' => 'CX', 'cnt' => 1, 'dow' => '3'],
                ],
                'time' => [
                    (object) ['title' => 'ＦＮＮＬｉｖｅＮｅｗｓｄａｙｓ', 'channel_code' => 'CX', 'start_time' => '11:30:00', 'end_time' => '11:54:59', 'cnt' => 1, 'minute' => '25'],
                    (object) ['title' => 'バイキング', 'channel_code' => 'CX', 'start_time' => '11:55:00', 'end_time' => '13:44:59', 'cnt' => 1, 'minute' => '110'],
                ],
            ],
            [ // case 3
                'mode' => [
                    (object) ['title' => 'バイキング', 'channel_code' => 'CX', 'cnt' => 1, 'dow' => '34'],
                ],
                'time' => [
                    (object) ['title' => 'バイキング', 'channel_code' => 'CX', 'start_time' => '11:55:00', 'end_time' => '13:44:59', 'cnt' => 1, 'minute' => '110'],
                    (object) ['title' => 'バイキング', 'channel_code' => 'CX', 'start_time' => '11:30:00', 'end_time' => '11:54:59', 'cnt' => 1, 'minute' => '25'],
                ],
            ],
            [ // case 4
                'mode' => [
                    (object) ['title' => 'バイキング', 'channel_code' => 'CX', 'cnt' => 1, 'dow' => '3'],
                    (object) ['title' => 'ＦＮＮＬｉｖｅＮｅｗｓｄａｙｓ', 'channel_code' => 'CX', 'cnt' => 1, 'dow' => '3'],
                ],
                'time' => [
                    (object) ['title' => 'ＦＮＮＬｉｖｅＮｅｗｓｄａｙｓ', 'channel_code' => 'CX', 'start_time' => '11:30:00', 'end_time' => '11:54:59', 'cnt' => 1, 'minute' => '25'],
                    (object) ['title' => 'バイキング', 'channel_code' => 'CX', 'start_time' => '11:55:00', 'end_time' => '13:44:59', 'cnt' => 1, 'minute' => '110'],
                ],
            ],
            [ // case 4
                'mode' => [
                    (object) ['title' => 'バイキング', 'channel_code' => 'CX', 'cnt' => 1, 'dow' => '3'],
                    (object) ['title' => 'ＦＮＮＬｉｖｅＮｅｗｓｄａｙｓ', 'channel_code' => 'CX', 'cnt' => 1, 'dow' => '3'],
                ],
                'time' => [
                    (object) ['title' => 'ＦＮＮＬｉｖｅＮｅｗｓｄａｙｓ', 'channel_code' => 'CX', 'start_time' => '11:30:00', 'end_time' => '11:54:59', 'cnt' => 1, 'minute' => '25'],
                    (object) ['title' => 'バイキング', 'channel_code' => 'CX', 'start_time' => '11:55:00', 'end_time' => '13:44:59', 'cnt' => 1, 'minute' => '110'],
                ],
            ],
        ];

        foreach ($cases as $i => $val) {
            yield
            $val => [
                $averageType[$i],
                $progIds[$i],
                $timeBoxIds[$i],
                $division[$i],
                $conditionCross[$i],
                $codes[$i],
                $divCodes[$i],
                $bsFlg[$i],
                $regionId[$i],
                $dataTypeConst[$i],
                $prefixes[$i],
                $selectedPersonalName[$i],
                $codeNumber[$i],
                $programListBinds[$i],
                $isConditionCross[$i],
                $sampleBinds[$i],
                $pivotBinds[$i],
                $expectBinds[$i],
                $modeTimeBindings[$i],
                $expected[$i],
                $resultListAverage[$i],
                $resultListAverageTs[$i],
                $resultListAverageGross[$i],
                $resultListAverageTotal[$i],
                $modeAndTime[$i],
            ];
        }
    }

    /**
     * @test
     * @dataProvider periodAverageDataProvider
     * @param mixed $startDate
     * @param mixed $endDate
     * @param mixed $startTime
     * @param mixed $endTime
     * @param mixed $division
     * @param mixed $conditionCross
     * @param mixed $codes
     * @param mixed $dispAveage
     * @param mixed $dataType
     * @param mixed $wdays
     * @param mixed $holiday
     * @param mixed $channels
     * @param mixed $genres
     * @param mixed $programTypes
     * @param mixed $length
     * @param mixed $regionId
     * @param mixed $page
     * @param mixed $straddlingFlg
     * @param mixed $csvFlag
     * @param mixed $dataTypeFlags
     * @param mixed $prefixes
     * @param mixed $isConditionCross
     * @param mixed $divCodes
     * @param mixed $obiBinds
     * @param mixed $expectBinds
     */
    public function periodAverageEmpty($startDate, $endDate, $startTime, $endTime, $division, $conditionCross, $codes, $dispAveage, $dataType, $wdays, $holiday, $channels, $genres, $programTypes, $length, $regionId, $page, $straddlingFlg, $csvFlag, $dataTypeFlags, $prefixes, $isConditionCross, $divCodes, $obiBinds, $expectBinds): void
    {
        $expected = [
            'list' => [],
            'cnt' => 0,
        ];

        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createProgramListWhere')
            ->with($startDate, $endDate, $channels, $genres, [], $division, $conditionCross, $codes, $regionId, false)
            ->andReturns([[], [], []])
            ->once()->ordered();

        $actual = $this->target->periodAverage($startDate, $endDate, $startTime, $endTime, $division, $conditionCross, $codes, $dispAveage, $dataType, $wdays, $holiday, $channels, $genres, $programTypes, $length, $regionId, $page, $straddlingFlg, $csvFlag, $dataTypeFlags, $prefixes);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider periodAverageDataProvider
     * @param mixed $startDate
     * @param mixed $endDate
     * @param mixed $startTime
     * @param mixed $endTime
     * @param mixed $division
     * @param mixed $conditionCross
     * @param mixed $codes
     * @param mixed $dispAveage
     * @param mixed $dataType
     * @param mixed $wdays
     * @param mixed $holiday
     * @param mixed $channels
     * @param mixed $genres
     * @param mixed $programTypes
     * @param mixed $length
     * @param mixed $regionId
     * @param mixed $page
     * @param mixed $straddlingFlg
     * @param mixed $csvFlag
     * @param mixed $dataTypeFlags
     * @param mixed $prefixes
     * @param mixed $isConditionCross
     * @param mixed $divCodes
     * @param mixed $obiBinds
     * @param mixed $expectBinds
     */
    public function periodAverage($startDate, $endDate, $startTime, $endTime, $division, $conditionCross, $codes, $dispAveage, $dataType, $wdays, $holiday, $channels, $genres, $programTypes, $length, $regionId, $page, $straddlingFlg, $csvFlag, $dataTypeFlags, $prefixes, $isConditionCross, $divCodes, $obiBinds, $expectBinds): void
    {
        $expected = [
            'list' => ['data'],
            'cnt' => (object) ['cnt' => 1],
        ];

        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createProgramListWhere')
            ->with($startDate, $endDate, $channels, $genres, [], $division, $conditionCross, $codes, $regionId, false)
            ->andReturns([[1], ['100', '200'], [3]])
            ->once()->ordered();

        // for day_of_week
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($obiBinds, 'obi binds'))
            ->once()->ordered();

        // for count
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->with(Mockery::any())
            ->andReturn((object) ['cnt' => 1])
            ->once()->ordered();

        // for result
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($expectBinds, 'result binds'))
            ->andReturn(['data'])
            ->once()->ordered();

        $actual = $this->target->periodAverage($startDate, $endDate, $startTime, $endTime, $division, $conditionCross, $codes, $dispAveage, $dataType, $wdays, $holiday, $channels, $genres, $programTypes, $length, $regionId, $page, $straddlingFlg, $csvFlag, $dataTypeFlags, $prefixes);

        $this->assertEquals($expected, $actual);
    }

    public function periodAverageDataProvider()
    {
        // テストマトリクス
        $cases = ['case1', 'case2', 'case3', 'case4', 'case5'];
        $startDate = ['19900614', '19900614', '19900614', '19900614', '19900614'];
        $endDate = ['19900614', '19900614', '19900614', '19900614', '19900614'];
        $startTime = ['', '000000', '100000', '050000', '050000'];
        $endTime = ['', '235959', '050000', '100000', '100000'];
        $division = ['ga8', 'ga8', 'ga8', 'condition_cross', 'condition_cross'];
        $conditionCross = [[], [], [], [], []];
        $codes = [['personal', 'c1', 'household'], ['personal', 'c1', 'household'], ['personal', 'household'], [''], ['']];
        $dispAveage = ['weight', 'simple', 'weight', 'simple', 'weight'];
        $dataType = [[1, 2, 3], [1, 2, 3], [2], [1, 2, 3], [1, 2, 3]];
        $wdays = [[0, 1, 2, 3, 4, 5, 6], [0, 1, 2, 3, 4, 5, 6], [0, 1, 2, 3, 4, 5, 6], [0, 1, 2, 3, 4, 5, 6], [0, 1, 2, 3, 4, 5, 6]];
        $holiday = [false, true, false, true, true];
        $channels = [[], [1, 2, 3, 4, 5, 6, 7, 8], [], [1, 2, 3, 4, 5, 6, 7, 8], [1, 2, 3, 4, 5, 6, 7, 8]];
        $genres = [[], [10, 11, 12, 13, 14], [], [10, 11, 12, 13, 14], [10, 11, 12, 13, 14]];
        $programTypes = [[], ['1', '2', '3', '4', '5'], [], ['1', '2', '3', '4', '5'], ['1', '2', '3', '4', '5']];
        $length = [30, 30, 30, 30, 30];
        $regionId = [1, 2, 1, 2, 2];
        $page = [1, 2, 3, 4, 4];
        $straddlingFlg = [true, false, true, false, false];
        $csvFlag = ['0', '0', '1', '1', '1'];
        $dataTypeFlags = [
            ['rt' => true, 'ts' => true, 'gross' => true, 'total' => false, 'rtTotal' => false],
            ['rt' => true, 'ts' => true, 'gross' => true, 'total' => false, 'rtTotal' => false],
            ['rt' => false, 'ts' => true, 'gross' => false, 'total' => false, 'rtTotal' => false],
            ['rt' => true, 'ts' => true, 'gross' => true, 'total' => false, 'rtTotal' => false],
            ['rt' => true, 'ts' => true, 'gross' => true, 'total' => false, 'rtTotal' => false],
        ];
        $prefixes = [['code' => 'code', 'number' => 'number'], ['code' => 'code', 'number' => 'number'], ['code' => 'code', 'number' => 'number'], ['code' => 'code', 'number' => 'number'], ['code' => 'code', 'number' => 'number']];
        $isConditionCross = [false, false, false, true, true];
        // expects
        $divCodes = [['c1'], ['c1'], [], ['condition_cross'], ['condition_cross']];
        $obiBinds = [
            [
                ':startDate' => '19900614',
                ':endDate' => '19900614',
                ':regionId' => 1,
                ':wdays0' => 0,
                ':wdays1' => 1,
                ':wdays2' => 2,
                ':wdays3' => 3,
                ':wdays4' => 4,
                ':wdays5' => 5,
                ':wdays6' => 6,
                ':startTime' => '',
                ':endTime' => '',
            ], [
                ':startDate' => '19900614',
                ':endDate' => '19900614',
                ':regionId' => 2,
                ':genres0' => 10,
                ':genres1' => 11,
                ':genres2' => 12,
                ':genres3' => 13,
                ':genres4' => 14,
                ':channels0' => 1,
                ':channels1' => 2,
                ':channels2' => 3,
                ':channels3' => 4,
                ':channels4' => 5,
                ':channels5' => 6,
                ':channels6' => 7,
                ':channels7' => 8,
                ':wdays0' => 0,
                ':wdays1' => 1,
                ':wdays2' => 2,
                ':wdays3' => 3,
                ':wdays4' => 4,
                ':wdays5' => 5,
                ':wdays6' => 6,
                ':startTime' => '000000',
                ':endTime' => '235959',
                ':programTypes0' => 'レギュラー',
                ':programTypes1' => 'スペシャル',
                ':programTypes2' => 'ミニ番',
                ':programTypes3' => '再放送',
                ':programTypes4' => '番宣',
            ], [
                ':startDate' => '19900614',
                ':endDate' => '19900614',
                ':regionId' => 1,
                ':wdays0' => 0,
                ':wdays1' => 1,
                ':wdays2' => 2,
                ':wdays3' => 3,
                ':wdays4' => 4,
                ':wdays5' => 5,
                ':wdays6' => 6,
                ':startTime' => '100000',
                ':endTime' => '050000',
            ], [
                ':startDate' => '19900614',
                ':endDate' => '19900614',
                ':regionId' => 2,
                ':genres0' => 10,
                ':genres1' => 11,
                ':genres2' => 12,
                ':genres3' => 13,
                ':genres4' => 14,
                ':channels0' => 1,
                ':channels1' => 2,
                ':channels2' => 3,
                ':channels3' => 4,
                ':channels4' => 5,
                ':channels5' => 6,
                ':channels6' => 7,
                ':channels7' => 8,
                ':wdays0' => 0,
                ':wdays1' => 1,
                ':wdays2' => 2,
                ':wdays3' => 3,
                ':wdays4' => 4,
                ':wdays5' => 5,
                ':wdays6' => 6,
                ':startTime' => '050000',
                ':endTime' => '100000',
                ':programTypes0' => 'レギュラー',
                ':programTypes1' => 'スペシャル',
                ':programTypes2' => 'ミニ番',
                ':programTypes3' => '再放送',
                ':programTypes4' => '番宣',
            ], [
                ':startDate' => '19900614',
                ':endDate' => '19900614',
                ':regionId' => 2,
                ':genres0' => 10,
                ':genres1' => 11,
                ':genres2' => 12,
                ':genres3' => 13,
                ':genres4' => 14,
                ':channels0' => 1,
                ':channels1' => 2,
                ':channels2' => 3,
                ':channels3' => 4,
                ':channels4' => 5,
                ':channels5' => 6,
                ':channels6' => 7,
                ':channels7' => 8,
                ':wdays0' => 0,
                ':wdays1' => 1,
                ':wdays2' => 2,
                ':wdays3' => 3,
                ':wdays4' => 4,
                ':wdays5' => 5,
                ':wdays6' => 6,
                ':startTime' => '050000',
                ':endTime' => '100000',
                ':programTypes0' => 'レギュラー',
                ':programTypes1' => 'スペシャル',
                ':programTypes2' => 'ミニ番',
                ':programTypes3' => '再放送',
                ':programTypes4' => '番宣',
            ],
        ];
        $expectBinds = [
            [
                ':division' => 'ga8',
                ':codeName0' => 'c1',
            ],
            [
                ':division' => 'ga8',
                ':codeName0' => 'c1',
            ],
            [
                ':division' => 'ga8',
            ],
            [
                ':division' => 'condition_cross',
                ':codeName0' => 'condition_cross',
            ],
            [
                ':division' => 'condition_cross',
                ':codeName0' => 'condition_cross',
            ],
        ];

        foreach ($cases as $i => $val) {
            yield
            $val => [
                $startDate[$i],
                $endDate[$i],
                $startTime[$i],
                $endTime[$i],
                $division[$i],
                $conditionCross[$i],
                $codes[$i],
                $dispAveage[$i],
                $dataType[$i],
                $wdays[$i],
                $holiday[$i],
                $channels[$i],
                $genres[$i],
                $programTypes[$i],
                $length[$i],
                $regionId[$i],
                $page[$i],
                $straddlingFlg[$i],
                $csvFlag[$i],
                $dataTypeFlags[$i],
                $prefixes[$i],
                $isConditionCross[$i],
                $divCodes[$i],
                $obiBinds[$i],
                $expectBinds[$i],
            ];
        }
    }

    /**
     * @test
     * @dataProvider periodAverageOriginalDataProvider
     * @param mixed $startDate
     * @param mixed $endDate
     * @param mixed $startTime
     * @param mixed $endTime
     * @param mixed $division
     * @param mixed $conditionCross
     * @param mixed $codes
     * @param mixed $dispAveage
     * @param mixed $wdays
     * @param mixed $holiday
     * @param mixed $channels
     * @param mixed $genres
     * @param mixed $programTypes
     * @param mixed $length
     * @param mixed $regionId
     * @param mixed $page
     * @param mixed $straddlingFlg
     * @param mixed $csvFlag
     * @param mixed $prefixes
     * @param mixed $isConditionCross
     * @param mixed $divCodes
     * @param mixed $obiBinds
     * @param mixed $expectBinds
     * @param mixed $selectedPersonalName
     * @param mixed $codeNumber
     */
    public function periodAverageOriginalTs($startDate, $endDate, $startTime, $endTime, $division, $conditionCross, $codes, $dispAveage, $wdays, $holiday, $channels, $genres, $programTypes, $length, $regionId, $page, $straddlingFlg, $csvFlag, $prefixes, $selectedPersonalName, $codeNumber, $isConditionCross, $divCodes, $obiBinds, $expectBinds): void
    {
        $expected = [
            'list' => ['data'],
            'cnt' => (object) ['cnt' => 1],
        ];

        $dataType = [2];
        $dataTypeFlags = ['rt' => false, 'ts' => true, 'gross' => false, 'total' => false, 'rtTotal' => false];

        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createProgramListWhere')
            ->with($startDate, $endDate, $channels, $genres, [], $division, $conditionCross, $codes, $regionId, false)
            ->andReturns([[1], ['100', '200'], [3]])
            ->once()->ordered();

        // for day_of_week
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($obiBinds, 'obi binds'))
            ->once()->ordered();

        // for count
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->with(Mockery::any())
            ->andReturn((object) ['cnt' => 1])
            ->once()->ordered();

        // createPvUniondTemplateTables
        if (count($divCodes) > 0) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createTsSampleTempTable')
                ->with($isConditionCross, $conditionCross, $division, $divCodes, [1], $regionId, 'code', 'number', $selectedPersonalName, false, $codeNumber)
                ->andReturns([[1], ['100', '200'], [3]])
                ->once()->ordered();
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->once()->ordered();
            // union
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->once()->ordered();
        }
        // for pivot
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        // for result
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->andReturn(['data'])
            ->once()->ordered();

        $actual = $this->target->periodAverageOriginal($startDate, $endDate, $startTime, $endTime, $division, $conditionCross, $codes, $dispAveage, $dataType, $wdays, $holiday, $channels, $genres, $programTypes, $length, $regionId, $page, $straddlingFlg, $csvFlag, $dataTypeFlags, $prefixes, $selectedPersonalName, $codeNumber);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider periodAverageOriginalDataProvider
     * @param mixed $startDate
     * @param mixed $endDate
     * @param mixed $startTime
     * @param mixed $endTime
     * @param mixed $division
     * @param mixed $conditionCross
     * @param mixed $codes
     * @param mixed $dispAveage
     * @param mixed $wdays
     * @param mixed $holiday
     * @param mixed $channels
     * @param mixed $genres
     * @param mixed $programTypes
     * @param mixed $length
     * @param mixed $regionId
     * @param mixed $page
     * @param mixed $straddlingFlg
     * @param mixed $csvFlag
     * @param mixed $prefixes
     * @param mixed $isConditionCross
     * @param mixed $divCodes
     * @param mixed $obiBinds
     * @param mixed $expectBinds
     * @param mixed $selectedPersonalName
     * @param mixed $codeNumber
     */
    public function periodAverageOriginalGross($startDate, $endDate, $startTime, $endTime, $division, $conditionCross, $codes, $dispAveage, $wdays, $holiday, $channels, $genres, $programTypes, $length, $regionId, $page, $straddlingFlg, $csvFlag, $prefixes, $selectedPersonalName, $codeNumber, $isConditionCross, $divCodes, $obiBinds, $expectBinds): void
    {
        $expected = [
            'list' => ['data'],
            'cnt' => (object) ['cnt' => 1],
        ];

        $dataType = [3];
        $dataTypeFlags = ['rt' => false, 'ts' => false, 'gross' => true, 'total' => false, 'rtTotal' => false];

        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createProgramListWhere')
            ->with($startDate, $endDate, $channels, $genres, [], $division, $conditionCross, $codes, $regionId, false)
            ->andReturns([[1], ['100', '200'], [3]])
            ->once()->ordered();

        // for day_of_week
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($obiBinds, 'obi binds'))
            ->once()->ordered();

        // for count
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->with(Mockery::any())
            ->andReturn((object) ['cnt' => 1])
            ->once()->ordered();

        // createPvUniondTemplateTables
        if (count($divCodes) > 0) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createRtSampleTempTable')
                ->with($isConditionCross, $conditionCross, $division, $divCodes, [1], 'code', 'number', $selectedPersonalName, false, $codeNumber)
                ->andReturns([[1], ['100', '200'], [3]])
                ->once()->ordered();
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createTsSampleTempTable')
                ->with($isConditionCross, $conditionCross, $division, $divCodes, [1], $regionId, 'code', 'number', $selectedPersonalName, false, $codeNumber)
                ->andReturns([[1], ['100', '200'], [3]])
                ->once()->ordered();
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->once()->ordered();
            // viewers
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->once()->ordered();
            // union
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->once()->ordered();
        }
        // for pivot
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        // for result
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->andReturn(['data'])
            ->once()->ordered();

        $actual = $this->target->periodAverageOriginal($startDate, $endDate, $startTime, $endTime, $division, $conditionCross, $codes, $dispAveage, $dataType, $wdays, $holiday, $channels, $genres, $programTypes, $length, $regionId, $page, $straddlingFlg, $csvFlag, $dataTypeFlags, $prefixes, $selectedPersonalName, $codeNumber);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider periodAverageOriginalDataProvider
     * @param mixed $startDate
     * @param mixed $endDate
     * @param mixed $startTime
     * @param mixed $endTime
     * @param mixed $division
     * @param mixed $conditionCross
     * @param mixed $codes
     * @param mixed $dispAveage
     * @param mixed $wdays
     * @param mixed $holiday
     * @param mixed $channels
     * @param mixed $genres
     * @param mixed $programTypes
     * @param mixed $length
     * @param mixed $regionId
     * @param mixed $page
     * @param mixed $straddlingFlg
     * @param mixed $csvFlag
     * @param mixed $prefixes
     * @param mixed $isConditionCross
     * @param mixed $divCodes
     * @param mixed $obiBinds
     * @param mixed $expectBinds
     * @param mixed $selectedPersonalName
     * @param mixed $codeNumber
     */
    public function periodAverageOriginal($startDate, $endDate, $startTime, $endTime, $division, $conditionCross, $codes, $dispAveage, $wdays, $holiday, $channels, $genres, $programTypes, $length, $regionId, $page, $straddlingFlg, $csvFlag, $prefixes, $selectedPersonalName, $codeNumber, $isConditionCross, $divCodes, $obiBinds, $expectBinds): void
    {
        $expected = [
            'list' => ['data'],
            'cnt' => (object) ['cnt' => 1],
        ];

        $dataType = [1];
        $dataTypeFlags = [['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => false]];

        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createProgramListWhere')
            ->with($startDate, $endDate, $channels, $genres, [], $division, $conditionCross, $codes, $regionId, false)
            ->andReturns([[1], ['100', '200'], [3]])
            ->once()->ordered();

        // for day_of_week
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($obiBinds, 'obi binds'))
            ->once()->ordered();

        // for count
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->with(Mockery::any())
            ->andReturn((object) ['cnt' => 1])
            ->once()->ordered();

        // createPvUniondTemplateTables
        if (count($divCodes) > 0) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createRtSampleTempTable')
                ->with($isConditionCross, $conditionCross, $division, $divCodes, [1], 'code', 'number', $selectedPersonalName, false, $codeNumber)
                ->andReturns([[1], ['100', '200'], [3]])
                ->once()->ordered();
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->once()->ordered();
            // union
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->once()->ordered();
        }
        // for pivot
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        // for result
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->andReturn(['data'])
            ->once()->ordered();

        $actual = $this->target->periodAverageOriginal($startDate, $endDate, $startTime, $endTime, $division, $conditionCross, $codes, $dispAveage, $dataType, $wdays, $holiday, $channels, $genres, $programTypes, $length, $regionId, $page, $straddlingFlg, $csvFlag, $dataTypeFlags, $prefixes, $selectedPersonalName, $codeNumber);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider periodAverageOriginalDataProvider
     * @param mixed $startDate
     * @param mixed $endDate
     * @param mixed $startTime
     * @param mixed $endTime
     * @param mixed $division
     * @param mixed $conditionCross
     * @param mixed $codes
     * @param mixed $dispAveage
     * @param mixed $wdays
     * @param mixed $holiday
     * @param mixed $channels
     * @param mixed $genres
     * @param mixed $programTypes
     * @param mixed $length
     * @param mixed $regionId
     * @param mixed $page
     * @param mixed $straddlingFlg
     * @param mixed $csvFlag
     * @param mixed $prefixes
     * @param mixed $isConditionCross
     * @param mixed $divCodes
     * @param mixed $obiBinds
     * @param mixed $expectBinds
     * @param mixed $selectedPersonalName
     * @param mixed $codeNumber
     */
    public function periodAverageOriginalEmpty($startDate, $endDate, $startTime, $endTime, $division, $conditionCross, $codes, $dispAveage, $wdays, $holiday, $channels, $genres, $programTypes, $length, $regionId, $page, $straddlingFlg, $csvFlag, $prefixes, $selectedPersonalName, $codeNumber, $isConditionCross, $divCodes, $obiBinds, $expectBinds): void
    {
        $expected = [
            'list' => [],
            'cnt' => 0,
        ];

        $dataType = [1];
        $dataTypeFlags = [['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => false]];

        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createProgramListWhere')
            ->with($startDate, $endDate, $channels, $genres, [], $division, $conditionCross, $codes, $regionId, false)
            ->andReturns([[], [], []])
            ->once()->ordered();

        $actual = $this->target->periodAverageOriginal($startDate, $endDate, $startTime, $endTime, $division, $conditionCross, $codes, $dispAveage, $dataType, $wdays, $holiday, $channels, $genres, $programTypes, $length, $regionId, $page, $straddlingFlg, $csvFlag, $dataTypeFlags, $prefixes, $selectedPersonalName, $codeNumber);

        $this->assertEquals($expected, $actual);
    }

    public function periodAverageOriginalDataProvider()
    {
        // テストマトリクス
        $cases = ['case1', 'case2', 'case3', 'case4', 'case5'];
        $startDate = ['19900614', '19900614', '19900614', '19900614', '19900614'];
        $endDate = ['19900614', '19900614', '19900614', '19900614', '19900614'];
        $startTime = ['', '000000', '100000', '050000', '050000'];
        $endTime = ['', '235959', '050000', '100000', '100000'];
        $division = ['ga8', 'ga8', 'ga8', 'condition_cross', 'condition_cross'];
        $conditionCross = [[], [], [], [], []];
        $codes = [['personal', 'c1', 'household'], ['personal', 'c1', 'household'], ['personal', 'household'], [], []];
        $dispAveage = ['weight', 'simple', 'weight', 'simple', 'weight'];
        $wdays = [[0, 1, 2, 3, 4, 5, 6], [0, 1, 2, 3, 4, 5, 6], [0, 1, 2, 3, 4, 5, 6], [0, 1, 2, 3, 4, 5, 6], [0, 1, 2, 3, 4, 5, 6]];
        $holiday = [false, true, false, true, true];
        $channels = [[], [1, 2, 3, 4, 5, 6, 7, 8], [], [1, 2, 3, 4, 5, 6, 7, 8], [1, 2, 3, 4, 5, 6, 7, 8]];
        $genres = [[], [10, 11, 12, 13, 14], [], [10, 11, 12, 13, 14], [10, 11, 12, 13, 14]];
        $programTypes = [[], ['1', '2', '3', '4', '5'], [], ['1', '2', '3', '4', '5'], ['1', '2', '3', '4', '5']];
        $length = [30, 30, 30, 30, 30];
        $regionId = [1, 2, 1, 2, 2];
        $page = [1, 2, 3, 4, 4];
        $straddlingFlg = [true, false, true, false, false];
        $csvFlag = ['0', '0', '1', '1', '1'];
        $prefixes = [['code' => 'code', 'number' => 'number'], ['code' => 'code', 'number' => 'number'], ['code' => 'code', 'number' => 'number'], ['code' => 'code', 'number' => 'number'], ['code' => 'code', 'number' => 'number']];
        $selectedPersonalName = ['selected_personal', 'selected_personal', 'selected_personal', 'selected_personal', 'selected_personal'];
        $codeNumber = [32, 32, 32, 32, 32];
        $isConditionCross = [false, false, false, true, true];
        // expects
        $divCodes = [['c1'], ['c1'], [], ['condition_cross'], ['condition_cross']];
        $obiBinds = [
            [
                ':startDate' => '19900614',
                ':endDate' => '19900614',
                ':regionId' => 1,
                ':wdays0' => 0,
                ':wdays1' => 1,
                ':wdays2' => 2,
                ':wdays3' => 3,
                ':wdays4' => 4,
                ':wdays5' => 5,
                ':wdays6' => 6,
                ':startTime' => '',
                ':endTime' => '',
            ], [
                ':startDate' => '19900614',
                ':endDate' => '19900614',
                ':regionId' => 2,
                ':genres0' => 10,
                ':genres1' => 11,
                ':genres2' => 12,
                ':genres3' => 13,
                ':genres4' => 14,
                ':channels0' => 1,
                ':channels1' => 2,
                ':channels2' => 3,
                ':channels3' => 4,
                ':channels4' => 5,
                ':channels5' => 6,
                ':channels6' => 7,
                ':channels7' => 8,
                ':wdays0' => 0,
                ':wdays1' => 1,
                ':wdays2' => 2,
                ':wdays3' => 3,
                ':wdays4' => 4,
                ':wdays5' => 5,
                ':wdays6' => 6,
                ':startTime' => '000000',
                ':endTime' => '235959',
                ':programTypes0' => 'レギュラー',
                ':programTypes1' => 'スペシャル',
                ':programTypes2' => 'ミニ番',
                ':programTypes3' => '再放送',
                ':programTypes4' => '番宣',
            ], [
                ':startDate' => '19900614',
                ':endDate' => '19900614',
                ':regionId' => 1,
                ':wdays0' => 0,
                ':wdays1' => 1,
                ':wdays2' => 2,
                ':wdays3' => 3,
                ':wdays4' => 4,
                ':wdays5' => 5,
                ':wdays6' => 6,
                ':startTime' => '100000',
                ':endTime' => '050000',
            ], [
                ':startDate' => '19900614',
                ':endDate' => '19900614',
                ':regionId' => 2,
                ':genres0' => 10,
                ':genres1' => 11,
                ':genres2' => 12,
                ':genres3' => 13,
                ':genres4' => 14,
                ':channels0' => 1,
                ':channels1' => 2,
                ':channels2' => 3,
                ':channels3' => 4,
                ':channels4' => 5,
                ':channels5' => 6,
                ':channels6' => 7,
                ':channels7' => 8,
                ':wdays0' => 0,
                ':wdays1' => 1,
                ':wdays2' => 2,
                ':wdays3' => 3,
                ':wdays4' => 4,
                ':wdays5' => 5,
                ':wdays6' => 6,
                ':startTime' => '050000',
                ':endTime' => '100000',
                ':programTypes0' => 'レギュラー',
                ':programTypes1' => 'スペシャル',
                ':programTypes2' => 'ミニ番',
                ':programTypes3' => '再放送',
                ':programTypes4' => '番宣',
            ], [
                ':startDate' => '19900614',
                ':endDate' => '19900614',
                ':regionId' => 2,
                ':genres0' => 10,
                ':genres1' => 11,
                ':genres2' => 12,
                ':genres3' => 13,
                ':genres4' => 14,
                ':channels0' => 1,
                ':channels1' => 2,
                ':channels2' => 3,
                ':channels3' => 4,
                ':channels4' => 5,
                ':channels5' => 6,
                ':channels6' => 7,
                ':channels7' => 8,
                ':wdays0' => 0,
                ':wdays1' => 1,
                ':wdays2' => 2,
                ':wdays3' => 3,
                ':wdays4' => 4,
                ':wdays5' => 5,
                ':wdays6' => 6,
                ':startTime' => '050000',
                ':endTime' => '100000',
                ':programTypes0' => 'レギュラー',
                ':programTypes1' => 'スペシャル',
                ':programTypes2' => 'ミニ番',
                ':programTypes3' => '再放送',
                ':programTypes4' => '番宣',
            ],
        ];
        $expectBinds = [
            [
                ':division' => 'ga8',
                ':codeName0' => 'c1',
            ],
            [
                ':division' => 'ga8',
                ':codeName0' => 'c1',
            ],
            [
                ':division' => 'ga8',
            ],
            [
                ':division' => 'condition_cross',
                ':codeName0' => 'condition_cross',
            ],
            [
                ':division' => 'condition_cross',
                ':codeName0' => 'condition_cross',
            ],
        ];

        foreach ($cases as $i => $val) {
            yield
            $val => [
                $startDate[$i],
                $endDate[$i],
                $startTime[$i],
                $endTime[$i],
                $division[$i],
                $conditionCross[$i],
                $codes[$i],
                $dispAveage[$i],
                $wdays[$i],
                $holiday[$i],
                $channels[$i],
                $genres[$i],
                $programTypes[$i],
                $length[$i],
                $regionId[$i],
                $page[$i],
                $straddlingFlg[$i],
                $csvFlag[$i],
                $prefixes[$i],
                $selectedPersonalName[$i],
                $codeNumber[$i],
                $isConditionCross[$i],
                $divCodes[$i],
                $obiBinds[$i],
                $expectBinds[$i],
            ];
        }
    }

    /**
     * @test
     * @dataProvider tableDataProvider
     * @param mixed $startDateTime
     * @param mixed $endDateTime
     * @param mixed $startTime
     * @param mixed $endTime
     * @param mixed $channels
     * @param mixed $division
     * @param mixed $conditionCross
     * @param mixed $codes
     * @param mixed $bsFlg
     * @param mixed $regionId
     * @param mixed $expectBinds
     */
    public function table($startDateTime, $endDateTime, $startTime, $endTime, $channels, $division, $conditionCross, $codes, $bsFlg, $regionId, $expectBinds): void
    {
        $expected = [
            'list' => ['data'],
        ];
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($expectBinds, 'expectBinds'))
            ->andReturn(['data'])
            ->once()->ordered();

        $actual = $this->target->table($startDateTime, $endDateTime, $startTime, $endTime, $channels, $division, $conditionCross, $codes, $bsFlg, $regionId, $expectBinds);

        $this->assertEquals($expected, $actual);
    }

    public function tableDataProvider()
    {
        // テストマトリクス
        $cases = ['case1', 'case2', 'case3', 'case4'];
        $startDateTime = ['1990-06-14 10:00:00', '1990-06-14 10:00:00', '1990-06-14 05:00:00', '1990-06-15 04:00:00'];
        $endDateTime = ['1990-06-15 04:59:59', '1990-06-15 04:59:59', '1990-06-15 04:59:59', '1990-06-14 07:59:59'];
        $startTime = ['', '050000', '000000', '230000'];
        $endTime = ['', '235959', '235959', '025959'];
        $channels = [[1, 2, 3], [1, 2, 3, 4, 5, 6, 7], [1, 2, 3], [1, 2, 3, 4, 5, 6, 7]];
        $division = ['', 'household', '', 'ga8'];
        $conditionCross = [[], [], [], []];
        $codes = [['personal'], ['household'], ['personal'], ['c1']];
        $bsFlg = [true, false, true, false];
        $regionId = [1, 2, 1, 2];
        $expectBinds = [
            [
                ':region_id' => 1,
                ':digit' => 2,
                ':division' => '',
                ':dwhStartDateTime' => new Carbon('1990-06-13 10:00:00.000000'),
                ':dwhEndDateTime' => new Carbon('1990-06-17 04:59:59.000000'),
                ':startDateTime' => '1990-06-14 10:00:00',
                ':endDateTime' => '1990-06-15 04:59:59',
                ':channels0' => 1,
                ':channels1' => 2,
                ':channels2' => 3,
                ':startTime' => '',
                ':endTime' => '',
                'toBoundary' => new Carbon('1990-06-15 04:59:59.000000'),
            ],
            [
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
            ],
            [
                ':region_id' => 1,
                ':digit' => 2,
                ':division' => '',
                ':dwhStartDateTime' => new Carbon('1990-06-13 05:00:00.000000'),
                ':dwhEndDateTime' => new Carbon('1990-06-17 04:59:59.000000'),
                ':startDateTime' => '1990-06-14 05:00:00',
                ':endDateTime' => '1990-06-15 04:59:59',
                ':channels0' => 1,
                ':channels1' => 2,
                ':channels2' => 3,
                'toBoundary' => new Carbon('1990-06-15 04:59:59.000000'),
            ],
            [
                ':region_id' => 2,
                ':digit' => 1,
                ':ga8_0' => 'c1',
                ':division' => 'ga8',
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
            ],
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
     * @dataProvider tableOriginalDataProvider
     * @param mixed $startDateTime
     * @param mixed $endDateTime
     * @param mixed $startTime
     * @param mixed $endTime
     * @param mixed $channels
     * @param mixed $division
     * @param mixed $conditionCross
     * @param mixed $codes
     * @param mixed $bsFlg
     * @param mixed $regionId
     * @param mixed $isConditionCross
     * @param mixed $tempBinds
     * @param mixed $expectBinds
     */
    public function tableOriginal($startDateTime, $endDateTime, $startTime, $endTime, $channels, $division, $conditionCross, $codes, $bsFlg, $regionId, $isConditionCross, $tempBinds, $expectBinds): void
    {
        $expected = [
            'list' => ['data'],
        ];

        if ($isConditionCross) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createConditionCrossSql')
                ->with($conditionCross, Mockery::any())
                ->andReturn('')
                ->once()->ordered();
        } else {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createCrossJoinWhereClause')
                ->with($division, $codes, Mockery::any())
                ->andReturn('')
                ->once()->ordered();
        }

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($tempBinds, 'tempBinds'))
            ->andReturn(['data'])
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($expectBinds, 'expectBinds'))
            ->andReturn(['data'])
            ->once()->ordered();

        $actual = $this->target->tableOriginal($startDateTime, $endDateTime, $startTime, $endTime, $channels, $division, $conditionCross, $codes, $bsFlg, $regionId, $expectBinds);

        $this->assertEquals($expected, $actual);
    }

    public function tableOriginalDataProvider()
    {
        // テストマトリクス
        $cases = ['case1', 'case2', 'case3', 'case4'];
        $startDateTime = ['1990-06-14 10:00:00', '1990-06-14 10:00:00', '1990-06-14 05:00:00', '1990-06-15 04:00:00'];
        $endDateTime = ['1990-06-15 04:59:59', '1990-06-15 04:59:59', '1990-06-15 04:59:59', '1990-06-14 07:59:59'];
        $startTime = ['', '050000', '000000', '230000'];
        $endTime = ['', '235959', '235959', '025959'];
        $channels = [[1, 2, 3], [1, 2, 3, 4, 5, 6, 7], [1, 2, 3], [1, 2, 3, 4, 5, 6, 7]];
        $division = ['', 'household', '', 'condition_cross'];
        $conditionCross = [[], [], [], []];
        $codes = [['personal'], ['household'], ['personal'], []];
        $bsFlg = [true, false, true, false];
        $regionId = [1, 2, 1, 2];
        $isConditionCross = [false, false, false, true];
        $tempBinds = [
            [
                ':channels0' => 1,
                ':channels1' => 2,
                ':channels2' => 3,
                ':toBoundary' => new Carbon('1990-06-15 04:59:59.000000'),
                ':startDateTime' => '1990-06-14 10:00:00',
                ':endDateTime' => '1990-06-15 04:59:59',
            ],
            [
                ':channels0' => 1,
                ':channels1' => 2,
                ':channels2' => 3,
                ':channels3' => 4,
                ':channels4' => 5,
                ':channels5' => 6,
                ':channels6' => 7,
                ':toBoundary' => new Carbon('1990-06-15 04:59:59.000000'),
                ':startDateTime' => '1990-06-14 10:00:00',
                ':endDateTime' => '1990-06-15 04:59:59',
            ],
            [
                ':channels0' => 1,
                ':channels1' => 2,
                ':channels2' => 3,
                ':toBoundary' => new Carbon('1990-06-15 04:59:59.000000'),
                ':startDateTime' => '1990-06-14 05:00:00',
                ':endDateTime' => '1990-06-15 04:59:59',
            ],
            [
                ':channels0' => 1,
                ':channels1' => 2,
                ':channels2' => 3,
                ':channels3' => 4,
                ':channels4' => 5,
                ':channels5' => 6,
                ':channels6' => 7,
                ':toBoundary' => new Carbon('1990-06-15 07:59:59.000000'),
                ':startDateTime' => '1990-06-15 04:00:00',
                ':endDateTime' => '1990-06-14 07:59:59',
            ],
        ];
        $expectBinds = [
            [
                ':region_id' => 1,
                ':digit' => 2,
                ':startDateTime' => '1990-06-14 10:00:00',
                ':endDateTime' => '1990-06-15 04:59:59',
                ':channels0' => 1,
                ':channels1' => 2,
                ':channels2' => 3,
                ':union__personal' => 'personal',
                ':vertical__personal' => 'personal',
                ':startTime' => '',
                ':endTime' => '',
            ],
            [
                ':region_id' => 2,
                ':digit' => 1,
                ':startDateTime' => '1990-06-14 10:00:00',
                ':endDateTime' => '1990-06-15 04:59:59',
                ':channels0' => 1,
                ':channels1' => 2,
                ':channels2' => 3,
                ':channels3' => 4,
                ':channels4' => 5,
                ':channels5' => 6,
                ':channels6' => 7,
                ':union_household_household' => 'household',
                ':vertical_household_household' => 'household',
                ':startTime' => '050000',
                ':endTime' => '235959',
            ],
            [
                ':region_id' => 1,
                ':digit' => 2,
                ':startDateTime' => '1990-06-14 05:00:00',
                ':endDateTime' => '1990-06-15 04:59:59',
                ':channels0' => 1,
                ':channels1' => 2,
                ':channels2' => 3,
                ':union__personal' => 'personal',
                ':vertical__personal' => 'personal',
            ],
            [
                ':region_id' => 2,
                ':digit' => 1,
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
            ],
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
                $isConditionCross[$i],
                $tempBinds[$i],
                $expectBinds[$i],
            ];
        }
    }

    /**
     * @test
     */
    public function getLatestObiProgramsDate(): void
    {
        $expected = (object) ['result'];
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->with(Mockery::any())
            ->andReturn($expected)
            ->once()->ordered();

        $actual = $this->target->getLatestObiProgramsDate();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider findProgramDataProvider
     * @param mixed $progId
     * @param mixed $timeBoxId
     * @param mixed $expectBinds
     */
    public function findProgram($progId, $timeBoxId, $expectBinds): void
    {
        $expected = (object) ['result'];
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->withArgs($this->bindAsserts($expectBinds, 'find'))
            ->andReturn($expected)
            ->once()->ordered();

        $actual = $this->target->findProgram($progId, $timeBoxId);

        $this->assertEquals($expected, $actual);
    }

    public function findProgramDataProvider()
    {
        // テストマトリクス
        $cases = ['case1'];
        $progId = ['progId'];
        $timeBoxId = ['timeBoxId'];
        $expectBinds = [
            [
                ':prog_id' => 'progId',
                ':time_box_id' => 'timeBoxId',
            ],
        ];

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
     * @dataProvider createMultiChannelProfileTablesDataProvider
     * @param int $regionId
     * @param string $startDate
     * @param string $endDate
     * @param array $progIds
     * @param array $timeBoxIds
     * @param array $channelIds
     * @param ?string $division
     * @param ?array $conditionCross
     * @param ?array $codes
     * @param array $targetProgramsBinds
     */
    public function createMultiChannelProfileTablesEnq(int $regionId, string $startDate, string $endDate, array $progIds, array $timeBoxIds, array $channelIds, ?string $division, ?array $conditionCross, ?array $codes, array $targetProgramsBinds): void
    {
        $isEnq = true;
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($targetProgramsBinds, 'targetPrograms'))
            ->andReturn(['data'])
            ->once()->ordered();

        /////// createEnqMultiChannelProfileTables
        // for converted_enq_generes
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();
        // for enq_question_panelers
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();
        // group_enq_questions
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();
        // new_enq_questions
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();
        // convert_enq
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();

        /////// createMultiChannelProfileCommonTables
        // samples
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();
        // sample_numbers
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();
        // target_audience_data
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();
        // household_audience_data
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();
        // household_reports
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();
        // target_program_viewers
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();
        // personal_reports
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();
        // enq_samples
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();
        // enq_sample_numbers
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();
        // target_program_enq_master
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();
        // target_enq_viewers
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();
        // main_target_enq_viewers
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();
        // enq_rate_list
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();
        // channel_rate
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();

        $this->target->createMultiChannelProfileTables($isEnq, $regionId, $startDate, $endDate, $progIds, $timeBoxIds, $channelIds, $division, $conditionCross, $codes);
    }

    public function createMultiChannelProfileTablesDataProvider()
    {
        // テストマトリクス
        $cases = ['case1'];
        $regionId = [1];
        $startDate = ['1990-06-14'];
        $endDate = ['1990-06-15'];
        $progIds = [['1', '2', '3']];
        $timeBoxIds = [['44', '44', '44']];
        $channelIds = [[1, 2, 3, 4, 5, 6, 7]];
        $division = ['ga8'];
        $conditionCross = [[]];
        $codes = [['c1']];
        $targetProgramsBinds = [
            [
                ':startDate' => '1990-06-14',
                ':endDate' => '1990-06-15',
                ':regionId' => 1,
                ':progId0' => '1',
                ':timeBoxId1' => 44,
                ':progId2' => '2',
                ':timeBoxId3' => 44,
                ':progId4' => '3',
                ':timeBoxId5' => 44,
                ':channelIds0' => 1,
                ':channelIds1' => 2,
                ':channelIds2' => 3,
                ':channelIds3' => 4,
                ':channelIds4' => 5,
                ':channelIds5' => 6,
                ':channelIds6' => 7,
            ],
        ];

        foreach ($cases as $i => $val) {
            yield $val => [
                $regionId[$i],
                $startDate[$i],
                $endDate[$i],
                $progIds[$i],
                $timeBoxIds[$i],
                $channelIds[$i],
                $division[$i],
                $conditionCross[$i],
                $codes[$i],
                $targetProgramsBinds[$i],
            ];
        }
    }

    /**
     * @test
     * @dataProvider createMultiChannelProfileTablesMultichannelDataProvider
     * @param int $regionId
     * @param string $startDate
     * @param string $endDate
     * @param array $progIds
     * @param array $timeBoxIds
     * @param array $channelIds
     * @param ?string $division
     * @param ?array $conditionCross
     * @param ?array $codes
     * @param bool $isConditionCross
     * @param array $targetProgramsBinds
     * @param array $divCodeToEnqBindings
     * @param array $divCodeBindings
     */
    public function createMultiChannelProfileTablesMultichannel(int $regionId, string $startDate, string $endDate, array $progIds, array $timeBoxIds, array $channelIds, ?string $division, ?array $conditionCross, ?array $codes, bool $isConditionCross, array $targetProgramsBinds, array $divCodeToEnqBindings, array $divCodeBindings): void
    {
        $isEnq = false;
        $caseWhenArr = [
            [
                'name' => 'ga8_c',
                'divisionName' => '性・年齢8区分',
                'codeName' => 'C',
                'condition' => 'tbp.age BETWEEN :originalga8cagefrom AND :originalga8cageto ',
            ],
        ];
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($targetProgramsBinds, 'targetPrograms'))
            ->andReturn(['data'])
            ->once()->ordered();

        /////// createDivCodeMultiChannelProfileTables
        if ($isConditionCross) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createConditionCrossArray')
                ->with($conditionCross, Mockery::any())
                ->andReturn($caseWhenArr)
                ->once()->ordered();
        } else {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createCrossJoinArray')
                ->with($division, $codes, Mockery::any())
                ->andReturn($caseWhenArr)
                ->once()->ordered();
        }

        /// div_code_to_enq_format
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($divCodeToEnqBindings, 'divCodeToEnqBindings'))
            ->andReturn(['data'])
            ->once()->ordered();

        /// convert_enq
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($divCodeBindings, 'divCodeBindings'))
            ->andReturn(['data'])
            ->once()->ordered();

        /////// createMultiChannelProfileCommonTables
        // samples
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();
        // sample_numbers
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();
        // target_audience_data
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();
        // household_audience_data
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();
        // household_reports
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();
        // target_program_viewers
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();
        // personal_reports
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();
        // enq_samples
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();
        // enq_sample_numbers
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();
        // target_program_enq_master
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();
        // target_enq_viewers
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();
        // main_target_enq_viewers
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();
        // enq_rate_list
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();
        // channel_rate
        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any())->andReturn(['data'])->once()->ordered();

        $this->target->createMultiChannelProfileTables($isEnq, $regionId, $startDate, $endDate, $progIds, $timeBoxIds, $channelIds, $division, $conditionCross, $codes);
    }

    public function createMultiChannelProfileTablesMultichannelDataProvider()
    {
        // テストマトリクス
        $cases = ['case1', 'case2'];
        $regionId = [1, 2];
        $startDate = ['1990-06-14', '1990-06-14'];
        $endDate = ['1990-06-15', '1990-06-15'];
        $progIds = [['1', '2', '3'], ['1', '2', '3']];
        $timeBoxIds = [['44', '44', '44'], ['44', '44', '44']];
        $channelIds = [[1, 2, 3, 4, 5, 6, 7], [1, 2, 3, 4, 5, 6, 7]];
        $division = ['ga8', 'condition_cross'];
        $conditionCross = [[], ['cross']];
        $codes = [['c1'], []];
        $isConditionCross = [false, true];
        $targetProgramsBinds = [
            [
                ':startDate' => '1990-06-14',
                ':endDate' => '1990-06-15',
                ':regionId' => 1,
                ':progId0' => '1',
                ':timeBoxId1' => 44,
                ':progId2' => '2',
                ':timeBoxId3' => 44,
                ':progId4' => '3',
                ':timeBoxId5' => 44,
                ':channelIds0' => 1,
                ':channelIds1' => 2,
                ':channelIds2' => 3,
                ':channelIds3' => 4,
                ':channelIds4' => 5,
                ':channelIds5' => 6,
                ':channelIds6' => 7,
            ],
            [
                ':startDate' => '1990-06-14',
                ':endDate' => '1990-06-15',
                ':regionId' => 2,
                ':progId0' => '1',
                ':timeBoxId1' => 44,
                ':progId2' => '2',
                ':timeBoxId3' => 44,
                ':progId4' => '3',
                ':timeBoxId5' => 44,
                ':channelIds0' => 1,
                ':channelIds1' => 2,
                ':channelIds2' => 3,
                ':channelIds3' => 4,
                ':channelIds4' => 5,
                ':channelIds5' => 6,
                ':channelIds6' => 7,
            ],
        ];
        $divCodeToEnqBindings = [
            [
                ':enqId0' => 0,
                ':question0' => '性・年齢8区分',
                ':option0' => 'C',
                ':genreId0' => '',
            ],
            [
                ':enqId0' => 0,
                ':question0' => '性・年齢8区分',
                ':option0' => 'C',
                ':genreId0' => '',
            ],
        ];
        $divCodeBindings = [
            [
                ':enqId0' => 0,
            ],
            [
                ':enqId0' => 0,
            ],
        ];

        foreach ($cases as $i => $val) {
            yield $val => [
                $regionId[$i],
                $startDate[$i],
                $endDate[$i],
                $progIds[$i],
                $timeBoxIds[$i],
                $channelIds[$i],
                $division[$i],
                $conditionCross[$i],
                $codes[$i],
                $isConditionCross[$i],
                $targetProgramsBinds[$i],
                $divCodeToEnqBindings[$i],
                $divCodeBindings[$i],
            ];
        }
    }

    /**
     * @test
     * @dataProvider getDetailMultiChannelProfileResultsDataProvider
     * @param bool $isEnq
     * @param array $channelIds
     * @param int $ptThreshold
     * @param array $bindings
     */
    public function getDetailMultiChannelProfileResults(bool $isEnq, array $channelIds, int $ptThreshold, array $bindings): void
    {
        $expect = ['data'];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($bindings, 'bindings'))
            ->andReturn(['data'])
            ->once()->ordered();

        $actual = $this->target->getDetailMultiChannelProfileResults($isEnq, $channelIds, $ptThreshold);
        $this->assertEquals($expect, $actual);
    }

    public function getDetailMultiChannelProfileResultsDataProvider()
    {
        // テストマトリクス
        $cases = ['case1', 'case2'];
        $isEnq = [true, false];
        $channelIds = [[1, 2, 3, 4, 5, 6, 7], [1]];
        $ptThreshold = [1, 2];
        $bindings = [
            [
                ':channel_1' => 1,
                ':channel_2' => 2,
                ':channel_3' => 3,
                ':channel_4' => 4,
                ':channel_5' => 5,
                ':channel_6' => 6,
                ':channel_7' => 7,
                ':sampleThreshold' => 1,
            ],
            [
                ':channel_1' => 1,
            ],
        ];

        foreach ($cases as $i => $val) {
            yield $val => [
                $isEnq[$i],
                $channelIds[$i],
                $ptThreshold[$i],
                $bindings[$i],
            ];
        }
    }

    /**
     * @test
     * @dataProvider getHeaderProfileResultsDataProvider
     * @param array $channelIds
     * @param array $bindings
     */
    public function getHeaderProfileResults(array $channelIds, array $bindings): void
    {
        $expect = ['data'];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($bindings, 'bindings'))
            ->andReturn(['data'])
            ->once()->ordered();

        $actual = $this->target->getHeaderProfileResults($channelIds);
        $this->assertEquals($expect, $actual);
    }

    public function getHeaderProfileResultsDataProvider()
    {
        // テストマトリクス
        $cases = ['case1'];
        $channelIds = [[1, 2, 3, 4, 5, 6, 7]];
        $bindings = [
            [
                ':channel_1' => 1,
                ':channel_2' => 2,
                ':channel_3' => 3,
                ':channel_4' => 4,
                ':channel_5' => 5,
                ':channel_6' => 6,
                ':channel_7' => 7,
            ],
        ];

        foreach ($cases as $i => $val) {
            yield $val => [
                $channelIds[$i],
                $bindings[$i],
            ];
        }
    }

    /**
     * @test
     */
    public function getSelectedProgramsForProfile(): void
    {
        $expect = ['data'];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->andReturn(['data'])
            ->once()->ordered();

        $actual = $this->target->getSelectedProgramsForProfile();
        $this->assertEquals($expect, $actual);
    }

    private function bindAsserts($bindings, $caller)
    {
        return function ($query, $binds) use ($bindings, $caller) {
            $this->assertCount(count($bindings), $binds, substr($query, 0, 100) . $caller . print_r($binds, true));

            foreach ($bindings as $key => $val) {
                $this->assertEquals($val, $binds[$key], "${caller} ${binds}" . print_r($binds, true));
            }
            return true;
        };
    }
}
