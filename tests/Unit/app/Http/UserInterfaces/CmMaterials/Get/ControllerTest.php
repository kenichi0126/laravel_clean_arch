<?php

namespace Tests\Unit\App\Http\UserInterfaces\CmMaterials\Get;

use App\Http\UserInterfaces\CmMaterials\Get\Controller;
use App\Http\UserInterfaces\CmMaterials\Get\Request;
use Switchm\SmartApi\Components\CmMaterials\Get\UseCases\InputBoundary;
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
            'startDateTime' => '2019-01-01 05:00:00',
            'endDateTime' => '2019-01-07 04:59:59',
            'dataType' => [0],
            'productName' => 'iPhone',
            'companyIds' => [],
            'regionIds' => [1],
            'productIds' => [],
            'channels' => [3, 4, 5, 6, 7],
            'cmType' => 0,
            'cmSeconds' => 1,
            'progIds' => [],
        ]);

        $request->passedValidation();

        $this->inputBoundary
            ->__invoke($request->inputData())
            ->shouldBeCalled();

        $this->target->index($this->inputBoundary->reveal(), $request);
    }
}
