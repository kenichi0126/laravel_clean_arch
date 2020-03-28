<?php

namespace Switchm\SmartApi\Components\Tests\ProgramMultiChannelProfile\Get\UseCases;

use Switchm\SmartApi\Components\ProgramMultiChannelProfile\Get\UseCases\InputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class InputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new InputData(
            // startDateTime
            '2019-01-01 05:00:00',
            // endDateTime
            '2019-01-07 05:00:00',
            // regionId
            1,
            // progIDs
            [1],
            // timeBoxIds
            [1],
            // division
            'ga8',
            // conditionCross
            [],
            // codes
            [],
            // channelIds
            [1],
            // sampleType
            '3',
            // isEnq
            true,
            // sampleCountMaxNumber
            50,
            100
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'startDateTime' => '2019-01-01 05:00:00',
            'endDateTime' => '2019-01-07 05:00:00',
            'regionId' => 1,
            'progIds' => [1],
            'timeBoxIds' => [1],
            'division' => 'ga8',
            'conditionCross' => [],
            'codes' => [],
            'channelIds' => [1],
            'sampleType' => '3',
            'isEnq' => true,
            'sampleCountMaxNumber' => 50,
            'ptThreshold' => 100,
        ];

        $this->assertSame($expected['startDateTime'], $this->target->startDateTime());
        $this->assertSame($expected['endDateTime'], $this->target->endDateTime());
        $this->assertSame($expected['regionId'], $this->target->regionId());
        $this->assertSame($expected['progIds'], $this->target->progIds());
        $this->assertSame($expected['timeBoxIds'], $this->target->timeBoxIds());
        $this->assertSame($expected['division'], $this->target->division());
        $this->assertSame($expected['conditionCross'], $this->target->conditionCross());
        $this->assertSame($expected['codes'], $this->target->codes());
        $this->assertSame($expected['channelIds'], $this->target->channelIds());
        $this->assertSame($expected['sampleType'], $this->target->sampleType());
        $this->assertSame($expected['isEnq'], $this->target->isEnq());
        $this->assertSame($expected['sampleCountMaxNumber'], $this->target->sampleCountMaxNumber());
        $this->assertSame($expected['ptThreshold'], $this->target->ptThreshold());
    }
}
