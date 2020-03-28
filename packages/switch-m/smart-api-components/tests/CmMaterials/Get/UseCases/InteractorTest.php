<?php

namespace Switchm\SmartApi\Components\Tests\CmMaterials\Get\UseCases;

use Prophecy\Argument as arg;
use Switchm\SmartApi\Components\CmMaterials\Get\UseCases\InputData;
use Switchm\SmartApi\Components\CmMaterials\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\CmMaterials\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\CmMaterials\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Queries\Dao\Rdb\CmMaterialDao;

class InteractorTest extends TestCase
{
    private $cmMaterialDao;

    private $outputBoundary;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->cmMaterialDao = $this->prophesize(CmMaterialDao::class);
        $this->outputBoundary = $this->prophesize(OutputBoundary::class);

        $this->target = new Interactor($this->cmMaterialDao->reveal(), $this->outputBoundary->reveal());
    }

    /**
     * @test
     */
    public function invoke(): void
    {
        $this->cmMaterialDao
            ->search(arg::cetera())
            ->willReturn(['data'])
            ->shouldBeCalled();

        $this->outputBoundary
            ->__invoke(new OutputData(['data']))
            ->willReturn()
            ->shouldBeCalled();

        $input = new InputData(
            [1],
            '2019-01-01 05:00:00',
            '2019-01-01 05:00:00',
            5,
            10,
            5,
            10,
            1,
            [1],
            0,
            1,
            [1],
            [1]
        );

        $this->target->__invoke($input);
    }
}
