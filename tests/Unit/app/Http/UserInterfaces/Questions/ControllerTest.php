<?php

namespace Tests\Unit\App\Http\UserInterfaces\Questions;

use App\Http\UserInterfaces\Questions\Get\Controller;
use App\Http\UserInterfaces\Questions\Get\Request;
use Switchm\SmartApi\Components\Questions\Get\UseCases\InputBoundary;
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
            'keyword' => '車',
            'qGroup' => '全選択',
            'tag' => '全選択',
        ]);
        $request->passedValidation();

        $this->inputBoundary
            ->__invoke($request->inputData())
            ->shouldBeCalled();

        $this->target->index($this->inputBoundary->reveal(), $request);
    }
}
