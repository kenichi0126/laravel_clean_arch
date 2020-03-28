<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Rdb;

use Mockery;
use Switchm\SmartApi\Queries\Dao\Rdb\ProgramNamesDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class ProgramNamesDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = Mockery::mock(ProgramNamesDao::class, [])->makePartial();
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param mixed $params
     * @param mixed $straddlingFlg
     */
    public function findProgramNames($params, $straddlingFlg): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->findProgramNames($params, $straddlingFlg);

        $this->assertEquals($expected, $actual);
    }

    public function dataProvider()
    {
        return [
            [
                [
                    'bsFlag' => true,
                ],
                false,
            ],
            [
                [
                    'bsFlag' => false,
                ],
                false,
            ],
            [
                [
                    'title' => ['WBS', 'いいとも'],
                ],
                false,
            ],
            [
                [
                    'programIds' => [123, 456],
                    'bsFlag' => true,
                ],
                false,
            ],
            [
                [
                    'programIds' => [123, 456],
                    'bsFlag' => false,
                ],
                false,
            ],
            [
                [
                    'startTime' => '050000',
                    'endTime' => '045959',
                ],
                false,
            ],
            [
                [
                    'startTime' => '060000',
                    'endTime' => '045959',
                ],
                true,
            ],
            [
                [
                    'startTime' => '060000',
                    'endTime' => '045959',
                ],
                false,
            ],
            [
                [
                    'channel' => [1, 2, 3],
                ],
                false,
            ],
            [
                [
                    'programFlag' => false,
                    'regionId' => 1,
                    'companyIds' => [1, 2, 3],
                ],
                false,
            ],
            [
                [
                    'programFlag' => false,
                    'regionId' => 1,
                    'cmType' => '1',
                ],
                false,
            ],
            [
                [
                    'programFlag' => false,
                    'regionId' => 1,
                    'cmType' => '2',
                ],
                false,
            ],
            [
                [
                    'programFlag' => false,
                    'regionId' => 1,
                    'cmSeconds' => '2',
                ],
                false,
            ],
            [
                [
                    'programFlag' => false,
                    'regionId' => 1,
                    'cmSeconds' => '3',
                ],
                false,
            ],
            [
                [
                    'programFlag' => false,
                    'regionId' => 1,
                    'productIds' => [123, 456],
                ],
                false,
            ],
            [
                [
                    'wdays' => [1, 2],
                    'holiday' => true,
                ],
                false,
            ],
            [
                [
                    'wdays' => [1, 2],
                    'holiday' => false,
                ],
                false,
            ],
            [
                [
                    'holiday' => true,
                ],
                false,
            ],
            [
                [
                    'holiday' => false,
                ],
                false,
            ],
        ];
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

        $actual = $this->target->find([1]);

        $this->assertEquals($expected, $actual);
    }
}
