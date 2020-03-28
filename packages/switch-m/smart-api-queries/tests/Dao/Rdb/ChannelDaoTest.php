<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Rdb;

use Mockery;
use Switchm\SmartApi\Queries\Dao\Rdb\ChannelDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class ChannelDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = Mockery::mock(ChannelDao::class, [])->makePartial();
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param mixed $params
     */
    public function search($params): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->search($params);

        $this->assertEquals($expected, $actual);
    }

    public function dataProvider(): array
    {
        return [
            [['regionId' => 1, 'division' => 'bs1', 'withCommercials' => 0]],
            [['regionId' => 1, 'division' => 'ga8', 'withCommercials' => 1]],
        ];
    }
}
