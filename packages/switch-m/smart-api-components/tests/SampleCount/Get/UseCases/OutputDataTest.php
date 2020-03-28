<?php

namespace Switchm\SmartApi\Components\Tests\SampleCount\Get\UseCases;

use Switchm\SmartApi\Components\SampleCount\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class OutputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new OutputData(
            ['cnt' => 100],
            false
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'cnt' => ['cnt' => 100],
            'editFlg' => false,
        ];

        $this->assertSame($expected['cnt'], $this->target->cnt());
        $this->assertSame($expected['editFlg'], $this->target->editFlg());
    }
}
