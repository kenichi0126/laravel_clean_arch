<?php

namespace Smart2\QueryModel\Dao\ReadRdb;

use Carbon\Carbon;
use Mockery;
use stdClass;
use Tests\TestCase;

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

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createConditionOriginalDivSql')
            ->andReturn('');

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createConditionCrossSql')
            ->andReturn('');
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

        $actual = $this->target->find(['ga8']);

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

        $actual = $this->target->findOriginalDiv([''], 1, 1);

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

    /**
     * @test
     */
    public function getSampleCountOriginalConditionCross(): void
    {
        $expected = new stdClass();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->getSampleCountOriginal(Carbon::now(), Carbon::now(), 1, 'condition_cross', '', []);

        $this->assertEquals($expected, $actual);
    }
}
