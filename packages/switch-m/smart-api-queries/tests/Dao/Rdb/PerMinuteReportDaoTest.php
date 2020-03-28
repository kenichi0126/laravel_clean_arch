<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Rdb;

use Carbon\Carbon;
use Mockery;
use Switchm\SmartApi\Queries\Dao\Rdb\PerMinuteReportDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class PerMinuteReportDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = Mockery::mock(PerMinuteReportDao::class, [])->makePartial();
    }

    /**
     * @test
     */
    public function getSeconds(): void
    {
        $expected = [];

        $sceneList = [
            ['startDateTime' => 10, 'endDateTime' => 10],
            ['startDateTime' => 20, 'endDateTime' => 30],
        ];
        $channelId = '1';

        $bindings = [
            ':channel_id' => '1',
            ':keyFrom0' => 10,
            ':keyTo0' => 10,
            ':keyIndex0' => 0,
            ':keyFrom1' => 20,
            ':keyTo1' => 30,
            ':keyIndex1' => 1,
            ':minDate' => 10,
            ':maxDate' => 30,
        ];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any(), $bindings)
            ->andReturn($expected)
            ->once();

        $actual = $this->target->getSeconds($sceneList, $channelId);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function latest(): void
    {
        $expected = (object) [];
        $regionId = 1;
        $bindings[':regionId'] = $regionId;

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->with(Mockery::any(), $bindings)
            ->andReturn($expected)
            ->once();

        $actual = $this->target->latest($regionId);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getTableDetailReport(): void
    {
        $expected = [];

        $timeBoxIds = [10, 20];
        $dates = ['2019-10-10', '2019-10-11'];
        $hours = [5, 6];
        $minutes = [1, 2];
        $channelId = '1';
        $division = 'ga8';

        $bindings = [
            ':channel_id' => '1',
            ':division' => 'ga8',
            ':timeBoxIds0' => 10,
            ':timeBoxIds1' => 20,
            ':dates0' => '2019-10-10',
            ':dates1' => '2019-10-11',
            ':hours0' => 5,
            ':hours1' => 6,
            ':minutes0' => 1,
            ':minutes1' => 2,
            ':minDate' => new Carbon('2019-10-09 00:00:00.000000'),
            ':maxDate' => new Carbon('2019-10-12 23:59:00.000000'),
        ];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any(), $bindings)
            ->andReturn($expected)
            ->once();

        $actual = $this->target->getTableDetailReport($timeBoxIds, $dates, $hours, $minutes, $channelId, $division);

        $this->assertEquals($expected, $actual);
    }
}
