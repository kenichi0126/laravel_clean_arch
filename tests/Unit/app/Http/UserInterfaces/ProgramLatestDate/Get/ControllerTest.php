<?php

namespace Tests\Unit\App\Http\UserInterfaces\ProgramLatestDate\Get;

use App\Http\UserInterfaces\ProgramLatestDate\Get\Controller;
use App\Http\UserInterfaces\ProgramLatestDate\Get\Request;
use Switchm\SmartApi\Components\ProgramLatestDate\Get\UseCases\InputBoundary;
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
        ]);

        $this->inputBoundary
            ->__invoke()
            ->shouldBeCalled();

        $this->target->index($this->inputBoundary->reveal(), $request);
    }
}
