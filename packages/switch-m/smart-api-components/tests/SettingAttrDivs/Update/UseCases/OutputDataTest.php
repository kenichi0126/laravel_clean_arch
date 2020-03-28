<?php

namespace Switchm\SmartApi\Components\Tests\SettingAttrDivs\Update\UseCases;

use Switchm\SmartApi\Components\SettingAttrDivs\Update\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class OutputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new OutputData(1);
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = 1;

        $this->assertSame($expected, $this->target->result());
    }
}
