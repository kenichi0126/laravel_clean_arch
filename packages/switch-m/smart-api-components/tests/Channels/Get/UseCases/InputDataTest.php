<?php

namespace Switchm\SmartApi\Components\Tests\Channels\Get\UseCases;

use Switchm\SmartApi\Components\Channels\Get\UseCases\InputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class InputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new InputData(
            'ga8',
            1,
            true
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'division' => 'ga8',
            'regionId' => 1,
            'withCommercials' => true,
        ];

        $this->assertSame($expected['division'], $this->target->division());
        $this->assertSame($expected['regionId'], $this->target->regionId());
        $this->assertEquals($expected['withCommercials'], $this->target->withCommercials());
    }
}
