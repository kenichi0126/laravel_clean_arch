<?php

namespace Tests\Unit\App\Http\UserInterfaces\ProgramListAverage\Get;

use App\Http\UserInterfaces\ProgramListAverage\Get\Controller;
use App\Http\UserInterfaces\ProgramListAverage\Get\Request;
use Switchm\SmartApi\Components\ProgramListAverage\Get\UseCases\InputBoundary;
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
            'averageType' => 'simple',
            'codes' => ['ga8'],
            'conditionCross' => [],
            'dataType' => [0],
            'digitalAndBs' => 'digital',
            'division' => 'ga8',
            'progIds' => [],
            'regionId' => 1,
            'timeBoxIds' => [1],
        ]);

        $request->passedValidation();

        $this->inputBoundary
            ->__invoke($request->inputData())
            ->shouldBeCalled();

        $this->target->index($this->inputBoundary->reveal(), $request);
    }
}
