<?php

namespace Switchm\SmartApi\Components\Tests\SettingAttrDivsOrder\Update\UseCases;

use Prophecy\Argument as arg;
use Switchm\SmartApi\Components\SettingAttrDivsOrder\Update\UseCases\DataAccessInterface;
use Switchm\SmartApi\Components\SettingAttrDivsOrder\Update\UseCases\InputData;
use Switchm\SmartApi\Components\SettingAttrDivsOrder\Update\UseCases\Interactor;
use Switchm\SmartApi\Components\SettingAttrDivsOrder\Update\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\SettingAttrDivsOrder\Update\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class InteractorTest extends TestCase
{
    private $target;

    private $dataAccess;

    private $outputBoundary;

    public function setUp(): void
    {
        parent::setUp();

        $this->dataAccess = $this->prophesize(DataAccessInterface::class);
        $this->outputBoundary = $this->prophesize(OutputBoundary::class);

        $this->target = new Interactor(
            $this->dataAccess->reveal(),
            $this->outputBoundary->reveal()
        );
    }

    /**
     * @test
     */
    public function invoke(): void
    {
        $input = new InputData(
            [
                'division_test' => [
                    'code_test',
                ],
            ]
        );
        $outputData = new OutputData();

        $this->dataAccess->__invoke(arg::cetera())->shouldBeCalled();

        $this->outputBoundary->__invoke($outputData)->shouldBeCalled();
        $this->target->__invoke($input);
    }
}
