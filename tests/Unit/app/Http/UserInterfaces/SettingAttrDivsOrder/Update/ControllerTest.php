<?php

namespace Tests\Unit\App\Http\UserInterfaces\SettingAttrDivsOrder\Update;

use App\Http\UserInterfaces\SettingAttrDivsOrder\Update\Controller;
use App\Http\UserInterfaces\SettingAttrDivsOrder\Update\Request;
use Switchm\SmartApi\Components\SettingAttrDivsOrder\Update\UseCases\InputBoundary;
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
            'divisions' => [],
        ]);

        $request->passedValidation();

        $this->inputBoundary
            ->__invoke($request->inputData())
            ->shouldBeCalled();

        $this->target->index($this->inputBoundary->reveal(), $request);
    }
}
