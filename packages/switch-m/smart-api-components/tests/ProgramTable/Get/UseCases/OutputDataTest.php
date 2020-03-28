<?php

namespace Switchm\SmartApi\Components\Tests\ProgramTable\Get\UseCases;

use Switchm\SmartApi\Components\ProgramTable\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class OutputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new OutputData(
            ['data'],
            1,
            ['dateList'],
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
            'draw' => 1,
            'dateList' => ['dateList'],
            'header' => ['header'],
        ];

        $this->assertSame($expected['data'], $this->target->data());
        $this->assertSame($expected['draw'], $this->target->draw());
        $this->assertSame($expected['dateList'], $this->target->dateList());
        $this->assertSame($expected['header'], $this->target->header());
    }
}
