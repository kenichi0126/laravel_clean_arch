<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Rdb;

use Mockery;
use Switchm\SmartApi\Queries\Dao\Rdb\DivisionDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class DivisionDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = Mockery::mock(DivisionDao::class, [])->makePartial();
    }

    /**
     * @test
     */
    public function find(): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->find(['ga8', 'ga12']);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function findOriginalDiv(): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->findOriginalDiv(['ga8', 'ga12'], 1, 1);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getPersonalHouseHold(): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->getPersonalHouseHold();

        $this->assertEquals($expected, $actual);
    }
}
