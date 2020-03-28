<?php

namespace Tests\Unit\App\Http\UserInterfaces\RankingCommercial\Get;

use App\Http\UserInterfaces\RankingCommercial\Get\Controller;
use App\Http\UserInterfaces\RankingCommercial\Get\Request;
use Switchm\SmartApi\Components\Common\Exceptions\TrialException;
use Switchm\SmartApi\Components\RankingCommercial\Get\UseCases\InputBoundary;
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
            'page' => 1,
            'holiday' => 'true',
            'wdays' => [1],
            'division' => 'ga8',
            'dateRange' => 100,
            'dataType' => [0],
            'regionId' => 1,
            'cmType' => '',
            'codes' => [],
            'conditionCross' => [],
            'channels' => [1],
            'order' => [],
            'conv_15_sec_flag' => '1',
            'period' => 'period',
            'dispCount' => 20,
            'csvFlag' => 0,
            'cmLargeGenres' => [],
            'axisType' => '',
            'draw' => 1,
        ]);

        $request->passedValidation();

        $this->inputBoundary
            ->__invoke($request->inputData())
            ->shouldBeCalled();

        $this->target->index($this->inputBoundary->reveal(), $request);
    }
}
