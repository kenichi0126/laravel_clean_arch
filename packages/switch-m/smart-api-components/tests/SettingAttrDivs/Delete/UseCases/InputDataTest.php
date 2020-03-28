<?php

namespace Switchm\SmartApi\Components\Tests\SettingAttrDivs\Delete\UseCases;

use Switchm\SmartApi\Components\SettingAttrDivs\Delete\UseCases\InputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class InputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new InputData(
            '',
            ''
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'division' => '',
            'code' => '',
        ];

        $this->assertSame($expected['division'], $this->target->division());
        $this->assertSame($expected['code'], $this->target->code());
    }
}
