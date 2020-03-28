<?php

namespace App\Http\UserInterfaces\CommercialAdvertising\Get;

use Carbon\Carbon;
use Switchm\SmartApi\Components\CommercialAdvertising\Get\UseCases\InputData;
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
            [],
            [],
            '',
            '',
            [],
            1,
            [],
            [1, 2, 3, 4],
            true,
            true,
            false,
            'ga8',
            [],
            '1',
            '',
            'personal',
            1,
            50,
            ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            [
                'ga8',
                'ga12',
                'ga10s',
                'gm',
                'oc',
            ],
            100,
            60,
            'codePrefix',
            'codeNumberPrefix',
            'personalName',
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
            'companyIds' => [],
            'productIds' => [],
            'cmType' => '',
            'cmSeconds' => '',
            'progIds' => [],
            'regionId' => 1,
            'cmIds' => [],
            'channels' => [1, 2, 3, 4],
            'heatMapRating' => true,
            'heatMapTciPersonal' => true,
            'heatMapTciHousehold' => false,
            'division' => 'ga8',
            'conditionCross' => [],
            'csvFlag' => '1',
            'draw' => '',
            'code' => 'personal',
            'userID' => 1,
            'sampleCountMaxNumber' => 50,
            'rdbDwhSearchPeriod' => ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
        ];

        $this->assertEquals($expected['startDateTime'], $this->target->startDateTime());
        $this->assertEquals($expected['endDateTime'], $this->target->endDateTime());
        $this->assertSame($expected['companyIds'], $this->target->companyIds());
        $this->assertSame($expected['productIds'], $this->target->productIds());
        $this->assertSame($expected['cmType'], $this->target->cmType());
        $this->assertSame($expected['cmSeconds'], $this->target->cmSeconds());
        $this->assertSame($expected['progIds'], $this->target->progIds());
        $this->assertSame($expected['regionId'], $this->target->regionId());
        $this->assertSame($expected['cmIds'], $this->target->cmIds());
        $this->assertSame($expected['channels'], $this->target->channels());
        $this->assertSame($expected['heatMapRating'], $this->target->heatMapRating());
        $this->assertSame($expected['heatMapTciPersonal'], $this->target->heatMapTciPersonal());
        $this->assertSame($expected['heatMapTciHousehold'], $this->target->heatMapTciHousehold());
        $this->assertSame($expected['division'], $this->target->division());
        $this->assertSame($expected['conditionCross'], $this->target->conditionCross());
        $this->assertSame($expected['csvFlag'], $this->target->csvFlag());
        $this->assertSame($expected['draw'], $this->target->draw());
        $this->assertSame($expected['code'], $this->target->code());
        $this->assertSame($expected['userID'], $this->target->userID());
        $this->assertSame($expected['sampleCountMaxNumber'], $this->target->sampleCountMaxNumber());
        $this->assertSame($expected['rdbDwhSearchPeriod'], $this->target->rdbDwhSearchPeriod());
    }
}
