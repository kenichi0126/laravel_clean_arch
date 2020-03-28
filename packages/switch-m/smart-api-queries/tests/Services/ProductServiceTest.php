<?php

namespace Switchm\SmartApi\Queries\Tests\Services;

use Prophecy\Argument as arg;
use Prophecy\Prophecy\ObjectProphecy;
use Switchm\SmartApi\Queries\Dao\Rdb\ProductDao;
use Switchm\SmartApi\Queries\Services\HolidayService;
use Switchm\SmartApi\Queries\Services\ProductService;
use Switchm\SmartApi\Queries\Tests\TestCase;

class ProductServiceTest extends TestCase
{
    /**
     * @var ObjectProphecy
     */
    private $productDao;

    /**
     * @var HolidayService
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->productDao = $this->prophesize(ProductDao::class);
        $this->target = new ProductService($this->productDao->reveal());
    }

    /**
     * @test
     * @dataProvider getCompanyIdsDataProvider
     * @param $productIds
     * @param $companyIds
     * @param $expected
     */
    public function getCompanyIds($productIds, $companyIds, $expected): void
    {
        $this->productDao
            ->findCompanyIds(arg::cetera())
            ->willReturn(['findCompanyIds']);

        $actual = $this->target->getCompanyIds($productIds, $companyIds);

        $this->assertEquals($expected, $actual);
    }

    public function getCompanyIdsDataProvider(): array
    {
        return [
            [[], [1], [1]],
            [[], [], []],
            [[1], [], []],
        ];
    }
}
