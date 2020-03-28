<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Rdb;

use Mockery;
use Switchm\SmartApi\Queries\Dao\Rdb\ProductDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class ProductDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = Mockery::mock(ProductDao::class, [])->makePartial();
    }

    /**
     * @test
     * @dataProvider searchDataProvider
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

    public function searchDataProvider(): array
    {
        return [
            [
                ['companyIds' => [], 'productNames' => [], 'productIds' => [], 'cmType' => '', 'cmSeconds' => '', 'progIds' => [], 'regionIds' => [], 'channels' => []],
            ],
            [
                ['companyIds' => [1, 2], 'productNames' => ['test1', 'test2'], 'productIds' => [1, 2], 'cmType' => '1', 'cmSeconds' => '2', 'progIds' => [1, 2], 'regionIds' => [1], 'channels' => [1, 2]],
            ],
            [
                ['companyIds' => [1, 2], 'productNames' => ['test1', 'test2'], 'productIds' => [1, 2], 'cmType' => '2', 'cmSeconds' => '3', 'progIds' => [1, 2], 'regionIds' => [1], 'channels' => [1, 2]],
            ],
            [
                ['startTimeHour' => 5, 'startTimeMin' => 0, 'endTimeHour' => 28, 'endTimeMin' => 59],
            ],
            [
                ['startTimeHour' => 5, 'startTimeMin' => 0, 'endTimeHour' => 4, 'endTimeMin' => 59],
            ],
            [
                ['startTimeHour' => 12, 'startTimeMin' => 0, 'endTimeHour' => 13, 'endTimeMin' => 59],
            ],
        ];
    }

    /**
     * @test
     */
    public function findCompanyIds(): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->findCompanyIds([1]);

        $this->assertEquals($expected, $actual);
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
