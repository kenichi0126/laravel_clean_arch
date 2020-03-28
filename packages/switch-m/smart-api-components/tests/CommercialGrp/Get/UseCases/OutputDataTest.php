<?php

namespace Switchm\SmartApi\Components\Tests\CommercialGrp\UseCases;

use Switchm\SmartApi\Components\CommercialGrp\Get\UseCases\OutputData;
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
            'ga8',
            [],
            [],
            'period',
            [0],
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
            'list' => [],
            'draw' => '1',
            'division' => 'ga8',
            'codes' => [],
            'codeList' => [],
            'period' => 'period',
            'dataType' => [0],
            'startDateShort' => '20190101',
            'endDateShort' => '20190107',
            'header' => [],
        ];
        $this->assertSame($expected['list'], $this->target->list());
        $this->assertSame($expected['draw'], $this->target->draw());
        $this->assertSame($expected['division'], $this->target->division());
        $this->assertSame($expected['codes'], $this->target->codes());
        $this->assertSame($expected['codeList'], $this->target->codeList());
        $this->assertSame($expected['period'], $this->target->period());
        $this->assertSame($expected['dataType'], $this->target->dataType());
        $this->assertSame($expected['startDateShort'], $this->target->startDateShort());
        $this->assertSame($expected['endDateShort'], $this->target->endDateShort());
        $this->assertSame($expected['header'], $this->target->header());
    }
}
