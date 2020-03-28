<?php

namespace Tests\Unit\App\Http\UserInterfaces\SettingAttrDivs\Update;

use App\Http\UserInterfaces\SettingAttrDivs\Update\Controller;
use App\Http\UserInterfaces\SettingAttrDivs\Update\Request;
use Switchm\SmartApi\Components\SettingAttrDivs\Update\UseCases\InputBoundary;
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
            'division' => '',
            'conditionCross' => [],
            'info' => [],
            'regionId' => 1,
            'sumpleName' => '',
        ]);

        $request->passedValidation();

        $this->inputBoundary
            ->__invoke($request->inputData())
            ->shouldBeCalled();

        $this->target->index($this->inputBoundary->reveal(), $request);
    }
}
