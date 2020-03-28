<?php

namespace Switchm\SmartApi\Components\Tests\RankingCommercial\UseCases;

use Switchm\SmartApi\Components\RankingCommercial\Get\UseCases\OutputData;
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
            'cnt' => 1,
            'startDateShort' => '20190101',
            'endDateShort' => '20190107',
            'header' => [],
        ];
        $this->assertSame($expected['list'], $this->target->list());
        $this->assertSame($expected['draw'], $this->target->draw());
        $this->assertSame($expected['cnt'], $this->target->cnt());
        $this->assertSame($expected['startDateShort'], $this->target->startDateShort());
        $this->assertSame($expected['endDateShort'], $this->target->endDateShort());
        $this->assertSame($expected['header'], $this->target->header());
    }
}
