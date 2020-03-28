<?php

namespace Switchm\SmartApi\Components\Tests\SettingAttrDivs\Create\UseCases;

use Switchm\SmartApi\Components\SettingAttrDivs\Create\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class OutputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new OutputData(
            false
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = false;

        $this->assertSame($expected, $this->target->isSuccess());
    }
}
