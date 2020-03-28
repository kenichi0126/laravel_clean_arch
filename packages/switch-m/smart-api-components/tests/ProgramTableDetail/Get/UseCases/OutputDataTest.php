<?php

namespace Switchm\SmartApi\Components\Tests\ProgramTableDetail\Get\UseCases;

use Switchm\SmartApi\Components\ProgramTableDetail\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class OutputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new OutputData(
            ['data'],
            ['headlines']
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'data' => ['data'],
            'headlines' => ['headlines'],
        ];

        $this->assertSame($expected['data'], $this->target->data());
        $this->assertSame($expected['headlines'], $this->target->headlines());
    }
}
