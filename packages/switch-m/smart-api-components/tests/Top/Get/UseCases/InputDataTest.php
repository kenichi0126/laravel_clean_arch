<?php

namespace Switchm\SmartApi\Components\Tests\Top\Get\UseCases;

use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Components\Top\Get\UseCases\InputData;

class InputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new InputData(
            1,
            [],
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
            'channelColors' => [],
            'channelColorsKansai' => [],
        ];

        $this->assertSame($expected['regionId'], $this->target->regionId());
        $this->assertSame($expected['channelColors'], $this->target->channelColors());
        $this->assertSame($expected['channelColorsKansai'], $this->target->channelColorsKansai());
    }
}
