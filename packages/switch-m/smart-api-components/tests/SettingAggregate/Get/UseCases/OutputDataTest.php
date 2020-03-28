<?php

namespace Switchm\SmartApi\Components\Tests\SettingAggregate\Get\UseCases;

use Switchm\SmartApi\Components\SettingAggregate\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class OutputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new OutputData(
            []
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [];

        $this->assertSame($expected, $this->target->data());
    }
}
