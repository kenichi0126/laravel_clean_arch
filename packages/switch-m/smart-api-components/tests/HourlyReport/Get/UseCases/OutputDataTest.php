<?php

namespace Switchm\SmartApi\Components\Tests\HourlyReport\Get\UseCases;

use Switchm\SmartApi\Components\HourlyReport\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class OutputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new OutputData(['data']);
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'data' => ['data'],
        ];

        $this->assertSame($expected['data'], $this->target->data());
    }
}
