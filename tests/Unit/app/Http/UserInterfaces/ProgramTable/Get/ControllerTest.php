<?php

namespace Tests\Unit\App\Http\UserInterfaces\ProgramTable\Get;

use App\Http\UserInterfaces\ProgramTable\Get\Controller;
use App\Http\UserInterfaces\ProgramTable\Get\Request;
use Switchm\SmartApi\Components\Common\Exceptions\TrialException;
use Switchm\SmartApi\Components\ProgramTable\Get\UseCases\InputBoundary;
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
                ->andReturn(new \stdClass());

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
            'digitalAndBs' => 'digital',
            'digitalKanto' => [1],
            'bs1' => [98],
            'bs2' => [99],
            'regionId' => 1,
            'division' => 'ga8',
            'conditionCross' => [],
            'draw' => 1,
            'codes' => ['ga8'],
            'channels' => [1, 2, 3],
            'dispPeriod' => '24',
        ]);

        $request->passedValidation();

        $this->inputBoundary
            ->__invoke($request->inputData())
            ->shouldBeCalled();

        $this->target->index($this->inputBoundary->reveal(), $request);
    }
}
