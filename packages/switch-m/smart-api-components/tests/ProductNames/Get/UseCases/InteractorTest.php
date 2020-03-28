<?php

namespace Switchm\SmartApi\Components\Tests\ProductNames\Get\UseCases;

use Prophecy\Argument as arg;
use Switchm\SmartApi\Components\ProductNames\Get\UseCases\InputData;
use Switchm\SmartApi\Components\ProductNames\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\ProductNames\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\ProductNames\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Queries\Dao\Rdb\ProductDao;

class InteractorTest extends TestCase
{
    private $productDao;

    private $outputBoundary;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->productDao = $this->prophesize(ProductDao::class);
        $this->outputBoundary = $this->prophesize(OutputBoundary::class);

        $this->target = new Interactor($this->productDao->reveal(), $this->outputBoundary->reveal());
    }

    /**
     * @test
     */
    public function invoke(): void
    {
        $this->productDao
            ->search(arg::cetera())
            ->willReturn(['data'])
            ->shouldBeCalled();

        $this->outputBoundary
            ->__invoke(new OutputData(['data']))
            ->willReturn()
            ->shouldBeCalled();

        $input = new InputData(
            '2019-01-01 05:00:00',
            '2019-01-07 04:59:59',
            [0],
            'iPhone',
            [],
            [1],
            [],
            [3, 4, 5, 6, 7],
            0,
            1,
            []
        );

        $this->target->__invoke($input);
    }
}
