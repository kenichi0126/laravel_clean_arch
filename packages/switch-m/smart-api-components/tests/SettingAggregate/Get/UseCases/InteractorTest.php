<?php

namespace Switchm\SmartApi\Components\Tests\SettingAggregate\Get\UseCases;

use Switchm\SmartApi\Components\SettingAggregate\Get\UseCases\InputData;
use Switchm\SmartApi\Components\SettingAggregate\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\SettingAggregate\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\SettingAggregate\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class InteractorTest extends TestCase
{
    private $outputBoundary;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->outputBoundary = $this->prophesize(OutputBoundary::class);

        $this->target = new Interactor(
            $this->outputBoundary->reveal()
        );
    }

    /**
     * @test
     */
    public function invoke(): void
    {
        $result = ['conv15SecFlag' => '1'];

        $output = new OutputData($result);

        $input = new InputData(
            (object) [
                'conv_15_sec_flag' => '1',
            ]
        );

        $this->outputBoundary->__invoke($output)->shouldBeCalled();
        $this->target->__invoke($input);
    }
}
