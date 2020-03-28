<?php

namespace Switchm\SmartApi\Components\Tests\ProgramLatestDate\Get\UseCases;

use Prophecy\Argument as arg;
use Switchm\SmartApi\Components\ProgramLatestDate\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\ProgramLatestDate\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\ProgramLatestDate\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Queries\Dao\Dwh\ProgramDao;

class InteractorTest extends TestCase
{
    private $programDao;

    private $outputBoundary;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->programDao = $this->prophesize(ProgramDao::class);
        $this->outputBoundary = $this->prophesize(OutputBoundary::class);

        $this->target = new Interactor($this->programDao->reveal(), $this->outputBoundary->reveal());
    }

    /**
     * @test
     */
    public function invoke(): void
    {
        $this->programDao
            ->getLatestObiProgramsDate(arg::cetera())
            ->willReturn((object) ['date' => '2019/01/01 12:00:00'])
            ->shouldBeCalled();

        $this->outputBoundary
            ->__invoke(new OutputData(['date' => '2019/01/01 12:00:00']))
            ->willReturn()
            ->shouldBeCalled();

        $this->target->__invoke();
    }
}
