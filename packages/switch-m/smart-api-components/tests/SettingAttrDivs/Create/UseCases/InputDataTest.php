<?php

namespace Switchm\SmartApi\Components\Tests\SettingAttrDivs\Create\UseCases;

use Switchm\SmartApi\Components\SettingAttrDivs\Create\UseCases\InputData;
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
            '',
            [],
            [],
            1,
            '',
            1
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'division' => '',
            'info' => [],
            'conditionCross' => [],
            'regionId' => 1,
            'sumpleName' => '',
            'id' => 1,
        ];

        $this->assertSame($expected['division'], $this->target->division());
        $this->assertSame($expected['info'], $this->target->info());
        $this->assertSame($expected['conditionCross'], $this->target->conditionCross());
        $this->assertSame($expected['regionId'], $this->target->regionId());
        $this->assertSame($expected['sumpleName'], $this->target->sumpleName());
        $this->assertSame($expected['id'], $this->target->id());
    }
}
