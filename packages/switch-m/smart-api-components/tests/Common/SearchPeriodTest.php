<?php

namespace Switchm\SmartApi\Components\Tests\Common;

use Carbon\Carbon;
use Switchm\SmartApi\Components\Common\SearchPeriod;
use Switchm\SmartApi\Components\Tests\TestCase;

class SearchPeriodTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::create(2019, 1, 1, 5, 0, 0));

        $this->target = new SearchPeriod();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    /**
     * @test
     * @dataProvider getRdbDwhSearchPeriodDataProvider
     * @param mixed $startDateTime
     * @param mixed $endDateTime
     * @param mixed $subDate
     * @param mixed $boundary
     * @param mixed $isDwh
     * @param mixed $isRdb
     */
    public function getRdbDwhSearchPeriod($startDateTime, $endDateTime, $subDate, $boundary, $isDwh, $isRdb): void
    {
        $expected = [
            'rdbStartDate' => $startDateTime,
            'rdbEndDate' => $endDateTime,
            'dwhStartDate' => $startDateTime,
            'dwhEndDate' => $endDateTime,
            'isDwh' => $isDwh,
            'isRdb' => $isRdb,
        ];

        $actual = $this->target->getRdbDwhSearchPeriod($startDateTime, $endDateTime, $subDate, $boundary);

        $this->assertEquals($expected, $actual);
    }

    public function getRdbDwhSearchPeriodDataProvider()
    {
        return [
          [new Carbon('20190101 05:00:00'), new Carbon('20190107 04:59:59'), 1, 2, false, true],
            [new Carbon('20180101 05:00:00'), new Carbon('20180107 04:59:59'), 1, 2, true, false],
        ];
    }
}
