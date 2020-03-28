<?php

namespace Switchm\SmartApi\Components\Tests\HourlyReport\Get\UseCases;

use Carbon\Carbon;
use Prophecy\Argument as arg;
use Switchm\SmartApi\Components\HourlyReport\Get\UseCases\InputData;
use Switchm\SmartApi\Components\HourlyReport\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\HourlyReport\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\HourlyReport\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Queries\Dao\Rdb\HourlyReportDao;

class InteractorTest extends TestCase
{
    private $hourlyReportDao;

    private $outputBoundary;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2019, 1, 1, 5, 0, 0));

        $this->hourlyReportDao = $this->prophesize(HourlyReportDao::class);
        $this->outputBoundary = $this->prophesize(OutputBoundary::class);

        $this->target = new Interactor($this->hourlyReportDao->reveal(), $this->outputBoundary->reveal());
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param $datetime
     * @param $trialSetting
     * @param $expected
     */
    public function invoke($datetime, $trialSetting, $expected): void
    {
        $data = (object) ['datetime' => $datetime];

        $this->hourlyReportDao
            ->latest(arg::cetera())
            ->willReturn($data)
            ->shouldBeCalled();

        $this->outputBoundary
            ->__invoke(new OutputData((array) ['datetime' => $expected]))
            ->willReturn()
            ->shouldBeCalled();

        $input = new InputData(
            1,
            $trialSetting
        );

        $this->target->__invoke($input);
    }

    public function dataProvider()
    {
        return [
            ['20190101', ['search_range' => ['start' => '2019-01-01', 'end' => '2019-01-07']], '20190101'],
            [null, null, '2019-01-01'],
            ['2019010', ['search_range' => ['start' => '2019-01-01', 'end' => '2019-01-07']], '2019-01-07 00:00:00'],
        ];
    }
}
