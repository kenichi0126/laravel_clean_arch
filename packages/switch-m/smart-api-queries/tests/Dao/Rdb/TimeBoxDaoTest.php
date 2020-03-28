<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Rdb;

use Mockery;
use Switchm\SmartApi\Queries\Dao\Rdb\TimeBoxDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class TimeBoxDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = Mockery::mock(TimeBoxDao::class, [])->makePartial();
    }

    /**
     * @test
     */
    public function latest(): void
    {
        $expected = null;

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->latest(1);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getNumber(): void
    {
        $expected = null;

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->getNumber(1);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getTimeBoxId(): void
    {
        $expected = null;

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->getTimeBoxId('20200101', '20200107');

        $this->assertEquals($expected, $actual);
    }
}
