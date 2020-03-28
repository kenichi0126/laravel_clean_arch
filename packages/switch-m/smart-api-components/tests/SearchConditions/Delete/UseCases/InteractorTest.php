<?php

namespace Switchm\SmartApi\Components\Tests\SearchConditions\Delete\UseCases;

use Prophecy\Argument as arg;
use Switchm\SmartApi\Components\SearchConditions\Delete\UseCases\DataAccessInterface;
use Switchm\SmartApi\Components\SearchConditions\Delete\UseCases\InputData;
use Switchm\SmartApi\Components\SearchConditions\Delete\UseCases\Interactor;
use Switchm\SmartApi\Components\SearchConditions\Delete\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\SearchConditions\Delete\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;

/**
 * Class InteractorTest.
 */
final class InteractorTest extends TestCase
{
    private $dataAccess;

    private $outputBoundary;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->dataAccess = $this->prophesize(DataAccessInterface::class);
        $this->outputBoundary = $this->prophesize(OutputBoundary::class);

        $this->target = new Interactor($this->dataAccess->reveal(), $this->outputBoundary->reveal());
    }

    /**
     * @test
     */
    public function invoke(): void
    {
        $input = new InputData(
            0
        );

        $output = new OutputData();

        $this->dataAccess->__invoke(arg::cetera())->shouldBeCalled();

        $this->outputBoundary->__invoke($output)->shouldBeCalled();

        $this->target->__invoke($input);
    }
}
