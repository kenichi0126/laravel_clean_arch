<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Rdb;

use Mockery;
use Switchm\SmartApi\Queries\Dao\Rdb\SearchConditionTextDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class SearchConditionTextDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = Mockery::mock(SearchConditionTextDao::class, [])->makePartial();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createArrayBindParam')
            ->andReturn([]);
    }

    /**
     * @test
     */
    public function getCompanyNames(): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->getCompanyNames([1]);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getProductNames(): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->getProductNames([1]);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getProgramNames(): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->getProgramNames([1]);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getCmMaterials(): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->getCmMaterials([1]);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getBasicNumbers(): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->getBasicNumbers('ga8', ['personal'], '2019-12-23 00:00:00', '2019-12-23 23:59:59', 1, false);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getBasicNumbersRealtime(): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->getBasicNumbers('ga8', ['personal'], '2019-12-23 00:00:00', '2019-12-23 23:59:59', 1, true);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getOriginalNumbers(): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createCrossJoinWhereClause')
            ->andReturn('')
            ->once();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->getOriginalNumbers('ga8', ['personal'], '2019-12-23 00:00:00', '2019-12-23 23:59:59', 1, false);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getOriginalNumbersRealtime(): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createCrossJoinWhereClause')
            ->andReturn('')
            ->once();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->getOriginalNumbers('ga8', ['personal'], '2019-12-23 00:00:00', '2019-12-23 23:59:59', 1, true);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getPersonalHouseholdNumbers(): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->getPersonalHouseholdNumbers('2019-12-23 00:00:00', '2019-12-23 23:59:59', 1, false);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getPersonalHouseholdNumbersRealtime(): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->getPersonalHouseholdNumbers('2019-12-23 00:00:00', '2019-12-23 23:59:59', 1, true);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getGenres(): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->getGenres([1]);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getCmLargeGenreNames(): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->getCmLargeGenreNames([1]);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getChannelCodeNames(): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->getChannelCodeNames([1]);

        $this->assertEquals($expected, $actual);
    }
}
