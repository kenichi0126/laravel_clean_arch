<?php

namespace Tests\Unit\App\Http\UserInterfaces\Channels\Get;

use App\Http\UserInterfaces\Channels\Get\Controller;
use App\Http\UserInterfaces\Channels\Get\Request;
use Switchm\SmartApi\Components\Channels\Get\UseCases\InputBoundary;
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
            'division' => 'ga8',
            'regionId' => 1,
            'withCommercials' => false,
        ]);

        $request->passedValidation();

        $this->inputBoundary
            ->__invoke($request->inputData())
            ->shouldBeCalled();

        $this->target->index($this->inputBoundary->reveal(), $request);
    }
}
