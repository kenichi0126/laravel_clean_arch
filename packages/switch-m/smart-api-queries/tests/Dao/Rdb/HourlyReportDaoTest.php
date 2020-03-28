<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Rdb;

use Mockery;
use Switchm\SmartApi\Queries\Dao\Rdb\HourlyReportDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class HourlyReportDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = Mockery::mock(HourlyReportDao::class, [])->makePartial();
    }

    /**
     * @test
     */
    public function latest(): void
    {
        $expected = new \stdClass();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->latest(1);

        $this->assertEquals($expected, $actual);
    }
}
