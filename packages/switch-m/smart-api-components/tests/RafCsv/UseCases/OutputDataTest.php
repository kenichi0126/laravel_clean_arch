<?php

namespace Switchm\SmartApi\Components\Tests\RafCsv\UseCases;

use Switchm\SmartApi\Components\RafCsv\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class OutputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $params = [
            $division = 'division',
            $startDateShort = 'startDateShort',
            $endDateShort = 'endDateShort',
            $header = ['header'],
            $generator = ['generator'],
            $data = (object) ['data'],
        ];

        $this->target = new OutputData(...$params);
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'division' => 'division',
            'startDateShort' => 'startDateShort',
            'endDateShort' => 'endDateShort',
            'header' => ['header'],
            'generator' => ['generator'],
            'data' => (object) ['data'],
        ];
        $this->assertSame($expected['division'], $this->target->division());
        $this->assertSame($expected['startDateShort'], $this->target->startDateShort());
        $this->assertSame($expected['endDateShort'], $this->target->endDateShort());
        $this->assertSame($expected['header'], $this->target->header());
        $this->assertSame($expected['generator'], $this->target->generator());
        $this->assertEquals($expected['data'], $this->target->data());
    }
}
