<?php

namespace Tests\Unit\App\Http\UserInterfaces\HourlyReport\Get;

use App\Http\UserInterfaces\HourlyReport\Get\Controller;
use App\Http\UserInterfaces\HourlyReport\Get\Request;
use Switchm\SmartApi\Components\HourlyReport\Get\UseCases\InputBoundary;
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
        $setting = (object) ['sponsor' => (object) ['sponsorTrial' => (object) ['settings' => []]]];

        \Auth
            ::shouldReceive('getUser')
                ->andReturn($setting);

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
