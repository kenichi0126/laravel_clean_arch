<?php

namespace Tests\Unit\App\Http\UserInterfaces\ProgramPeriodAverage\Get;

use App\Http\UserInterfaces\ProgramPeriodAverage\Get\Controller;
use App\Http\UserInterfaces\ProgramPeriodAverage\Get\Request;
use Switchm\SmartApi\Components\Common\Exceptions\TrialException;
use Switchm\SmartApi\Components\ProgramPeriodAverage\Get\UseCases\InputBoundary;
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
     * @throws TrialException
     */
    public function index(): void
    {
        \Auth
            ::shouldReceive('id')
                ->andReturn(1);

        \UserInfo
            ::shouldReceive('execute')
                ->andReturn(new \stdClass())
                ->once();

        $user = new class {
            public function isDuringTrial()
            {
                return true;
            }

            public function hasPermission()
            {
                return true;
            }
        };

        \Auth
            ::shouldReceive('getUser')
                ->andReturn($user)
                ->once();

        $request = new Request([
            'startDateTime' => '2019-01-01 05:00:00',
            'endDateTime' => '2019-01-07 05:00:00',
            'holiday' => 'true',
            'dataType' => [0],
            'wdays' => [1],
            'genres' => [],
            'dispCount' => 20,
            'dateRange' => 100,
            'page' => 1,
            'regionId' => 1,
            'division' => 'ga8',
            'conditionCross' => [],
            'csvFlag' => 0,
            'draw' => 1,
            'codes' => [],
            'channels' => [],
            'programTypes' => [],
            'dispAverage' => '',
        ]);

        $request->passedValidation();

        $this->inputBoundary
            ->__invoke($request->inputData())
            ->shouldBeCalled();

        $this->target->index($this->inputBoundary->reveal(), $request);
    }
}
