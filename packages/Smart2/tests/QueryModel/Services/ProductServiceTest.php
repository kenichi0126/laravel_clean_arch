<?php

namespace Smart2\QueryModel\Service;

use Prophecy\Argument as arg;
use Prophecy\Prophecy\ObjectProphecy;
use Switchm\SmartApi\Queries\Dao\Rdb\ProductDao;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    /**
     * @var ObjectProphecy
     */
    private $productDao;

    /**
     * @var ProductService
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
     */
    public function getCompanyIds(): void
    {
        $expected = [1, 2];

        $companyIds = array_map(function ($val) {
            return ['company_id' => $val];
        }, $expected);

        $this->productDao
            ->findCompanyIds(arg::cetera())
            ->willReturn($companyIds)
            ->shouldBeCalled();

        $actual = $this->target->getCompanyIds([1], []);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getCompanyIdsExistsCompanyIds(): void
    {
        $expected = [1, 2];

        $actual = $this->target->getCompanyIds([], $expected);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getCompanyIdWhenAllowProgIds(): void
    {
        $expected = [];

        $actual = $this->target->getCompanyIds([], []);

        $this->assertEquals($expected, $actual);
    }
}
