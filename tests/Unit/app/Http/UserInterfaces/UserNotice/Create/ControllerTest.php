<?php

namespace Tests\Unit\App\Http\UserInterfaces\UserNotice\Create;

use App\Http\UserInterfaces\UserNotice\Create\Controller;
use App\Http\UserInterfaces\UserNotice\Create\Request;
use Switchm\SmartApi\Components\UserNotice\Create\UseCases\InputBoundary;
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

        $request = new Request([
            'notice_id' => 2,
        ]);

        $request->passedValidation();

        $this->inputBoundary
            ->__invoke($request->inputData())
            ->shouldBeCalled();

        $this->target->index($this->inputBoundary->reveal(), $request);
    }
}
