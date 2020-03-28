<?php

namespace Tests\Unit\App\Http\UserInterfaces\SearchConditions\Delete;

use App\Http\UserInterfaces\SearchConditions\Delete\Controller;
use App\Http\UserInterfaces\SearchConditions\Delete\Request;
use Switchm\SmartApi\Components\SearchConditions\Delete\UseCases\InputBoundary;
use Tests\TestCase;

/**
 * Class ControllerTest.
 */
final class ControllerTest extends TestCase
{
    private $inputBoundary;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->inputBoundary = $this->prophesize(InputBoundary::class);

        $this->target = new Controller();
    }

    /**
     * @test
     */
    public function index(): void
    {
        \Auth::shouldReceive('id')->andReturn(1);

        $request = new Request([
            'id' => 0,
        ]);

        $request->passedValidation();

        $this->inputBoundary->__invoke($request->inputData())->shouldBeCalled();

        $this->target->index($this->inputBoundary->reveal(), $request);
    }
}
