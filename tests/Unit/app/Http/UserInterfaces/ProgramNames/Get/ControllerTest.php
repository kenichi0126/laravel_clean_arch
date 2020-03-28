<?php

namespace Tests\Unit\App\Http\UserInterfaces\ProgramNames\Get;

use App\Http\UserInterfaces\ProgramNames\Get\Controller;
use App\Http\UserInterfaces\ProgramNames\Get\Request;
use Switchm\SmartApi\Components\ProgramNames\Get\UseCases\InputBoundary;
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
            'cmtype' => 0,
            'cmSeconds' => '1',
            'companies' => [],
            'startDateTime' => '2019-01-01 05:00:00',
            'endDateTime' => '2019-01-31 04:59:00',
            'programName' => '世界',
            'programIds' => [],
            'productIds' => [],
            'regionId' => 1,
            'wdays' => ['1', '2', '3', '4', '5', '6', '0'],
            'holiday' => true,
            'dataType' => [0],
            'digitalAndBs' => 'digital',
            'digitalKanto' => [1, 2, 3, 4, 5, 6, 7],
            'bs1' => [15, 16, 17, 18, 19, 20, 21],
            'bs2' => [22, 23, 24, 25],
            'programFlag' => true,
            'channels' => [],
        ]);

        $request->passedValidation();

        $this->inputBoundary
            ->__invoke($request->inputData())
            ->shouldBeCalled();

        $this->target->index($this->inputBoundary->reveal(), $request);
    }
}
