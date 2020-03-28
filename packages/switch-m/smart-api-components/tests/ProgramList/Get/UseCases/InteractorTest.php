<?php

namespace Switchm\SmartApi\Components\Tests\ProgramList\Get\UseCases;

use Prophecy\Argument as arg;
use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Components\Common\Exceptions\SampleCountException;
use Switchm\SmartApi\Components\ProgramList\Get\UseCases\InputData;
use Switchm\SmartApi\Components\ProgramList\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\ProgramList\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\ProgramList\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Queries\Dao\Dwh\ProgramDao;
use Switchm\SmartApi\Queries\Services\DivisionService;
use Switchm\SmartApi\Queries\Services\SampleService;

class InteractorTest extends TestCase
{
    private $programDao;

    private $rdbProgramDao;

    private $divisionService;

    private $sampleService;

    private $searchConditionTextAppService;

    private $outputBoundary;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->programDao = $this->prophesize(ProgramDao::class);
        $this->rdbProgramDao = $this->prophesize(\Switchm\SmartApi\Queries\Dao\Rdb\ProgramDao::class);
        $this->divisionService = $this->prophesize(DivisionService::class);
        $this->sampleService = $this->prophesize(SampleService::class);
        $this->searchConditionTextAppService = $this->prophesize(SearchConditionTextAppService::class);
        $this->outputBoundary = $this->prophesize(OutputBoundary::class);

        $this->target = new Interactor(
            $this->programDao->reveal(),
            $this->rdbProgramDao->reveal(),
            $this->divisionService->reveal(),
            $this->sampleService->reveal(),
            $this->searchConditionTextAppService->reveal(),
            $this->outputBoundary->reveal()
        );
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function checkSampleCount_exception(): void
    {
        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('checkSampleCount');
        $method->setAccessible(true);

        $this->sampleService
            ->getConditionCrossCount(arg::cetera())
            ->willReturn(0)
            ->shouldBeCalled();

        $dataTypeFlags = ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isRtTotal' => false];

        $this->expectException(SampleCountException::class);

        $method->invoke(
            $this->target,
            $dataTypeFlags,
            50,
            [],
            '2019-01-01',
            '2019-01-07',
            1
        );
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function checkSampleCount_exception_timeshift(): void
    {
        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('checkSampleCount');
        $method->setAccessible(true);

        $this->sampleService
            ->getConditionCrossCount(arg::cetera())
            ->willReturn(0)
            ->shouldBeCalled();

        $dataTypeFlags = ['isRt' => false, 'isTs' => true, 'isGross' => false, 'isRtTotal' => false];

        $this->expectException(SampleCountException::class);

        $method->invoke(
            $this->target,
            $dataTypeFlags,
            50,
            [],
            '2019-01-01',
            '2019-01-07',
            1
        );
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function checkSampleCount_no_exception(): void
    {
        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('checkSampleCount');
        $method->setAccessible(true);

        $this->sampleService
            ->getConditionCrossCount(arg::cetera())
            ->willReturn(100)
            ->shouldBeCalled();

        $dataTypeFlags = ['isRt' => true, 'isTs' => true, 'isGross' => false, 'isRtTotal' => false];

        $method->invoke(
            $this->target,
            $dataTypeFlags,
            50,
            [],
            '2019-01-01',
            '2019-01-07',
            1
        );
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getData_original(): void
    {
        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('getData');
        $method->setAccessible(true);

        $this->programDao
            ->searchOriginal(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->rdbProgramDao
            ->search(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->programDao
            ->search(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $expected = [];

        $division = 'original';
        $baseDivision = ['ga8', 'ga12'];
        $csvFlag = '0';
        $bsFlag = false;
        $hasPermission = false;

        $actual = $method->invoke(
            $this->target,
            $division,
            $baseDivision,
            $csvFlag,
            $bsFlag,
            $hasPermission,
            ['', '', '', '', [], false, [], [], [], '', [], [], null, null, 1, null, false, false, '', false, []],
            [],
            ['const'],
            ['prefixes'],
            'selected_personal',
            32
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getData_baseDivision_not_csv(): void
    {
        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('getData');
        $method->setAccessible(true);

        $this->programDao
            ->searchOriginal(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbProgramDao
            ->search(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->programDao
            ->search(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $expected = [];

        $division = 'ga8';
        $baseDivision = ['ga8', 'ga12'];
        $csvFlag = '0';
        $bsFlag = false;
        $hasPermission = false;

        $actual = $method->invoke(
            $this->target,
            $division,
            $baseDivision,
            $csvFlag,
            $bsFlag,
            $hasPermission,
            ['', '', '', '', [], false, [], [], [], '', [], [], null, null, 1, null, false, false, '', false, []],
            [],
            ['const'],
            ['prefixes'],
            'selected_personal',
            32
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getData_not_bs_csv(): void
    {
        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('getData');
        $method->setAccessible(true);

        $this->programDao
            ->searchOriginal(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbProgramDao
            ->search(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->programDao
            ->search(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $expected = [];

        $division = 'ga8';
        $baseDivision = ['ga8', 'ga12'];
        $csvFlag = '1';
        $bsFlag = false;
        $hasPermission = true;

        $actual = $method->invoke(
            $this->target,
            $division,
            $baseDivision,
            $csvFlag,
            $bsFlag,
            $hasPermission,
            ['', '', '', '', [], false, [], [], [], '', [], [], null, null, 1, null, false, false, '', false, []],
            [],
            ['const'],
            ['prefixes'],
            'selected_personal',
            32
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getData_ohter(): void
    {
        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('getData');
        $method->setAccessible(true);

        $this->programDao
            ->searchOriginal(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbProgramDao
            ->search(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->programDao
            ->search(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $expected = [];

        $division = 'ga8';
        $baseDivision = ['ga8', 'ga12'];
        $csvFlag = '1';
        $bsFlag = false;
        $hasPermission = false;

        $actual = $method->invoke(
            $this->target,
            $division,
            $baseDivision,
            $csvFlag,
            $bsFlag,
            $hasPermission,
            ['', '', '', '', [], false, [], [], [], '', [], [], null, null, 1, null, false, false, '', false, []],
            [],
            ['const'],
            ['prefixes'],
            'selected_personal',
            32
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getHeader_csv(): void
    {
        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('getHeader');
        $method->setAccessible(true);

        $this->searchConditionTextAppService
            ->getProgramListCsv(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->searchConditionTextAppService
            ->getProgramListHeader(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $expected = [];

        $csvFlag = '1';

        $actual = $method->invoke(
            $this->target,
            $csvFlag,
            ['', '', '', '', [], false, [], [], [], '', [], [], null, null, 1, null, false, false, '', false, [], 1, 1, '', []]
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getHeader_list(): void
    {
        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('getHeader');
        $method->setAccessible(true);

        $this->searchConditionTextAppService
            ->getProgramListCsv(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->searchConditionTextAppService
            ->getProgramListHeader(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $expected = [];

        $csvFlag = '0';

        $actual = $method->invoke(
            $this->target,
            $csvFlag,
            ['', '', '', '', [], false, [], [], [], '', [], [], null, null, 1, null, false, false, '', false, [], 1, 1, '', []]
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function invoke_digital_list_ga8(): void
    {
        $this->divisionService
            ->getCodeList(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->sampleService
            ->getConditionCrossCount(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->programDao
            ->searchOriginal(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbProgramDao
            ->search(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->programDao
            ->search(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->searchConditionTextAppService
            ->getProgramListCsv(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->searchConditionTextAppService
            ->getProgramListHeader(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->outputBoundary
            ->__invoke(new OutputData(null, 1, null, '20190101', '20190107', []))
            ->willReturn()
            ->shouldBeCalled();

        $input = new InputData(
            // startDateTime
            '2019-01-01 05:00:00',
            // endDateTime
            '2019-01-07 05:00:00',
            // digitalAndBs
            'digital',
            // digitalKanto
            [1],
            // bs1
            [98],
            // bs2
            [99],
            // holiday
            'true',
            // dataType
            [0],
            // wdays
            [1],
            // genres
            [],
            // programNames
            ['test'],
            // order
            [],
            // dispCount
            20,
            // dateRange
            100,
            // page
            1,
            // regionId
            1,
            // division
            'ga8',
            // conditionCross
            [],
            // csvFlag
            '0',
            // draw
            1,
            // codes
            [],
            // dataTypeFlag
            ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            // userId
            1,
            // hasPermission
            true,
            // BaseDivision
            ['ga8', 'ga12', 'ga10s', 'gm', 'oc'],
            // sampleMax
            50,
            ['rt' => 0, 'ts' => 1, 'total' => 3, 'gross' => 2, 'rtTotal' => 4],
            ['code' => 'code', 'number' => 'number'],
            'selected_personal',
            32
        );

        $this->target->__invoke($input);
    }

    /**
     * @test
     */
    public function invoke_bs1_csv_condition_cross(): void
    {
        $this->divisionService
            ->getCodeList(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->sampleService
            ->getConditionCrossCount(arg::cetera())
            ->willReturn(100)
            ->shouldBeCalled();

        $this->programDao
            ->searchOriginal(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->rdbProgramDao
            ->search(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->programDao
            ->search(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->searchConditionTextAppService
            ->getProgramListCsv(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->searchConditionTextAppService
            ->getProgramListHeader(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->outputBoundary
            ->__invoke(new OutputData(null, 1, null, '20190101', '20190107', []))
            ->willReturn()
            ->shouldBeCalled();

        $input = new InputData(
        // startDateTime
            '2019-01-01 05:00:00',
            // endDateTime
            '2019-01-07 05:00:00',
            // digitalAndBs
            'bs1',
            // digitalKanto
            [1],
            // bs1
            [98],
            // bs2
            [99],
            // holiday
            'true',
            // dataType
            [0],
            // wdays
            [1],
            // genres
            [],
            // programNames
            ['test'],
            // order
            [],
            // dispCount
            20,
            // dateRange
            100,
            // page
            1,
            // regionId
            1,
            // division
            'condition_cross',
            // conditionCross
            [],
            // csvFlag
            '1',
            // draw
            1,
            // codes
            [],
            // dataTypeFlag
            ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            // userId
            1,
            // hasPermission
            true,
            // BaseDivision
            ['ga8', 'ga12', 'ga10s', 'gm', 'oc'],
            // sampleMax
            50,
            ['rt' => 0, 'ts' => 1, 'total' => 3, 'gross' => 2, 'rtTotal' => 4],
            ['code' => 'code', 'number' => 'number'],
            'selected_personal',
            32
        );

        $this->target->__invoke($input);
    }

    /**
     * @test
     */
    public function invoke_bs2_csv_condition_cross(): void
    {
        $this->divisionService
            ->getCodeList(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->sampleService
            ->getConditionCrossCount(arg::cetera())
            ->willReturn(100)
            ->shouldBeCalled();

        $this->programDao
            ->searchOriginal(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->rdbProgramDao
            ->search(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->programDao
            ->search(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->searchConditionTextAppService
            ->getProgramListCsv(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->searchConditionTextAppService
            ->getProgramListHeader(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->outputBoundary
            ->__invoke(new OutputData(null, 1, null, '20190101', '20190107', []))
            ->willReturn()
            ->shouldBeCalled();

        $input = new InputData(
        // startDateTime
            '2019-01-01 05:00:00',
            // endDateTime
            '2019-01-07 05:00:00',
            // digitalAndBs
            'bs2',
            // digitalKanto
            [1],
            // bs1
            [98],
            // bs2
            [99],
            // holiday
            'true',
            // dataType
            [0],
            // wdays
            [1],
            // genres
            [],
            // programNames
            ['test'],
            // order
            [],
            // dispCount
            20,
            // dateRange
            100,
            // page
            1,
            // regionId
            1,
            // division
            'condition_cross',
            // conditionCross
            [],
            // csvFlag
            '1',
            // draw
            1,
            // codes
            [],
            // dataTypeFlag
            ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            // userId
            1,
            // hasPermission
            true,
            // BaseDivision
            ['ga8', 'ga12', 'ga10s', 'gm', 'oc'],
            // sampleMax
            50,
            ['rt' => 0, 'ts' => 1, 'total' => 3, 'gross' => 2, 'rtTotal' => 4],
            ['code' => 'code', 'number' => 'number'],
            'selected_personal',
            32
        );

        $this->target->__invoke($input);
    }
}
