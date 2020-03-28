<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Rdb;

use Carbon\Carbon;
use Mockery;
use Switchm\SmartApi\Queries\Dao\Rdb\PerMinutesDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class PerMinutesDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(new Carbon('2020-01-01 10:00:00'));
        $this->target = Mockery::mock(PerMinutesDao::class, [])->makePartial();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    /**
     * @test
     * @dataProvider getDataProvider
     * @param $channelType
     * @param $hour
     * @param $regionId
     */
    public function getRatingData($channelType, $hour, $regionId): void
    {
        $startDateTime = new Carbon();
        $endDateTime = new Carbon();
        $channelIds = [1, 2, 3];
        $division = 'ga8';
        $code = 'personal';
        $dataDivision = '';
        $conditionCross = [];
        $isOriginal = false;

        $expected = [];

        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getPerMinutesLatestDateTime')
            ->with($regionId, 100, 60)
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->getRatingData(
            $startDateTime,
            $endDateTime,
            $channelType,
            $channelIds,
            $division,
            $code,
            $hour,
            $dataDivision,
            $conditionCross,
            $isOriginal,
            $regionId,
            100,
            60
        );

        $this->assertEquals($expected, $actual);
    }

    public function getDataProvider(): array
    {
        return [
            [
                /*channelType*/ 'dt1',
                /*hour*/ '4',
                /*regionId*/ 1,
            ],
            [
                /*channelType*/ 'dt2',
                /*hour*/ '6',
                /*regionId*/ 1,
            ],
            [
                /*channelType*/ 'summary',
                /*hour*/ '8',
                /*regionId*/ 1,
            ],
            [
                /*channelType*/ 'dt2',
                /*hour*/ '6',
                /*regionId*/ 2,
            ],
            [
                /*channelType*/ 'bs1',
                /*hour*/ '6',
                /*regionId*/ 2,
            ],
            [
                /*channelType*/ 'summary',
                /*hour*/ '8',
                /*regionId*/ 2,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider getDataProvider
     * @param $channelType
     * @param $hour
     * @param $regionId
     */
    public function getShareData($channelType, $hour, $regionId): void
    {
        $startDateTime = new Carbon();
        $endDateTime = new Carbon();
        $channelIds = [1, 2, 3];
        $division = 'ga8';
        $code = 'personal';
        $dataDivision = '';
        $conditionCross = [];
        $isOriginal = false;

        $expected = [];

        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getPerMinutesLatestDateTime')
            ->with($regionId, 100, 60)
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->getShareData(
            $startDateTime,
            $endDateTime,
            $channelType,
            $channelIds,
            $division,
            $code,
            $hour,
            $dataDivision,
            $conditionCross,
            $isOriginal,
            $regionId,
            100,
            60
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider getTargetDataDataProvider
     * @param $channelType
     * @param $hour
     * @param $regionId
     * @param $dataDivision
     */
    public function getTargetData($channelType, $hour, $regionId, $dataDivision): void
    {
        $startDateTime = new Carbon();
        $endDateTime = new Carbon();
        $channelIds = [1, 2, 3];
        $division = 'ga8';
        $code = 'personal';
        $conditionCross = [];
        $isOriginal = false;

        $expected = [];

        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getPerMinutesLatestDateTime')
            ->with($regionId, 100, 60)
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->getTargetData(
            $startDateTime,
            $endDateTime,
            $channelType,
            $channelIds,
            $division,
            $code,
            $hour,
            $dataDivision,
            $conditionCross,
            $isOriginal,
            $regionId,
            100,
            60
        );

        $this->assertEquals($expected, $actual);
    }

    public function getTargetDataDataProvider()
    {
        return [
            [
                /*channelType*/ 'dt1',
                /*hour*/ '4',
                /*regionId*/ 1,
                /*dataDivision*/ '',
            ],
            [
                /*channelType*/ 'dt2',
                /*hour*/ '6',
                /*regionId*/ 1,
                /*dataDivision*/ 'target_content_personal',
            ],
            [
                /*channelType*/ 'summary',
                /*hour*/ '8',
                /*regionId*/ 1,
                /*dataDivision*/ 'target_content_household',
            ],
            [
                /*channelType*/ 'dt2',
                /*hour*/ '6',
                /*regionId*/ 2,
                /*dataDivision*/ 'target_content_household',
            ],
            [
                /*channelType*/ 'bs1',
                /*hour*/ '6',
                /*regionId*/ 2,
                /*dataDivision*/ 'target_content_personal',
            ],
            [
                /*channelType*/ 'summary',
                /*hour*/ '8',
                /*regionId*/ 2,
                /*dataDivision*/ '',
            ],
        ];
    }
}
