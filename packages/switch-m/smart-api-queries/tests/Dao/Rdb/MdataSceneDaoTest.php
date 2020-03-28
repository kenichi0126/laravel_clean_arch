<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Rdb;

use Mockery;
use Switchm\SmartApi\Queries\Dao\Rdb\MdataSceneDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class MdataSceneDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = Mockery::mock(MdataSceneDao::class, [])->makePartial();
    }

    /**
     * @test
     */
    public function findMdataScenes(): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->findMdataScenes('1');

        $this->assertEquals($expected, $actual);
    }
}
