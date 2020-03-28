<?php

namespace Switchm\SmartApi\Components\Tests\RatingPerMinutes\Get\UseCases;

use Switchm\SmartApi\Components\RatingPerMinutes\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class OutputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new OutputData(
            ['data'],
            '1',
            1,
            1,
            ['dateList'],
            'channelType',
            'displayType',
            'aggregateType',
            '20190101',
            '20190107',
            ['header']
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'data' => ['data'],
            'draw' => '1',
            'recordsFiltered' => 1,
            'recordsTotal' => 1,
            'dateList' => ['dateList'],
            'channelType' => 'channelType',
            'displayType' => 'displayType',
            'aggregateType' => 'aggregateType',
            'startDateShort' => '20190101',
            'endDateShort' => '20190107',
            'header' => ['header'],
        ];
        $this->assertSame($expected['data'], $this->target->data());
        $this->assertSame($expected['draw'], $this->target->draw());
        $this->assertSame($expected['recordsFiltered'], $this->target->recordsFiltered());
        $this->assertSame($expected['recordsTotal'], $this->target->recordsTotal());
        $this->assertSame($expected['dateList'], $this->target->dateList());
        $this->assertSame($expected['channelType'], $this->target->channelType());
        $this->assertSame($expected['displayType'], $this->target->displayType());
        $this->assertSame($expected['aggregateType'], $this->target->aggregateType());
        $this->assertSame($expected['startDateShort'], $this->target->startDateShort());
        $this->assertSame($expected['endDateShort'], $this->target->endDateShort());
        $this->assertSame($expected['header'], $this->target->header());
    }
}
