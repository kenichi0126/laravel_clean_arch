<?php

namespace Switchm\SmartApi\Components\Tests\SettingAttrDivs\Update\UseCases;

use Switchm\SmartApi\Components\SettingAttrDivs\Update\UseCases\InputData;
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
            'conditionCross' => [],
            'info' => [],
            'regionId' => 1,
            'sumpleName' => '',
            'code' => '',
        ];

        $this->assertSame($expected['division'], $this->target->division());
        $this->assertSame($expected['conditionCross'], $this->target->conditionCross());
        $this->assertSame($expected['info'], $this->target->info());
        $this->assertSame($expected['regionId'], $this->target->regionId());
        $this->assertSame($expected['sumpleName'], $this->target->sumpleName());
        $this->assertSame($expected['code'], $this->target->code());
    }
}
