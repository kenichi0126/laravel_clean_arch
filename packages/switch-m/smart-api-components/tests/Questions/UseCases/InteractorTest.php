<?php

namespace Switchm\SmartApi\Components\Tests\Questions\UseCases;

use Prophecy\Argument as arg;
use Switchm\SmartApi\Components\Questions\Get\UseCases\InputData;
use Switchm\SmartApi\Components\Questions\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\Questions\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\Questions\Get\UseCases\OutputData;
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
            ->getQuestion(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->outputBoundary
            ->__invoke(new OutputData([]))
            ->willReturn()
            ->shouldBeCalled();

        $input = new InputData(
            '車',
            '全選択',
            '全選択'
        );

        $this->target->__invoke($input);
    }
}
