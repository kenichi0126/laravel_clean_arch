<?php

namespace Tests\Unit\App\Http\UserInterfaces\SampleCount\Get;

use App\Http\UserInterfaces\SampleCount\Get\Controller;
use App\Http\UserInterfaces\SampleCount\Get\Request;
use Switchm\SmartApi\Components\SampleCount\Get\UseCases\InputBoundary;
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
        $request = new Request([
            'info' => [],
            'conditionCross' => [],
            'regionId' => 1,
            'editFlg' => false,
            ]);

        $request->passedValidation();

        $this->inputBoundary
            ->__invoke($request->inputData())
            ->shouldBeCalled();

        $this->target->index($this->inputBoundary->reveal(), $request);
    }
}
