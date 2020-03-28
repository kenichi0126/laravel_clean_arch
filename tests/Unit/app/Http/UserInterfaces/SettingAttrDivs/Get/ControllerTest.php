<?php

namespace Tests\Unit\App\Http\UserInterfaces\SettingAttrDivs\Get;

use App\Http\UserInterfaces\SettingAttrDivs\Get\Controller;
use App\Http\UserInterfaces\SettingAttrDivs\Get\Request;
use Switchm\SmartApi\Components\SettingAttrDivs\Get\UseCases\InputBoundary;
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
            'regionId' => 1,
        ]);

        $request->passedValidation();

        $this->inputBoundary
            ->__invoke($request->inputData())
            ->shouldBeCalled();

        $this->target->index($this->inputBoundary->reveal(), $request);
    }
}
