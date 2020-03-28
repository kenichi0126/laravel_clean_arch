<?php

namespace Switchm\SmartApi\Components\Tests\HourlyReport\Get\UseCases;

use Switchm\SmartApi\Components\HourlyReport\Get\UseCases\InputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class InputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new InputData(
            1,
            []
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'regionId' => 1,
            'trialSettings' => [],
        ];

        $this->assertSame($expected['regionId'], $this->target->regionId());
        $this->assertEquals($expected['trialSettings'], $this->target->trialSettings());
    }
}
