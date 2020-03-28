<?php

namespace Tests\Unit\App\Http\UserInterfaces\CommercialAdvertising\Get;

use App\Http\UserInterfaces\CommercialAdvertising\Get\Controller;
use App\Http\UserInterfaces\CommercialAdvertising\Get\Request;
use Switchm\SmartApi\Components\CommercialAdvertising\Get\UseCases\InputBoundary;
use Switchm\SmartApi\Components\Common\Exceptions\TrialException;
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
            'companyIds' => [],
            'productIds' => [],
            'cmType' => 0,
            'cmSeconds' => 0,
            'progIds' => [],
            'regionId' => 1,
            'cmIds' => [],
            'channels' => [1],
            'heatMapRating' => false,
            'heatMapTciPersonal' => false,
            'heatMapTciHousehold' => false,
            'division' => 'ga8',
            'conditionCross' => [],
            'csvFlag' => 0,
            'draw' => 1,
            'codes' => [],
        ]);
        $request->passedValidation();

        $this->inputBoundary
            ->__invoke($request->inputData())
            ->shouldBeCalled();

        $this->target->index($this->inputBoundary->reveal(), $request);
    }
}
