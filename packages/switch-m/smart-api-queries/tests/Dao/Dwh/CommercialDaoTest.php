<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Dwh;

use Mockery;
use Switchm\SmartApi\Queries\Dao\Dwh\CommercialDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class CommercialDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = Mockery::mock(CommercialDao::class, [])->makePartial();
    }

    /**
     * @test
     * @dataProvider searchListOriginalDivsDataProvider
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param null|string $cmType
     * @param null|string $cmSeconds
     * @param null|array $progIds
     * @param int $regionId
     * @param string $division
     * @param null|array $codes
     * @param null|array $conditionCross
     * @param null|array $companyIds
     * @param null|array $productIds
     * @param null|array $cmIds
     * @param array $channels
     * @param null|array $order
     * @param int $page
     * @param null|int $length
     * @param null|bool $conv_15_sec_flag
     * @param bool $straddlingFlg
     * @param null|array $codeList
     * @param string $csvFlag
     * @param array $dataType
     * @param bool $cmMaterialFlag
     * @param bool $cmTypeFlag
     * @param string $codeNumber
     * @param string $sampleCodePrefix
     * @param string $sampleCodeNumberPrefix
     * @param string $selectedPersonalName
     * @param array $dataTypes
     * @param array $dataTypeFlags
     * @param array $divCodes
     * @param bool $isConditionCross
     * @param string $divisionKey
     * @param array $companyBind
     * @param array $rsTimeBoxIds
     * @param array $unionViewerBindings
     * @param array $rateBindings
     * @param array $bindings
     */
    public function searchListOriginalDivsRt(String $startDate, String $endDate, String $startTime, String $endTime, ?String $cmType, ?String $cmSeconds, ?array $progIds, int $regionId, String $division, ?array $codes, ?array $conditionCross, ?array $companyIds, ?array $productIds, ?array $cmIds, array $channels, ?array $order, int $page, ?int $length, ?bool $conv_15_sec_flag, bool $straddlingFlg, ?array $codeList, string $csvFlag, bool $cmMaterialFlag, bool $cmTypeFlag, string $codeNumber, string $sampleCodePrefix, string $sampleCodeNumberPrefix, string $selectedPersonalName, array $dataTypes, array $divCodes, bool $isConditionCross, string $divisionKey, array $companyBind, array $rsTimeBoxIds, array $bindings, array $unionViewerBindings, array $rateBindings): void
    {
        $expected = ['list' => [], 'cnt' => 1];

        $dataType = [1];
        $dataTypeFlags = ['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => false];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createCommercialListWhere')
            ->with($startDate, $endDate, $cmType, $cmSeconds, $progIds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $straddlingFlg)
            ->andReturn([$rsTimeBoxIds, [20], [30]])
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($bindings, 'bindings'))
            ->once()->ordered();

        // expects
        if (count($divCodes) > 0) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createRtSampleTempTable')
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn()
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('insertTemporaryTable')
                ->withArgs($this->bindAsserts($unionViewerBindings, 'unionViewerBindings'))
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn()
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn()
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

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->withArgs($this->bindAsserts($rateBindings, 'rateBindings'))
                ->once()->ordered();
        }

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($rateBindings, 'lastSelect'))
            ->andReturn([])
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->withArgs($this->bindAsserts($rateBindings, 'selectOne'))
            ->andReturn((object) ['cnt' => 1])
            ->once()->ordered();

        $actual = $this->target->searchListOriginalDivs($startDate, $endDate, $startTime, $endTime, $cmType, $cmSeconds, $progIds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $order, $page, $length, $conv_15_sec_flag, $straddlingFlg, $codeList, $csvFlag, $dataType, $cmMaterialFlag, $cmTypeFlag, $codeNumber, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName, $dataTypes, $dataTypeFlags);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider searchListOriginalDivsDataProvider
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param null|string $cmType
     * @param null|string $cmSeconds
     * @param null|array $progIds
     * @param int $regionId
     * @param string $division
     * @param null|array $codes
     * @param null|array $conditionCross
     * @param null|array $companyIds
     * @param null|array $productIds
     * @param null|array $cmIds
     * @param array $channels
     * @param null|array $order
     * @param int $page
     * @param null|int $length
     * @param null|bool $conv_15_sec_flag
     * @param bool $straddlingFlg
     * @param null|array $codeList
     * @param string $csvFlag
     * @param array $dataType
     * @param bool $cmMaterialFlag
     * @param bool $cmTypeFlag
     * @param string $codeNumber
     * @param string $sampleCodePrefix
     * @param string $sampleCodeNumberPrefix
     * @param string $selectedPersonalName
     * @param array $dataTypes
     * @param array $dataTypeFlags
     * @param array $divCodes
     * @param bool $isConditionCross
     * @param string $divisionKey
     * @param array $companyBind
     * @param array $rsTimeBoxIds
     * @param array $unionViewerBindings
     * @param array $rateBindings
     * @param array $bindings
     */
    public function searchListOriginalDivsTs(String $startDate, String $endDate, String $startTime, String $endTime, ?String $cmType, ?String $cmSeconds, ?array $progIds, int $regionId, String $division, ?array $codes, ?array $conditionCross, ?array $companyIds, ?array $productIds, ?array $cmIds, array $channels, ?array $order, int $page, ?int $length, ?bool $conv_15_sec_flag, bool $straddlingFlg, ?array $codeList, string $csvFlag, bool $cmMaterialFlag, bool $cmTypeFlag, string $codeNumber, string $sampleCodePrefix, string $sampleCodeNumberPrefix, string $selectedPersonalName, array $dataTypes, array $divCodes, bool $isConditionCross, string $divisionKey, array $companyBind, array $rsTimeBoxIds, array $bindings, array $unionViewerBindings, array $rateBindings): void
    {
        $expected = ['list' => [], 'cnt' => 1];

        $dataType = [2, 3, 4, 5];
        $dataTypeFlags = ['rt' => false, 'ts' => true, 'gross' => true, 'total' => true, 'rtTotal' => true];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createCommercialListWhere')
            ->with($startDate, $endDate, $cmType, $cmSeconds, $progIds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $straddlingFlg)
            ->andReturn([$rsTimeBoxIds, [20], [30]])
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($bindings, 'bindings'))
            ->once()->ordered();

        // expects
        if (count($divCodes) > 0) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createTsSampleTempTable')
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn()
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('insertTemporaryTable')
                ->withArgs($this->bindAsserts($unionViewerBindings, 'unionViewerBindings'))
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn()
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn()
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('insertTemporaryTable')
                ->withArgs($this->bindAsserts($unionViewerBindings, 'unionViewerBindings'))
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn()
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn()
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
                ->andReturn()
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn()
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
                ->andReturn()
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->withArgs($this->bindAsserts($rateBindings, 'lastSelect'))
                ->andReturn([])
                ->once()->ordered();
        }

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($rateBindings, 'lastSelect'))
            ->andReturn([])
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->withArgs($this->bindAsserts($rateBindings, 'selectOne'))
            ->andReturn((object) ['cnt' => 1])
            ->once()->ordered();

        $actual = $this->target->searchListOriginalDivs($startDate, $endDate, $startTime, $endTime, $cmType, $cmSeconds, $progIds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $order, $page, $length, $conv_15_sec_flag, $straddlingFlg, $codeList, $csvFlag, $dataType, $cmMaterialFlag, $cmTypeFlag, $codeNumber, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName, $dataTypes, $dataTypeFlags);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider searchListOriginalDivsDataProvider
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param null|string $cmType
     * @param null|string $cmSeconds
     * @param null|array $progIds
     * @param int $regionId
     * @param string $division
     * @param null|array $codes
     * @param null|array $conditionCross
     * @param null|array $companyIds
     * @param null|array $productIds
     * @param null|array $cmIds
     * @param array $channels
     * @param null|array $order
     * @param int $page
     * @param null|int $length
     * @param null|bool $conv_15_sec_flag
     * @param bool $straddlingFlg
     * @param null|array $codeList
     * @param string $csvFlag
     * @param bool $cmMaterialFlag
     * @param bool $cmTypeFlag
     * @param string $codeNumber
     * @param string $sampleCodePrefix
     * @param string $sampleCodeNumberPrefix
     * @param string $selectedPersonalName
     * @param array $dataTypes
     * @param array $divCodes
     * @param bool $isConditionCross
     * @param string $divisionKey
     * @param array $companyBind
     * @param array $rsTimeBoxIds
     * @param array $bindings
     * @param array $unionViewerBindings
     * @param array $rateBindings
     */
    public function searchListOriginalDivsEmpty(String $startDate, String $endDate, String $startTime, String $endTime, ?String $cmType, ?String $cmSeconds, ?array $progIds, int $regionId, String $division, ?array $codes, ?array $conditionCross, ?array $companyIds, ?array $productIds, ?array $cmIds, array $channels, ?array $order, int $page, ?int $length, ?bool $conv_15_sec_flag, bool $straddlingFlg, ?array $codeList, string $csvFlag, bool $cmMaterialFlag, bool $cmTypeFlag, string $codeNumber, string $sampleCodePrefix, string $sampleCodeNumberPrefix, string $selectedPersonalName, array $dataTypes, array $divCodes, bool $isConditionCross, string $divisionKey, array $companyBind, array $rsTimeBoxIds, array $bindings, array $unionViewerBindings, array $rateBindings): void
    {
        $expected = ['list' => [], 'cnt' => 0];

        $dataType = [1];
        $dataTypeFlags = ['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => false];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createCommercialListWhere')
            ->with($startDate, $endDate, $cmType, $cmSeconds, $progIds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $straddlingFlg)
            ->andReturn([[], [], []])
            ->once()->ordered();

        $actual = $this->target->searchListOriginalDivs($startDate, $endDate, $startTime, $endTime, $cmType, $cmSeconds, $progIds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $order, $page, $length, $conv_15_sec_flag, $straddlingFlg, $codeList, $csvFlag, $dataType, $cmMaterialFlag, $cmTypeFlag, $codeNumber, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName, $dataTypes, $dataTypeFlags);

        $this->assertEquals($expected, $actual);
    }

    public function searchListOriginalDivsDataProvider()
    {
        $cases = ['case1', 'case2', 'case3'];
        $startDate = ['19900614', '19900614', '19900614'];
        $endDate = ['19900615', '19900615', '19900615'];
        $startTime = ['050000', '100000', '120000'];
        $endTime = ['045959', '045959', '200000'];
        $cmType = ['1', '2', ''];
        $cmSeconds = ['2', '3', ''];
        $progIds = [['10'], ['10'], ['10'], ['10']];
        $regionId = [1, 2, 1];
        $division = ['ga8', 'ga8', 'condition_cross'];
        $codes = [['personal'], ['personal', 'c1', 'c2', 'household'], ['condition_cross']];
        $conditionCross = [[], [], []];
        $companyIds = [[1, 2, 3], [1, 2, 3], [1, 2, 3]];
        $productIds = [[10, 11, 12], [], []];
        $cmIds = [['20', '21', '22'], [], []];
        $channels = [[1, 2, 3], [], []];
        $order = [[['column' => 'date', 'dir' => 'desc'], ['column' => 'started_at', 'dir' => 'asc']], [], [['column' => 'date', 'dir' => 'desc'], ['column' => 'started_at', 'dir' => 'asc']], []];
        $page = [1, 2, 3, 4];
        $length = [30, 30, 30, 30];
        $conv15SecFlag = [null, false, true];
        $straddlingFlg = [true, true, false];
        $codeList = [[], ['personal', 'c1', 'c2', 'c3', 'household'], []];
        $csvFlag = ['0', '0', '1', '1'];
        $cmMaterialFlag = [true, false, true];
        $cmTypeFlag = [true, false, true];
        $codeNumber = ['44', '44', '44'];
        $sampleCodePrefix = ['sampleCodePrefix', 'sampleCodePrefix', 'sampleCodePrefix'];
        $sampleCodeNumberPrefix = ['sampleCodeNumberPrefix', 'sampleCodeNumberPrefix', 'sampleCodeNumberPrefix'];
        $selectedPersonalName = ['selectedPersonalName', 'selectedPersonalName', 'selectedPersonalName'];
        $dataTypes = [['rt' => 1, 'ts' => 2, 'gross' => 3, 'total' => 4, 'rtTotal' => 5], ['rt' => 1, 'ts' => 2, 'gross' => 3, 'total' => 4, 'rtTotal' => 5], ['rt' => 1, 'ts' => 2, 'gross' => 3, 'total' => 4, 'rtTotal' => 5]];
        $divCodes = [[], ['c1', 'c2'], ['condition_cross']];
        $isConditionCross = [false, false, true];
        $divisionKey = ['divisoinKey', 'divisoinKey', 'divisoinKey'];
        $companyBind = [['companyBind'], ['companyBind'], ['companyBind']];
        $rsTimeBoxIds = [[33], [33], [33]];
        $bindings = [
            [
                ':startDate' => '19900614',
                ':endDate' => '19900615',
                ':channels0' => 1,
                ':channels1' => 2,
                ':channels2' => 3,
                ':progIds0' => '10',
                ':region_id' => 1,
                ':companyIds0' => 1,
                ':companyIds1' => 2,
                ':companyIds2' => 3,
                ':productIds0' => 10,
                ':productIds1' => 11,
                ':productIds2' => 12,
                ':cmIds0' => '20',
                ':cmIds1' => '21',
                ':cmIds2' => '22',
            ],
            [
                ':startDate' => '19900614',
                ':endDate' => '19900615',
                ':progIds0' => '10',
                ':region_id' => 2,
                ':companyIds0' => 1,
                ':companyIds1' => 2,
                ':companyIds2' => 3,
                ':startTime' => '100000',
                ':endTime' => '045959',
            ],
            [
                ':startDate' => '19900614',
                ':endDate' => '19900615',
                ':progIds0' => '10',
                ':region_id' => 1,
                ':companyIds0' => 1,
                ':companyIds1' => 2,
                ':companyIds2' => 3,
                ':startTime' => '120000',
                ':endTime' => '200000',
            ],
        ];
        $unionViewerBindings = [
            [],
            [
                ':startDate' => '19900614',
                ':endDate' => '19900615',
                ':progStartDate' => '199006130000000000',
                ':progEndDate' => '199006169999999999',
                ':companyIds0' => 1,
                ':companyIds1' => 2,
                ':companyIds2' => 3,
            ],
            [
                ':startDate' => '19900614',
                ':endDate' => '19900615',
                ':progStartDate' => '199006130000000000',
                ':progEndDate' => '199006169999999999',
                ':companyIds0' => 1,
                ':companyIds1' => 2,
                ':companyIds2' => 3,
            ],
        ];
        $rateBindings = [
            [
            ],
            [
                ':conv15SecFlag' => 15,
            ],
            [
                ':conv15SecFlag' => 1,
            ],
        ];

        foreach ($cases as $i => $case) {
            yield $case => [
                $startDate[$i],
                $endDate[$i],
                $startTime[$i],
                $endTime[$i],
                $cmType[$i],
                $cmSeconds[$i],
                $progIds[$i],
                $regionId[$i],
                $division[$i],
                $codes[$i],
                $conditionCross[$i],
                $companyIds[$i],
                $productIds[$i],
                $cmIds[$i],
                $channels[$i],
                $order[$i],
                $page[$i],
                $length[$i],
                $conv15SecFlag[$i],
                $straddlingFlg[$i],
                $codeList[$i],
                $csvFlag[$i],
                $cmMaterialFlag[$i],
                $cmTypeFlag[$i],
                $codeNumber[$i],
                $sampleCodePrefix[$i],
                $sampleCodeNumberPrefix[$i],
                $selectedPersonalName[$i],
                $dataTypes[$i],
                $divCodes[$i],
                $isConditionCross[$i],
                $divisionKey[$i],
                $companyBind[$i],
                $rsTimeBoxIds[$i],
                $bindings[$i],
                $unionViewerBindings[$i],
                $rateBindings[$i],
            ];
        }
    }

    /**
     * @test
     * @dataProvider searchAdvertisingDataProvider
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param null|string $cmType
     * @param null|string $cmSeconds
     * @param null|array $progIds
     * @param int $regionId
     * @param null|array $companyIds
     * @param null|array $productIds
     * @param null|array $cmIds
     * @param array $channels
     * @param bool $straddlingFlg
     * @param array $bindings
     */
    public function searchAdvertising(String $startDate, String $endDate, String $startTime, String $endTime, ?String $cmType, ?String $cmSeconds, ?array $progIds, int $regionId, ?array $companyIds, ?array $productIds, ?array $cmIds, array $channels, bool $straddlingFlg, array $bindings): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($bindings, 'result'))
            ->andReturn($expected)
            ->once()->ordered();

        $actual = $this->target->searchAdvertising($startDate, $endDate, $startTime, $endTime, $cmType, $cmSeconds, $progIds, $regionId, $companyIds, $productIds, $cmIds, $channels, $straddlingFlg);

        $this->assertEquals($expected, $actual);
    }

    public function searchAdvertisingDataProvider()
    {
        $cases = ['case1', 'case2', 'case3'];
        $startDate = ['19900614', '19900614', '19900614'];
        $endDate = ['19900615', '19900615', '19900615'];
        $startTime = ['050000', '100000', '120000'];
        $endTime = ['045959', '045959', '200000'];
        $cmType = ['1', '2', ''];
        $cmSeconds = ['2', '3', ''];
        $progIds = [['10'], ['10'], ['10'], ['10']];
        $regionId = [1, 2, 1];
        $companyIds = [[1, 2, 3], [1, 2, 3], [1, 2, 3]];
        $productIds = [[10, 11, 12], [], []];
        $cmIds = [['20', '21', '22'], [], []];
        $channels = [[1, 2, 3], [], []];
        $straddlingFlg = [true, true, false];
        $bindings = [
            [
                ':startDate' => '19900614',
                ':endDate' => '19900615',
                ':channels0' => 1,
                ':channels1' => 2,
                ':channels2' => 3,
                ':progIds0' => '10',
                ':region_id' => 1,
                ':companyIds0' => 1,
                ':companyIds1' => 2,
                ':companyIds2' => 3,
                ':productIds0' => 10,
                ':productIds1' => 11,
                ':productIds2' => 12,
                ':cmIds0' => '20',
                ':cmIds1' => '21',
                ':cmIds2' => '22',
            ],
            [
                ':startDate' => '19900614',
                ':endDate' => '19900615',
                ':progIds0' => '10',
                ':region_id' => 2,
                ':companyIds0' => 1,
                ':companyIds1' => 2,
                ':companyIds2' => 3,
                ':startTime' => '100000',
                ':endTime' => '045959',
            ],
            [
                ':startDate' => '19900614',
                ':endDate' => '19900615',
                ':progIds0' => '10',
                ':region_id' => 1,
                ':companyIds0' => 1,
                ':companyIds1' => 2,
                ':companyIds2' => 3,
                ':startTime' => '120000',
                ':endTime' => '200000',
            ],
        ];

        foreach ($cases as $i => $case) {
            yield $case => [
                $startDate[$i],
                $endDate[$i],
                $startTime[$i],
                $endTime[$i],
                $cmType[$i],
                $cmSeconds[$i],
                $progIds[$i],
                $regionId[$i],
                $companyIds[$i],
                $productIds[$i],
                $cmIds[$i],
                $channels[$i],
                $straddlingFlg[$i],
                $bindings[$i],
            ];
        }
    }

    /**
     * @test
     * @dataProvider searchGrpOriginalDivsDataProvider
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param null|string $cmType
     * @param null|string $cmSeconds
     * @param null|array $progIds
     * @param int $regionId
     * @param string $division
     * @param null|array $codes
     * @param null|array $conditionCross
     * @param null|array $companyIds
     * @param null|array $productIds
     * @param null|array $cmIds
     * @param array $channels
     * @param null|bool $conv_15_sec_flag
     * @param string $period
     * @param null|string $allChannels
     * @param bool $straddlingFlg
     * @param int $length
     * @param int $page
     * @param string $csvFlag
     * @param string $codeNumber
     * @param string $sampleCodePrefix
     * @param string $sampleCodeNumberPrefix
     * @param string $selectedPersonalName
     * @param array $dataTypes
     * @param null|array $codeList
     * @param array $divCodes
     * @param array $unionViewerBindings
     * @param array $rateBindings
     * @param array $bindings
     * @param array $resultBindings
     */
    public function searchGrpOriginalDivs(String $startDate, String $endDate, String $startTime, String $endTime, ?String $cmType, ?String $cmSeconds, ?array $progIds, int $regionId, String $division, ?array $codes, ?array $conditionCross, ?array $companyIds, ?array $productIds, ?array $cmIds, array $channels, ?bool $conv_15_sec_flag, string $period, ?string $allChannels, bool $straddlingFlg, int $length, int $page, string $csvFlag, string $codeNumber, string $sampleCodePrefix, string $sampleCodeNumberPrefix, string $selectedPersonalName, array $dataTypes, ?array $codeList, array $divCodes, array $bindings, array $unionViewerBindings, array $rateBindings, array $resultBindings): void
    {
        $expected = [];
        $rsTimeBoxIds = [33];
        $dataType = [1];
        $dataTypeFlags = ['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => false];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createCommercialListWhere')
            ->with($startDate, $endDate, $cmType, $cmSeconds, $progIds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $straddlingFlg)
            ->andReturn([$rsTimeBoxIds, [20], [30]])
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($bindings, 'bindings'))
            ->once()->ordered();

        // expects
        if (count($divCodes) > 0) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createRtSampleTempTable')
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn()
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('insertTemporaryTable')
                ->withArgs($this->bindAsserts($unionViewerBindings, 'unionViewerBindings'))
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn()
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn()
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

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->withArgs($this->bindAsserts($rateBindings, 'rateBindings'))
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->withArgs($this->bindAsserts($rateBindings, 'rateBindings'))
                ->once()->ordered();
        }

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($resultBindings, 'result'))
            ->once()->ordered();

        $actual = $this->target->searchGrpOriginalDivs($startDate, $endDate, $startTime, $endTime, $cmType, $cmSeconds, $progIds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $conv_15_sec_flag, $period, $allChannels, $straddlingFlg, $length, $page, $csvFlag, $dataType, $codeNumber, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName, $dataTypes, $dataTypeFlags, $codeList);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider searchGrpOriginalDivsDataProvider
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param null|string $cmType
     * @param null|string $cmSeconds
     * @param null|array $progIds
     * @param int $regionId
     * @param string $division
     * @param null|array $codes
     * @param null|array $conditionCross
     * @param null|array $companyIds
     * @param null|array $productIds
     * @param null|array $cmIds
     * @param array $channels
     * @param null|bool $conv_15_sec_flag
     * @param string $period
     * @param null|string $allChannels
     * @param bool $straddlingFlg
     * @param int $length
     * @param int $page
     * @param string $csvFlag
     * @param string $codeNumber
     * @param string $sampleCodePrefix
     * @param string $sampleCodeNumberPrefix
     * @param string $selectedPersonalName
     * @param array $dataTypes
     * @param null|array $codeList
     * @param array $divCodes
     * @param array $unionViewerBindings
     * @param array $rateBindings
     * @param array $bindings
     * @param array $resultBindings
     */
    public function searchGrpOriginalDivsTs(String $startDate, String $endDate, String $startTime, String $endTime, ?String $cmType, ?String $cmSeconds, ?array $progIds, int $regionId, String $division, ?array $codes, ?array $conditionCross, ?array $companyIds, ?array $productIds, ?array $cmIds, array $channels, ?bool $conv_15_sec_flag, string $period, ?string $allChannels, bool $straddlingFlg, int $length, int $page, string $csvFlag, string $codeNumber, string $sampleCodePrefix, string $sampleCodeNumberPrefix, string $selectedPersonalName, array $dataTypes, ?array $codeList, array $divCodes, array $bindings, array $unionViewerBindings, array $rateBindings, array $resultBindings): void
    {
        $expected = [];
        $rsTimeBoxIds = [33];
        $dataType = [2, 3, 4, 5];
        $dataTypeFlags = ['rt' => false, 'ts' => true, 'gross' => true, 'total' => true, 'rtTotal' => true];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createCommercialListWhere')
            ->with($startDate, $endDate, $cmType, $cmSeconds, $progIds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $straddlingFlg)
            ->andReturn([$rsTimeBoxIds, [20], [30]])
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($bindings, 'bindings'))
            ->once()->ordered();

        // expects
        if (count($divCodes) > 0) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createTsSampleTempTable')
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn()
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('insertTemporaryTable')
                ->withArgs($this->bindAsserts($unionViewerBindings, 'unionViewerBindings'))
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn()
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn()
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('insertTemporaryTable')
                ->withArgs($this->bindAsserts($unionViewerBindings, 'unionViewerBindings'))
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn()
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn()
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
                ->andReturn()
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn()
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
                ->andReturn()
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->withArgs($this->bindAsserts($rateBindings, 'lastSelect'))
                ->andReturn([])
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->withArgs($this->bindAsserts($rateBindings, 'rateBindings'))
                ->once()->ordered();
        }

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($resultBindings, 'result'))
            ->once()->ordered();

        $actual = $this->target->searchGrpOriginalDivs($startDate, $endDate, $startTime, $endTime, $cmType, $cmSeconds, $progIds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $conv_15_sec_flag, $period, $allChannels, $straddlingFlg, $length, $page, $csvFlag, $dataType, $codeNumber, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName, $dataTypes, $dataTypeFlags, $codeList);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider searchGrpOriginalDivsDataProvider
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param null|string $cmType
     * @param null|string $cmSeconds
     * @param null|array $progIds
     * @param int $regionId
     * @param string $division
     * @param null|array $codes
     * @param null|array $conditionCross
     * @param null|array $companyIds
     * @param null|array $productIds
     * @param null|array $cmIds
     * @param array $channels
     * @param null|bool $conv_15_sec_flag
     * @param string $period
     * @param null|string $allChannels
     * @param bool $straddlingFlg
     * @param int $length
     * @param int $page
     * @param string $csvFlag
     * @param string $codeNumber
     * @param string $sampleCodePrefix
     * @param string $sampleCodeNumberPrefix
     * @param string $selectedPersonalName
     * @param array $dataTypes
     * @param null|array $codeList
     * @param array $divCodes
     * @param array $unionViewerBindings
     * @param array $rateBindings
     * @param array $bindings
     * @param array $resultBindings
     */
    public function searchGrpOriginalDivsRtTotal(String $startDate, String $endDate, String $startTime, String $endTime, ?String $cmType, ?String $cmSeconds, ?array $progIds, int $regionId, String $division, ?array $codes, ?array $conditionCross, ?array $companyIds, ?array $productIds, ?array $cmIds, array $channels, ?bool $conv_15_sec_flag, string $period, ?string $allChannels, bool $straddlingFlg, int $length, int $page, string $csvFlag, string $codeNumber, string $sampleCodePrefix, string $sampleCodeNumberPrefix, string $selectedPersonalName, array $dataTypes, ?array $codeList, array $divCodes, array $bindings, array $unionViewerBindings, array $rateBindings, array $resultBindings): void
    {
        $expected = [];
        $rsTimeBoxIds = [33];
        $dataType = [5];
        $dataTypeFlags = ['rt' => false, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => true];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createCommercialListWhere')
            ->with($startDate, $endDate, $cmType, $cmSeconds, $progIds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $straddlingFlg)
            ->andReturn([$rsTimeBoxIds, [20], [30]])
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($bindings, 'bindings'))
            ->once()->ordered();

        // expects
        if (count($divCodes) > 0) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createTsSampleTempTable')
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn()
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('insertTemporaryTable')
                ->withArgs($this->bindAsserts($unionViewerBindings, 'unionViewerBindings'))
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn()
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn()
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('insertTemporaryTable')
                ->withArgs($this->bindAsserts($unionViewerBindings, 'unionViewerBindings'))
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn()
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn()
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
                ->andReturn()
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn()
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
                ->andReturn()
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->withArgs($this->bindAsserts($rateBindings, 'lastSelect'))
                ->andReturn([])
                ->once()->ordered();

            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->withArgs($this->bindAsserts($rateBindings, 'rateBindings'))
                ->once()->ordered();
        }

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($resultBindings, 'result'))
            ->once()->ordered();

        $actual = $this->target->searchGrpOriginalDivs($startDate, $endDate, $startTime, $endTime, $cmType, $cmSeconds, $progIds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $conv_15_sec_flag, $period, $allChannels, $straddlingFlg, $length, $page, $csvFlag, $dataType, $codeNumber, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName, $dataTypes, $dataTypeFlags, $codeList);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider searchGrpOriginalDivsDataProvider
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param null|string $cmType
     * @param null|string $cmSeconds
     * @param null|array $progIds
     * @param int $regionId
     * @param string $division
     * @param null|array $codes
     * @param null|array $conditionCross
     * @param null|array $companyIds
     * @param null|array $productIds
     * @param null|array $cmIds
     * @param array $channels
     * @param null|bool $conv_15_sec_flag
     * @param string $period
     * @param null|string $allChannels
     * @param bool $straddlingFlg
     * @param int $length
     * @param int $page
     * @param string $csvFlag
     * @param string $codeNumber
     * @param string $sampleCodePrefix
     * @param string $sampleCodeNumberPrefix
     * @param string $selectedPersonalName
     * @param array $dataTypes
     * @param null|array $codeList
     * @param array $divCodes
     * @param array $unionViewerBindings
     * @param array $rateBindings
     * @param array $bindings
     * @param array $resultBindings
     */
    public function searchGrpOriginalDivsEmpty(String $startDate, String $endDate, String $startTime, String $endTime, ?String $cmType, ?String $cmSeconds, ?array $progIds, int $regionId, String $division, ?array $codes, ?array $conditionCross, ?array $companyIds, ?array $productIds, ?array $cmIds, array $channels, ?bool $conv_15_sec_flag, string $period, ?string $allChannels, bool $straddlingFlg, int $length, int $page, string $csvFlag, string $codeNumber, string $sampleCodePrefix, string $sampleCodeNumberPrefix, string $selectedPersonalName, array $dataTypes, ?array $codeList, array $divCodes, array $bindings, array $unionViewerBindings, array $rateBindings, array $resultBindings): void
    {
        $expected = [];

        $dataType = [1];
        $dataTypeFlags = ['rt' => true, 'ts' => false, 'gross' => false, 'total' => false, 'rtTotal' => false];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createCommercialListWhere')
            ->with($startDate, $endDate, $cmType, $cmSeconds, $progIds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $straddlingFlg)
            ->andReturn([[], [], []])
            ->once()->ordered();

        $actual = $this->target->searchGrpOriginalDivs($startDate, $endDate, $startTime, $endTime, $cmType, $cmSeconds, $progIds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $conv_15_sec_flag, $period, $allChannels, $straddlingFlg, $length, $page, $csvFlag, $dataType, $codeNumber, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName, $dataTypes, $dataTypeFlags, $codeList);

        $this->assertEquals($expected, $actual);
    }

    public function searchGrpOriginalDivsDataProvider()
    {
        $cases = ['case1', 'case2', 'case3', 'case4'];
        $startDate = ['19900614', '19900614', '19900614', '19900614'];
        $endDate = ['19900615', '19900615', '19900615', '19900615'];
        $startTime = ['050000', '100000', '120000', '120000'];
        $endTime = ['045959', '045959', '200000', '200000'];
        $cmType = ['1', '2', '', ''];
        $cmSeconds = ['2', '3', '', ''];
        $progIds = [['10'], ['10'], ['10'], ['10'], ['10']];
        $regionId = [1, 2, 1, 1];
        $division = ['ga8', 'ga8', 'condition_cross', 'condition_cross'];
        $codes = [['personal'], ['personal', 'c1', 'c2', 'household'], ['condition_cross'], ['condition_cross']];
        $conditionCross = [[], [], [], []];
        $companyIds = [[1, 2, 3], [1, 2, 3], [1, 2, 3], [1, 2, 3]];
        $productIds = [[10, 11, 12], [], [], []];
        $cmIds = [['20', '21', '22'], [], [], []];
        $channels = [[1, 2, 3], [], [], []];
        $conv15SecFlag = [null, false, true];
        $period = ['period', 'day', 'week', 'month'];
        $allChannels = ['true', 'false', 'true', 'false'];
        $straddlingFlg = [true, true, false, false];
        $length = [30, 30, 30, 30];
        $page = [1, 2, 3, 4];
        $csvFlag = ['0', '0', '1', '1'];
        $codeNumber = ['44', '44', '44', '44'];
        $sampleCodePrefix = ['sampleCodePrefix', 'sampleCodePrefix', 'sampleCodePrefix', 'sampleCodePrefix'];
        $sampleCodeNumberPrefix = ['sampleCodeNumberPrefix', 'sampleCodeNumberPrefix', 'sampleCodeNumberPrefix', 'sampleCodeNumberPrefix'];
        $selectedPersonalName = ['selectedPersonalName', 'selectedPersonalName', 'selectedPersonalName', 'selectedPersonalName'];
        $dataTypes = [['rt' => 1, 'ts' => 2, 'gross' => 3, 'total' => 4, 'rtTotal' => 5], ['rt' => 1, 'ts' => 2, 'gross' => 3, 'total' => 4, 'rtTotal' => 5], ['rt' => 1, 'ts' => 2, 'gross' => 3, 'total' => 4, 'rtTotal' => 5], ['rt' => 1, 'ts' => 2, 'gross' => 3, 'total' => 4, 'rtTotal' => 5, 'dummy' => 5]];
        $codeList = [[], ['personal', 'c1', 'c2', 'c3', 'household'], [], []];
        $divCodes = [[], ['c1', 'c2'], ['condition_cross'], ['condition_cross'], ['condition_cross']];
        $bindings = [
            [
                ':startDate' => '19900614',
                ':endDate' => '19900615',
                ':channels0' => 1,
                ':channels1' => 2,
                ':channels2' => 3,
                ':progIds0' => '10',
                ':region_id' => 1,
                ':companyIds0' => 1,
                ':companyIds1' => 2,
                ':companyIds2' => 3,
                ':productIds0' => 10,
                ':productIds1' => 11,
                ':productIds2' => 12,
                ':cmIds0' => '20',
                ':cmIds1' => '21',
                ':cmIds2' => '22',
            ],
            [
                ':conv15SecFlag' => 15,
                ':startDate' => '19900614',
                ':endDate' => '19900615',
                ':progIds0' => '10',
                ':region_id' => 2,
                ':companyIds0' => 1,
                ':companyIds1' => 2,
                ':companyIds2' => 3,
                ':startTime' => '100000',
                ':endTime' => '045959',
            ],
            [
                ':conv15SecFlag' => 1,
                ':startDate' => '19900614',
                ':endDate' => '19900615',
                ':progIds0' => '10',
                ':region_id' => 1,
                ':companyIds0' => 1,
                ':companyIds1' => 2,
                ':companyIds2' => 3,
                ':startTime' => '120000',
                ':endTime' => '200000',
            ],
            [
                ':startDate' => '19900614',
                ':endDate' => '19900615',
                ':progIds0' => '10',
                ':region_id' => 1,
                ':companyIds0' => 1,
                ':companyIds1' => 2,
                ':companyIds2' => 3,
                ':startTime' => '120000',
                ':endTime' => '200000',
            ],
        ];
        $unionViewerBindings = [
            [
            ],
            [
                ':startDate' => '19900614',
                ':endDate' => '19900615',
                ':progStartDate' => '199006130000000000',
                ':progEndDate' => '199006169999999999',
                ':companyIds0' => 1,
                ':companyIds1' => 2,
                ':companyIds2' => 3,
            ],
            [
                ':startDate' => '19900614',
                ':endDate' => '19900615',
                ':progStartDate' => '199006130000000000',
                ':progEndDate' => '199006169999999999',
                ':companyIds0' => 1,
                ':companyIds1' => 2,
                ':companyIds2' => 3,
            ],
            [
                ':startDate' => '19900614',
                ':endDate' => '19900615',
                ':progStartDate' => '199006130000000000',
                ':progEndDate' => '199006169999999999',
                ':companyIds0' => 1,
                ':companyIds1' => 2,
                ':companyIds2' => 3,
            ],
        ];
        $rateBindings = [
            [
            ],
            [
                ':conv15SecFlag' => 15,
            ],
            [
                ':conv15SecFlag' => 1,
            ],
            [
            ],
        ];
        $resultBindings = [
            [
                ':from' => 1,
                ':to' => 30,
            ],
            [
                ':from' => 31,
                ':to' => 60,
            ],
            [
            ],
            [
            ],
        ];

        foreach ($cases as $i => $case) {
            yield $case => [
                $startDate[$i],
                $endDate[$i],
                $startTime[$i],
                $endTime[$i],
                $cmType[$i],
                $cmSeconds[$i],
                $progIds[$i],
                $regionId[$i],
                $division[$i],
                $codes[$i],
                $conditionCross[$i],
                $companyIds[$i],
                $productIds[$i],
                $cmIds[$i],
                $channels[$i],
                $conv15SecFlag[$i],
                $period[$i],
                $allChannels[$i],
                $straddlingFlg[$i],
                $length[$i],
                $page[$i],
                $csvFlag[$i],
                $codeNumber[$i],
                $sampleCodePrefix[$i],
                $sampleCodeNumberPrefix[$i],
                $selectedPersonalName[$i],
                $dataTypes[$i],
                $codeList[$i],
                $divCodes[$i],
                $bindings[$i],
                $unionViewerBindings[$i],
                $rateBindings[$i],
                $resultBindings[$i],
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
