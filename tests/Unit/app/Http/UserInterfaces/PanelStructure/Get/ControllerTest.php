<?php

namespace Tests\Unit\App\Http\PanelStructure\Top\Get;

use App\Http\UserInterfaces\PanelStructure\Get\Controller;
use App\Http\UserInterfaces\PanelStructure\Get\Request;
use Switchm\SmartApi\Components\PanelStructure\Get\UseCases\InputBoundary;
use Tests\TestCase;

class ControllerTest extends TestCase
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
        \Auth::
        shouldReceive('id')
            ->andReturn(1)
            ->once();

        $user = new \stdClass();
        $user->id = 1;

        \UserInfo
            ::shouldReceive('execute')
                ->andReturn($user)
                ->once();

        $request = new Request(['division' => 'ga8', 'regionId' => 1]);

        $request->passedValidation();

        $this->inputBoundary
            ->__invoke($request->inputData())
            ->shouldBeCalled();

        $this->target->index($this->inputBoundary->reveal(), $request);
    }
}
