<?php

namespace Switchm\SmartApi\Components\Tests\CommercialAdvertising\Get\UseCases;

use Switchm\SmartApi\Components\CommercialAdvertising\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class OutputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new OutputData(
            ['data'],
            [1, 2, 3],
            '1',
            '',
            [],
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
            'data' => ['data'],
            'channels' => [1, 2, 3],
            'csvFlag' => '1',
            'draw' => '',
            'rp' => [],
            'startDateTimeShort' => '20190101',
            'endDateTimeShort' => '20190107',
            'header' => [],
        ];
        $this->assertSame($expected['data'], $this->target->data());
        $this->assertSame($expected['channels'], $this->target->channels());
        $this->assertSame($expected['csvFlag'], $this->target->csvFlag());
        $this->assertSame($expected['draw'], $this->target->draw());
        $this->assertSame($expected['rp'], $this->target->rp());
        $this->assertSame($expected['startDateTimeShort'], $this->target->startDateTimeShort());
        $this->assertSame($expected['endDateTimeShort'], $this->target->endDateTimeShort());
        $this->assertSame($expected['header'], $this->target->header());
    }
}
