<?php

namespace Switchm\SmartApi\Components\Tests\ProgramTable\Get\UseCases;

use Switchm\SmartApi\Components\ProgramTable\Get\UseCases\InputData;
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
            // digitalAndBs
            'digital',
            // digitalKanto
            [1],
            // bs1
            [98],
            // bs2
            [99],
            // regionId
            1,
            // division
            'ga8',
            // conditionCross
            [],
            // draw
            1,
            // codes
            ['ga8'],
            // channels
            [1, 2, 3],
            // dispPeriod
            '24',
            // BaseDivision
            ['ga8', 'ga12', 'ga10s', 'gm', 'oc'],
            // priod
            [],
            // userId
            1
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
            'baseDivision' => ['ga8', 'ga12', 'ga10s', 'gm', 'oc'],
            'period' => [],
            'userId' => 1,
        ];

        $this->assertSame($expected['startDateTime'], $this->target->startDateTime());
        $this->assertSame($expected['endDateTime'], $this->target->endDateTime());
        $this->assertSame($expected['digitalAndBs'], $this->target->digitalAndBs());
        $this->assertSame($expected['digitalKanto'], $this->target->digitalKanto());
        $this->assertSame($expected['bs1'], $this->target->bs1());
        $this->assertSame($expected['bs2'], $this->target->bs2());
        $this->assertSame($expected['regionId'], $this->target->regionId());
        $this->assertSame($expected['division'], $this->target->division());
        $this->assertSame($expected['conditionCross'], $this->target->conditionCross());
        $this->assertSame($expected['draw'], $this->target->draw());
        $this->assertSame($expected['codes'], $this->target->codes());
        $this->assertSame($expected['channels'], $this->target->channels());
        $this->assertSame($expected['dispPeriod'], $this->target->dispPeriod());
        $this->assertSame($expected['baseDivision'], $this->target->baseDivision());
        $this->assertSame($expected['period'], $this->target->period());
        $this->assertSame($expected['userId'], $this->target->userId());
    }
}
