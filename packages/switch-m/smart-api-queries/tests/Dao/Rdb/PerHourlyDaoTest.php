<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Rdb;

use Carbon\Carbon;
use Mockery;
use Switchm\SmartApi\Queries\Dao\Rdb\PerHourlyDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class PerHourlyDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = Mockery::mock(PerHourlyDao::class, [])->makePartial();
    }

    /**
     * @test
     * @dataProvider commonRatingDataProvider
     *
     * @param Carbon $startDateTime
     * @param Carbon $endDateTime
     * @param string $channelType
     * @param array $channelIds
     * @param string $division
     * @param string $code
     * @param string $dataDivision
     * @param array $conditionCross
     * @param bool $isOriginal
     * @param int $regionId
     * @param array $dataType
     * @param array $dataTypeFlags
     * @param array $expectBindsResult
     */
    public function searchgetRatingData(Carbon $startDateTime, Carbon $endDateTime, string $channelType, array $channelIds, string $division, string $code, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, array $dataType, array $dataTypeFlags, array $expectBindsResult): void
    {
        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getPerHourlyLatestDateTime')
            ->with($regionId, 100, 60)
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($expectBindsResult, 'expectBindsResult'))
            ->andReturn([])
            ->once()->ordered();

        $actual = $this->target->getRatingData($startDateTime, $endDateTime, $channelType, $channelIds, $division, $code, $dataDivision, $conditionCross, $isOriginal, $regionId, $dataType, $dataTypeFlags, 100, 60);
    }

    /**
     * @test
     * @dataProvider commonRatingDataProvider
     *
     * @param Carbon $startDateTime
     * @param Carbon $endDateTime
     * @param string $channelType
     * @param array $channelIds
     * @param string $division
     * @param string $code
     * @param string $dataDivision
     * @param array $conditionCross
     * @param bool $isOriginal
     * @param int $regionId
     * @param array $dataType
     * @param array $dataTypeFlags
     * @param array $expectBindsResult
     */
    public function getShareData(Carbon $startDateTime, Carbon $endDateTime, string $channelType, array $channelIds, string $division, string $code, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, array $dataType, array $dataTypeFlags, array $expectBindsResult): void
    {
        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getPerHourlyLatestDateTime')
            ->with($regionId, 100, 60)
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($expectBindsResult, 'expectBindsResult'))
            ->andReturn([])
            ->once()->ordered();

        $actual = $this->target->getShareData($startDateTime, $endDateTime, $channelType, $channelIds, $division, $code, $dataDivision, $conditionCross, $isOriginal, $regionId, $dataType, $dataTypeFlags, 100, 60);
    }

    public function commonRatingDataProvider()
    {
        // テストマトリクス
        $cases = ['case1', 'case2', 'case3'];
        $startDateTime = [new Carbon('2019-01-01 05:00:00'), new Carbon('2019-01-01 05:00:00'), new Carbon('2019-01-01 05:00:00')];
        $endDateTime = [new Carbon('2019-01-02 05:00:00'), new Carbon('2019-01-02 05:00:00'), new Carbon('2019-01-02 05:00:00')];
        $channelType = ['summary', 'dt2', 'dt2'];
        $channelIds = [[1], [1], [1]];
        $division = ['ga8', 'ga8', 'ga8'];
        $code = ['personal', 'personal', 'personal'];
        $dataDivision = ['dataDivision', 'dataDivision', 'dataDivision'];
        $conditionCross = [[], [], []];
        $isOriginal = [false, false, false];
        $regionId = [1, 2, 1, 2, 1, 2, 1];
        $dataType = [[1], [1, 2, 3, 4, 5], [1]];
        $dataTypeFlags = [['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => false], ['rt' => true, 'ts' => true, 'gross' => true, 'total' => true, 'rtTotal' => true], ['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => false]];
        $expectBindsResult = [[
            ':division' => 'ga8',
            ':code' => 'personal',
            ':startDate' => new Carbon('2019-01-01 05:00:00'),
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':latestDateTime' => null,
            ':channelId0' => 1,
            ':regionId' => 1,
        ], [
            ':division' => 'ga8',
            ':code' => 'personal',
            ':startDate' => new Carbon('2019-01-01 05:00:00'),
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':latestDateTime' => null,
            ':channelId0' => 1,
            ':regionId' => 2,
        ], [
            ':division' => 'ga8',
            ':code' => 'personal',
            ':startDate' => new Carbon('2019-01-01 05:00:00'),
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':latestDateTime' => null,
            ':channelId0' => 1,
            ':regionId' => 1,
        ]];

        foreach ($cases as $i => $val) {
            yield
            $val => [
                $startDateTime[$i],
                $endDateTime[$i],
                $channelType[$i],
                $channelIds[$i],
                $division[$i],
                $code[$i],
                $dataDivision[$i],
                $conditionCross[$i],
                $isOriginal[$i],
                $regionId[$i],
                $dataType[$i],
                $dataTypeFlags[$i],
                $expectBindsResult[$i],
            ];
        }
    }

    /**
     * @test
     * @dataProvider getTargetDataDataProvider
     *
     * @param Carbon $startDateTime
     * @param Carbon $endDateTime
     * @param string $channelType
     * @param array $channelIds
     * @param string $division
     * @param string $code
     * @param string $dataDivision
     * @param array $conditionCross
     * @param bool $isOriginal
     * @param int $regionId
     * @param array $dataType
     * @param array $dataTypeFlags
     * @param array $expectBindsResult
     */
    public function getTargetData(Carbon $startDateTime, Carbon $endDateTime, string $channelType, array $channelIds, string $division, string $code, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, array $dataType, array $dataTypeFlags, array $expectBindsResult): void
    {
        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getPerHourlyLatestDateTime')
            ->with($regionId, 100, 60)
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($expectBindsResult, 'expectBindsResult'))
            ->andReturn([])
            ->once()->ordered();

        $actual = $this->target->getTargetData($startDateTime, $endDateTime, $channelType, $channelIds, $division, $code, $dataDivision, $conditionCross, $isOriginal, $regionId, $dataType, $dataTypeFlags, 100, 60);
    }

    public function getTargetDataDataProvider()
    {
        // テストマトリクス
        $cases = ['case1', 'case2', 'case3'];
        $startDateTime = [new Carbon('2019-01-01 05:00:00'), new Carbon('2019-01-01 05:00:00'), new Carbon('2019-01-01 05:00:00')];
        $endDateTime = [new Carbon('2019-01-02 05:00:00'), new Carbon('2019-01-02 05:00:00'), new Carbon('2019-01-02 05:00:00')];
        $channelType = ['dt1', 'dt2', 'dt2'];
        $channelIds = [[1], [1], [1]];
        $division = ['ga8', 'ga8', 'ga8'];
        $code = ['personal', 'personal', 'personal'];
        $dataDivision = ['target_content_personal', 'target_content_household', 'dataDivision'];
        $conditionCross = [[], [], []];
        $isOriginal = [false, false, false];
        $regionId = [1, 2, 1, 2, 1, 2, 1];
        $dataType = [[1], [1, 2, 3, 4, 5], [1]];
        $dataTypeFlags = [['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => false], ['rt' => true, 'ts' => true, 'gross' => true, 'total' => true, 'rtTotal' => true], ['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => false]];
        $expectBindsResult = [[
            ':division' => 'ga8',
            ':code' => 'personal',
            ':startDate' => new Carbon('2019-01-01 05:00:00'),
            ':startTimestamp' => '2018-12-31 05:00:00',
            ':endTimestamp' => '2019-01-03 05:00:00',
            ':latestDateTime' => null,
            ':channelId0' => 1,
            ':regionId' => 1,
        ], [
            ':division' => 'ga8',
            ':code' => 'personal',
            ':startDate' => new Carbon('2019-01-01 05:00:00'),
            ':startTimestamp' => '2018-12-31 05:00:00',
            ':endTimestamp' => '2019-01-03 05:00:00',
            ':latestDateTime' => null,
            ':channelId0' => 1,
            ':regionId' => 2,
        ], [
            ':division' => 'ga8',
            ':code' => 'personal',
            ':startDate' => new Carbon('2019-01-01 05:00:00'),
            ':startTimestamp' => '2018-12-31 05:00:00',
            ':endTimestamp' => '2019-01-03 05:00:00',
            ':latestDateTime' => null,
            ':channelId0' => 1,
            ':regionId' => 1,
        ]];

        foreach ($cases as $i => $val) {
            yield
            $val => [
                $startDateTime[$i],
                $endDateTime[$i],
                $channelType[$i],
                $channelIds[$i],
                $division[$i],
                $code[$i],
                $dataDivision[$i],
                $conditionCross[$i],
                $isOriginal[$i],
                $regionId[$i],
                $dataType[$i],
                $dataTypeFlags[$i],
                $expectBindsResult[$i],
            ];
        }
    }

    /**
     * @test
     * @dataProvider commonCustomRatingDataProvider
     *
     * @param Carbon $startDateTime
     * @param Carbon $endDateTime
     * @param string $channelType
     * @param array $channelIds
     * @param string $division
     * @param array $code
     * @param string $dataDivision
     * @param array $conditionCross
     * @param bool $isOriginal
     * @param int $regionId
     * @param array $dataType
     * @param array $dataTypeFlags
     * @param bool $isConditionCross
     * @param array $timeBoxIds
     * @param bool $hasSelectedPersonal
     * @param int $codeNumber
     * @param array $expectBindsResult
     */
    public function getConditionCrossRatingData(Carbon $startDateTime, Carbon $endDateTime, string $channelType, array $channelIds, string $division, array $code, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, array $dataType, array $dataTypeFlags, bool $isConditionCross, array $timeBoxIds, bool $hasSelectedPersonal, int $codeNumber, array $expectBindsResult): void
    {
        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getPerHourlyLatestDateTime')
            ->with($regionId, 100, 60)
            ->once()->ordered();

        if ($isOriginal) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createCrossJoinWhereClause')
                ->with($division, $code, Mockery::any())
                ->once()->ordered();
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createCrossJoinWhereClause')
                ->with($division, $code, Mockery::any())
                ->once()->ordered();
        } else {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createConditionCrossSql')
                ->with($conditionCross, Mockery::any())
                ->once()->ordered();
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createConditionCrossSql')
                ->with($conditionCross, Mockery::any())
                ->once()->ordered();
        }

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($expectBindsResult, 'expectBindsResult'))
            ->andReturn([])
            ->once()->ordered();

        $actual = $this->target->getConditionCrossRatingData($startDateTime, $endDateTime, $channelType, $channelIds, $division, $code, $dataDivision, $conditionCross, $isOriginal, $regionId, $dataType, $dataTypeFlags, 100, 60);
    }

    public function commonCustomRatingDataProvider()
    {
        // テストマトリクス
        $cases = ['case1', 'case2', 'case3', 'case4'];
        $startDateTime = [new Carbon('2019-01-01 05:00:00'), new Carbon('2019-01-01 05:00:00'), new Carbon('2019-01-01 05:00:00'), new Carbon('2019-01-01 05:00:00')];
        $endDateTime = [new Carbon('2019-01-02 05:00:00'), new Carbon('2019-01-02 05:00:00'), new Carbon('2019-01-02 05:00:00'), new Carbon('2019-01-02 05:00:00')];
        $channelType = ['dt1', 'dt2', 'dt2', 'summary'];
        $channelIds = [[1], [1], [1], [1]];
        $division = ['ga8', 'ga8', 'ga8', 'condition_cross'];
        $code = [['custom'], ['custom'], ['custom'], ['condition_cross']];
        $dataDivision = ['dataDivision', 'dataDivision', 'dataDivision', 'dataDivision'];
        $conditionCross = [[], [], [], []];
        $isOriginal = [true, true, true, false];
        $regionId = [1, 2, 1, 2];
        $dataType = [[1], [1, 2, 3, 4, 5], [1], [1]];
        $dataTypeFlags = [['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => false], ['rt' => false, 'ts' => true, 'gross' => true, 'total' => true, 'rtTotal' => true], ['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => false], ['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => false]];
        $isConditionCross = [false, false, false, true];
        $timeBoxIds = [[11], [11], [11], [11]];
        $hasSelectedPersonal = [false, false, false, false];
        $codeNumber = [1, 1, 1, 1];
        $expectBindsResult = [[
            ':union_ga8_custom' => 'custom',
            ':startDate' => new Carbon('2019-01-01 05:00:00'),
            ':startTimestamp' => '2018-12-31 05:00:00',
            ':endTimestamp' => '2019-01-03 05:00:00',
            ':latestDateTime' => null,
            ':channelId0' => 1,
            ':regionId' => 1,
        ], [
            ':union_ga8_custom' => 'custom',
            ':startDate' => new Carbon('2019-01-01 05:00:00'),
            ':startTimestamp' => '2018-12-31 05:00:00',
            ':endTimestamp' => '2019-01-03 05:00:00',
            ':latestDateTime' => null,
            ':channelId0' => 1,
            ':regionId' => 2,
        ], [
            ':union_ga8_custom' => 'custom',
            ':startDate' => new Carbon('2019-01-01 05:00:00'),
            ':startTimestamp' => '2018-12-31 05:00:00',
            ':endTimestamp' => '2019-01-03 05:00:00',
            ':latestDateTime' => null,
            ':channelId0' => 1,
            ':regionId' => 1,
        ], [
            ':condition_cross_code' => 'condition_cross',
            ':startDate' => new Carbon('2019-01-01 05:00:00'),
            ':startTimestamp' => '2018-12-31 05:00:00',
            ':endTimestamp' => '2019-01-03 05:00:00',
            ':latestDateTime' => null,
            ':channelId0' => 1,
            ':regionId' => 2,
        ]];

        foreach ($cases as $i => $val) {
            yield
            $val => [
                $startDateTime[$i],
                $endDateTime[$i],
                $channelType[$i],
                $channelIds[$i],
                $division[$i],
                $code[$i],
                $dataDivision[$i],
                $conditionCross[$i],
                $isOriginal[$i],
                $regionId[$i],
                $dataType[$i],
                $dataTypeFlags[$i],
                $isConditionCross[$i],
                $timeBoxIds[$i],
                $hasSelectedPersonal[$i],
                $codeNumber[$i],
                $expectBindsResult[$i],
            ];
        }
    }

    /**
     * @test
     * @dataProvider getConditionCrossShareDataDataProvider
     *
     * @param Carbon $startDateTime
     * @param Carbon $endDateTime
     * @param string $channelType
     * @param array $channelIds
     * @param string $division
     * @param array $code
     * @param string $dataDivision
     * @param array $conditionCross
     * @param bool $isOriginal
     * @param int $regionId
     * @param array $dataType
     * @param array $dataTypeFlags
     * @param bool $isConditionCross
     * @param array $timeBoxIds
     * @param bool $hasSelectedPersonal
     * @param int $codeNumber
     * @param array $expectBindsResult
     */
    public function getConditionCrossShareData(Carbon $startDateTime, Carbon $endDateTime, string $channelType, array $channelIds, string $division, array $code, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, array $dataType, array $dataTypeFlags, bool $isConditionCross, array $timeBoxIds, bool $hasSelectedPersonal, int $codeNumber, array $expectBindsResult): void
    {
        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getPerHourlyLatestDateTime')
            ->with($regionId, 100, 60)
            ->once()->ordered();

        if ($isOriginal) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createCrossJoinWhereClause')
                ->with($division, $code, Mockery::any())
                ->once()->ordered();
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createCrossJoinWhereClause')
                ->with($division, $code, Mockery::any())
                ->once()->ordered();
        } else {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createConditionCrossSql')
                ->with($conditionCross, Mockery::any())
                ->once()->ordered();
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createConditionCrossSql')
                ->with($conditionCross, Mockery::any())
                ->once()->ordered();
        }

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($expectBindsResult, 'expectBindsResult'))
            ->andReturn([])
            ->once()->ordered();

        $actual = $this->target->getConditionCrossShareData($startDateTime, $endDateTime, $channelType, $channelIds, $division, $code, $dataDivision, $conditionCross, $isOriginal, $regionId, $dataType, $dataTypeFlags, 100, 60);
    }

    public function getConditionCrossShareDataDataProvider()
    {
        // テストマトリクス
        $cases = ['case1', 'case2', 'case3', 'case4'];
        $startDateTime = [new Carbon('2019-01-01 05:00:00'), new Carbon('2019-01-01 05:00:00'), new Carbon('2019-01-01 05:00:00'), new Carbon('2019-01-01 05:00:00')];
        $endDateTime = [new Carbon('2019-01-02 05:00:00'), new Carbon('2019-01-02 05:00:00'), new Carbon('2019-01-02 05:00:00'), new Carbon('2019-01-02 05:00:00')];
        $channelType = ['dt1', 'dt2', 'dt2', 'dt2'];
        $channelIds = [[1], [1], [1], [1]];
        $division = ['ga8', 'ga8', 'ga8', 'condition_cross'];
        $code = [['custom'], ['custom'], ['custom'], ['condition_cross']];
        $dataDivision = ['dataDivision', 'dataDivision', 'dataDivision', 'dataDivision'];
        $conditionCross = [[], [], [], []];
        $isOriginal = [true, true, true, false];
        $regionId = [1, 2, 1, 2];
        $dataType = [[1], [1, 2, 3, 4, 5], [1], [1]];
        $dataTypeFlags = [['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => false], ['rt' => false, 'ts' => true, 'gross' => true, 'total' => true, 'rtTotal' => true], ['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => false], ['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => false]];
        $isConditionCross = [false, false, false, true];
        $timeBoxIds = [[11], [11], [11], [11]];
        $hasSelectedPersonal = [false, false, false, false];
        $codeNumber = [1, 1, 1, 1];
        $expectBindsResult = [[
            ':union_ga8_custom' => 'custom',
            ':startDate' => new Carbon('2019-01-01 05:00:00'),
            ':startTimestamp' => '2018-12-31 05:00:00',
            ':endTimestamp' => '2019-01-03 05:00:00',
            ':latestDateTime' => null,
            ':channelId0' => 1,
            ':regionId' => 1,
        ], [
            ':union_ga8_custom' => 'custom',
            ':startDate' => new Carbon('2019-01-01 05:00:00'),
            ':startTimestamp' => '2018-12-31 05:00:00',
            ':endTimestamp' => '2019-01-03 05:00:00',
            ':latestDateTime' => null,
            ':channelId0' => 1,
            ':regionId' => 2,
        ], [
            ':union_ga8_custom' => 'custom',
            ':startDate' => new Carbon('2019-01-01 05:00:00'),
            ':startTimestamp' => '2018-12-31 05:00:00',
            ':endTimestamp' => '2019-01-03 05:00:00',
            ':latestDateTime' => null,
            ':channelId0' => 1,
            ':regionId' => 1,
        ], [
            ':condition_cross_code' => 'condition_cross',
            ':startDate' => new Carbon('2019-01-01 05:00:00'),
            ':startTimestamp' => '2018-12-31 05:00:00',
            ':endTimestamp' => '2019-01-03 05:00:00',
            ':latestDateTime' => null,
            ':channelId0' => 1,
            ':regionId' => 2,
        ]];

        foreach ($cases as $i => $val) {
            yield
            $val => [
                $startDateTime[$i],
                $endDateTime[$i],
                $channelType[$i],
                $channelIds[$i],
                $division[$i],
                $code[$i],
                $dataDivision[$i],
                $conditionCross[$i],
                $isOriginal[$i],
                $regionId[$i],
                $dataType[$i],
                $dataTypeFlags[$i],
                $isConditionCross[$i],
                $timeBoxIds[$i],
                $hasSelectedPersonal[$i],
                $codeNumber[$i],
                $expectBindsResult[$i],
            ];
        }
    }

    /**
     * @test
     * @dataProvider getConditionCrossTargetDataDataProvider
     *
     * @param Carbon $startDateTime
     * @param Carbon $endDateTime
     * @param string $channelType
     * @param array $channelIds
     * @param string $division
     * @param array $code
     * @param string $dataDivision
     * @param array $conditionCross
     * @param bool $isOriginal
     * @param int $regionId
     * @param array $dataType
     * @param array $dataTypeFlags
     * @param bool $isConditionCross
     * @param array $timeBoxIds
     * @param bool $hasSelectedPersonal
     * @param int $codeNumber
     * @param array $expectBindsResult
     */
    public function getConditionCrossTargetData(Carbon $startDateTime, Carbon $endDateTime, string $channelType, array $channelIds, string $division, array $code, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, array $dataType, array $dataTypeFlags, bool $isConditionCross, array $timeBoxIds, bool $hasSelectedPersonal, int $codeNumber, array $expectBindsResult): void
    {
        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getPerHourlyLatestDateTime')
            ->with($regionId, 100, 60)
            ->once()->ordered();

        if ($isOriginal) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createCrossJoinWhereClause')
                ->with($division, $code, Mockery::any())
                ->once()->ordered();
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createCrossJoinWhereClause')
                ->with($division, $code, Mockery::any())
                ->once()->ordered();
        } else {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createConditionCrossSql')
                ->with($conditionCross, Mockery::any())
                ->once()->ordered();
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createConditionCrossSql')
                ->with($conditionCross, Mockery::any())
                ->once()->ordered();
        }

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($expectBindsResult, 'expectBindsResult'))
            ->andReturn([])
            ->once()->ordered();

        $actual = $this->target->getConditionCrossTargetData($startDateTime, $endDateTime, $channelType, $channelIds, $division, $code, $dataDivision, $conditionCross, $isOriginal, $regionId, $dataType, $dataTypeFlags, 100, 60);
    }

    public function getConditionCrossTargetDataDataProvider()
    {
        // テストマトリクス
        $cases = ['case1', 'case2', 'case3', 'case4'];
        $startDateTime = [new Carbon('2019-01-01 05:00:00'), new Carbon('2019-01-01 05:00:00'), new Carbon('2019-01-01 05:00:00'), new Carbon('2019-01-01 05:00:00')];
        $endDateTime = [new Carbon('2019-01-02 05:00:00'), new Carbon('2019-01-02 05:00:00'), new Carbon('2019-01-02 05:00:00'), new Carbon('2019-01-02 05:00:00')];
        $channelType = ['dt1', 'dt2', 'dt2', 'dt2'];
        $channelIds = [[1], [1], [1], [1]];
        $division = ['ga8', 'ga8', 'ga8', 'condition_cross'];
        $code = [['custom'], ['custom'], ['custom'], ['condition_cross']];
        $dataDivision = ['target_content_personal', 'target_content_household', 'dataDivision', 'dataDivision'];
        $conditionCross = [[], [], [], []];
        $isOriginal = [true, true, true, false];
        $regionId = [1, 2, 1, 2];
        $dataType = [[1], [1, 2, 3, 4, 5], [1, 5], [1, 2]];
        $dataTypeFlags = [
            ['rt' => true, 'ts' => false, 'gross' => true, 'total' => false, 'rtTotal' => false],
            ['rt' => false, 'ts' => true, 'gross' => true, 'total' => true, 'rtTotal' => true],
            ['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => true],
            ['rt' => true, 'ts' => true, 'gross' => false, 'total' => false, 'rtTotal' => false],
        ];
        $isConditionCross = [false, false, false, true];
        $timeBoxIds = [[11], [11], [11], [11]];
        $hasSelectedPersonal = [false, false, false, false];
        $codeNumber = [1, 1, 1, 1];
        $expectBindsResult = [[
            ':division' => 'personal',
            ':code' => 1,
            ':union_ga8_custom' => 'custom',
            ':startDate' => new Carbon('2019-01-01 05:00:00'),
            ':startTimestamp' => '2018-12-31 05:00:00',
            ':endTimestamp' => '2019-01-03 05:00:00',
            ':latestDateTime' => null,
            ':channelId0' => 1,
            ':regionId' => 1,
        ], [
            ':division' => 'household',
            ':code' => 1,
            ':union_ga8_custom' => 'custom',
            ':startDate' => new Carbon('2019-01-01 05:00:00'),
            ':startTimestamp' => '2018-12-31 05:00:00',
            ':endTimestamp' => '2019-01-03 05:00:00',
            ':latestDateTime' => null,
            ':channelId0' => 1,
            ':regionId' => 2,
        ], [
            ':union_ga8_custom' => 'custom',
            ':startDate' => new Carbon('2019-01-01 05:00:00'),
            ':startTimestamp' => '2018-12-31 05:00:00',
            ':endTimestamp' => '2019-01-03 05:00:00',
            ':latestDateTime' => null,
            ':channelId0' => 1,
            ':regionId' => 1,
        ], [
            ':condition_cross_code' => 'condition_cross',
            ':startDate' => new Carbon('2019-01-01 05:00:00'),
            ':startTimestamp' => '2018-12-31 05:00:00',
            ':endTimestamp' => '2019-01-03 05:00:00',
            ':latestDateTime' => null,
            ':channelId0' => 1,
            ':regionId' => 2,
        ]];

        foreach ($cases as $i => $val) {
            yield
            $val => [
                $startDateTime[$i],
                $endDateTime[$i],
                $channelType[$i],
                $channelIds[$i],
                $division[$i],
                $code[$i],
                $dataDivision[$i],
                $conditionCross[$i],
                $isOriginal[$i],
                $regionId[$i],
                $dataType[$i],
                $dataTypeFlags[$i],
                $isConditionCross[$i],
                $timeBoxIds[$i],
                $hasSelectedPersonal[$i],
                $codeNumber[$i],
                $expectBindsResult[$i],
            ];
        }
    }

    private function bindAsserts($bindings, $caller)
    {
        return function ($query, $binds) use ($bindings, $caller) {
            $this->assertCount(count($bindings), $binds, substr($query, 0, 100) . $caller . print_r($binds, true));

            foreach ($bindings as $key => $val) {
                $this->assertEquals($val, $binds[$key], "${caller} ${binds}" . print_r($binds, true));
            }
            return true;
        };
    }
}
