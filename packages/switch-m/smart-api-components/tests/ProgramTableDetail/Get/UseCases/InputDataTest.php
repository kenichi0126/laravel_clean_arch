<?php

namespace Switchm\SmartApi\Components\Tests\ProgramTableDetail\Get\UseCases;

use Switchm\SmartApi\Components\ProgramTableDetail\Get\UseCases\InputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class InputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new InputData(
            // regionId
            1,
            // division
            1,
            // progId
            '12345',
            // timeBoxId
            1,
            // subDate
            2,
            // boundary
            0
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'regionId' => 1,
            'division' => 1,
            'progId' => '12345',
            'timeBoxId' => 1,
            'subDate' => 2,
            'boundary' => 0,
        ];

        $this->assertSame($expected['regionId'], $this->target->regionId());
        $this->assertSame($expected['division'], $this->target->division());
        $this->assertSame($expected['progId'], $this->target->progId());
        $this->assertSame($expected['timeBoxId'], $this->target->timeBoxId());
        $this->assertSame($expected['subDate'], $this->target->subDate());
        $this->assertSame($expected['boundary'], $this->target->boundary());
    }
}
