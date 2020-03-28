<?php

namespace Tests\Unit\App\Http\UserInterfaces\RatingPerMinutes\Get;

use App\Http\UserInterfaces\RatingPerMinutes\Get\Controller;
use App\Http\UserInterfaces\RatingPerMinutes\Get\Request;
use Switchm\SmartApi\Components\RatingPerMinutes\Get\UseCases\InputBoundary;
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
        \Auth
            ::shouldReceive('id')
                ->andReturn(1);

        $user = new class {
            public function isDuringTrial()
            {
                return true;
            }
        };

        \Auth
            ::shouldReceive('getUser')
                ->andReturn($user);

        $request = new Request([
            'startDateTime' => '2019-01-01 05:00:00',
            'endDateTime' => '2019-01-31 04:59:00',
            'regionId' => 1,
            'channels' => [],
            'channelType' => 'dt1',
            'division' => 'ga8',
            'conditionCross' => [],
            'csvFlag' => '0',
            'draw' => '1',
            'code' => 'personal',
            'dataDivision' => 'viewing_rate',
            'dataType' => [0],
            'displayType' => 'channelBy',
            'aggregateType' => '6',
            'hour' => '6',
        ]);

        $request->passedValidation();

        $this->inputBoundary
            ->__invoke($request->inputData())
            ->shouldBeCalled();

        $this->target->index($this->inputBoundary->reveal(), $request);
    }
}
