<?php

namespace Switchm\SmartApi\Components\Tests\CommercialGrp\UseCases;

use Carbon\Carbon;
use Switchm\SmartApi\Components\CommercialGrp\Get\UseCases\InputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class InputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new InputData(
            '2019-01-01 05:00:00',
            '2019-01-07 04:59:59',
            1,
            [0],
            'ga8',
            [],
            1,
            1,
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            '1',
            'period',
            '0',
            20,
            '1',
            '1',
            null,
            1,
            50,
            ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            [],
            32,
            'code',
            'number',
            'selected_personal',
            [
                'REALTIME' => 0,
                'TIMESHIFT' => 1,
                'GROSS' => 2,
                'TOTAL' => 3,
                'RT_TOTAL' => 4,
            ]
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
            'page' => 1,
            'dataType' => [0],
            'division' => 'ga8',
            'conditionCross' => [],
            'regionId' => 1,
            'dateRange' => 1,
            'productIds' => [],
            'companyIds' => [],
            'cmType' => [],
            'cmSeconds' => [],
            'progIds' => [],
            'codes' => [],
            'cmIds' => [],
            'channels' => [],
            'conv15SecFlag' => '1',
            'period' => 'period',
            'allChannels' => '0',
            'dispCount' => 20,
            'csvFlag' => '1',
            'draw' => '1',
            'user' => null,
            'userId' => 1,
            'sampleCountMaxNumber' => 50,
            'dataTypeFlags' => ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            'baseDivision' => [],
            'codeNumber' => 32,
            'sampleCodePrefix' => 'code',
            'sampleCodeNumberPrefix' => 'number',
            'selectedPersonalName' => 'selected_personal',
            'dataTypes' => [
                'REALTIME' => 0,
                'TIMESHIFT' => 1,
                'GROSS' => 2,
                'TOTAL' => 3,
                'RT_TOTAL' => 4,
            ],
        ];

        $this->assertEquals($expected['startDateTime'], $this->target->startDateTime());
        $this->assertEquals($expected['endDateTime'], $this->target->endDateTime());
        $this->assertSame($expected['page'], $this->target->page());
        $this->assertSame($expected['dataType'], $this->target->dataType());
        $this->assertSame($expected['division'], $this->target->division());
        $this->assertSame($expected['conditionCross'], $this->target->conditionCross());
        $this->assertSame($expected['regionId'], $this->target->regionId());
        $this->assertSame($expected['dateRange'], $this->target->dateRange());
        $this->assertSame($expected['productIds'], $this->target->productIds());
        $this->assertSame($expected['companyIds'], $this->target->companyIds());
        $this->assertSame($expected['cmType'], $this->target->cmType());
        $this->assertSame($expected['cmSeconds'], $this->target->cmSeconds());
        $this->assertSame($expected['progIds'], $this->target->progIds());
        $this->assertSame($expected['codes'], $this->target->codes());
        $this->assertSame($expected['cmIds'], $this->target->cmIds());
        $this->assertSame($expected['channels'], $this->target->channels());
        $this->assertSame($expected['conv15SecFlag'], $this->target->conv15SecFlag());
        $this->assertSame($expected['period'], $this->target->period());
        $this->assertSame($expected['allChannels'], $this->target->allChannels());
        $this->assertSame($expected['dispCount'], $this->target->dispCount());
        $this->assertSame($expected['csvFlag'], $this->target->csvFlag());
        $this->assertSame($expected['draw'], $this->target->draw());
        $this->assertSame($expected['user'], $this->target->user());
        $this->assertSame($expected['userId'], $this->target->userId());
        $this->assertSame($expected['sampleCountMaxNumber'], $this->target->sampleCountMaxNumber());
        $this->assertSame($expected['dataTypeFlags'], $this->target->dataTypeFlags());
        $this->assertSame($expected['baseDivision'], $this->target->baseDivision());
        $this->assertSame($expected['codeNumber'], $this->target->codeNumber());
        $this->assertSame($expected['sampleCodePrefix'], $this->target->sampleCodePrefix());
        $this->assertSame($expected['sampleCodeNumberPrefix'], $this->target->sampleCodeNumberPrefix());
        $this->assertSame($expected['selectedPersonalName'], $this->target->selectedPersonalName());
        $this->assertSame($expected['dataTypes'], $this->target->dataTypes());
    }
}
