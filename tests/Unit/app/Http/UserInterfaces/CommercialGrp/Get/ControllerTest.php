<?php

namespace Tests\Unit\App\Http\UserInterfaces\CommercialGrp\Get;

use App\Http\UserInterfaces\CommercialGrp\Get\Controller;
use App\Http\UserInterfaces\CommercialGrp\Get\Request;
use Switchm\SmartApi\Components\CommercialGrp\Get\UseCases\InputBoundary;
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
                ->twice();

        $request = new Request([
            'startDateTime' => '2019-01-01 05:00:00',
            'endDateTime' => '2019-01-07 04:59:59',
            'page' => 1,
            'dataType' => [0],
            'division' => 'ga8',
            'conditionCross' => [],
            'regionId' => 1,
            'dateRange' => 1,
            'productIds' => [],
            'companyIds' => [],
            'cmType' => [],
            'cmSeconds' => [],
            'progIds' => [],
            'codes' => ['personal'],
            'cmIds' => [],
            'channels' => [],
            'conv_15_sec_flag' => '1',
            'period' => 'period',
            'allChannels' => '0',
            'dispCount' => 20,
            'csvFlag' => '1',
            'draw' => 1,
        ]);

        $request->passedValidation();

        $this->inputBoundary
            ->__invoke($request->inputData())
            ->shouldBeCalled();

        $this->target->index($this->inputBoundary->reveal(), $request);
    }
}
