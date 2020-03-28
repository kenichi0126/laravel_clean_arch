<?php

namespace Switchm\SmartApi\Components\Tests\ProgramPeriodAverage\Get\UseCases;

use Switchm\SmartApi\Components\ProgramPeriodAverage\Get\UseCases\OutputData;
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
            100,
            100,
            '20200101',
            '20200107',
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
            'recordsFiltered' => 100,
            'recordsTotal' => 100,
            'startDateShort' => '20200101',
            'endDateShort' => '20200107',
            'header' => ['header'],
        ];

        $this->assertSame($expected['data'], $this->target->data());
        $this->assertSame($expected['draw'], $this->target->draw());
        $this->assertSame($expected['recordsFiltered'], $this->target->recordsFiltered());
        $this->assertSame($expected['recordsTotal'], $this->target->recordsTotal());
        $this->assertSame($expected['startDateShort'], $this->target->startDateShort());
        $this->assertSame($expected['endDateShort'], $this->target->endDateShort());
        $this->assertSame($expected['header'], $this->target->header());
    }
}
