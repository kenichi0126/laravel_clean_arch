<?php

namespace Tests\Unit\App\Http\UserInterfaces\TopRanking\Get;

use App\Http\UserInterfaces\TopRanking\Get\Controller;
use App\Http\UserInterfaces\TopRanking\Get\Request;
use Switchm\SmartApi\Components\TopRanking\Get\UseCases\InputBoundary;
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
        \Auth::
        shouldReceive('id')
            ->andReturn(1)
            ->once();

        $user = new \stdClass();
        $user->conv_15_sec_flag = 'true';

        \UserInfo::
        shouldReceive('execute')
            ->andReturn($user)
            ->once();

        $request = new Request(['regionId' => 1]);

        $request->passedValidation();

        $this->inputBoundary
            ->__invoke($request->inputData())
            ->shouldBeCalled();

        $this->target->index($this->inputBoundary->reveal(), $request);
    }
}
