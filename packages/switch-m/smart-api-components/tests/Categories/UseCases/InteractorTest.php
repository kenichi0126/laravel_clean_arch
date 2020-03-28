<?php

namespace Switchm\SmartApi\Components\Tests\Categories\UseCases;

use Switchm\SmartApi\Components\Categories\Get\UseCases\InputData;
use Switchm\SmartApi\Components\Categories\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\Categories\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\Categories\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Queries\Dao\Rdb\EnqDao;

class InteractorTest extends TestCase
{
    private $enqDao;

    private $outputBoundary;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->enqDao = $this->prophesize(EnqDao::class);
        $this->outputBoundary = $this->prophesize(OutputBoundary::class);

        $this->target = new Interactor($this->enqDao->reveal(), $this->outputBoundary->reveal());
    }

    /**
     * @test
     */
    public function invoke(): void
    {
        $this->enqDao
            ->getCategory()
            ->willReturn(['data'])
            ->shouldBeCalled();

        $this->outputBoundary
            ->__invoke(new OutputData(['data']))
            ->willReturn()
            ->shouldBeCalled();

        $input = new InputData();

        $this->target->__invoke($input);
    }
}
