<?php

namespace Switchm\SmartApi\Components\Tests\RatingPerHourly\Get\UseCases;

use Carbon\Carbon;
use Switchm\SmartApi\Components\RatingPerHourly\Get\UseCases\InputData;
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
            '2019-01-07 04:59:59',
            // regionId
            1,
            // channels
            [3, 4, 5, 6, 7],
            // channelType
            [0],
            // division
            'ga8',
            // conditionCross
            [],
            // csvFlag
            '0',
            // draw
            '1',
            // code
            'personal',
            // dataDivision
            'viewing_rate',
            // dataType
            [0],
            // displayType
            'channelBy',
            // aggregateType
            'hourly',
            // hour
            'hourly',
            // sampleCountMaxNumber
            50,
            // dataTypeFlags
            ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            // userId
            1,
            // rdbDwhSearchPeriod
            [
                'rdbStartDate' => '20190101',
                'rdbEndDate' => '20190101',
                'dwhStartDate' => '20190101',
                'dwhEndDate' => '20190101',
                'isDwh' => true,
                'isRdb' => false,
            ],
            [
                'ga8',
                'ga12',
                'ga10s',
                'gm',
                'oc',
            ],
            100,
            60,
            'sampleCodePrefix',
            'sampleCodeNumberPrefix',
            'selectedPersonalName'
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'startDateTime' => Carbon::parse('2019-01-01 05:00:00'),
            'endDateTime' => Carbon::parse('2019-01-07 04:59:59'),
            'regionId' => 1,
            'channels' => [3, 4, 5, 6, 7],
            'channelType' => [0],
            'division' => 'ga8',
            'conditionCross' => [],
            'csvFlag' => '0',
            'draw' => '1',
            'code' => 'personal',
            'dataDivision' => 'viewing_rate',
            'dataType' => [0],
            'displayType' => 'channelBy',
            'aggregateType' => 'hourly',
            'hour' => 'hourly',
            'sampleCountMaxNumber' => 50,
            'dataTypeFlags' => ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            'userId' => 1,
            'rdbDwhSearchPeriod' => [
                'rdbStartDate' => '20190101',
                'rdbEndDate' => '20190101',
                'dwhStartDate' => '20190101',
                'dwhEndDate' => '20190101',
                'isDwh' => true,
                'isRdb' => false,
            ],
            'intervalHourly' => 100,
            'intervalMinutes' => 60,
            'sampleCodePrefix' => 'sampleCodePrefix',
            'sampleCodeNumberPrefix' => 'sampleCodeNumberPrefix',
            'selectedPersonalName' => 'selectedPersonalName',
        ];

        $this->assertEquals($expected['startDateTime'], $this->target->startDateTime());
        $this->assertEquals($expected['endDateTime'], $this->target->endDateTime());
        $this->assertSame($expected['regionId'], $this->target->regionId());
        $this->assertSame($expected['channels'], $this->target->channels());
        $this->assertSame($expected['channelType'], $this->target->channelType());
        $this->assertSame($expected['division'], $this->target->division());
        $this->assertSame($expected['conditionCross'], $this->target->conditionCross());
        $this->assertSame($expected['csvFlag'], $this->target->csvFlag());
        $this->assertSame($expected['draw'], $this->target->draw());
        $this->assertSame($expected['code'], $this->target->code());
        $this->assertSame($expected['dataDivision'], $this->target->dataDivision());
        $this->assertSame($expected['dataType'], $this->target->dataType());
        $this->assertSame($expected['displayType'], $this->target->displayType());
        $this->assertSame($expected['aggregateType'], $this->target->aggregateType());
        $this->assertSame($expected['hour'], $this->target->hour());
        $this->assertSame($expected['sampleCountMaxNumber'], $this->target->sampleCountMaxNumber());
        $this->assertSame($expected['dataTypeFlags'], $this->target->dataTypeFlags());
        $this->assertSame($expected['userId'], $this->target->userId());
        $this->assertSame($expected['rdbDwhSearchPeriod'], $this->target->rdbDwhSearchPeriod());
        $this->assertSame($expected['intervalHourly'], $this->target->intervalHourly());
        $this->assertSame($expected['intervalMinutes'], $this->target->intervalMinutes());
        $this->assertSame($expected['sampleCodePrefix'], $this->target->sampleCodePrefix());
        $this->assertSame($expected['sampleCodeNumberPrefix'], $this->target->sampleCodeNumberPrefix());
        $this->assertSame($expected['selectedPersonalName'], $this->target->selectedPersonalName());
    }
}
