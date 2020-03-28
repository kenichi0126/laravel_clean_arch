<?php

namespace Switchm\SmartApi\Components\Tests\Divisions\Get\UseCases;

use Switchm\SmartApi\Components\Divisions\Get\UseCases\InputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class InputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new InputData(
            '',
            1,
            new \stdClass(),
            true
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'menu' => '',
            'regionId' => 1,
            'userInfo' => new \stdClass(),
            'hasCrossConditionPermission' => true,
        ];

        $this->assertSame($expected['menu'], $this->target->menu());
        $this->assertSame($expected['regionId'], $this->target->regionId());
        $this->assertEquals($expected['userInfo'], $this->target->userInfo());
        $this->assertSame($expected['hasCrossConditionPermission'], $this->target->hasCrossConditionPermission());
    }
}
