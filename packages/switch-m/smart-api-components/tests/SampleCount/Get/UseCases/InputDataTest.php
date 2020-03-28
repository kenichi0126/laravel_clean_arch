<?php

namespace Switchm\SmartApi\Components\Tests\SampleCount\Get\UseCases;

use Switchm\SmartApi\Components\SampleCount\Get\UseCases\InputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class InputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new InputData(
            [],
            [],
            1,
            false
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'info' => [],
            'conditionCross' => [],
            'regionId' => 1,
            'editFlg' => false,
        ];

        $this->assertSame($expected['info'], $this->target->info());
        $this->assertSame($expected['conditionCross'], $this->target->conditionCross());
        $this->assertSame($expected['regionId'], $this->target->regionId());
        $this->assertSame($expected['editFlg'], $this->target->editFlg());
    }
}
