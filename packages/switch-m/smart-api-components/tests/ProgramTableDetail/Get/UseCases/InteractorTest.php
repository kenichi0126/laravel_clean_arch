<?php

namespace Switchm\SmartApi\Components\Tests\ProgramTableDetail\Get\UseCases;

use Carbon\Carbon;
use Prophecy\Argument as arg;
use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Components\Common\SearchPeriod;
use Switchm\SmartApi\Components\ProgramTableDetail\Get\UseCases\InputData;
use Switchm\SmartApi\Components\ProgramTableDetail\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\ProgramTableDetail\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\ProgramTableDetail\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Queries\Dao\Dwh\PerMinuteReportDao;
use Switchm\SmartApi\Queries\Dao\Dwh\ProgramDao;
use Switchm\SmartApi\Queries\Dao\Rdb\AttrDivDao;
use Switchm\SmartApi\Queries\Dao\Rdb\MdataSceneDao;
use Switchm\SmartApi\Queries\Dao\Rdb\TimeBoxDao;

class InteractorTest extends TestCase
{
    private $dwhProgramDao;

    private $rdbProgramDao;

    private $dwhPerMinuteReportDao;

    private $rdbPerMinuteReportDao;

    private $mdataSceneDao;

    private $timeBoxDao;

    private $attrDivDao;

    private $searchPeriod;

    private $searchConditionTextAppService;

    private $outputBoundary;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->dwhProgramDao = $this->prophesize(ProgramDao::class);
        $this->rdbProgramDao = $this->prophesize(\Switchm\SmartApi\Queries\Dao\Rdb\ProgramDao::class);
        $this->dwhPerMinuteReportDao = $this->prophesize(PerMinuteReportDao::class);
        $this->rdbPerMinuteReportDao = $this->prophesize(\Switchm\SmartApi\Queries\Dao\Rdb\PerMinuteReportDao::class);
        $this->mdataSceneDao = $this->prophesize(MdataSceneDao::class);
        $this->timeBoxDao = $this->prophesize(TimeBoxDao::class);
        $this->attrDivDao = $this->prophesize(AttrDivDao::class);
        $this->searchPeriod = $this->prophesize(SearchPeriod::class);
        $this->searchConditionTextAppService = $this->prophesize(SearchConditionTextAppService::class);
        $this->outputBoundary = $this->prophesize(OutputBoundary::class);

        $this->target = new Interactor(
            $this->dwhProgramDao->reveal(),
            $this->rdbProgramDao->reveal(),
            $this->dwhPerMinuteReportDao->reveal(),
            $this->rdbPerMinuteReportDao->reveal(),
            $this->mdataSceneDao->reveal(),
            $this->timeBoxDao->reveal(),
            $this->attrDivDao->reveal(),
            $this->searchPeriod->reveal(),
            $this->searchConditionTextAppService->reveal(),
            $this->outputBoundary->reveal()
        );
    }

    /**
     * @test
     * @dataProvider getGraphIntervalDataProvider
     * @param mixed $minute
     * @param mixed $expected
     * @throws \ReflectionException
     */
    public function getGraphInterval($minute, $expected): void
    {
        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('getGraphInterval');
        $method->setAccessible(true);

        $actual = $method->invoke(
            $this->target,
            $minute
        );

        $this->assertSame($expected, $actual);
    }

    public function getGraphIntervalDataProvider()
    {
        return [
            [15, 1],
            [16, 2],
            [30, 2],
            [31, 3],
            [45, 3],
            [46, 5],
            [90, 5],
            [91, 10],
            [120, 10],
            [1200, 100],
        ];
    }

    /**
     * @test
     * @dataProvider getPerminuteDatabyDivCodeParamsDataProvider
     * @param mixed $start
     * @param mixed $end
     * @param mixed $regionId
     * @param mixed $expected
     * @throws \ReflectionException
     */
    public function getPerminuteDatabyDivCodeParams($start, $end, $regionId, $expected): void
    {
        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('getPerminuteDatabyDivCodeParams');
        $method->setAccessible(true);

        $actual = $method->invoke(
            $this->target,
            $start,
            $end,
            $regionId
        );

        $this->assertSame($expected, $actual);
    }

    public function getPerminuteDatabyDivCodeParamsDataProvider()
    {
        return [
            [new Carbon('2019-01-01 06:00:00'), new Carbon('2019-01-01 05:00:00'), 1, [[], [], [], [], [], []]],
            [new Carbon('2019-01-01 12:00:00'), new Carbon('2019-01-01 12:17:00'), 1, [['2019-01-01'], [11, 12], [59, 1, 3, 5, 7, 9, 11, 13, 15, 17], [null], ['11:59', '12:01', '12:03', '12:05', '12:07', '12:09', '12:11', '12:13', '12:15', '12:17'], ['2019-01-01 11:59', '2019-01-01 12:01', '2019-01-01 12:03', '2019-01-01 12:05', '2019-01-01 12:07', '2019-01-01 12:09', '2019-01-01 12:11', '2019-01-01 12:13', '2019-01-01 12:15', '2019-01-01 12:17']]],
        ];
    }

    /**
     * @test
     * @dataProvider perMinRpForGraphDataProvider
     * @param mixed $program
     * @param mixed $regionId
     * @param mixed $division
     * @param mixed $expected
     * @throws \ReflectionException
     */
    public function perMinRpForGraph($program, $regionId, $division, $expected): void
    {
        $this->attrDivDao
            ->getCode(arg::cetera())
            ->willReturn(['list' => [[(object) ['code' => 'fc', 'name' => 'FC']], [(object) ['code' => 'ft', 'name' => 'FT']]]]);

        $this->searchPeriod
            ->getRdbDwhSearchPeriod(arg::cetera())
            ->willReturn(['isRdb' => false, 'isDwh' => false]);

        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('perMinRpForGraph');
        $method->setAccessible(true);

        $actual = $method->invoke(
            $this->target,
            $program,
            $regionId,
            $division,
            2,
            0
        );

        $this->assertSame($expected, $actual);
    }

    public function perMinRpForGraphDataProvider()
    {
        return [
            [(object) ['real_started_at' => '2019-01-01 12:00:00', 'real_ended_at' => '2019-01-01 12:01:00'], '1', 'ga8',  ['graph' => [], 'xAxis' => [], 'personalMax' => '-', 'personalMin' => '-', 'householdMax' => '-', 'householdMin' => '-', 'minuteFlg' => false]],
            [(object) ['real_started_at' => '2019-01-01 12:00:00', 'real_ended_at' => '2019-01-01 12:05:00', 'channel_id' => '1'], '1', 'ga8',
                [
                    'graph' => [
                        ['name' => '個人', 'data' => [0, 0, 0, 0, 0, 0]],
                        ['name' => null, 'data' => [0, 0, 0, 0, 0, 0]],
                        ['name' => null, 'data' => [0, 0, 0, 0, 0, 0]],
                        ['name' => '世帯', 'data' => [0, 0, 0, 0, 0, 0]],
                    ],
                    'xAxis' => ['12:00', '12:01', '12:02', '12:03', '12:04', '12:05'],
                    'personalMax' => 0, 'personalMin' => 0, 'householdMax' => 0, 'householdMin' => 0, 'minuteFlg' => true, ], ],
            [(object) ['real_started_at' => '2019-01-01 12:00:00', 'real_ended_at' => '2019-01-01 12:01:00'], '1', 'ga8',  ['graph' => [], 'xAxis' => [], 'personalMax' => '-', 'personalMin' => '-', 'householdMax' => '-', 'householdMin' => '-', 'minuteFlg' => false]],
        ];
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getPerMinuteDataByDivCode_all_if_false(): void
    {
        $this->searchPeriod
            ->getRdbDwhSearchPeriod(arg::cetera())
            ->willReturn(['isRdb' => false, 'isDwh' => false]);

        $this->rdbPerMinuteReportDao
            ->getSeconds(arg::cetera())
            ->willReturn()
            ->shouldNotBeCalled();

        $this->dwhPerMinuteReportDao
            ->getSeconds(arg::cetera())
            ->willReturn()
            ->shouldNotBeCalled();

        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('getPerMinuteDataByDivCode');
        $method->setAccessible(true);

        $actual = $method->invoke(
            $this->target,
            [],
            ['20190101'],
            [],
            [],
            1,
            'ga8',
            ['2019-10-27 09:55'],
            [(object) ['code' => 'fc', 'name' => 'FC']],
            2,
            0
        );

        $this->assertSame(['ga8fc' => [0]], $actual);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getPerMinuteDataByDivCode_all_if_true(): void
    {
        $this->searchPeriod
            ->getRdbDwhSearchPeriod(arg::cetera())
            ->willReturn(['isRdb' => true, 'isDwh' => false]);

        $this->rdbPerMinuteReportDao
            ->getTableDetailReport(arg::cetera())
            ->willReturn([(object) ['concatdate' => '2019-01-01 12:00', 'rate' => '0.50000', 'division' => 'personal', 'code' => '1']])
            ->shouldBeCalled();

        $this->dwhPerMinuteReportDao
            ->getTableDetailReport(arg::cetera())
            ->willReturn()
            ->shouldNotBeCalled();

        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('getPerMinuteDataByDivCode');
        $method->setAccessible(true);

        $actual = $method->invoke(
            $this->target,
            [],
            ['20190101'],
            [],
            [],
            1,
            'personal',
            ['2019-01-01 12:00'],
            [(object) ['code' => '1', 'name' => '個人'], (object) ['code' => '1', 'name' => '世帯']],
            2,
            0
        );

        $this->assertSame(['personal1' => [0.5], 'household1' => [0]], $actual);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getOnePerMinuteDataByDivCode_false(): void
    {
        $this->searchPeriod
            ->getRdbDwhSearchPeriod(arg::cetera())
            ->willReturn(['isRdb' => true, 'isDwh' => false]);

        $this->rdbPerMinuteReportDao
            ->getSeconds(arg::cetera())
            ->willReturn([(object) ['concatdate' => '2019-01-01 12:00', 'rate' => '0.50000', 'division' => 'personal', 'code' => '1']])
            ->shouldBeCalled();

        $this->dwhPerMinuteReportDao
            ->getSeconds(arg::cetera())
            ->willReturn()
            ->shouldNotBeCalled();

        $this->timeBoxDao
            ->getNumber(arg::cetera())
            ->willReturn()
            ->shouldNotBeCalled();

        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('getOnePerMinuteDataByDivCode');
        $method->setAccessible(true);

        $actual = $method->invoke(
            $this->target,
            [['startDateTime' => '2019-01-01 12:00:00', 'endDateTime' => '2019-01-01 13:00:00']],
            (object) ['division' => 'ga8', 'time_box_id' => 1, 'channel_id' => 1],
            2,
            0
        );

        $this->assertSame(['personal' => ['0.0'], 'household' => ['0.0']], $actual);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getOnePerMinuteDataByDivCode_true(): void
    {
        $this->searchPeriod
            ->getRdbDwhSearchPeriod(arg::cetera())
            ->willReturn(['isRdb' => true, 'isDwh' => false]);

        $this->rdbPerMinuteReportDao
            ->getSeconds(arg::cetera())
            ->willReturn([(object) ['time_group' => '0', 'concatdate' => '2019-01-01 12:00', 'rate' => '0.50000', 'division' => 'personal', 'code' => '1']])
            ->shouldBeCalled();

        $this->dwhPerMinuteReportDao
            ->getSeconds(arg::cetera())
            ->willReturn()
            ->shouldNotBeCalled();

        $this->timeBoxDao
            ->getNumber(arg::cetera())
            ->willReturn((object) ['panelers_number' => 100, 'households_number' => 100])
            ->shouldBeCalled();

        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('getOnePerMinuteDataByDivCode');
        $method->setAccessible(true);

        $actual = $method->invoke(
            $this->target,
            [['personal' => 'personal', 'startDateTime' => '2019-01-01 12:00:00', 'endDateTime' => '2019-01-01 13:00:00']],
            (object) ['division' => 'ga8', 'time_box_id' => 1, 'channel_id' => 1],
            2,
            0
        );

        $this->assertSame(['personal' => ['0.0'], 'household' => ['0.0']], $actual);
    }

    /**
     * @test
     * @dataProvider convert28TimeDataProvider
     * @param mixed $dateTime
     * @param mixed $expected
     * @throws \ReflectionException
     */
    public function convert28Time($dateTime, $expected): void
    {
        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('convert28Time');
        $method->setAccessible(true);

        $actual = $method->invoke(
            $this->target,
            $dateTime
        );

        $this->assertSame($expected, $actual);
    }

    public function convert28TimeDataProvider()
    {
        return [
            ['2019-01-01 04:00:00', '28:00:00'],
            ['2019-01-01 06:00:00', '6:00:00'],
        ];
    }

    /**
     * @test
     * @dataProvider roundOneDataProvider
     * @param mixed $val
     * @param mixed $expected
     * @throws \ReflectionException
     */
    public function roundOne($val, $expected): void
    {
        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('roundOne');
        $method->setAccessible(true);

        $actual = $method->invoke(
            $this->target,
            $val
        );

        $this->assertSame($expected, $actual);
    }

    public function roundOneDataProvider()
    {
        return [
            [null, '0.0'],
            ['1.234', '1.2'],
        ];
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getSeconds_all_false(): void
    {
        $this->searchPeriod
            ->getRdbDwhSearchPeriod(arg::cetera())
            ->willReturn(['isRdb' => false, 'isDwh' => false])
            ->shouldBeCalled();

        $this->rdbPerMinuteReportDao
            ->getSeconds(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->dwhPerMinuteReportDao
            ->getSeconds(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('getSeconds');
        $method->setAccessible(true);

        $expected = [];

        $actual = $method->invoke(
            $this->target,
            [['startDateTime' => '20190101', 'endDateTime' => '20190102']],
            '1',
            2,
            0
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getSeconds_isRdb(): void
    {
        $this->searchPeriod
            ->getRdbDwhSearchPeriod(arg::cetera())
            ->willReturn(['isRdb' => true, 'isDwh' => false])
            ->shouldBeCalled();

        $this->rdbPerMinuteReportDao
            ->getSeconds(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->dwhPerMinuteReportDao
            ->getSeconds(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('getSeconds');
        $method->setAccessible(true);

        $expected = [];

        $actual = $method->invoke(
            $this->target,
            [['startDateTime' => '20190101', 'endDateTime' => '20190102']],
            '1',
            2,
            0
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getSeconds_isDwh(): void
    {
        $this->searchPeriod
            ->getRdbDwhSearchPeriod(arg::cetera())
            ->willReturn(['isRdb' => false, 'isDwh' => true])
            ->shouldBeCalled();

        $this->rdbPerMinuteReportDao
            ->getSeconds(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->dwhPerMinuteReportDao
            ->getSeconds(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('getSeconds');
        $method->setAccessible(true);

        $expected = [];

        $actual = $method->invoke(
            $this->target,
            [['startDateTime' => '20190101', 'endDateTime' => '20190102']],
            '1',
            2,
            0
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getTableDetailReport(): void
    {
        $this->searchPeriod
            ->getRdbDwhSearchPeriod(arg::cetera())
            ->willReturn(['isRdb' => false, 'isDwh' => false])
            ->shouldBeCalled();

        $this->rdbPerMinuteReportDao
            ->getTableDetailReport(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->dwhPerMinuteReportDao
            ->getTableDetailReport(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('getTableDetailReport');
        $method->setAccessible(true);

        $expected = [];

        $actual = $method->invoke(
            $this->target,
            [],
            ['20190101'],
            [],
            [],
            '',
            '',
            2,
            0
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getTableDetailReport_isRdb(): void
    {
        $this->searchPeriod
            ->getRdbDwhSearchPeriod(arg::cetera())
            ->willReturn(['isRdb' => true, 'isDwh' => false])
            ->shouldBeCalled();

        $this->rdbPerMinuteReportDao
            ->getTableDetailReport(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->dwhPerMinuteReportDao
            ->getTableDetailReport(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('getTableDetailReport');
        $method->setAccessible(true);

        $expected = [];

        $actual = $method->invoke(
            $this->target,
            [],
            ['20190101'],
            [],
            [],
            '',
            '',
            2,
            0
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getTableDetailReport_isDwh(): void
    {
        $this->searchPeriod
            ->getRdbDwhSearchPeriod(arg::cetera())
            ->willReturn(['isRdb' => false, 'isDwh' => true])
            ->shouldBeCalled();

        $this->rdbPerMinuteReportDao
            ->getTableDetailReport(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->dwhPerMinuteReportDao
            ->getTableDetailReport(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('getTableDetailReport');
        $method->setAccessible(true);

        $expected = [];

        $actual = $method->invoke(
            $this->target,
            [],
            ['20190101'],
            [],
            [],
            '',
            '',
            2,
            0
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function invoke_no_data(): void
    {
        $this->dwhProgramDao
            ->findProgram(arg::cetera())
            ->willReturn(null)
            ->shouldBeCalled();

        $this->outputBoundary
            ->__invoke(new OutputData(null, null))
            ->willReturn()
            ->shouldBeCalled();

        $input = new InputData(
            // regionId
            '1',
            // division
            'ga8',
            // progId
            '123',
            // timeBoxId
            1,
            // subDate
            2,
            // boundary
            0
        );

        $this->target->__invoke($input);
    }

    /**
     * @test
     */
    public function invoke_not_prepared(): void
    {
        $this->dwhProgramDao
            ->findProgram(arg::cetera())
            ->willReturn((object) ['real_started_at' => '20190101 12:00:00', 'real_ended_at' => '20190101 13:00:00'])
            ->shouldBeCalled();

        $this->searchPeriod
            ->getRdbDwhSearchPeriod(arg::cetera())
            ->willReturn(['isRdb' => true, 'isDwh' => false])
            ->shouldBeCalled();

        $this->rdbProgramDao
            ->findProgram(arg::cetera())
            ->willReturn((object) ['real_started_at' => '20190101 12:00:00', 'real_ended_at' => '20190101 13:00:00'])
            ->shouldBeCalled();

        $this->outputBoundary
            ->__invoke(new OutputData([
                'channelName' => null,
                'title' => null,
                'date' => '2019年01月01日(Tue)',
                'fromTime' => '12:00:00',
                'toTime' => '13:00:00',
                'personalAvg' => '-',
                'personalMax' => '-',
                'personalMin' => '-',
                'personalEnd' => '-',
                'householdAvg' => '-',
                'householdMax' => '-',
                'householdMin' => '-',
                'householdEnd' => '-',
                'prepared' => false,
                'minuteFlg' => false,
                'graph' => [],
                'xAxis' => [],
            ], []))
            ->willReturn()
            ->shouldBeCalled();

        $input = new InputData(
        // regionId
            '1',
            // division
            'ga8',
            // progId
            '123',
            // timeBoxId
            1,
            // subDate
            2,
            // boundary
            0
        );

        $this->target->__invoke($input);
    }

    /**
     * @test
     */
    public function invoke_prepared(): void
    {
        $this->dwhProgramDao
            ->findProgram(arg::cetera())
            ->willReturn((object) ['real_started_at' => '20190101 12:00:00', 'real_ended_at' => '20190101 13:00:00', 'prepared' => true, 'channel_id' => 1])
            ->shouldBeCalled();

        $this->searchPeriod
            ->getRdbDwhSearchPeriod(arg::cetera())
            ->willReturn(['isRdb' => true, 'isDwh' => false])
            ->shouldBeCalled();

        $this->rdbProgramDao
            ->findProgram(arg::cetera())
            ->willReturn((object) ['real_started_at' => '20190101 12:00:00', 'real_ended_at' => '20190101 13:00:00', 'prepared' => true, 'channel_id' => 1])
            ->shouldBeCalled();

        $this->attrDivDao
            ->getCode(arg::cetera())
            ->willReturn(['list' => ['ga8']])
            ->shouldBeCalled();

        $this->rdbPerMinuteReportDao
            ->getTableDetailReport(arg::cetera())
            ->willReturn([(object) ['startDateTime' => '20190101 12:00:00', 'endDateTime' => '20190101 13:00:00', 'tm_start' => '20190101']])
            ->shouldBeCalled();

        $this->mdataSceneDao
            ->findMdataScenes(arg::cetera())
            ->willReturn([(object) ['startDateTime' => '20190101 12:00:00', 'endDateTime' => '20190101 13:00:00', 'tm_start' => '20190101']])
            ->shouldBeCalled();

        $this->rdbPerMinuteReportDao
            ->getSeconds(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->outputBoundary
            ->__invoke(new OutputData(
                [
                'channelName' => null,
                'title' => null,
                'date' => '2019年01月01日(Tue)',
                'fromTime' => '12:00:00',
                'toTime' => '13:00:00',
                'personalAvg' => '0.0',
                'personalMax' => '0',
                'personalMin' => '0',
                'personalEnd' => '0.0',
                'householdAvg' => '0.0',
                'householdMax' => '0',
                'householdMin' => '0',
                'householdEnd' => '0.0',
                'prepared' => true,
                'minuteFlg' => true,
                'graph' => [
                    ['name' => '個人', 'data' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]],
                    ['name' => null, 'data' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]],
                    ['name' => '世帯', 'data' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]],
                ],
                'xAxis' => ['12:00', '12:05', '12:10', '12:15', '12:20', '12:25', '12:30', '12:35', '12:40', '12:45', '12:50', '12:55', '13:00'],
            ],
                [['24:00:00', '', null, '0.0', '0.0']]
            ))
            ->willReturn()
            ->shouldBeCalled();

        $input = new InputData(
        // regionId
            '1',
            // division
            'ga8',
            // progId
            '123',
            // timeBoxId
            1,
            // subDate
            2,
            // boundary
            0
        );

        $this->target->__invoke($input);
    }
}
