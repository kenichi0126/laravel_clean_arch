<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Rdb;

use Carbon\Carbon;
use Mockery;
use Switchm\SmartApi\Queries\Dao\Rdb\HolidayDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class HolidayDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = Mockery::mock(HolidayDao::class, [])->makePartial();
    }

    /**
     * @test
     */
    public function getHoliday(): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->findHoliday(Carbon::now(), Carbon::now());

        $this->assertEquals($expected, $actual);
    }
}
