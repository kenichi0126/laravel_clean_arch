<?php

namespace Switchm\SmartApi\Components\Tests\CommercialList\Get\UseCases;

use Carbon\Carbon;
use Switchm\SmartApi\Components\CommercialList\Get\UseCases\InputData;
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
            '1',
            '7',
            '1',
            '2',
            null,
            '1',
            'ga8',
            'f1',
            [],
            [],
            [],
            null,
            [1, 2, 3, 4, 5],
            [],
            '20',
            '1',
            '0',
            [0],
            '1',
            new \stdClass(),
            1,
            50,
            [],
            [],
            'codeNumber',
            'sampleCodePrefix',
            'sampleCodeNumberPrefix',
            'selectedPersonalName',
            ['dataTypes'],
            true,
            false
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
            'page' => '1',
            'dateRange' => '7',
            'cmType' => '1',
            'cmSeconds' => '2',
            'progIds' => null,
            'regionId' => '1',
            'division' => 'ga8',
            'codes' => 'f1',
            'conditionCross' => [],
            'companyIds' => [],
            'productIds' => [],
            'cmIds' => null,
            'channels' => [1, 2, 3, 4, 5],
            'order' => [],
            'dispCount' => '20',
            'conv_15_sec_flag' => '1',
            'csvFlag' => '0',
            'dataType' => [0],
            'draw' => '1',
            'user' => new \stdClass(),
            'userId' => 1,
            'sampleCountMaxNumber' => 50,
            'dataTypeFlags' => [],
            'baseDivision' => [],
            'codeNumber' => 'codeNumber',
            'sampleCodePrefix' => 'sampleCodePrefix',
            'sampleCodeNumberPrefix' => 'sampleCodeNumberPrefix',
            'selectedPersonalName' => 'selectedPersonalName',
            'dataTypes' => ['dataTypes'],
            'cmMaterialFlag' => true,
            'cmTypeFlag' => false,
        ];

        $this->assertEquals($expected['startDateTime'], $this->target->startDateTime());
        $this->assertEquals($expected['endDateTime'], $this->target->endDateTime());
        $this->assertSame($expected['page'], $this->target->page());
        $this->assertSame($expected['dateRange'], $this->target->dateRange());
        $this->assertSame($expected['cmType'], $this->target->cmType());
        $this->assertSame($expected['cmSeconds'], $this->target->cmSeconds());
        $this->assertSame($expected['progIds'], $this->target->progIds());
        $this->assertSame($expected['regionId'], $this->target->regionId());
        $this->assertSame($expected['division'], $this->target->division());
        $this->assertSame($expected['codes'], $this->target->codes());
        $this->assertSame($expected['conditionCross'], $this->target->conditionCross());
        $this->assertSame($expected['companyIds'], $this->target->companyIds());
        $this->assertSame($expected['productIds'], $this->target->productIds());
        $this->assertSame($expected['cmIds'], $this->target->cmIds());
        $this->assertSame($expected['channels'], $this->target->channels());
        $this->assertSame($expected['order'], $this->target->order());
        $this->assertSame($expected['dispCount'], $this->target->dispCount());
        $this->assertSame($expected['conv_15_sec_flag'], $this->target->conv15SecFlag());
        $this->assertSame($expected['csvFlag'], $this->target->csvFlag());
        $this->assertSame($expected['dataType'], $this->target->dataType());
        $this->assertSame($expected['draw'], $this->target->draw());
        $this->assertEquals($expected['user'], $this->target->user());
        $this->assertSame($expected['userId'], $this->target->userId());
        $this->assertSame($expected['sampleCountMaxNumber'], $this->target->sampleCountMaxNumber());
        $this->assertSame($expected['dataTypeFlags'], $this->target->dataTypeFlags());
        $this->assertSame($expected['baseDivision'], $this->target->baseDivision());
        $this->assertSame($expected['codeNumber'], $this->target->codeNumber());
        $this->assertSame($expected['sampleCodePrefix'], $this->target->sampleCodePrefix());
        $this->assertSame($expected['sampleCodeNumberPrefix'], $this->target->sampleCodeNumberPrefix());
        $this->assertSame($expected['selectedPersonalName'], $this->target->selectedPersonalName());
        $this->assertSame($expected['dataTypes'], $this->target->dataTypes());
        $this->assertSame($expected['cmMaterialFlag'], $this->target->cmMaterialFlag());
        $this->assertSame($expected['cmTypeFlag'], $this->target->cmTypeFlag());
    }
}
