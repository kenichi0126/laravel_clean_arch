<?php

namespace Switchm\SmartApi\Components\Tests\RafCsv\UseCases;

use Carbon\Carbon;
use Switchm\SmartApi\Components\RafCsv\Get\UseCases\InputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class InputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $request = [
            'endDateTime' => '2019-06-13 04:59:00',
            'startDateTime' => '2019-06-12 05:00:00',
            'channels' => [
                3,
                4,
                5,
                6,
                7,
            ],
            'companyIds' => [],
            'cmType' => 0,
            'cmSeconds' => 1,
            'division' => 'ga8',
            'codes' => [
                'c',
            ],
            'conditionCross' => [
                'gender' => [
                    '',
                ],
                'age' => [
                    'from' => 4,
                    'to' => 99,
                ],
                'occupation' => [
                    '',
                ],
                'married' => [
                    '',
                ],
                'dispOccupation' => [
                    '',
                ],
            ],
            'reachAndFrequencyGroupingUnit' => [
                3,
                6,
                9,
            ],
            'axisType' => 0,
            'channelAxis' => 0,
            'period' => 'day',
            'codeNames' => [
                [
                    'division' => 'ga8',
                    'code' => 'c',
                    'division_name' => '性・年齢8区分',
                    'name' => 'C',
                    'division_order' => 101,
                    'display_order' => 1,
                ],
            ],
            'productIds' => [
                52874,
            ],
            'cmIds' => [],
            'regionId' => 1,
            'conv_15_sec_flag' => 1,
            'progIds' => [],
            'dataType' => [
                0,
            ],
            'dateRange' => 2,
            'csvFlag' => 0,
            'dataTypeFlags' => [
                'isRt' => true,
                'isTs' => false,
                'isGross' => false,
                'isTotal' => false,
                'isRtTotal' => false,
            ],
            'axisTypeProduct' => '2',
            'axisTypeCompany' => '1',
            'axisLimit' => 30,
            'userId' => 1,
            'baseDivision' => [
                'ga8',
                'ga12',
                'ga10s',
                'gm',
                'oc',
            ],
        ];

        $this->target = new InputData(
            $request['startDateTime'],
            $request['endDateTime'],
            $request['dataType'],
            $request['dateRange'],
            $request['regionId'],
            $request['division'],
            $request['conditionCross'],
            $request['csvFlag'],
            $request['codes'],
            $request['channels'],
            $request['axisType'],
            $request['channelAxis'],
            $request['cmIds'],
            $request['cmSeconds'],
            $request['cmType'],
            $request['codeNames'],
            $request['companyIds'],
            $request['conv_15_sec_flag'],
            $request['period'],
            $request['productIds'],
            $request['progIds'],
            $request['reachAndFrequencyGroupingUnit'],
            $request['dataTypeFlags'],
            $request['axisTypeProduct'],
            $request['axisTypeCompany'],
            $request['axisLimit'],
            $request['userId'],
            $request['baseDivision']
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'startDateTime' => Carbon::parse('2019-06-12 05:00:00'),
            'endDateTime' => Carbon::parse('2019-06-13 04:59:00'),
            'dataType' => [0],
            'dateRange' => 2,
            'regionId' => 1,
            'division' => 'ga8',
            'conditionCross' => ['gender' => [''], 'age' => ['from' => 4, 'to' => 99], 'occupation' => [''], 'married' => [''], 'dispOccupation' => ['']],
            'csvFlag' => 0,
            'codes' => ['c'],
            'channels' => [3, 4, 5, 6, 7],
            'axisType' => '0',
            'channelAxis' => 0,
            'cmIds' => [],
            'cmSeconds' => 1,
            'cmType' => 0,
            'codeNames' => [['division' => 'ga8', 'code' => 'c', 'division_name' => '性・年齢8区分', 'name' => 'C', 'division_order' => 101, 'display_order' => 1]],
            'companyIds' => [],
            'conv_15_sec_flag' => 1,
            'period' => 'day',
            'productIds' => [52874],
            'progIds' => [],
            'reachAndFrequencyGroupingUnit' => [3, 6, 9],
            'dataTypeFlags' => [
                'isRt' => true,
                'isTs' => false,
                'isGross' => false,
                'isTotal' => false,
                'isRtTotal' => false,
            ],
            'axisTypeProduct' => '2',
            'axisTypeCompany' => '1',
            'axisLimit' => 30,
            'userId' => 1,
            'baseDivision' => [
                'ga8',
                'ga12',
                'ga10s',
                'gm',
                'oc',
            ],
        ];

        $this->assertEquals($expected['startDateTime'], $this->target->startDateTime());
        $this->assertEquals($expected['endDateTime'], $this->target->endDateTime());
        $this->assertSame($expected['dataType'], $this->target->dataType());
        $this->assertSame($expected['dateRange'], $this->target->dateRange());
        $this->assertSame($expected['regionId'], $this->target->regionId());
        $this->assertSame($expected['division'], $this->target->division());
        $this->assertSame($expected['conditionCross'], $this->target->conditionCross());
        $this->assertSame($expected['csvFlag'], $this->target->csvFlag());
        $this->assertSame($expected['codes'], $this->target->codes());
        $this->assertSame($expected['channels'], $this->target->channels());
        $this->assertSame($expected['axisType'], $this->target->axisType());
        $this->assertSame($expected['channelAxis'], $this->target->channelAxis());
        $this->assertSame($expected['cmIds'], $this->target->cmIds());
        $this->assertSame($expected['cmSeconds'], $this->target->cmSeconds());
        $this->assertSame($expected['cmType'], $this->target->cmType());
        $this->assertSame($expected['codeNames'], $this->target->codeNames());
        $this->assertSame($expected['companyIds'], $this->target->companyIds());
        $this->assertSame($expected['conv_15_sec_flag'], $this->target->conv15SecFlag());
        $this->assertSame($expected['period'], $this->target->period());
        $this->assertSame($expected['productIds'], $this->target->productIds());
        $this->assertSame($expected['progIds'], $this->target->progIds());
        $this->assertSame($expected['reachAndFrequencyGroupingUnit'], $this->target->reachAndFrequencyGroupingUnit());
        $this->assertSame($expected['dataTypeFlags'], $this->target->dataTypeFlags());
        $this->assertSame($expected['axisTypeProduct'], $this->target->axisTypeProduct());
        $this->assertSame($expected['axisLimit'], $this->target->productAxisLimit());
        $this->assertSame($expected['userId'], $this->target->userID());
        $this->assertSame($expected['axisTypeCompany'], $this->target->axisTypeCompany());
    }
}
