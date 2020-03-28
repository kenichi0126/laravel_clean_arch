<?php

namespace Switchm\SmartApi\Components\Tests\ProgramList\Get\UseCases;

use Switchm\SmartApi\Components\ProgramList\Get\UseCases\InputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class InputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new InputData(
            // startDateTime
            '2019-01-01 05:00:00',
            // endDateTime
            '2019-01-07 05:00:00',
            // digitalAndBs
            'digital',
            // digitalKanto
            [1],
            // bs1
            [98],
            // bs2
            [99],
            // holiday
            'true',
            // dataType
            [0],
            // wdays
            [1],
            // genres
            [],
            // programNames
            ['test'],
            // order
            [],
            // dispCount
            20,
            // dateRange
            100,
            // page
            1,
            // regionId
            1,
            // division
            'ga8',
            // conditionCross
            [],
            // csvFlag
            0,
            // draw
            1,
            // codes
            [],
            // dataTypeFlag
            ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            // userId
            1,
            // hasPermission
            true,
            // BaseDivision
            ['ga8', 'ga12', 'ga10s', 'gm', 'oc'],
            // sampleMax
            50,
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
            'startDateTime' => '2019-01-01 05:00:00',
            'endDateTime' => '2019-01-07 05:00:00',
            'digitalAndBs' => 'digital',
            'digitalKanto' => [1],
            'bs1' => [98],
            'bs2' => [99],
            'holiday' => 'true',
            'dataType' => [0],
            'wdays' => [1],
            'genres' => [],
            'programNames' => ['test'],
            'order' => [],
            'dispCount' => 20,
            'dateRange' => 100,
            'page' => 1,
            'regionId' => 1,
            'division' => 'ga8',
            'conditionCross' => [],
            'csvFlag' => 0,
            'draw' => 1,
            'codes' => [],
            'dataTypeFlags' => ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            'userId' => 1,
            'hasPermission' => true,
            'baseDivision' => ['ga8', 'ga12', 'ga10s', 'gm', 'oc'],
            'sampleCountMaxNumber' => 50,
            'dataTypeConst' => ['rt' => 0, 'ts' => 1, 'total' => 3, 'gross' => 2, 'rtTotal' => 4],
            'prefixes' => ['code' => 'code', 'number' => 'number'],
            'selectedPersonalName' => 'selected_personal',
            'codeNumber' => 32,
        ];

        $this->assertSame($expected['startDateTime'], $this->target->startDateTime());
        $this->assertSame($expected['endDateTime'], $this->target->endDateTime());
        $this->assertSame($expected['digitalAndBs'], $this->target->digitalAndBs());
        $this->assertSame($expected['digitalKanto'], $this->target->digitalKanto());
        $this->assertSame($expected['bs1'], $this->target->bs1());
        $this->assertSame($expected['bs2'], $this->target->bs2());
        $this->assertSame($expected['holiday'], $this->target->holiday());
        $this->assertSame($expected['dataType'], $this->target->dataType());
        $this->assertSame($expected['wdays'], $this->target->wdays());
        $this->assertSame($expected['genres'], $this->target->genres());
        $this->assertSame($expected['programNames'], $this->target->programNames());

        $this->assertSame($expected['order'], $this->target->order());
        $this->assertSame($expected['dispCount'], $this->target->dispCount());
        $this->assertSame($expected['dateRange'], $this->target->dateRange());
        $this->assertSame($expected['page'], $this->target->page());
        $this->assertSame($expected['regionId'], $this->target->regionId());
        $this->assertSame($expected['division'], $this->target->division());
        $this->assertSame($expected['conditionCross'], $this->target->conditionCross());
        $this->assertSame($expected['csvFlag'], $this->target->csvFlag());
        $this->assertSame($expected['draw'], $this->target->draw());
        $this->assertSame($expected['codes'], $this->target->codes());
        $this->assertSame($expected['dataTypeFlags'], $this->target->dataTypeFlags());

        $this->assertSame($expected['userId'], $this->target->userId());
        $this->assertSame($expected['hasPermission'], $this->target->hasPermission());
        $this->assertSame($expected['baseDivision'], $this->target->baseDivision());
        $this->assertSame($expected['sampleCountMaxNumber'], $this->target->sampleCountMaxNumber());
        $this->assertSame($expected['dataTypeConst'], $this->target->dataTypeConst());
        $this->assertSame($expected['prefixes'], $this->target->prefixes());
        $this->assertSame($expected['selectedPersonalName'], $this->target->selectedPersonalName());
        $this->assertSame($expected['codeNumber'], $this->target->codeNumber());
    }
}
