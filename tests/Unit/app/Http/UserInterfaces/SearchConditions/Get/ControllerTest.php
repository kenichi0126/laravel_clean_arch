<?php

namespace Tests\Unit\App\Http\UserInterfaces\SearchConditions\Get;

use App\Http\UserInterfaces\SearchConditions\Get\Controller;
use App\Http\UserInterfaces\SearchConditions\Get\Request;
use Switchm\SmartApi\Components\SearchConditions\Get\UseCases\InputBoundary;
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
        \Auth::shouldReceive('getUser')->andReturn(new class {
            public function hasPermission()
            {
                return true;
            }
        });

        $request = new Request([
            'regionId' => 1,
            'orderColumn' => 'name',
            'orderDirection' => 'desc',
        ]);

        $request->passedValidation();

        $this->inputBoundary->__invoke($request->inputData())->shouldBeCalled();

        $this->target->index($this->inputBoundary->reveal(), $request);
    }
}
