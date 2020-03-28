<?php

namespace Tests\Unit\App\Http\UserInterfaces\ProgramTableDetail\Get;

use App\Http\UserInterfaces\ProgramTableDetail\Get\Controller;
use App\Http\UserInterfaces\ProgramTableDetail\Get\Request;
use Switchm\SmartApi\Components\ProgramTableDetail\Get\UseCases\InputBoundary;
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
            'regionId' => 1,
            'division' => 'ga8',
            'progId' => '123',
            'timeBoxId' => '123',
        ]);

        $request->passedValidation();

        $this->inputBoundary
            ->__invoke($request->inputData())
            ->shouldBeCalled();

        $this->target->index($this->inputBoundary->reveal(), $request);
    }
}
