<?php

namespace Tests\Unit\App\Http\UserInterfaces\SettingAttrDivs\Delete;

use App\Http\UserInterfaces\SettingAttrDivs\Delete\Controller;
use App\Http\UserInterfaces\SettingAttrDivs\Delete\Request;
use Switchm\SmartApi\Components\SettingAttrDivs\Delete\UseCases\InputBoundary;
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
            'division' => '',
            'code' => '',
        ]);

        $request->passedValidation();

        $this->inputBoundary
            ->__invoke($request->inputData())
            ->shouldBeCalled();

        $this->target->index($this->inputBoundary->reveal(), $request);
    }
}
