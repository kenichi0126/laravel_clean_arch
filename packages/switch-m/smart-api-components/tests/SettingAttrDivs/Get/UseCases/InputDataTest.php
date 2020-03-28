<?php

namespace Switchm\SmartApi\Components\Tests\SettingAttrDivs\Get\UseCases;

use Switchm\SmartApi\Components\SettingAttrDivs\Get\UseCases\InputData;
use Switchm\SmartApi\Components\Tests\TestCase;

/**
 * Class InputDataTest.
 */
class InputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new InputData(
            1,
            1
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'regionId' => 1,
            'id' => 1,
        ];

        $this->assertSame($expected['regionId'], $this->target->regionId());
        $this->assertSame($expected['id'], $this->target->id());
    }
}
