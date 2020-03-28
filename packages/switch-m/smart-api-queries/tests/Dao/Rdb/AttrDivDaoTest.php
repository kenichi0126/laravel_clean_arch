<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Rdb;

use Mockery;
use Switchm\SmartApi\Queries\Dao\Rdb\AttrDivDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class AttrDivDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = Mockery::mock(AttrDivDao::class, [])->makePartial();
    }

    /**
     * @test
     */
    public function getAttrDiv(): void
    {
        $expected = ['list' => ['result']];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn(['result'])
            ->once();

        $actual = $this->target->getAttrDiv('ga8', 'personal');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getDisplayOrder(): void
    {
        $expected = ['list' => ['result']];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn(['result'])
            ->once();

        $actual = $this->target->getDisplayOrder('ga8');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getCode(): void
    {
        $expected = ['list' => ['result']];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn(['result'])
            ->once();

        $actual = $this->target->getCode('ga8');

        $this->assertEquals($expected, $actual);
    }
}
