<?php

namespace Switchm\SmartApi\Components\Tests\SettingAttrDivs\Get\UseCases;

use Switchm\SmartApi\Components\SettingAttrDivs\Get\UseCases\OutputData;
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
