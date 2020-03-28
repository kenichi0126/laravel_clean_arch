<?php

namespace Tests\Unit\App\Http\UserInterfaces\SettingAggregate\Get;

use App\Http\UserInterfaces\SettingAggregate\Get\Controller;
use App\Http\UserInterfaces\SettingAggregate\Get\Request;
use Switchm\SmartApi\Components\SettingAggregate\Get\UseCases\InputBoundary;
use Tests\TestCase;

class ControllerTest extends TestCase
{
    private $inputBoundary;

    private $target;

    public function setUp(): void
    {
        parent::setUp();
        \Auth
            ::shouldReceive('id')
                ->andReturn(1);

        \UserInfo
            ::shouldReceive('execute')
                ->andReturn(new \stdClass());
        $this->inputBoundary = $this->prophesize(InputBoundary::class);

        $this->target = new Controller();
    }

    /**
     * @test
     */
    public function index(): void
    {
        $request = new Request([]);

        $request->passedValidation();

        $this->inputBoundary
            ->__invoke($request->inputData())
            ->shouldBeCalled();

        $this->target->index($this->inputBoundary->reveal(), $request);
    }
}
