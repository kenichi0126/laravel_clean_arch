<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Dwh;

use Mockery;
use Switchm\SmartApi\Queries\Dao\Dwh\Dao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class DaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = Mockery::mock(Dao::class, [])->makePartial();
    }

    /**
     * @test
     * @dataProvider createRtSampleTempTableDataProvider
     * @param bool $isConditionCross
     * @param array $conditionCross
     * @param string $division
     * @param array $codes
     * @param array $timeBoxIds
     * @param string $sampleCodePrefix
     * @param string $sampleCodeNumberPrefix
     * @param string $selectedPersonalName
     * @param bool $hasSelectedPersonal
     * @param int $codeNumber
     * @param array $bindings
     * @param array $caseWhenArr
     */
    public function createRtSampleTempTable(bool $isConditionCross, array $conditionCross, String $division, array $codes, array $timeBoxIds, string $sampleCodePrefix, string $sampleCodeNumberPrefix, string $selectedPersonalName, bool $hasSelectedPersonal, int $codeNumber, array $bindings, array $caseWhenArr): void
    {
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createCrossJoinArray')
            ->with($division, $codes, Mockery::any())
            ->andReturn($caseWhenArr)
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
            ->shouldReceive('insertTemporaryTable')
            ->with(Mockery::any(), $bindings)
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        $this->target->createRtSampleTempTable($isConditionCross, $conditionCross, $division, $codes, $timeBoxIds, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName, $hasSelectedPersonal, $codeNumber);
        $this->assertTrue(true); // zero assert だと coverage が通らない為設置
    }

    public function createRtSampleTempTableDataProvider()
    {
        $cases = ['TimeBoxあり,caseWhenArrあり,hasSelectedPersonal=true', 'TimeBoxなし,caseWhenArrなし,hasSelectedPersonal=false'];
        $isConditionCross = [false, false];
        $conditionCross = [[], []];
        $division = ['ga8', 'ga8'];
        $codes = [['personal'], ['f2']];
        $timeBoxIds = [[1], []];
        $sampleCodePrefix = ['sampleCodePrefix', 'sampleCodePrefix'];
        $sampleCodeNumberPrefix = ['sampleCodeNumberPrefix', 'sampleCodeNumberPrefix'];
        $selectedPersonalName = ['selectedPersonalName', 'selectedPersonalName'];
        $hasSelectedPersonal = [true, false];
        $codeNumber = [32, 32];
        $bindings = [[':time_box_ids0' => 1], []];
        $caseWhenArr = [[['condition' => 'condition']], []];

        foreach ($cases as $i => $case) {
            yield $case => [
                $isConditionCross[$i],
                $conditionCross[$i],
                $division[$i],
                $codes[$i],
                $timeBoxIds[$i],
                $sampleCodePrefix[$i],
                $sampleCodeNumberPrefix[$i],
                $selectedPersonalName[$i],
                $hasSelectedPersonal[$i],
                $codeNumber[$i],
                $bindings[$i],
                $caseWhenArr[$i],
            ];
        }
    }

    /**
     * @test
     * @dataProvider createRtSampleTempTable_conditionCrossDataProvider
     * @param bool $isConditionCross
     * @param array $conditionCross
     * @param string $division
     * @param array $codes
     * @param array $timeBoxIds
     * @param string $sampleCodePrefix
     * @param string $sampleCodeNumberPrefix
     * @param string $selectedPersonalName
     * @param bool $hasSelectedPersonal
     * @param int $codeNumber
     * @param array $bindings
     * @param array $caseWhenArr
     */
    public function createRtSampleTempTable_conditionCross(bool $isConditionCross, array $conditionCross, String $division, array $codes, array $timeBoxIds, string $sampleCodePrefix, string $sampleCodeNumberPrefix, string $selectedPersonalName, bool $hasSelectedPersonal, int $codeNumber, array $bindings, array $caseWhenArr): void
    {
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createConditionCrossArray')
            ->with($conditionCross, Mockery::any())
            ->andReturn($caseWhenArr)
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->andReturn((object) ['has_record' => false])
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('insertTemporaryTable')
            ->with(Mockery::any(), $bindings)
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        $this->target->createRtSampleTempTable($isConditionCross, $conditionCross, $division, $codes, $timeBoxIds, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName, $hasSelectedPersonal, $codeNumber);
        $this->assertTrue(true); // zero assert だと coverage が通らない為設置
    }

    /**
     * @test
     * @dataProvider createRtSampleTempTable_conditionCrossDataProvider
     * @param bool $isConditionCross
     * @param array $conditionCross
     * @param string $division
     * @param array $codes
     * @param array $timeBoxIds
     * @param string $sampleCodePrefix
     * @param string $sampleCodeNumberPrefix
     * @param string $selectedPersonalName
     * @param bool $hasSelectedPersonal
     * @param int $codeNumber
     * @param array $bindings
     * @param array $caseWhenArr
     */
    public function createRtSampleTempTable_exists(bool $isConditionCross, array $conditionCross, String $division, array $codes, array $timeBoxIds, string $sampleCodePrefix, string $sampleCodeNumberPrefix, string $selectedPersonalName, bool $hasSelectedPersonal, int $codeNumber, array $bindings, array $caseWhenArr): void
    {
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createConditionCrossArray')
            ->with($conditionCross, Mockery::any())
            ->andReturn($caseWhenArr)
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->andReturn((object) ['has_record' => true])
            ->once()->ordered();

        $this->target->createRtSampleTempTable($isConditionCross, $conditionCross, $division, $codes, $timeBoxIds, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName, $hasSelectedPersonal, $codeNumber);
        $this->assertTrue(true); // zero assert だと coverage が通らない為設置
    }

    public function createRtSampleTempTable_conditionCrossDataProvider()
    {
        $cases = ['TimeBoxあり,caseWhenArrあり,hasSelectedPersonal=true', 'TimeBoxなし,caseWhenArrなし,hasSelectedPersonal=false'];
        $isConditionCross = [true, true];
        $conditionCross = [[], []];
        $division = ['ga8', 'ga8'];
        $codes = [['personal'], ['f2']];
        $timeBoxIds = [[1], []];
        $sampleCodePrefix = ['sampleCodePrefix', 'sampleCodePrefix'];
        $sampleCodeNumberPrefix = ['sampleCodeNumberPrefix', 'sampleCodeNumberPrefix'];
        $selectedPersonalName = ['selectedPersonalName', 'selectedPersonalName'];
        $hasSelectedPersonal = [true, false];
        $codeNumber = [32, 32];
        $bindings = [[':time_box_ids0' => 1], []];
        $caseWhenArr = [[['condition' => 'condition']], []];

        foreach ($cases as $i => $case) {
            yield $case => [
                $isConditionCross[$i],
                $conditionCross[$i],
                $division[$i],
                $codes[$i],
                $timeBoxIds[$i],
                $sampleCodePrefix[$i],
                $sampleCodeNumberPrefix[$i],
                $selectedPersonalName[$i],
                $hasSelectedPersonal[$i],
                $codeNumber[$i],
                $bindings[$i],
                $caseWhenArr[$i],
            ];
        }
    }

    /**
     * @test
     * @dataProvider createTsSampleTempTableDataProvider
     * @param bool $isConditionCross
     * @param array $conditionCross
     * @param string $division
     * @param array $codes
     * @param array $timeBoxIds
     * @param string $sampleCodePrefix
     * @param string $sampleCodeNumberPrefix
     * @param string $selectedPersonalName
     * @param bool $hasSelectedPersonal
     * @param int $codeNumber
     * @param array $bindings
     * @param array $caseWhenArr
     * @param int $regionId
     */
    public function createTsSampleTempTable(bool $isConditionCross, array $conditionCross, String $division, array $codes, array $timeBoxIds, int $regionId, string $sampleCodePrefix, string $sampleCodeNumberPrefix, string $selectedPersonalName, bool $hasSelectedPersonal, ?int $codeNumber, array $bindings, array $caseWhenArr): void
    {
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createCrossJoinArray')
            ->with($division, $codes, Mockery::any())
            ->andReturn($caseWhenArr)
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
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('insertTemporaryTable')
            ->with(Mockery::any(), $bindings)
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        $this->target->createTsSampleTempTable($isConditionCross, $conditionCross, $division, $codes, $timeBoxIds, $regionId, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName, $hasSelectedPersonal, $codeNumber);
        $this->assertTrue(true); // zero assert だと coverage が通らない為設置
    }

    public function createTsSampleTempTableDataProvider()
    {
        $cases = ['TimeBoxあり,caseWhenArrあり,hasSelectedPersonal=true', 'TimeBoxなし,caseWhenArrなし,hasSelectedPersonal=false'];
        $isConditionCross = [false, false];
        $conditionCross = [[], []];
        $division = ['ga8', 'ga8'];
        $codes = [['personal'], ['f2']];
        $timeBoxIds = [[1], []];
        $regionId = [1, 2];
        $sampleCodePrefix = ['sampleCodePrefix', 'sampleCodePrefix'];
        $sampleCodeNumberPrefix = ['sampleCodeNumberPrefix', 'sampleCodeNumberPrefix'];
        $selectedPersonalName = ['selectedPersonalName', 'selectedPersonalName'];
        $hasSelectedPersonal = [true, false];
        $codeNumber = [32, 32];
        $bindings = [[':time_box_ids0' => 1], []];
        $caseWhenArr = [[['condition' => 'condition']], []];

        foreach ($cases as $i => $case) {
            yield $case => [
                $isConditionCross[$i],
                $conditionCross[$i],
                $division[$i],
                $codes[$i],
                $timeBoxIds[$i],
                $regionId[$i],
                $sampleCodePrefix[$i],
                $sampleCodeNumberPrefix[$i],
                $selectedPersonalName[$i],
                $hasSelectedPersonal[$i],
                $codeNumber[$i],
                $bindings[$i],
                $caseWhenArr[$i],
            ];
        }
    }

    /**
     * @test
     * @dataProvider createTsSampleTempTable_conditionCrossDataProvider
     * @param bool $isConditionCross
     * @param array $conditionCross
     * @param string $division
     * @param array $codes
     * @param array $timeBoxIds
     * @param string $sampleCodePrefix
     * @param string $sampleCodeNumberPrefix
     * @param string $selectedPersonalName
     * @param bool $hasSelectedPersonal
     * @param int $codeNumber
     * @param array $bindings
     * @param array $caseWhenArr
     * @param int $regionId
     */
    public function createTsSampleTempTable_conditionCross(bool $isConditionCross, array $conditionCross, String $division, array $codes, array $timeBoxIds, int $regionId, string $sampleCodePrefix, string $sampleCodeNumberPrefix, string $selectedPersonalName, bool $hasSelectedPersonal, ?int $codeNumber, array $bindings, array $caseWhenArr): void
    {
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createConditionCrossArray')
            ->with($conditionCross, Mockery::any())
            ->andReturn($caseWhenArr)
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
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('insertTemporaryTable')
            ->with(Mockery::any(), $bindings)
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        $this->target->createTsSampleTempTable($isConditionCross, $conditionCross, $division, $codes, $timeBoxIds, $regionId, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName, $hasSelectedPersonal, $codeNumber);
        $this->assertTrue(true); // zero assert だと coverage が通らない為設置
    }

    public function createTsSampleTempTable_conditionCrossDataProvider()
    {
        $cases = ['TimeBoxあり,caseWhenArrあり,hasSelectedPersonal=true', 'TimeBoxなし,caseWhenArrなし,hasSelectedPersonal=false'];
        $isConditionCross = [true, true];
        $conditionCross = [[], []];
        $division = ['ga8', 'ga8'];
        $codes = [['personal'], ['f2']];
        $timeBoxIds = [[1], []];
        $regionId = [1, 2];
        $sampleCodePrefix = ['sampleCodePrefix', 'sampleCodePrefix'];
        $sampleCodeNumberPrefix = ['sampleCodeNumberPrefix', 'sampleCodeNumberPrefix'];
        $selectedPersonalName = ['selectedPersonalName', 'selectedPersonalName'];
        $hasSelectedPersonal = [true, false];
        $codeNumber = [32, 32];
        $bindings = [[':time_box_ids0' => 1], []];
        $caseWhenArr = [[['condition' => 'condition']], []];

        foreach ($cases as $i => $case) {
            yield $case => [
                $isConditionCross[$i],
                $conditionCross[$i],
                $division[$i],
                $codes[$i],
                $timeBoxIds[$i],
                $regionId[$i],
                $sampleCodePrefix[$i],
                $sampleCodeNumberPrefix[$i],
                $selectedPersonalName[$i],
                $hasSelectedPersonal[$i],
                $codeNumber[$i],
                $bindings[$i],
                $caseWhenArr[$i],
            ];
        }
    }

    /**
     * @test
     * @dataProvider createTsSampleTempTable_conditionCrossDataProvider
     * @param bool $isConditionCross
     * @param array $conditionCross
     * @param string $division
     * @param array $codes
     * @param array $timeBoxIds
     * @param string $sampleCodePrefix
     * @param string $sampleCodeNumberPrefix
     * @param string $selectedPersonalName
     * @param bool $hasSelectedPersonal
     * @param int $codeNumber
     * @param array $bindings
     * @param array $caseWhenArr
     * @param int $regionId
     */
    public function createTsSampleTempTable_exists(bool $isConditionCross, array $conditionCross, String $division, array $codes, array $timeBoxIds, int $regionId, string $sampleCodePrefix, string $sampleCodeNumberPrefix, string $selectedPersonalName, bool $hasSelectedPersonal, ?int $codeNumber, array $bindings, array $caseWhenArr): void
    {
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createConditionCrossArray')
            ->with($conditionCross, Mockery::any())
            ->andReturn($caseWhenArr)
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

        $this->target->createTsSampleTempTable($isConditionCross, $conditionCross, $division, $codes, $timeBoxIds, $regionId, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName, $hasSelectedPersonal, $codeNumber);
        $this->assertTrue(true); // zero assert だと coverage が通らない為設置
    }

    /**
     * @test
     */
    public function insertTemporaryTable(): void
    {
        $expected = true;

        $con = Mockery::mock(\Illuminate\Database\Connection::class);

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getConnection')
            ->andReturn($con)
            ->once()->ordered();

        $con->shouldAllowMockingProtectedMethods()
            ->shouldReceive('insert')
            ->andReturn($expected)
            ->once()->ordered();

        $actual = $this->target->insertTemporaryTable('', []);
        $this->assertSame($expected, $actual); // zero assert だと coverage が通らない為設置
    }
}
