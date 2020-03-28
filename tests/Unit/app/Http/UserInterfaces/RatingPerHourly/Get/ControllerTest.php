<?php

namespace Tests\Unit\App\Http\UserInterfaces\RatingPerHourly\Get;

use App\Http\UserInterfaces\RatingPerHourly\Get\Controller;
use App\Http\UserInterfaces\RatingPerHourly\Get\Request;
use Switchm\SmartApi\Components\Common\Exceptions\TrialException;
use Switchm\SmartApi\Components\RatingPerHourly\Get\UseCases\InputBoundary;
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
            'regionId' => 1,
            'channels' => [1],
            'channelType' => '1',
            'division' => 'ga8',
            'conditionCross' => [],
            'csvFlag' => 0,
            'draw' => 1,
            'code' => 'personal',
            'dataType' => [0],
            'displayType' => '',
            'aggregateType' => '',
            'hour' => '',
        ]);

        $request->passedValidation();

        $this->inputBoundary
            ->__invoke($request->inputData())
            ->shouldBeCalled();

        $this->target->index($this->inputBoundary->reveal(), $request);
    }
}
