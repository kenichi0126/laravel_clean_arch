<?php

namespace Switchm\SmartApi\Components\Tests\ProgramListAverage\Get\UseCases;

use Switchm\SmartApi\Components\ProgramListAverage\Get\UseCases\InputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class InputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new InputData(
            'simple',
            ['fc'],
            [],
            [0],
            'digital',
            'ga8',
            [],
            1,
            [1],
            ['ga8', 'ga12', 'ga10s', 'gm', 'oc'],
            ['rt' => true, 'ts' => false, 'total' => false, 'gross' => false, 'rtTotal' => false],
            ['rt' => 0, 'ts' => 1, 'total' => 3, 'gross' => 2, 'rtTotal' => 4],
            ['code' => 'code', 'number' => 'number'],
            'selected_personal',
            32
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'averageType' => 'simple',
            'codes' => ['fc'],
            'conditionCross' => [],
            'dataType' => [0],
            'division' => 'ga8',
            'progIds' => [],
            'regionId' => 1,
            'timeBoxIds' => [1],
            'baseDivision' => ['ga8', 'ga12', 'ga10s', 'gm', 'oc'],
            'dataTypeFlags' => ['rt' => true, 'ts' => false, 'total' => false, 'gross' => false, 'rtTotal' => false],
            'dataTypeConst' => ['rt' => 0, 'ts' => 1, 'total' => 3, 'gross' => 2, 'rtTotal' => 4],
            'prefixes' => ['code' => 'code', 'number' => 'number'],
            'selectedPersonalName' => 'selected_personal',
            'codeNumber' => 32,
        ];

        $this->assertSame($expected['averageType'], $this->target->averageType());
        $this->assertSame($expected['codes'], $this->target->codes());
        $this->assertSame($expected['conditionCross'], $this->target->conditionCross());
        $this->assertSame($expected['dataType'], $this->target->dataType());
        $this->assertSame($expected['division'], $this->target->division());
        $this->assertSame($expected['progIds'], $this->target->progIds());
        $this->assertSame($expected['regionId'], $this->target->regionId());
        $this->assertSame($expected['timeBoxIds'], $this->target->timeBoxIds());
        $this->assertSame($expected['baseDivision'], $this->target->baseDivision());
        $this->assertSame($expected['dataTypeFlags'], $this->target->dataTypeFlags());
        $this->assertSame($expected['dataTypeConst'], $this->target->dataTypeConst());
        $this->assertSame($expected['prefixes'], $this->target->prefixes());
        $this->assertSame($expected['selectedPersonalName'], $this->target->selectedPersonalName());
        $this->assertSame($expected['codeNumber'], $this->target->codeNumber());
    }
}
