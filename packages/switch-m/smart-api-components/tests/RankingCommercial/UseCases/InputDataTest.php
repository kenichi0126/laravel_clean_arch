<?php

namespace Switchm\SmartApi\Components\Tests\RankingCommercial\UseCases;

use Carbon\Carbon;
use Switchm\SmartApi\Components\RankingCommercial\Get\UseCases\InputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class InputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param $startDateTime
     * @param $endDateTime
     * @param $page
     * @param $holiday
     * @param $wdays
     * @param $division
     * @param $dateRange
     * @param $dataType
     * @param $regionId
     * @param $cmType
     * @param $codes
     * @param $conditionCross
     * @param $channels
     * @param $order
     * @param $conv15SecFlag
     * @param $period
     * @param $dispCount
     * @param $csvFlag
     * @param $cmLargeGenres
     * @param $axisType
     * @param $draw
     * @param $userId
     * @param mixed $broadcasterCompanyIds
     * @param mixed $axisTypeCompany
     * @param mixed $axisTypeProduct
     * @param $baseDivision
     * @param $expected
     */
    public function getterTest(
        $startDateTime,
        $endDateTime,
        $page,
        $holiday,
        $wdays,
        $division,
        $dateRange,
        $dataType,
        $regionId,
        $cmType,
        $codes,
        $conditionCross,
        $channels,
        $order,
        $conv15SecFlag,
        $period,
        $dispCount,
        $csvFlag,
        $cmLargeGenres,
        $axisType,
        $draw,
        $userId,
        $broadcasterCompanyIds,
        $axisTypeCompany,
        $axisTypeProduct,
        $baseDivision,
        $expected
    ): void {
        $this->target = new InputData(
            $startDateTime,
            $endDateTime,
            $page,
            $holiday,
            $wdays,
            $division,
            $dateRange,
            $dataType,
            $regionId,
            $cmType,
            $codes,
            $conditionCross,
            $channels,
            $order,
            $conv15SecFlag,
            $period,
            $dispCount,
            $csvFlag,
            $cmLargeGenres,
            $axisType,
            $draw,
            $userId,
            $broadcasterCompanyIds,
            $axisTypeCompany,
            $axisTypeProduct,
            $baseDivision
        );

        $this->assertEquals($expected['startDateTime'], $this->target->startDateTime());
        $this->assertEquals($expected['endDateTime'], $this->target->endDateTime());
        $this->assertEquals($expected['page'], $this->target->page());
        $this->assertEquals($expected['holiday'], $this->target->isHoliday());
        $this->assertEquals($expected['wdays'], $this->target->wdays());
        $this->assertEquals($expected['division'], $this->target->division());
        $this->assertEquals($expected['dateRange'], $this->target->dateRange());
        $this->assertEquals($expected['dataType'], $this->target->dataType());
        $this->assertEquals($expected['regionId'], $this->target->regionId());
        $this->assertEquals($expected['cmType'], $this->target->cmType());
        $this->assertEquals($expected['codes'], $this->target->codes());
        $this->assertEquals($expected['conditionCross'], $this->target->conditionCross());
        $this->assertEquals($expected['channels'], $this->target->channels());
        $this->assertEquals($expected['order'], $this->target->order());
        $this->assertEquals($expected['conv15SecFlag'], $this->target->conv15SecFlag());
        $this->assertEquals($expected['period'], $this->target->period());
        $this->assertEquals($expected['dispCount'], $this->target->dispCount());
        $this->assertEquals($expected['csvFlag'], $this->target->csvFlag());
        $this->assertEquals($expected['cmLargeGenres'], $this->target->cmLargeGenres());
        $this->assertEquals($expected['axisType'], $this->target->axisType());
        $this->assertEquals($expected['draw'], $this->target->draw());
        $this->assertEquals($expected['userId'], $this->target->userId());
        $this->assertEquals($expected['broadcasterCompanyIds'], $this->target->broadcasterCompanyIds());
        $this->assertEquals($expected['axisTypeCompany'], $this->target->axisTypeCompany());
        $this->assertEquals($expected['axisTypeProduct'], $this->target->axisTypeProduct());
        $this->assertEquals($expected['baseDivision'], $this->target->baseDivision());
    }

    public function dataProvider()
    {
        return [
            [
                /*startDateTime*/ '2019-01-01 05:00:00',
                /*endDateTime*/ '2019-01-07 04:59:59',
                /*page*/ 1,
                /*holiday*/ true,
                /*wdays*/ [1, 2, 3, 4, 5, 6, 0],
                /*division*/ 'ga8',
                /*dateRange*/ 1,
                /*dataType*/ [0],
                /*regionId*/ 1,
                /*cmType*/ [0],
                /*codes*/ [],
                /*conditionCross*/ [],
                /*channels*/ [1, 2, 3, 4, 5],
                /*order*/ [],
                /*conv15SecFlag*/ '1',
                /*period*/ 'period',
                /*dispCount*/ 20,
                /*csvFlag*/ '1',
                /*cmLargeGenres*/ '0',
                /*axisType*/ [],
                /*draw*/ '1',
                /*userId*/ 1,
                /*broadcasterCompanyIds*/ [1],
                /*axisTypeCompany*/ '2',
                /*axisTypeProduct*/ '1',
                /*baseDivision*/ ['ga8', 'ga12', 'ga10s', 'gm', 'oc'],
                /*expected*/ [
                    'startDateTime' => Carbon::parse('2019-01-01 05:00:00'),
                    'endDateTime' => Carbon::parse('2019-01-07 04:59:59'),
                    'page' => 1,
                    'holiday' => true,
                    'wdays' => [1, 2, 3, 4, 5, 6, 0],
                    'division' => 'ga8',
                    'dateRange' => 1,
                    'dataType' => [0],
                    'regionId' => 1,
                    'cmType' => [0],
                    'codes' => [],
                    'conditionCross' => [],
                    'channels' => [1, 2, 3, 4, 5],
                    'order' => [],
                    'conv15SecFlag' => '1',
                    'period' => 'period',
                    'dispCount' => 20,
                    'csvFlag' => '1',
                    'cmLargeGenres' => '0',
                    'axisType' => [],
                    'draw' => '1',
                    'userId' => 1,
                    'broadcasterCompanyIds' => [1],
                    'axisTypeCompany' => '2',
                    'axisTypeProduct' => '1',
                    'baseDivision' => ['ga8', 'ga12', 'ga10s', 'gm', 'oc'],
                ],
            ],
            [
                /*startDateTime*/ '2019-01-01 05:00:00',
                /*endDateTime*/ '2019-01-07 04:59:59',
                /*page*/ 1,
                /*holiday*/ false,
                /*wdays*/ [],
                /*division*/ 'ga8',
                /*dateRange*/ 1,
                /*dataType*/ [0],
                /*regionId*/ 1,
                /*cmType*/ [0],
                /*codes*/ [],
                /*conditionCross*/ [],
                /*channels*/ [1, 2, 3, 4, 5],
                /*order*/ [],
                /*conv15SecFlag*/ '1',
                /*period*/ 'period',
                /*dispCount*/ 20,
                /*csvFlag*/ '1',
                /*cmLargeGenres*/ '0',
                /*axisType*/ [],
                /*draw*/ '1',
                /*userId*/ 1,
                /*broadcasterCompanyIds*/ [1],
                /*axisTypeCompany*/ '2',
                /*axisTypeProduct*/ '1',
                /*baseDivision*/ ['ga8', 'ga12', 'ga10s', 'gm', 'oc'],
                /*expected*/ [
                'startDateTime' => Carbon::parse('2019-01-01 05:00:00'),
                'endDateTime' => Carbon::parse('2019-01-07 04:59:59'),
                'page' => 1,
                'holiday' => false,
                'wdays' => [],
                'division' => 'ga8',
                'dateRange' => 1,
                'dataType' => [0],
                'regionId' => 1,
                'cmType' => [0],
                'codes' => [],
                'conditionCross' => [],
                'channels' => [1, 2, 3, 4, 5],
                'order' => [],
                'conv15SecFlag' => '1',
                'period' => 'period',
                'dispCount' => 20,
                'csvFlag' => '1',
                'cmLargeGenres' => '0',
                'axisType' => [],
                'draw' => '1',
                'userId' => 1,
                'broadcasterCompanyIds' => [1],
                'axisTypeCompany' => '2',
                'axisTypeProduct' => '1',
                'baseDivision' => ['ga8', 'ga12', 'ga10s', 'gm', 'oc'],
            ],
            ],
        ];
    }
}
