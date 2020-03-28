<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Rdb;

use Mockery;
use Switchm\SmartApi\Queries\Dao\Rdb\CompanyNamesDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class CompanyNamesDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = Mockery::mock(CompanyNamesDao::class, [])->makePartial();
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

    /**
     * @test
     * @dataProvider dataProvider
     * @param mixed $params
     * @param mixed $straddlingFlg
     */
    public function findForCondition($params, $straddlingFlg): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->findForCondition($params, $straddlingFlg);

        $this->assertEquals($expected, $actual);
    }

    public function dataProvider()
    {
        return [
            [
                [
                    'companyNames' => ['ソフトバンク', '花王'],
                ],
                false,
            ],
            [
                [
                    'companyId' => [123, 456],
                ],
                true,
            ],
            [
                [
                    'progIds' => [123, 456],
                ],
                true,
            ],
            [
                [
                    'channels' => [1, 2],
                ],
                true,
            ],
            [
                [
                    'cmType' => '1',
                ],
                true,
            ],
            [
                [
                    'cmType' => '2',
                ],
                true,
            ],
            [
                [
                    'cmSeconds' => '2',
                ],
                true,
            ],
            [
                [
                    'cmSeconds' => '3',
                ],
                true,
            ],
            [
                [
                    'productIds' => [1, 2],
                ],
                true,
            ],
        ];
    }
}
