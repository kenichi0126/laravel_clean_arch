<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Dwh;

use Carbon\Carbon;
use Mockery;
use Switchm\SmartApi\Queries\Dao\Dwh\PerHourlyDao;
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
     * @dataProvider getRatingDataProvider
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
     * @param array $commonBinds
     * @param array $expectBinds
     * @param array $expectBindsResult
     */
    public function getRatingData(Carbon $startDateTime, Carbon $endDateTime, string $channelType, array $channelIds, string $division, string $code, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, array $dataType, array $dataTypeFlags, array $commonBinds, array $expectBinds, array $expectBindsResult): void
    {
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->with(Mockery::any())
            ->andReturn((object) ['has_record' => true])
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($commonBinds, 'common binds'))
            ->once()->ordered();

        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getPerHourlyLatestDateTime')
            ->with($regionId, 100, 60)
            ->andReturn('2019-01-05 05:00:00')
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($expectBindsResult, 'expectBindsResult'))
            ->andReturn([])
            ->once()->ordered();

        $actual = $this->target->getRatingData($startDateTime, $endDateTime, $channelType, $channelIds, $division, $code, $dataDivision, $conditionCross, $isOriginal, $regionId, $dataType, $dataTypeFlags, 100, 60);
    }

    public function getRatingDataProvider()
    {
        // テストマトリクス
        $cases = ['case1', 'case2', 'case3'];
        $startDateTime = [new Carbon('2019-01-01 05:00:00'), new Carbon('2019-01-01 05:00:00'), new Carbon('2019-01-01 05:00:00')];
        $endDateTime = [new Carbon('2019-01-02 05:00:00'), new Carbon('2019-01-02 05:00:00'), new Carbon('2019-01-02 05:00:00')];
        $channelType = ['dt1', 'dt2', 'dt2'];
        $channelIds = [[1], [1], [1]];
        $division = ['ga8', 'ga8', 'ga8'];
        $code = ['personal', 'personal', 'personal'];
        $dataDivision = ['dataDivision', 'dataDivision', 'dataDivision'];
        $conditionCross = [[], [], []];
        $isOriginal = [false, false, false];
        $regionId = [1, 2, 1, 2, 1, 2, 1];
        $dataType = [[1], [1, 2, 3, 4, 5], [1]];
        $dataTypeFlags = [['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => false], ['rt' => true, 'ts' => true, 'gross' => true, 'total' => true, 'rtTotal' => true], ['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => false]];
        $commonBinds = [[
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':regionId' => 1,
            ':division' => 'ga8',
            ':code' => 'personal',
            ':channelId0' => 1,
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':regionId' => 2,
            ':division' => 'ga8',
            ':code' => 'personal',
            ':channelId0' => 1,
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':regionId' => 1,
            ':division' => 'ga8',
            ':code' => 'personal',
            ':channelId0' => 1,
        ]];
        $expectBinds = [[
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':latestDateTime' => '2019-01-05 05:00:00',
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':latestDateTime' => '2019-01-05 05:00:00',
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':latestDateTime' => '2019-01-05 05:00:00',
        ]];
        $expectBindsResult = [[
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':latestDateTime' => '2019-01-05 05:00:00',
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':latestDateTime' => '2019-01-05 05:00:00',
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':latestDateTime' => '2019-01-05 05:00:00',
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
                $commonBinds[$i],
                $expectBinds[$i],
                $expectBindsResult[$i],
            ];
        }
    }

    /**
     * @test
     * @dataProvider getShareDataProvider
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
     * @param array $commonBinds
     * @param array $expectBinds
     * @param array $expectBindsResult
     */
    public function getShareData(Carbon $startDateTime, Carbon $endDateTime, string $channelType, array $channelIds, string $division, string $code, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, array $dataType, array $dataTypeFlags, array $expectBinds): void
    {
        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getPerHourlyLatestDateTime')
            ->with($regionId, 100, 60)
            ->andReturn('2019-01-05 05:00:00')
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->with(Mockery::any())
            ->andReturn((object) ['has_record' => true])
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($expectBinds, 'expectBinds'))
            ->andReturn([])
            ->once()->ordered();

        $actual = $this->target->getShareData($startDateTime, $endDateTime, $channelType, $channelIds, $division, $code, $dataDivision, $conditionCross, $isOriginal, $regionId, $dataType, $dataTypeFlags, 100, 60);
    }

    public function getShareDataProvider()
    {
        // テストマトリクス
        $cases = ['case1', 'case2', 'case3'];
        $startDateTime = [new Carbon('2019-01-01 05:00:00'), new Carbon('2019-01-01 05:00:00'), new Carbon('2019-01-01 05:00:00')];
        $endDateTime = [new Carbon('2019-01-02 05:00:00'), new Carbon('2019-01-02 05:00:00'), new Carbon('2019-01-02 05:00:00')];
        $channelType = ['dt1', 'dt2', 'dt2'];
        $channelIds = [[1], [1], [1]];
        $division = ['ga8', 'ga8', 'ga8'];
        $code = ['personal', 'personal', 'personal'];
        $dataDivision = ['dataDivision', 'dataDivision', 'dataDivision'];
        $conditionCross = [[], [], []];
        $isOriginal = [false, false, false];
        $regionId = [1, 2, 1, 2, 1, 2, 1];
        $dataType = [[1], [1, 2, 3, 4, 5], [1]];
        $dataTypeFlags = [['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => false], ['rt' => true, 'ts' => true, 'gross' => true, 'total' => true, 'rtTotal' => true], ['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => false]];
        $expectBinds = [[
            ':latestDateTime' => '2019-01-05 05:00:00',
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':regionId' => 1,
            ':division' => 'ga8',
            ':code' => 'personal',
            ':channelId0' => 1,
        ], [
            ':latestDateTime' => '2019-01-05 05:00:00',
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':regionId' => 2,
            ':division' => 'ga8',
            ':code' => 'personal',
            ':channelId0' => 1,
        ], [
            ':latestDateTime' => '2019-01-05 05:00:00',
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':regionId' => 1,
            ':division' => 'ga8',
            ':code' => 'personal',
            ':channelId0' => 1,
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
                $expectBinds[$i],
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
     * @param array $commonBinds
     * @param array $expectBinds
     */
    public function getTargetData(Carbon $startDateTime, Carbon $endDateTime, string $channelType, array $channelIds, string $division, string $code, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, array $dataType, array $dataTypeFlags, array $commonBinds, array $expectBinds): void
    {
        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getPerHourlyLatestDateTime')
            ->with($regionId, 100, 60)
            ->andReturn('2019-01-05 05:00:00')
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->with(Mockery::any())
            ->andReturn((object) ['has_record' => false])
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($commonBinds, 'commonBinds'))
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($expectBinds, 'expectBinds'))
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
        $commonBinds = [[
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':channelId0' => 1,
            'regionId' => 1,
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':channelId0' => 1,
            'regionId' => 2,
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':channelId0' => 1,
            'regionId' => 1,
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':channelId0' => 1,
            'regionId' => 2,
        ]];
        $expectBinds = [[
            ':latestDateTime' => '2019-01-05 05:00:00',
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':regionId' => 1,
            ':division' => 'ga8',
            ':denominator' => 'personal',
            ':code' => 'personal',
            ':channelId0' => 1,
        ], [
            ':latestDateTime' => '2019-01-05 05:00:00',
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':regionId' => 2,
            ':division' => 'ga8',
            ':denominator' => 'household',
            ':code' => 'personal',
            ':channelId0' => 1,
        ], [
            ':latestDateTime' => '2019-01-05 05:00:00',
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':regionId' => 1,
            ':division' => 'ga8',
            ':code' => 'personal',
            ':channelId0' => 1,
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
                $commonBinds[$i],
                $expectBinds[$i],
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
     * @param array $commonBinds
     * @param array $insertBinds
     * @param array $hvListBinds
     * @param array $expectBindsResult
     */
    public function getConditionCrossRatingData(Carbon $startDateTime, Carbon $endDateTime, string $channelType, array $channelIds, string $division, array $code, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, array $dataType, array $dataTypeFlags, bool $isConditionCross, array $timeBoxIds, bool $hasSelectedPersonal, int $codeNumber, array $commonBinds, array $insertBinds, array $hvListBinds, array $expectBindsResult): void
    {
        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getPerHourlyLatestDateTime')
            ->with($regionId, 100, 60)
            ->andReturn('2019-01-05 05:00:00')
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createTimeBoxListWhere')
            ->with('2019-01-01 05:00:00', '2019-01-02 05:00:00', $regionId)
            ->andReturn($timeBoxIds)
            ->once()->ordered();

        if ($dataTypeFlags['rt'] || $dataTypeFlags['gross'] || $dataTypeFlags['rtTotal']) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createRtSampleTempTable')
                ->with($isConditionCross, $conditionCross, $division, $code, $timeBoxIds, 'codePrefix', 'codeNumberPrefix', 'selectedPersonalName', $hasSelectedPersonal, $codeNumber)
                ->once()->ordered();
        }

        if ($dataTypeFlags['ts'] || $dataTypeFlags['gross'] || $dataTypeFlags['rtTotal']) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createTsSampleTempTable')
                ->with($isConditionCross, $conditionCross, $division, $code, $timeBoxIds, $regionId, 'codePrefix', 'codeNumberPrefix', 'selectedPersonalName', $hasSelectedPersonal, $codeNumber)
                ->once()->ordered();
        }

        // for master
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        // for cnt check
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->with(Mockery::any())
            ->andReturn((object) ['has_record' => false])
            ->once()->ordered();

        // for create temptable
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($commonBinds, 'commonBinds'))
            ->once()->ordered();

        if ($dataTypeFlags['rt'] || $dataTypeFlags['gross'] || $dataTypeFlags['rtTotal']) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->once()->ordered();
            // for cnt check
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('selectOne')
                ->with(Mockery::any())
                ->andReturn((object) ['has_record' => false])
                ->once()->ordered();
            // for cnt check
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('insertTemporaryTable')
                ->withArgs($this->bindAsserts($insertBinds, 'insertBindings'))
                ->once()->ordered();
        }

        if ($dataTypeFlags['ts'] || $dataTypeFlags['gross'] || $dataTypeFlags['rtTotal']) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->once()->ordered();
            // for cnt check
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('selectOne')
                ->with(Mockery::any())
                ->andReturn((object) ['has_record' => false])
                ->once()->ordered();
            // for cnt check
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('insertTemporaryTable')
                ->withArgs($this->bindAsserts($insertBinds, 'insertBindings'))
                ->once()->ordered();
        }

        if ($dataTypeFlags['gross'] || $dataTypeFlags['rtTotal']) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->once()->ordered();
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('insertTemporaryTable')
                ->with(Mockery::any())
                ->once()->ordered();
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->once()->ordered();
        }

        // for hvlist
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($hvListBinds, 'hvListBinds'))
            ->once()->ordered();

        // hv_reports
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($expectBindsResult, 'expectBindsResult'))
            ->once()->ordered();

        $actual = $this->target->getConditionCrossRatingData($startDateTime, $endDateTime, $channelType, $channelIds, $division, $code, $dataDivision, $conditionCross, $isOriginal, $regionId, $dataType, $dataTypeFlags, 100, 60, 'codePrefix', 'codeNumberPrefix', 'selectedPersonalName');
    }

    public function commonCustomRatingDataProvider()
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
        $isOriginal = [true, true, true, true];
        $regionId = [1, 2, 1, 2];
        $dataType = [[1], [1, 2, 3, 4, 5], [1], [1]];
        $dataTypeFlags = [['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => false], ['rt' => true, 'ts' => true, 'gross' => true, 'total' => true, 'rtTotal' => true], ['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => false], ['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => false]];
        $isConditionCross = [false, false, false, true];
        $timeBoxIds = [[11], [11], [11], [11]];
        $hasSelectedPersonal = [false, false, false, false];
        $codeNumber = [1, 1, 1, 1];
        $commonBinds = [[
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':channelId0' => 1,
            'regionId' => 1,
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':channelId0' => 1,
            'regionId' => 2,
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':channelId0' => 1,
            'regionId' => 1,
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':channelId0' => 1,
            'regionId' => 2,
        ]];
        $insertBinds = [[
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':channelId0' => 1,
            ':regionId' => 1,
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':channelId0' => 1,
            ':regionId' => 2,
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':channelId0' => 1,
            ':regionId' => 1,
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':channelId0' => 1,
            ':regionId' => 2,
        ]];
        $hvListBinds = [[
            ':regionId' => 1,
        ], [
            ':regionId' => 2,
        ], [
            ':regionId' => 1,
        ], [
            ':regionId' => 2,
        ]];
        $expectBindsResult = [[
            ':latestDateTime' => '2019-01-05 05:00:00',
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':channelId0' => 1,
        ], [
            ':latestDateTime' => '2019-01-05 05:00:00',
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':channelId0' => 1,
        ], [
            ':latestDateTime' => '2019-01-05 05:00:00',
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':channelId0' => 1,
        ], [
            ':latestDateTime' => '2019-01-05 05:00:00',
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':channelId0' => 1,
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
                $commonBinds[$i],
                $insertBinds[$i],
                $hvListBinds[$i],
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
     * @param array $commonBinds
     * @param array $insertBinds
     * @param array $hvListBinds
     * @param array $expectBindsResult
     */
    public function getConditionCrossShareData(Carbon $startDateTime, Carbon $endDateTime, string $channelType, array $channelIds, string $division, array $code, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, array $dataType, array $dataTypeFlags, bool $isConditionCross, array $timeBoxIds, bool $hasSelectedPersonal, int $codeNumber, array $commonBinds, array $insertBinds, array $hvListBinds, array $expectBindsResult): void
    {
        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getPerHourlyLatestDateTime')
            ->with($regionId, 100, 60)
            ->andReturn('2019-01-05 05:00:00')
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createTimeBoxListWhere')
            ->with('2019-01-01 05:00:00', '2019-01-02 05:00:00', $regionId)
            ->andReturn($timeBoxIds)
            ->once()->ordered();

        if ($dataTypeFlags['rt'] || $dataTypeFlags['gross'] || $dataTypeFlags['rtTotal']) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createRtSampleTempTable')
                ->with($isConditionCross, $conditionCross, $division, $code, $timeBoxIds, 'codePrefix', 'codeNumberPrefix', 'selectedPersonalName', $hasSelectedPersonal, $codeNumber)
                ->once()->ordered();
        }

        if ($dataTypeFlags['ts'] || $dataTypeFlags['gross'] || $dataTypeFlags['rtTotal']) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createTsSampleTempTable')
                ->with($isConditionCross, $conditionCross, $division, $code, $timeBoxIds, $regionId, 'codePrefix', 'codeNumberPrefix', 'selectedPersonalName', $hasSelectedPersonal, $codeNumber)
                ->once()->ordered();
        }

        // for master
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        // for cnt check
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->with(Mockery::any())
            ->andReturn((object) ['has_record' => false])
            ->once()->ordered();

        // for create temptable
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($commonBinds, 'commonBinds'))
            ->once()->ordered();

        if ($dataTypeFlags['rt'] || $dataTypeFlags['gross'] || $dataTypeFlags['rtTotal']) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->once()->ordered();
            // for cnt check
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('selectOne')
                ->with(Mockery::any())
                ->andReturn((object) ['has_record' => false])
                ->once()->ordered();
            // for cnt check
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('insertTemporaryTable')
                ->withArgs($this->bindAsserts($insertBinds, 'insertBindings'))
                ->once()->ordered();
        }

        if ($dataTypeFlags['ts'] || $dataTypeFlags['gross'] || $dataTypeFlags['rtTotal']) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->once()->ordered();
            // for cnt check
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('selectOne')
                ->with(Mockery::any())
                ->andReturn((object) ['has_record' => false])
                ->once()->ordered();
            // for cnt check
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('insertTemporaryTable')
                ->withArgs($this->bindAsserts($insertBinds, 'insertBindings'))
                ->once()->ordered();
        }

        if ($dataTypeFlags['gross'] || $dataTypeFlags['rtTotal']) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->once()->ordered();
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('insertTemporaryTable')
                ->with(Mockery::any())
                ->once()->ordered();
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->once()->ordered();
        }

        // for hvlist
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($hvListBinds, 'hvListBinds'))
            ->once()->ordered();

        // hv_reports
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($expectBindsResult, 'expectBindsResult'))
            ->once()->ordered();

        $actual = $this->target->getConditionCrossShareData($startDateTime, $endDateTime, $channelType, $channelIds, $division, $code, $dataDivision, $conditionCross, $isOriginal, $regionId, $dataType, $dataTypeFlags, 100, 60, 'codePrefix', 'codeNumberPrefix', 'selectedPersonalName');
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
        $isOriginal = [true, true, true, true];
        $regionId = [1, 2, 1, 2];
        $dataType = [[1], [1, 2, 3, 4, 5], [1], [1]];
        $dataTypeFlags = [['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => false], ['rt' => true, 'ts' => true, 'gross' => true, 'total' => true, 'rtTotal' => true], ['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => false], ['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => false]];
        $isConditionCross = [false, false, false, true];
        $timeBoxIds = [[11], [11], [11], [11]];
        $hasSelectedPersonal = [false, false, false, false];
        $codeNumber = [1, 1, 1, 1];
        $commonBinds = [[
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':channelId0' => 1,
            'regionId' => 1,
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':channelId0' => 1,
            'regionId' => 2,
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':channelId0' => 1,
            'regionId' => 1,
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':channelId0' => 1,
            'regionId' => 2,
        ]];
        $insertBinds = [[
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':channelId0' => 1,
            ':regionId' => 1,
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':channelId0' => 1,
            ':regionId' => 2,
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':channelId0' => 1,
            ':regionId' => 1,
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':channelId0' => 1,
            ':regionId' => 2,
        ]];
        $hvListBinds = [[
            ':regionId' => 1,
        ], [
            ':regionId' => 2,
        ], [
            ':regionId' => 1,
        ], [
            ':regionId' => 2,
        ]];
        $expectBindsResult = [[
            ':latestDateTime' => '2019-01-05 05:00:00',
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
        ], [
            ':latestDateTime' => '2019-01-05 05:00:00',
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
        ], [
            ':latestDateTime' => '2019-01-05 05:00:00',
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
        ], [
            ':latestDateTime' => '2019-01-05 05:00:00',
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
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
                $commonBinds[$i],
                $insertBinds[$i],
                $hvListBinds[$i],
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
     * @param array $commonBinds
     * @param array $insertBinds
     * @param array $hvListBinds
     * @param array $expectBindsResult
     */
    public function getConditionCrossTargetData(Carbon $startDateTime, Carbon $endDateTime, string $channelType, array $channelIds, string $division, array $code, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, array $dataType, array $dataTypeFlags, bool $isConditionCross, array $timeBoxIds, bool $hasSelectedPersonal, int $codeNumber, array $commonBinds, array $insertBinds, array $hvListBinds, array $expectBindsResult): void
    {
        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getPerHourlyLatestDateTime')
            ->with($regionId, 100, 60)
            ->andReturn('2019-01-05 05:00:00')
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createTimeBoxListWhere')
            ->with('2019-01-01 05:00:00', '2019-01-02 05:00:00', $regionId)
            ->andReturn($timeBoxIds)
            ->once()->ordered();

        if ($dataTypeFlags['rt'] || $dataTypeFlags['gross'] || $dataTypeFlags['rtTotal']) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createRtSampleTempTable')
                ->with($isConditionCross, $conditionCross, $division, $code, $timeBoxIds, 'codePrefix', 'codeNumberPrefix', 'selectedPersonalName', $hasSelectedPersonal, $codeNumber)
                ->once()->ordered();
        }

        if ($dataTypeFlags['ts'] || $dataTypeFlags['gross'] || $dataTypeFlags['rtTotal']) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createTsSampleTempTable')
                ->with($isConditionCross, $conditionCross, $division, $code, $timeBoxIds, $regionId, 'codePrefix', 'codeNumberPrefix', 'selectedPersonalName', $hasSelectedPersonal, $codeNumber)
                ->once()->ordered();
        }

        // for master
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        // for cnt check
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->with(Mockery::any())
            ->andReturn((object) ['has_record' => false])
            ->once()->ordered();

        // for create temptable
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($commonBinds, 'commonBinds'))
            ->once()->ordered();

        if ($dataTypeFlags['rt'] || $dataTypeFlags['gross'] || $dataTypeFlags['rtTotal']) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->once()->ordered();
            // for cnt check
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('selectOne')
                ->with(Mockery::any())
                ->andReturn((object) ['has_record' => false])
                ->once()->ordered();
            // for cnt check
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('insertTemporaryTable')
                ->withArgs($this->bindAsserts($insertBinds, 'insertBindings'))
                ->once()->ordered();
        }

        if ($dataTypeFlags['ts'] || $dataTypeFlags['gross'] || $dataTypeFlags['rtTotal']) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->once()->ordered();
            // for cnt check
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('selectOne')
                ->with(Mockery::any())
                ->andReturn((object) ['has_record' => false])
                ->once()->ordered();
            // for cnt check
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('insertTemporaryTable')
                ->withArgs($this->bindAsserts($insertBinds, 'insertBindings'))
                ->once()->ordered();
        }

        if ($dataTypeFlags['gross'] || $dataTypeFlags['rtTotal']) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->once()->ordered();
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('insertTemporaryTable')
                ->with(Mockery::any())
                ->once()->ordered();
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->once()->ordered();
        }

        // for hvlist
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($hvListBinds, 'hvListBinds'))
            ->once()->ordered();

        // hv_reports
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($expectBindsResult, 'expectBindsResult'))
            ->once()->ordered();

        $actual = $this->target->getConditionCrossTargetData($startDateTime, $endDateTime, $channelType, $channelIds, $division, $code, $dataDivision, $conditionCross, $isOriginal, $regionId, $dataType, $dataTypeFlags, 100, 60, 'codePrefix', 'codeNumberPrefix', 'selectedPersonalName');
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
        $isOriginal = [true, true, true, true];
        $regionId = [1, 2, 1, 2];
        $dataType = [[1], [1, 2, 3, 4, 5], [1, 5], [1, 2]];
        $dataTypeFlags = [
            ['rt' => true, 'ts' => false, 'gross' => true, 'total' => false, 'rtTotal' => false],
            ['rt' => true, 'ts' => true, 'gross' => true, 'total' => true, 'rtTotal' => true],
            ['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => true],
            ['rt' => true, 'ts' => true, 'gross' => false, 'total' => false, 'rtTotal' => false],
        ];
        $isConditionCross = [false, false, false, true];
        $timeBoxIds = [[11], [11], [11], [11]];
        $hasSelectedPersonal = [false, false, false, false];
        $codeNumber = [1, 1, 1, 1];
        $commonBinds = [[
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':channelId0' => 1,
            'regionId' => 1,
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':channelId0' => 1,
            'regionId' => 2,
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':channelId0' => 1,
            'regionId' => 1,
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':channelId0' => 1,
            'regionId' => 2,
        ]];
        $insertBinds = [[
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':channelId0' => 1,
            ':regionId' => 1,
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':channelId0' => 1,
            ':regionId' => 2,
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':channelId0' => 1,
            ':regionId' => 1,
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':channelId0' => 1,
            ':regionId' => 2,
        ]];
        $hvListBinds = [[
            ':regionId' => 1,
        ], [
            ':regionId' => 2,
        ], [
            ':regionId' => 1,
        ], [
            ':regionId' => 2,
        ]];
        $expectBindsResult = [[
            ':latestDateTime' => '2019-01-05 05:00:00',
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':channelId0' => 1,
        ], [
            ':latestDateTime' => '2019-01-05 05:00:00',
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':channelId0' => 1,
        ], [
            ':latestDateTime' => '2019-01-05 05:00:00',
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':channelId0' => 1,
        ], [
            ':latestDateTime' => '2019-01-05 05:00:00',
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':channelId0' => 1,
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
                $commonBinds[$i],
                $insertBinds[$i],
                $hvListBinds[$i],
                $expectBindsResult[$i],
            ];
        }
    }

    // has_record true のバージョンも作る

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
     * @param array $commonBinds
     * @param array $insertBinds
     * @param array $hvListBinds
     * @param array $expectBindsResult
     */
    public function getConditionCrossTargetDataHasRecord(Carbon $startDateTime, Carbon $endDateTime, string $channelType, array $channelIds, string $division, array $code, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, array $dataType, array $dataTypeFlags, bool $isConditionCross, array $timeBoxIds, bool $hasSelectedPersonal, int $codeNumber, array $commonBinds, array $insertBinds, array $hvListBinds, array $expectBindsResult): void
    {
        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getPerHourlyLatestDateTime')
            ->with($regionId, 100, 60)
            ->andReturn('2019-01-05 05:00:00')
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createTimeBoxListWhere')
            ->with('2019-01-01 05:00:00', '2019-01-02 05:00:00', $regionId)
            ->andReturn($timeBoxIds)
            ->once()->ordered();

        if ($dataTypeFlags['rt'] || $dataTypeFlags['gross'] || $dataTypeFlags['rtTotal']) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createRtSampleTempTable')
                ->with($isConditionCross, $conditionCross, $division, $code, $timeBoxIds, 'codePrefix', 'codeNumberPrefix', 'selectedPersonalName', $hasSelectedPersonal, $codeNumber)
                ->once()->ordered();
        }

        if ($dataTypeFlags['ts'] || $dataTypeFlags['gross'] || $dataTypeFlags['rtTotal']) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createTsSampleTempTable')
                ->with($isConditionCross, $conditionCross, $division, $code, $timeBoxIds, $regionId, 'codePrefix', 'codeNumberPrefix', 'selectedPersonalName', $hasSelectedPersonal, $codeNumber)
                ->once()->ordered();
        }

        // for master
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        // for cnt check
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->with(Mockery::any())
            ->andReturn((object) ['has_record' => true])
            ->once()->ordered();

        if ($dataTypeFlags['rt'] || $dataTypeFlags['gross'] || $dataTypeFlags['rtTotal']) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->once()->ordered();
            // for cnt check
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('selectOne')
                ->with(Mockery::any())
                ->andReturn((object) ['has_record' => true])
                ->once()->ordered();
        }

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($expectBindsResult, 'expectBindsResult'))
            ->once()->ordered();

        $actual = $this->target->getConditionCrossTargetData($startDateTime, $endDateTime, $channelType, $channelIds, $division, $code, $dataDivision, $conditionCross, $isOriginal, $regionId, $dataType, $dataTypeFlags, 100, 60, 'codePrefix', 'codeNumberPrefix', 'selectedPersonalName');
    }

    /**
     * @test
     * @dataProvider getConditionCrossTargetDataTsDataProvider
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
     * @param array $commonBinds
     * @param array $insertBinds
     * @param array $hvListBinds
     * @param array $expectBindsResult
     */
    public function getConditionCrossTargetDataHasRecordTs(Carbon $startDateTime, Carbon $endDateTime, string $channelType, array $channelIds, string $division, array $code, string $dataDivision, array $conditionCross, bool $isOriginal, int $regionId, array $dataType, array $dataTypeFlags, bool $isConditionCross, array $timeBoxIds, bool $hasSelectedPersonal, int $codeNumber, array $commonBinds, array $insertBinds, array $hvListBinds, array $expectBindsResult): void
    {
        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getPerHourlyLatestDateTime')
            ->with($regionId, 100, 60)
            ->andReturn('2019-01-05 05:00:00')
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createTimeBoxListWhere')
            ->with('2019-01-01 05:00:00', '2019-01-02 05:00:00', $regionId)
            ->andReturn($timeBoxIds)
            ->once()->ordered();

        if ($dataTypeFlags['rt'] || $dataTypeFlags['gross'] || $dataTypeFlags['rtTotal']) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createRtSampleTempTable')
                ->with($isConditionCross, $conditionCross, $division, $code, $timeBoxIds, 'codePrefix', 'codeNumberPrefix', 'selectedPersonalName', $hasSelectedPersonal, $codeNumber)
                ->once()->ordered();
        }

        if ($dataTypeFlags['ts'] || $dataTypeFlags['gross'] || $dataTypeFlags['rtTotal']) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createTsSampleTempTable')
                ->with($isConditionCross, $conditionCross, $division, $code, $timeBoxIds, $regionId, 'codePrefix', 'codeNumberPrefix', 'selectedPersonalName', $hasSelectedPersonal, $codeNumber)
                ->once()->ordered();
        }

        // for master
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        // for cnt check
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->with(Mockery::any())
            ->andReturn((object) ['has_record' => true])
            ->once()->ordered();

        if ($dataTypeFlags['rt'] || $dataTypeFlags['gross'] || $dataTypeFlags['rtTotal']) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->once()->ordered();
            // for cnt check
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('selectOne')
                ->with(Mockery::any())
                ->andReturn((object) ['has_record' => false])
                ->once()->ordered();
            // for cnt check
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('insertTemporaryTable')
                ->withArgs($this->bindAsserts($insertBinds, 'insertBindings'))
                ->once()->ordered();
        }

        if ($dataTypeFlags['ts'] || $dataTypeFlags['gross'] || $dataTypeFlags['rtTotal']) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->once()->ordered();
            // for cnt check
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('selectOne')
                ->with(Mockery::any())
                ->andReturn((object) ['has_record' => true])
                ->once()->ordered();
        }

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($expectBindsResult, 'expectBindsResult'))
            ->once()->ordered();

        $actual = $this->target->getConditionCrossTargetData($startDateTime, $endDateTime, $channelType, $channelIds, $division, $code, $dataDivision, $conditionCross, $isOriginal, $regionId, $dataType, $dataTypeFlags, 100, 60, 'codePrefix', 'codeNumberPrefix', 'selectedPersonalName');
    }

    public function getConditionCrossTargetDataTsDataProvider()
    {
        // テストマトリクス
        $cases = ['case1', 'case2', 'case3'];
        $startDateTime = [new Carbon('2019-01-01 05:00:00'), new Carbon('2019-01-01 05:00:00'), new Carbon('2019-01-01 05:00:00'), new Carbon('2019-01-01 05:00:00')];
        $endDateTime = [new Carbon('2019-01-02 05:00:00'), new Carbon('2019-01-02 05:00:00'), new Carbon('2019-01-02 05:00:00'), new Carbon('2019-01-02 05:00:00')];
        $channelType = ['dt1', 'dt2', 'dt2', 'dt2'];
        $channelIds = [[1], [1], [1], [1]];
        $division = ['ga8', 'ga8', 'ga8', 'condition_cross'];
        $code = [['custom'], ['custom'], ['custom'], []];
        $dataDivision = ['dataDivision', 'dataDivision', 'dataDivision', 'dataDivision'];
        $conditionCross = [[], [], [], []];
        $isOriginal = [true, true, true, true];
        $regionId = [1, 2, 1, 2];
        $dataType = [[1], [1, 2, 3, 4, 5], [1], [1]];
        $dataTypeFlags = [['rt' => true, 'ts' => true, 'gross' => true, 'total' => true, 'rtTotal' => true], ['rt' => true, 'ts' => true, 'gross' => true, 'total' => true, 'rtTotal' => true], ['rt' => true, 'ts' => true, 'gross' => true, 'total' => true, 'rtTotal' => true]];
        $isConditionCross = [false, false, false, true];
        $timeBoxIds = [[11], [11], [11], [11]];
        $hasSelectedPersonal = [false, false, false, false];
        $codeNumber = [1, 1, 1, 1];
        $commonBinds = [[
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':channelId0' => 1,
            'regionId' => 1,
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':channelId0' => 1,
            'regionId' => 2,
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':channelId0' => 1,
            'regionId' => 1,
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':channelId0' => 1,
            'regionId' => 2,
        ]];
        $insertBinds = [[
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':channelId0' => 1,
            ':regionId' => 1,
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':channelId0' => 1,
            ':regionId' => 2,
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':channelId0' => 1,
            ':regionId' => 1,
        ], [
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':channelId0' => 1,
            ':regionId' => 2,
        ]];
        $hvListBinds = [[
            ':regionId' => 1,
        ], [
            ':regionId' => 2,
        ], [
            ':regionId' => 1,
        ], [
            ':regionId' => 2,
        ]];
        $expectBindsResult = [[
            ':latestDateTime' => '2019-01-05 05:00:00',
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':channelId0' => 1,
        ], [
            ':latestDateTime' => '2019-01-05 05:00:00',
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':channelId0' => 1,
        ], [
            ':latestDateTime' => '2019-01-05 05:00:00',
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':channelId0' => 1,
        ], [
            ':latestDateTime' => '2019-01-05 05:00:00',
            ':startTimestamp' => '2019-01-01 05:00:00',
            ':endTimestamp' => '2019-01-02 05:00:00',
            ':channelId0' => 1,
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
                $commonBinds[$i],
                $insertBinds[$i],
                $hvListBinds[$i],
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
