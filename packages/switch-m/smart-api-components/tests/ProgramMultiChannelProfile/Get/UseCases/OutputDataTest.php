<?php

namespace Switchm\SmartApi\Components\Tests\ProgramMultiChannelProfile\Get\UseCases;

use Switchm\SmartApi\Components\ProgramMultiChannelProfile\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class OutputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new OutputData(
            ['data' => []],
            '20190101',
            '20190107',
            ['header' => []]
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'data' => ['data' => []],
            'startDateShort' => '20190101',
            'endDateShort' => '20190107',
            'header' => ['header' => []],
        ];

        $this->assertSame($expected['data'], $this->target->data());
        $this->assertSame($expected['startDateShort'], $this->target->startDateShort());
        $this->assertSame($expected['endDateShort'], $this->target->endDateShort());
        $this->assertSame($expected['header'], $this->target->header());
    }
}
