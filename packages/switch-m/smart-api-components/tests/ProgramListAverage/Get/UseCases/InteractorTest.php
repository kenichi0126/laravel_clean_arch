<?php

namespace Switchm\SmartApi\Components\Tests\ProgramListAverage\Get\UseCases;

use Prophecy\Argument as arg;
use Switchm\SmartApi\Components\ProgramListAverage\Get\UseCases\InputData;
use Switchm\SmartApi\Components\ProgramListAverage\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\ProgramListAverage\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\ProgramListAverage\Get\UseCases\OutputData;
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
    public function invoke_baseDivision(): void
    {
        $this->programDao
            ->average(arg::cetera())
            ->willReturn(['data'])
            ->shouldBeCalled();

        $this->programDao
            ->averageOriginal(arg::cetera())
            ->willReturn(['data'])
            ->shouldNotBeCalled();

        $this->outputBoundary
            ->__invoke(new OutputData(['data']))
            ->willReturn()
            ->shouldBeCalled();

        $input = new InputData(
            'simple',
            ['fc'],
            [],
            [0],
            'digital',
            'ga8',
            [],
            1,
            [1],
            ['ga8', 'ga12', 'ga10s', 'gm', 'oc'],
            ['rt' => true, 'ts' => false, 'total' => false, 'gross' => false, 'rtTotal' => false],
            ['rt' => 0, 'ts' => 1, 'total' => 3, 'gross' => 2, 'rtTotal' => 4],
            ['code' => 'code', 'number' => 'number'],
            'selected_personal',
            32
        );

        $this->target->__invoke($input);
    }

    /**
     * @test
     */
    public function invoke_original(): void
    {
        $this->programDao
            ->average(arg::cetera())
            ->willReturn(['data'])
            ->shouldNotBeCalled();

        $this->programDao
            ->averageOriginal(arg::cetera())
            ->willReturn(['data'])
            ->shouldBeCalled();

        $this->outputBoundary
            ->__invoke(new OutputData(['data']))
            ->willReturn()
            ->shouldBeCalled();

        $input = new InputData(
            'simple',
            ['fc'],
            [],
            [0],
            'bs1',
            'original',
            [],
            1,
            [1],
            ['ga8', 'ga12', 'ga10s', 'gm', 'oc'],
            ['rt' => true, 'ts' => false, 'total' => false, 'gross' => false, 'rtTotal' => false],
            ['rt' => 0, 'ts' => 1, 'total' => 3, 'gross' => 2, 'rtTotal' => 4],
            ['code' => 'code', 'number' => 'number'],
            'selected_personal',
            32
        );

        $this->target->__invoke($input);
    }
}
