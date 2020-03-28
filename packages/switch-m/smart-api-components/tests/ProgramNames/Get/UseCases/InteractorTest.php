<?php

namespace Switchm\SmartApi\Components\Tests\ProgramNames\Get\UseCases;

use Prophecy\Argument as arg;
use Switchm\SmartApi\Components\ProgramNames\Get\UseCases\InputData;
use Switchm\SmartApi\Components\ProgramNames\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\ProgramNames\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\ProgramNames\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Queries\Dao\Rdb\ProgramNamesDao;

class InteractorTest extends TestCase
{
    private $programNamesDao;

    private $outputBoundary;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->programNamesDao = $this->prophesize(ProgramNamesDao::class);
        $this->outputBoundary = $this->prophesize(OutputBoundary::class);

        $this->target = new Interactor($this->programNamesDao->reveal(), $this->outputBoundary->reveal());
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param $programFlag
     * @param $digitalAndBs
     */
    public function invoke($programFlag, $digitalAndBs): void
    {
        $this->programNamesDao
            ->findProgramNames(arg::cetera())
            ->willReturn(['data'])
            ->shouldBeCalled();

        $this->outputBoundary
            ->__invoke(new OutputData(['data']))
            ->willReturn()
            ->shouldBeCalled();

        $input = new InputData(
            '2019-01-01 05:00:00',
            '2019-01-31 04:59:00',
            '世界',
            [],
            $digitalAndBs,
            $programFlag,
            [1, 2, 3, 4, 5, 6, 7],
            [15, 16, 17, 18, 19, 20, 21],
            [22, 23, 24, 25],
            0,
            '1',
            [],
            [],
            1,
            [0],
            [],
            ['1', '2', '3', '4', '5', '6', '0'],
            true
        );

        $this->target->__invoke($input);
    }

    public function dataProvider()
    {
        return [
            [
                /*programFlag*/ false,
                /*digitalAndBs*/ 'digital',
            ],
            [
                /*programFlag*/ true,
                /*digitalAndBs*/ 'digital',
            ],
            [
                /*programFlag*/ true,
                /*digitalAndBs*/ 'bs1',
            ],
            [
                /*programFlag*/ true,
                /*digitalAndBs*/ 'bs2',
            ],
        ];
    }
}
