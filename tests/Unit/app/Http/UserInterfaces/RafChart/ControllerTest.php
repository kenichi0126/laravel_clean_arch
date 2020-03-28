<?php

namespace Tests\Unit\App\Http\UserInterfaces\RafChart;

use App\Http\UserInterfaces\RafChart\Get\Controller;
use App\Http\UserInterfaces\RafChart\Get\Request;
use Switchm\SmartApi\Components\RafChart\Get\UseCases\InputBoundary;
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
     * @dataProvider indexDataProvider
     * @param mixed $params
     */
    public function index($params): void
    {
        $request = new Request($params);

        $user = new class {
            public function isDuringTrial()
            {
                return true;
            }
        };
        \Auth::shouldReceive('id')->andReturn(1);
        \UserInfo::shouldReceive('execute');
        \Auth::shouldReceive('getUser')->andReturn($user);

        $request->passedValidation();

        $this->inputBoundary
            ->__invoke($request->inputData())
            ->shouldBeCalled();

        $this->target->index($this->inputBoundary->reveal(), $request);
    }

    public function indexDataProvider(): array
    {
        return [
            'params' => [
                [
                    'endDateTime' => '2019-06-13 04:59:00',
                    'startDateTime' => '2019-06-12 05:00:00',
                    'channels' => [
                        3,
                        4,
                        5,
                        6,
                        7,
                    ],
                    'companyIds' => [],
                    'cmType' => 0,
                    'cmSeconds' => 1,
                    'division' => 'ga8',
                    'codes' => [
                        'c',
                    ],
                    'conditionCross' => [
                        'gender' => [
                            '',
                        ],
                        'age' => [
                            'from' => 4,
                            'to' => 99,
                        ],
                        'occupation' => [
                            '',
                        ],
                        'married' => [
                            '',
                        ],
                        'dispOccupation' => [
                            '',
                        ],
                    ],
                    'reachAndFrequencyGroupingUnit' => [
                        3,
                        6,
                        9,
                    ],
                    'axisType' => 0,
                    'channelAxis' => 0,
                    'period' => 'day',
                    'codeNames' => [
                        [
                            'division' => 'ga8',
                            'code' => 'c',
                            'division_name' => '性・年齢8区分',
                            'name' => 'C',
                            'division_order' => 101,
                            'display_order' => 1,
                        ],
                    ],
                    'productIds' => [
                        52874,
                    ],
                    'cmIds' => [],
                    'regionId' => 1,
                    'conv_15_sec_flag' => 1,
                    'progIds' => [],
                    'dataType' => [
                        0,
                    ],
                    'dateRange' => 2,
                    'csvFlag' => 0,
                ], ], ];
    }
}
