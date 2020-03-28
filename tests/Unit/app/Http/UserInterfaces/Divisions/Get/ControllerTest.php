<?php

namespace Tests\Unit\App\Http\UserInterfaces\Divisions\Get;

use App\Http\UserInterfaces\Divisions\Get\Controller;
use App\Http\UserInterfaces\Divisions\Get\Request;
use Switchm\SmartApi\Components\Divisions\Get\UseCases\InputBoundary;
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
        \Auth
            ::shouldReceive('id')
                ->andReturn(1);

        $userInfo = new \stdClass();
        $userInfo->id = 1;
        \UserInfo
            ::shouldReceive('execute')
                ->andReturn($userInfo)
                ->once();

        \Auth
            ::shouldReceive('user->hasPermission')
                ->andReturn(false)
                ->once();

        $request = new Request([
            'menu' => '2019-01-01 05:00:00',
            'regionId' => 1,
            $userInfo,
            false,
        ]);

        $request->passedValidation();

        $this->inputBoundary
            ->__invoke($request->inputData())
            ->shouldBeCalled();

        $this->target->index($this->inputBoundary->reveal(), $request);
    }
}
