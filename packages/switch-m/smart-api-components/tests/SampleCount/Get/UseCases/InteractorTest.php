<?php

namespace Switchm\SmartApi\Components\Tests\SampleCount\Get\UseCases;

use Prophecy\Argument as arg;
use ReflectionClass;
use Switchm\SmartApi\Components\SampleCount\Get\UseCases\InputData;
use Switchm\SmartApi\Components\SampleCount\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\SampleCount\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\SampleCount\Get\UseCases\OutputData;
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
     * @throws \ReflectionException
     */
    public function produceOutputData(): void
    {
        $reflection = new ReflectionClass($this->target);
        $method = $reflection->getMethod('produceOutputData');
        $method->setAccessible(true);

        $cnt = ['cnt' => 1000];
        $editFlg = false;

        $expected = new OutputData(
            ['cnt' => 1000],
            false
        );
        $actual = $method->invoke($this->target, $cnt, $editFlg);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param $editFlg
     */
    public function invoke($editFlg): void
    {
        $this->enqDao
            ->getSampleCount(arg::cetera())
            ->willReturn(['cnt' => 1000])
            ->shouldBeCalled();

        $input = new InputData(
            [],
            [],
            1,
            $editFlg
        );

        $this->target->__invoke($input);

        $outputData = new OutputData(
            ['cnt' => 1000],
            $editFlg
        );
        $this->outputBoundary->__invoke($outputData)->shouldBeCalled();
        $this->target->__invoke($input);
    }

    public function dataProvider()
    {
        return [
            [
                true,
            ],
            [
                false,
            ],
        ];
    }
}
