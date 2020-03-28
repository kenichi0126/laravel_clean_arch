<?php

namespace Switchm\SmartApi\Components\Tests\RatingPerHourly\Get\UseCases;

use Switchm\SmartApi\Components\RatingPerHourly\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class OutputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new OutputData(
            [],
            '1',
            1,
            [],
            [],
            [],
            'hourly',
            '20190101',
            '20190107',
            []
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'data' => [],
            'draw' => '1',
            'cnt' => 1,
            'dateList' => [],
            'channelType' => [],
            'displayType' => [],
            'aggregateType' => 'hourly',
            'startDateShort' => '20190101',
            'endDateShort' => '20190107',
            'header' => [],
        ];
        $this->assertSame($expected['data'], $this->target->data());
        $this->assertSame($expected['draw'], $this->target->draw());
        $this->assertSame($expected['cnt'], $this->target->cnt());
        $this->assertSame($expected['dateList'], $this->target->dateList());
        $this->assertSame($expected['channelType'], $this->target->channelType());
        $this->assertSame($expected['displayType'], $this->target->displayType());
        $this->assertSame($expected['aggregateType'], $this->target->aggregateType());
        $this->assertSame($expected['startDateShort'], $this->target->startDateShort());
        $this->assertSame($expected['endDateShort'], $this->target->endDateShort());
        $this->assertSame($expected['header'], $this->target->header());
    }
}
