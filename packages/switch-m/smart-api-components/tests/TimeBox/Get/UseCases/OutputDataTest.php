<?php

namespace Switchm\SmartApi\Components\Tests\TimeBox\Get\UseCases;

use Carbon\Carbon;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Components\TimeBox\Get\UseCases\OutputData;

class OutputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new OutputData(
            1,
            2,
            Carbon::parse('2019-01-01 05:00:00'),
            7,
            1,
            Carbon::parse('2019-01-02 05:00:00'),
            Carbon::parse('2019-01-03 05:00:00'),
            4,
            5
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'id' => 1,
            'regionId' => 2,
            'startDate' => Carbon::parse('2019-01-01 05:00:00'),
            'duration' => 7,
            'version' => 1,
            'startedAt' => Carbon::parse('2019-01-02 05:00:00'),
            'endedAt' => Carbon::parse('2019-01-03 05:00:00'),
            'panelersNumber' => 4,
            'householdsNumber' => 5,
        ];

        $this->assertSame($expected['id'], $this->target->id());
        $this->assertSame($expected['regionId'], $this->target->regionId());
        $this->assertEquals($expected['startDate'], $this->target->startDate());
        $this->assertSame($expected['duration'], $this->target->duration());
        $this->assertSame($expected['version'], $this->target->version());
        $this->assertEquals($expected['startedAt'], $this->target->startedAt());
        $this->assertEquals($expected['endedAt'], $this->target->endedAt());
        $this->assertSame($expected['panelersNumber'], $this->target->panelersNumber());
        $this->assertSame($expected['householdsNumber'], $this->target->householdsNumber());
    }
}
