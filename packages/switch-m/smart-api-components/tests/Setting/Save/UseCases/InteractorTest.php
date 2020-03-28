<?php

namespace Switchm\SmartApi\Components\Tests\Setting\Save\UseCases;

use Prophecy\Argument as arg;
use Switchm\SmartApi\Components\Setting\Save\UseCases\DataAccessInterface;
use Switchm\SmartApi\Components\Setting\Save\UseCases\InputData;
use Switchm\SmartApi\Components\Setting\Save\UseCases\Interactor;
use Switchm\SmartApi\Components\Setting\Save\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\Setting\Save\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class InteractorTest extends TestCase
{
    private $dataAccess;

    private $outputBoundary;

    private $target;

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
            1,
            'ga12',
            ['f1'],
            1,
            1
        );

        $output = new OutputData();

        $this->dataAccess->__invoke(arg::cetera())->shouldBeCalled();

        $this->outputBoundary->__invoke($output)->shouldBeCalled();
        $this->target->__invoke($input);
    }
}
