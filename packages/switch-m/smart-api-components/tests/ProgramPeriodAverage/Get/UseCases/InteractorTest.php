<?php

namespace Switchm\SmartApi\Components\Tests\ProgramPeriodAverage\Get\UseCases;

use Prophecy\Argument as arg;
use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Components\Common\Exceptions\SampleCountException;
use Switchm\SmartApi\Components\ProgramPeriodAverage\Get\UseCases\InputData;
use Switchm\SmartApi\Components\ProgramPeriodAverage\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\ProgramPeriodAverage\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\ProgramPeriodAverage\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Queries\Dao\Dwh\ProgramDao;
use Switchm\SmartApi\Queries\Services\DivisionService;
use Switchm\SmartApi\Queries\Services\SampleService;

class InteractorTest extends TestCase
{
    private $programDao;

    private $divisionService;

    private $sampleService;

    private $searchConditionTextAppService;

    private $outputBoundary;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->programDao = $this->prophesize(ProgramDao::class);
        $this->divisionService = $this->prophesize(DivisionService::class);
        $this->sampleService = $this->prophesize(SampleService::class);
        $this->searchConditionTextAppService = $this->prophesize(SearchConditionTextAppService::class);
        $this->outputBoundary = $this->prophesize(OutputBoundary::class);

        $this->target = new Interactor(
            $this->programDao->reveal(),
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
    public function getHeader_csv(): void
    {
        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('getHeader');
        $method->setAccessible(true);

        $this->searchConditionTextAppService
            ->getPeriodAverageCsv(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->searchConditionTextAppService
            ->getPeriodAverageHeader(arg::cetera())
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
            ->getPeriodAverageCsv(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->searchConditionTextAppService
            ->getPeriodAverageHeader(arg::cetera())
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
    public function invoke_list_ga8_nodata(): void
    {
        $this->divisionService
            ->getCodeList(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->sampleService
            ->getConditionCrossCount(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->programDao
            ->periodAverageOriginal(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->programDao
            ->periodAverage(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->searchConditionTextAppService
            ->getPeriodAverageCsv(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->searchConditionTextAppService
            ->getPeriodAverageHeader(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->outputBoundary
            ->__invoke(new OutputData([], 1, 0, 0, '20190101', '20190107', []))
            ->willReturn()
            ->shouldBeCalled();

        $input = new InputData(
            // startDateTime
            '2019-01-01 05:00:00',
            // endDateTime
            '2019-01-07 05:00:00',
            // holiday
            true,
            // dataType
            [0],
            // wdays
            [1],
            // genres
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
            // channels
            [],
            // programTypes
            [],
            // dispAverage
            '',
            // dataTypeFlag
            ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            // BaseDivision
            ['ga8', 'ga12', 'ga10s', 'gm', 'oc'],
            // sampleMax
            50,
            // userId
            1,
            ['code' => 'code', 'number' => 'number'],
            'selected_personal',
            32
        );

        $this->target->__invoke($input);
    }

    /**
     * @test
     */
    public function invoke_csv_condition_cross(): void
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
            ->periodAverageOriginal(arg::cetera())
            ->willReturn(['list' => [], 'cnt' => (object) ['cnt' => 100]])
            ->shouldBeCalled();

        $this->programDao
            ->periodAverage(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->searchConditionTextAppService
            ->getPeriodAverageCsv(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->searchConditionTextAppService
            ->getPeriodAverageHeader(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->outputBoundary
            ->__invoke(new OutputData([], 1, 100, 100, '20190101', '20190107', []))
            ->willReturn()
            ->shouldBeCalled();

        $input = new InputData(
        // startDateTime
            '2019-01-01 05:00:00',
            // endDateTime
            '2019-01-07 05:00:00',
            // holiday
            true,
            // dataType
            [0],
            // wdays
            [1],
            // genres
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
            // channels
            [],
            // programTypes
            [],
            // dispAverage
            '',
            // dataTypeFlag
            ['isRt' => false, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            // BaseDivision
            ['ga8', 'ga12', 'ga10s', 'gm', 'oc'],
            // sampleMax
            50,
            // userId
            1,
            ['code' => 'code', 'number' => 'number'],
            'selected_personal',
            32
        );

        $this->target->__invoke($input);
    }
}
