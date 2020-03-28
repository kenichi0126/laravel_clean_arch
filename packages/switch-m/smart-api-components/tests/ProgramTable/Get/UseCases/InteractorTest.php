<?php

namespace Switchm\SmartApi\Components\Tests\ProgramTable\Get\UseCases;

use Prophecy\Argument as arg;
use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Components\ProgramTable\Get\UseCases\InputData;
use Switchm\SmartApi\Components\ProgramTable\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\ProgramTable\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\ProgramTable\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Queries\Dao\Dwh\ProgramDao;
use Switchm\SmartApi\Queries\Services\DivisionService;
use Switchm\SmartApi\Queries\Services\HolidayService;
use Switchm\SmartApi\Queries\Services\SampleService;

class InteractorTest extends TestCase
{
    private $programDao;

    private $rdbProgramDao;

    private $holidayService;

    private $sampleService;

    private $divisionService;

    private $searchConditionTextAppService;

    private $outputBoundary;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->programDao = $this->prophesize(ProgramDao::class);
        $this->rdbProgramDao = $this->prophesize(\Switchm\SmartApi\Queries\Dao\Rdb\ProgramDao::class);
        $this->holidayService = $this->prophesize(HolidayService::class);
        $this->sampleService = $this->prophesize(SampleService::class);
        $this->divisionService = $this->prophesize(DivisionService::class);
        $this->searchConditionTextAppService = $this->prophesize(SearchConditionTextAppService::class);
        $this->outputBoundary = $this->prophesize(OutputBoundary::class);

        $this->target = new Interactor(
            $this->programDao->reveal(),
            $this->rdbProgramDao->reveal(),
            $this->holidayService->reveal(),
            $this->sampleService->reveal(),
            $this->divisionService->reveal(),
            $this->searchConditionTextAppService->reveal(),
            $this->outputBoundary->reveal()
        );
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getData_all_false(): void
    {
        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('getData');
        $method->setAccessible(true);

        $this->rdbProgramDao
            ->table(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbProgramDao
            ->tableOriginal(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->programDao
            ->table(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->programDao
            ->tableOriginal(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $expected = ['list' => []];

        $period = [
            'rdbStartDate' => '20190101',
            'rdbEndDate' => '20190101',
            'dwhStartDate' => '20190101',
            'dwhEndDate' => '20190101',
            'isDwh' => false,
            'isRdb' => false,
        ];
        $division = 'ga8';
        $baseDivision = ['ga8', 'ga12'];

        $actual = $method->invoke(
            $this->target,
            $period,
            $division,
            $baseDivision,
            ['personal'],
            ['startDateTime', 'endDateTime', 'startTime', 'endTime', ['channels'], 'division', ['conditionCross'], ['codes'], false, 1]
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getData_isRdb_baseDivision(): void
    {
        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('getData');
        $method->setAccessible(true);

        $this->rdbProgramDao
            ->table(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->rdbProgramDao
            ->tableOriginal(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->programDao
            ->table(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->programDao
            ->tableOriginal(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $expected = [];

        $period = [
            'rdbStartDate' => '20190101',
            'rdbEndDate' => '20190101',
            'dwhStartDate' => '20190101',
            'dwhEndDate' => '20190101',
            'isDwh' => false,
            'isRdb' => true,
        ];
        $division = 'ga8';
        $baseDivision = ['ga8', 'ga12'];

        $actual = $method->invoke(
            $this->target,
            $period,
            $division,
            $baseDivision,
            ['personal'],
            ['startDateTime', 'endDateTime', 'startTime', 'endTime', ['channels'], 'division', ['conditionCross'], ['codes'], false, 1]
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getData_isRdb_originalDivision(): void
    {
        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('getData');
        $method->setAccessible(true);

        $this->rdbProgramDao
            ->table(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbProgramDao
            ->tableOriginal(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->programDao
            ->table(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->programDao
            ->tableOriginal(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $expected = [];

        $period = [
            'rdbStartDate' => '20190101',
            'rdbEndDate' => '20190101',
            'dwhStartDate' => '20190101',
            'dwhEndDate' => '20190101',
            'isDwh' => false,
            'isRdb' => true,
        ];
        $division = 'original';
        $baseDivision = ['ga8', 'ga12'];

        $actual = $method->invoke(
            $this->target,
            $period,
            $division,
            $baseDivision,
            ['original'],
            ['startDateTime', 'endDateTime', 'startTime', 'endTime', ['channels'], 'division', ['conditionCross'], ['codes'], false, 1]
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getData_isDwh_baseDivision(): void
    {
        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('getData');
        $method->setAccessible(true);

        $this->rdbProgramDao
            ->table(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbProgramDao
            ->tableOriginal(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->programDao
            ->table(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->programDao
            ->tableOriginal(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $expected = [];

        $period = [
            'rdbStartDate' => '20190101',
            'rdbEndDate' => '20190101',
            'dwhStartDate' => '20190101',
            'dwhEndDate' => '20190101',
            'isDwh' => true,
            'isRdb' => false,
        ];
        $division = 'ga8';
        $baseDivision = ['ga8', 'ga12'];

        $actual = $method->invoke(
            $this->target,
            $period,
            $division,
            $baseDivision,
            ['ga8'],
            ['startDateTime', 'endDateTime', 'startTime', 'endTime', ['channels'], 'division', ['conditionCross'], ['codes'], false, 1]
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getData_isDwh_originalDivision(): void
    {
        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('getData');
        $method->setAccessible(true);

        $this->rdbProgramDao
            ->table(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbProgramDao
            ->tableOriginal(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->programDao
            ->table(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->programDao
            ->tableOriginal(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $expected = [];

        $period = [
            'rdbStartDate' => '20190101',
            'rdbEndDate' => '20190101',
            'dwhStartDate' => '20190101',
            'dwhEndDate' => '20190101',
            'isDwh' => true,
            'isRdb' => false,
        ];
        $division = 'original';
        $baseDivision = ['ga8', 'ga12'];

        $actual = $method->invoke(
            $this->target,
            $period,
            $division,
            $baseDivision,
            ['original'],
            ['startDateTime', 'endDateTime', 'startTime', 'endTime', ['channels'], 'division', ['conditionCross'], ['codes'], false, 1]
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param mixed $digitalAndBs
     * @param mixed $channels
     * @param mixed $date
     * @param mixed $endDateTime
     * @param mixed $realStartedAt
     * @param mixed $realEndedAt
     * @param mixed $tbStartTime
     * @param mixed $tbEndTime
     * @param mixed $dispPeriod
     */
    public function invoke($digitalAndBs, $channels, $date, $endDateTime, $realStartedAt, $realEndedAt, $tbStartTime, $tbEndTime, $dispPeriod): void
    {
        $data = new \stdClass();
        $data->dow = '月';
        $data->date = $date;
        $data->real_started_at = $realStartedAt;
        $data->real_ended_at = $realEndedAt;
        $data->d = '1';
        $data->from_hh_mm = '28:55';
        $data->to_hh_mm = '08:00';
        $data->channel_code_name = 'EX';
        $data->title = 'グッド！モーニング';
        $data->rate = '2.7';
        $data->end_rate = '3.3';
        $data->prog_id = '201909301004550428';
        $data->genre_id = '20003';
        $data->time_box_id = 2257;
        $data->prepared = 1;
        $data->tb_start_time = $tbStartTime;
        $data->tb_end_time = $tbEndTime;
        $data->time_box_count = 1;

        $dateList = [
            [
                'carbon' => null,
                'date' => '',
                'holidayFlg' => false,
            ],
        ];

        $this->rdbProgramDao
            ->table(arg::cetera())
            ->willReturn(['list' => ['data' => $data]])
            ->shouldNotBeCalled();

        $this->rdbProgramDao
            ->tableOriginal(arg::cetera())
            ->willReturn(['list' => ['data' => $data]])
            ->shouldNotBeCalled();

        $this->programDao
            ->table(arg::cetera())
            ->willReturn(['list' => ['data' => $data]])
            ->shouldNotBeCalled();

        $this->programDao
            ->tableOriginal(arg::cetera())
            ->willReturn(['list' => ['data' => $data]])
            ->shouldBeCalled();

        $this->holidayService
            ->getDateList(arg::cetera())
            ->willReturn($dateList)
            ->shouldBeCalled();

        $this->sampleService
            ->getConditionCrossCount(arg::cetera())
            ->willReturn(100)
            ->shouldBeCalled();

        $this->divisionService
            ->getCodeList(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->searchConditionTextAppService
            ->getProgramTableHeader(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $result = ['data' => $data];

        $this->outputBoundary
            ->__invoke(new OutputData(
                $result,
                1,
                array_map(function ($v) {
                    unset($v['carbon']);
                    return $v;
                }, $dateList),
                []
            ))
            ->willReturn()
            ->shouldBeCalled();

        $input = new InputData(
        // startDateTime
            '2019-01-01 05:00:00',
            // endDateTime
            $endDateTime,
            // digitalAndBs
            $digitalAndBs,
            // digitalKanto
            $channels,
            // bs1
            [90],
            // bs2
            [91],
            // regionId
            1,
            // division
            'condition_cross',
            // conditionCross
            [],
            // draw
            1,
            // codes
            [],
            // channels
            [],
            // dispPeriod
            $dispPeriod,
            // baseDivision
            ['ga8', 'ga12', 'ga10s', 'gm', 'oc'],
            // period
            ['isDwh' => true],
            // userId
            1
        );

        $this->target->__invoke($input);
    }

    public function dataProvider()
    {
        return [
//          [$digitalAndBs, $channels, $date, $endDateTime, $realStartedAt, $realEndedAt, $tbStartTime, $tbEndTime, $dispPeriod],
            ['digital', [1, 2, 3], '2019-01-01', '2019-01-02 04:59:59', '2019-01-01 19:00:00', '2019-01-01 20:00:00', '2019-01-01 19:00:00', '2019-01-01 20:00:00', '6'],
            ['digital', [1], '2019-01-01', '2019-01-02 04:59:59', '2019-01-01 19:00:00', '2019-01-01 20:00:00', '2019-01-01 19:00:00', '2019-01-01 20:00:00', '6'],
            ['bs1', [1], '2019-01-01', '2019-01-02 04:59:59', '2019-01-01 4:00:00', '2019-01-02 20:00:00', '2019-01-01 19:00:00', '2019-01-01 20:00:00', '6'],
            ['bs2', [1], '2019-01-01', '2019-01-02 04:59:59', '2019-01-01 19:00:00', '2019-01-01 20:00:00', '2019-01-01 19:00:00', '2019-01-01 20:00:00', '24'],
            ['digital', [1], '2019-01-01', '2019-01-02 04:59:59', '2019-01-01 19:00:00', '2019-01-01 20:00:00', '2019-01-03 19:00:00', '2019-01-03 20:00:00', '24'],
            ['digital', [1], '2018-12-31', '2019-01-02 04:59:59', '2019-01-01 19:00:00', '2019-01-01 20:00:00', '2019-01-01 19:00:00', '2019-01-01 20:00:00', '24'],
            ['digital', [1], '2019-01-01', '2019-01-02 04:59:59', '2019-01-01 19:00:00', '2019-01-01 20:00:00', '2018-12-31 19:00:00', '2019-01-01 20:00:00', '24'],
        ];
    }
}
