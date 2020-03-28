<?php

namespace Switchm\SmartApi\Components\Tests\ProgramPeriodAverage\Get\UseCases;

use Switchm\SmartApi\Components\ProgramPeriodAverage\Get\UseCases\InputData;
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
            // holiday
            'true',
            // dataType
            [0],
            // wdays
            [1],
            // genres
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
            // channels
            [],
            // programTypes
            [],
            // dispAverage
            '',
            // dataTypeFlags
            ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            // BaseDivision
            ['ga8', 'ga12', 'ga10s', 'gm', 'oc'],
            // sampleMax
            50,
            // userId
            1,
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
            'holiday' => 'true',
            'dataType' => [0],
            'wdays' => [1],
            'genres' => [],
            'dispCount' => 20,
            'dateRange' => 100,
            'page' => 1,
            'regionId' => 1,
            'division' => 'ga8',
            'conditionCross' => [],
            'csvFlag' => 0,
            'draw' => 1,
            'codes' => [],
            'channels' => [],
            'programTypes' => [],
            'dispAverage' => '',
            'dataTypeFlags' => ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            'baseDivision' => ['ga8', 'ga12', 'ga10s', 'gm', 'oc'],
            'sampleCountMaxNumber' => 50,
            'userId' => 1,
            'prefixes' => ['code' => 'code', 'number' => 'number'],
            'selectedPersonalName' => 'selected_personal',
            'codeNumber' => 32,
        ];

        $this->assertSame($expected['startDateTime'], $this->target->startDateTime());
        $this->assertSame($expected['endDateTime'], $this->target->endDateTime());
        $this->assertSame($expected['holiday'], $this->target->holiday());
        $this->assertSame($expected['dataType'], $this->target->dataType());
        $this->assertSame($expected['wdays'], $this->target->wdays());
        $this->assertSame($expected['genres'], $this->target->genres());
        $this->assertSame($expected['dispCount'], $this->target->dispCount());
        $this->assertSame($expected['dateRange'], $this->target->dateRange());
        $this->assertSame($expected['page'], $this->target->page());
        $this->assertSame($expected['regionId'], $this->target->regionId());
        $this->assertSame($expected['division'], $this->target->division());
        $this->assertSame($expected['conditionCross'], $this->target->conditionCross());
        $this->assertSame($expected['csvFlag'], $this->target->csvFlag());
        $this->assertSame($expected['draw'], $this->target->draw());
        $this->assertSame($expected['codes'], $this->target->codes());
        $this->assertSame($expected['channels'], $this->target->channels());
        $this->assertSame($expected['programTypes'], $this->target->programTypes());
        $this->assertSame($expected['dispAverage'], $this->target->dispAverage());
        $this->assertSame($expected['dataTypeFlags'], $this->target->dataTypeFlags());
        $this->assertSame($expected['baseDivision'], $this->target->baseDivision());
        $this->assertSame($expected['sampleCountMaxNumber'], $this->target->sampleCountMaxNumber());
        $this->assertSame($expected['userId'], $this->target->userId());
        $this->assertSame($expected['prefixes'], $this->target->prefixes());
        $this->assertSame($expected['selectedPersonalName'], $this->target->selectedPersonalName());
        $this->assertSame($expected['codeNumber'], $this->target->codeNumber());
    }
}
