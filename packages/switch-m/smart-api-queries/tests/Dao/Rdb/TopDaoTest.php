<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Rdb;

use Mockery;
use Switchm\SmartApi\Queries\Dao\Rdb\TopDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class TopDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = Mockery::mock(TopDao::class, [])->makePartial();
    }

    /**
     * @test
     */
    public function findLatestPrograms(): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->findLatestPrograms('20190101', 1);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function findHourViewingRate(): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->findHourViewingRate('20190101', 1, [1]);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getTerms(): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->getTerms(1);

        $this->assertEquals($expected, $actual);
    }
}
