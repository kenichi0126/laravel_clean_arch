<?php

namespace Tests\Unit\App\Http\UserInterfaces\CompanyNames\Get;

use App\Http\UserInterfaces\CompanyNames\Get\Controller;
use App\Http\UserInterfaces\CompanyNames\Get\Request;
use Switchm\SmartApi\Components\CompanyNames\Get\UseCases\InputBoundary;
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
            'companyName' => 'ソフトバンク',
            'progIds' => [],
            'regionId' => 1,
            'companyId' => [],
            'channels' => [3, 4, 5, 6, 7],
            'cmType' => 0,
            'cmSeconds' => 1,
            'productIds' => [],
            'dataType' => [0],
        ]);

        $request->passedValidation();

        $this->inputBoundary
            ->__invoke($request->inputData())
            ->shouldBeCalled();

        $this->target->index($this->inputBoundary->reveal(), $request);
    }
}
