<?php

namespace Tests\Unit\App\Http\UserInterfaces\CommercialList\Get;

use App\Http\UserInterfaces\CommercialList\Get\Controller;
use App\Http\UserInterfaces\CommercialList\Get\Request;
use Switchm\SmartApi\Components\CommercialList\Get\UseCases\InputBoundary;
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

            public function hasPermission(string $name)
            {
                return true;
            }
        };

        \Auth
            ::shouldReceive('getUser')
                ->andReturn($user)
                ->times(4);

        $request = new Request([
            'startDateTime' => '2019-01-01 05:00:00',
            'endDateTime' => '2019-01-07 04:59:59',
            'page' => 1,
            'dateRange' => 1,
            'cmType' => [],
            'dataType' => [0],
            'cmSeconds' => [],
            'progIds' => [],
            'regionId' => 1,
            'division' => 'ga8',
            'codes' => ['personal'],
            'conditionCross' => [],
            'companyIds' => [],
            'productIds' => [],
            'cmIds' => [],
            'channels' => [],
            'order' => 20,
            'dispCount' => 20,
            'conv_15_sec_flag' => '1',
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
