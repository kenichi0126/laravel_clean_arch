<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Rdb;

use Mockery;
use Switchm\SmartApi\Queries\Dao\Rdb\SampleDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class SampleDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = Mockery::mock(SampleDao::class, [])->makePartial();
    }

    /**
     * @test
     */
    public function getCrossConditionCount(): void
    {
        $expected = new \stdClass();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->andReturn($expected);

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createConditionCrossSql')
            ->andReturn('');

        $actual = $this->target->getCrossConditionCount([], '', '', 1, false);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getCrossConditionCountRealtime(): void
    {
        $expected = new \stdClass();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->andReturn($expected);

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createConditionCrossSql')
            ->andReturn('');

        $actual = $this->target->getCrossConditionCount([], '', '', 1);

        $this->assertEquals($expected, $actual);
    }
}
