<?php

namespace Tests\Unit\App\Http\UserInterfaces\Setting\Save;

use App\Http\UserInterfaces\Setting\Save\Controller;
use App\Http\UserInterfaces\Setting\Save\Request;
use Switchm\SmartApi\Components\Setting\Save\UseCases\InputBoundary;
use Tests\TestCase;

class ControllerTest extends TestCase
{
    private $inputBoundary;

    private $target;

    public function setUp(): void
    {
        parent::setUp();
        \Auth::shouldReceive('id')->andReturn(1);
        $this->inputBoundary = $this->prophesize(InputBoundary::class);

        $this->target = new Controller();
    }

    /**
     * @test
     */
    public function index(): void
    {
        $request = new Request([
            'secFlag' => 1,
            'division' => '',
            'codes' => [],
            'regionId' => 1,
        ]);

        $request->passedValidation();

        $this->inputBoundary
            ->__invoke($request->inputData())
            ->shouldBeCalled();

        $this->target->index($this->inputBoundary->reveal(), $request);
    }
}
