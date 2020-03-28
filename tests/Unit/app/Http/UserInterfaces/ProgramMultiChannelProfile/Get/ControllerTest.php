<?php

namespace Tests\Unit\App\Http\UserInterfaces\ProgramMultiChannelProfile\Get;

use App\Http\UserInterfaces\ProgramMultiChannelProfile\Get\Controller;
use App\Http\UserInterfaces\ProgramMultiChannelProfile\Get\Request;
use Switchm\SmartApi\Components\Common\Exceptions\TrialException;
use Switchm\SmartApi\Components\ProgramMultiChannelProfile\Get\UseCases\InputBoundary;
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
                ->andReturn($user);

        $request = new Request([
            'startDateTime' => '2019-01-01 05:00:00',
            'endDateTime' => '2019-01-07 05:00:00',
            'regionId' => 1,
            'progIds' => [],
            'timeBoxIds' => [0],
            'division' => 'ga8',
            'conditionCross' => [],
            'codes' => [],
            'channelIds' => [],
            'sampleType' => '3',
        ]);

        $request->passedValidation();

        $this->inputBoundary
            ->__invoke($request->inputData())
            ->shouldBeCalled();

        $this->target->index($this->inputBoundary->reveal(), $request);
    }
}
