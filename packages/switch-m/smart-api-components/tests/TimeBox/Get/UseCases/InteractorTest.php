<?php

namespace Switchm\SmartApi\Components\Tests\TimeBox\Get\UseCases;

use Carbon\Carbon;
use Smart2\CommandModel\Eloquent\Member;
use stdClass;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Components\TimeBox\Get\UseCases\InputData;
use Switchm\SmartApi\Components\TimeBox\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\TimeBox\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\TimeBox\Get\UseCases\OutputData;
use Switchm\SmartApi\Queries\Dao\Rdb\TimeBoxDao;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InteractorTest extends TestCase
{
    private $timeBoxDao;

    private $outputBoundary;

    public function setUp(): void
    {
        parent::setUp();

        $this->timeBoxDao = $this->prophesize(TimeBoxDao::class);
        $this->outputBoundary = $this->prophesize(OutputBoundary::class);

        $this->target = new Interactor($this->timeBoxDao->reveal(), $this->outputBoundary->reveal());
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param regionId
     * @param timeBox
     * @param trialSettings
     * @param outputData
     * @param mixed $regionId
     * @param mixed $timeBox
     * @param mixed $trialSettings
     * @param mixed $outputData
     */
    public function invoke($regionId, $timeBox, $trialSettings, $outputData): void
    {
        $this->timeBoxDao
            ->latest($regionId)
            ->willReturn($timeBox)
            ->shouldBeCalled();

        $input = new InputData(
            $regionId,
            $trialSettings
        );

        $outputData = new OutputData(...$outputData);
        $this->outputBoundary->__invoke($outputData)->shouldBeCalled();
        $this->target->__invoke($input);
    }

    public function dataProvider()
    {
        $timeBox = new stdClass();
        $timeBox->id = 1;
        $timeBox->region_id = 2;
        $timeBox->start_date = Carbon::parse('2019-01-01 00:00:00');
        $timeBox->duration = 7;
        $timeBox->version = 1;
        $timeBox->started_at = Carbon::parse('2019-01-01 05:00:00');
        $timeBox->ended_at = Carbon::parse('2019-01-08 05:00:00');
        $timeBox->panelers_number = 10;
        $timeBox->households_number = 20;

        $outputData = [1, 2, Carbon::parse('2019-01-01 00:00:00'), 7, 1, Carbon::parse('2019-01-01 05:00:00'), Carbon::parse('2019-01-08 05:00:00'), 10, 20];
        $outputDataOutOfDate = [1, 2, Carbon::parse('2019-01-01 00:00:00'), 7, 1, Carbon::parse('2018-02-01 00:00:00'), Carbon::parse('2018-02-01 00:00:00'), 10, 20];

        $member = new Member();
        $trialMember = new Member();
        $trialMember['search_range'] = [
            'start' => '2019-01-01',
            'end' => '2019-02-01',
        ];
        $memberOutOfDate = new Member();
        $memberOutOfDate['search_range'] = [
            'start' => '2018-01-01',
            'end' => '2018-02-01',
        ];

        return [
            '非トライアル' => [
                1,
                $timeBox,
                $member,
                $outputData,
            ],
            'トライアル（期間内）' => [
                1,
                $timeBox,
                $member,
                $outputData,
            ],
            'トライアル（期間切れ）' => [
                1,
                $timeBox,
                $memberOutOfDate,
                $outputDataOutOfDate,
            ],
        ];
    }

    /**
     * @test
     */
    public function invokeEmpty(): void
    {
        $this->timeBoxDao
            ->latest(1)
            ->willReturn(null)
            ->shouldBeCalled();

        $input = new InputData(
            1,
            null
        );

        $this->expectException(NotFoundHttpException::class);
        $this->target->__invoke($input);
    }
}
