<?php

namespace Switchm\SmartApi\Queries\Tests\Services;

use Carbon\Carbon;
use Prophecy\Argument as arg;
use Prophecy\Prophecy\ObjectProphecy;
use Switchm\SmartApi\Queries\Dao\Rdb\HolidayDao;
use Switchm\SmartApi\Queries\Services\HolidayService;
use Switchm\SmartApi\Queries\Tests\TestCase;

class HolidayServiceTest extends TestCase
{
    /**
     * @var ObjectProphecy
     */
    private $holidayDao;

    /**
     * @var HolidayService
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->holidayDao = $this->prophesize(HolidayDao::class);
        $this->target = new HolidayService($this->holidayDao->reveal());
    }

    /**
     * @test
     */
    public function getHolidays(): void
    {
        $records = [
            ['holiday' => '2010-11-03'],
        ];

        $this->holidayDao
            ->findHoliday(arg::cetera())
            ->willReturn($records)
            ->shouldBeCalled();

        $start = Carbon::parse('2010-11-03');
        $end = Carbon::parse('2010-11-04');

        $holidays = (function () use ($records): array {
            $list = [];

            array_walk_recursive($records, function ($e) use (&$list): void {
                $list[] = $e;
            });

            return $list;
        })();

        $expected = [
            [
                'carbon' => $start,
                'date' => $start->format('Y-m-d H:i:s'),
                'holidayFlg' => in_array($start->format('Y-m-d'), $holidays),
            ],
            [
                'carbon' => $end,
                'date' => $end->format('Y-m-d H:i:s'),
                'holidayFlg' => in_array($end->format('Y-m-d'), $holidays),
            ],
        ];

        $actual = $this->target->getDateList($start, $end);

        $this->assertEquals($expected, $actual);
    }
}
