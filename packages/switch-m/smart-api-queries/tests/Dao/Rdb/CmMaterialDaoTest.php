<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Rdb;

use Mockery;
use Switchm\SmartApi\Queries\Dao\Rdb\CmMaterialDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class CmMaterialDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = Mockery::mock(CmMaterialDao::class, [])->makePartial();
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

    public function dataProvider()
    {
        return [
            [
                [
                  'start_time_hour' => 5,
                  'start_time_min' => 0,
                  'end_time_hour' => 28,
                  'end_time_min' => 59,
                ],
            ],
            [
                [
                    'start_time_hour' => 22,
                    'start_time_min' => 0,
                    'end_time_hour' => 4,
                    'end_time_min' => 59,
                ],
            ],
            [
                [
                    'start_time_hour' => 23,
                    'start_time_min' => 0,
                    'end_time_hour' => 4,
                    'end_time_min' => 59,
                ],
            ],
            [
                [
                    'channels' => [1],
                ],
            ],
            [
                [
                    'progIds' => [1],
                ],
            ],
            [
                [
                    'cmType' => 1,
                ],
            ],
            [
                [
                    'cmType' => 2,
                ],
            ],
            [
                [
                    'cmSeconds' => '2',
                ],
            ],
            [
                [
                    'cmSeconds' => '3',
                ],
            ],
            [
                [
                    'companyIds' => [1],
                ],
            ],
        ];
    }
}
