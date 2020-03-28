<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Dwh;

use Mockery;
use Switchm\SmartApi\Queries\Dao\Dwh\RafDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class RafDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = Mockery::mock(RafDao::class, [])->makePartial();
    }

    /**
     * @test
     */
    public function selectTempResultsForCsv(): void
    {
        $expected = [];

        $limit = 1;
        $offset = 2;

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->andReturn($expected)
            ->once();

        $actual = $this->target->selectTempResultsForCsv($limit, $offset);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider createCsvTempTableDataProvider
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param string $cmType
     * @param string $cmSeconds
     * @param int $regionId
     * @param string $division
     * @param null|array $codes
     * @param array $conditionCross
     * @param array $companyIds
     * @param null|array $productIds
     * @param null|array $cmIds
     * @param array $channels
     * @param null|bool $conv15SecFlag
     * @param null|array $progIds
     * @param bool $straddlingFlg
     * @param array $dataType
     * @param string $axisType
     * @param null|string $channelAxis
     * @param string $period
     * @param array $fqBindings
     * @param array $listBinds
     * @param array $dataTypeFlags
     * @param string $axisTypeProduct
     * @param string $axisTypeCompany
     */
    public function createCsvTempTable(String $startDate, String $endDate, String $startTime, String $endTime, String $cmType, String $cmSeconds, int $regionId, String $division, ?array $codes, array $conditionCross, array $companyIds, ?array $productIds, ?array $cmIds, array $channels, ?bool $conv15SecFlag, ?array $progIds, bool $straddlingFlg, array $dataType, string $axisType, ?string $channelAxis, string $period, array $dataTypeFlags, string $axisTypeProduct, string $axisTypeCompany, array $fqBindings, array $listBinds): void
    {
        // for fq_list
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once();

        // rt_total_cm_viewers  $isGross || $isRtTotal
        if ($dataTypeFlags['isGross'] || $dataTypeFlags['isRtTotal']) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->withArgs($this->bindAsserts($fqBindings, 'fqBindings'))
                ->once();
        }

        // for insertTemporaryTable
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('insertTemporaryTable')
            ->withArgs($this->bindAsserts($fqBindings, 'fqBindings'))
            ->once();

        // ANALYZE fq_list
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once();

        // for results
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once();

        // for list insertTemporaryTable
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('insertTemporaryTable')
            ->withArgs($this->bindAsserts($listBinds, 'insertTemporaryTable'))
            ->once();

        // for Analyze results
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once();

        $actual = $this->target->createCsvTempTable($startDate, $endDate, $startTime, $endTime, $cmType, $cmSeconds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $conv15SecFlag, $progIds, $straddlingFlg, $dataType, $axisType, $channelAxis, $period, $dataTypeFlags, $axisTypeProduct, $axisTypeCompany);
    }

    public function createCsvTempTableDataProvider()
    {
        $cases = ['case1', 'case2', 'case3', 'case4', 'case5', 'case6', 'case7'];
        $startDate = ['19900614', '19900614', '19900614', '19900614', '19900614', '19900614', '19900614'];
        $endDate = ['19900615', '19900615', '19900615', '19900615', '19900615', '19900615', '19900615'];
        $startTime = ['050000', '100000', '120000', '120000', '050000', '100000', '120000'];
        $endTime = ['045959', '045959', '200000', '200000', '045959', '045959', '200000'];
        $cmType = ['1', '2', '', '', '1', '2', ''];
        $cmSeconds = ['2', '3', '', '', '2', '3', ''];
        $regionId = [1, 2, 1, 2, 1, 2, 1];
        $division = ['ga8', 'ga8', 'ga8', 'condition_cross', 'ga8', 'ga8', 'ga8'];
        $codes = [['personal'], ['personal'], ['personal'], [], ['household'], ['household'], ['household']];
        $conditionCross = [[], [], [], [], [], [], []];
        $companyIds = [[1, 2, 3], [1, 2, 3], [1, 2, 3], [1, 2, 3], [1, 2, 3], [1, 2, 3], [1, 2, 3]];
        $productIds = [[10, 11, 12], [], [], [], [10, 11, 12], [], []];
        $cmIds = [['20', '21', '22'], [], [], [], ['20', '21', '22'], [], []];
        $channels = [[1, 2, 3], [], [], [], [1, 2, 3], [], []];
        $conv15SecFlag = [null, true, false, false, null, true, false];
        $progIds = [['30', '31', '32'], [], [], [], ['30', '31', '32'], [], []];
        $straddlingFlg = [true, true, false, false, true, true, false];
        $dataType = [[], [], [], [], [], [], []];
        $axisType = ['1', '2', '', '', '1', '2', ''];
        $channelAxis = ['1', '0', '', '', '1', '0', ''];
        $period = ['cm', '', '', '', 'cm', '', ''];
        $dataTypeFlags = [
            ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            ['isRt' => false, 'isTs' => true, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            ['isRt' => false, 'isTs' => false, 'isGross' => true, 'isTotal' => false, 'isRtTotal' => false],
            ['isRt' => false, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => true],
            ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            ['isRt' => false, 'isTs' => true, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            ['isRt' => false, 'isTs' => false, 'isGross' => true, 'isTotal' => false, 'isRtTotal' => false],
        ];
        $axisTypeProduct = ['2', '2', '2', '2', '2', '2', '2'];
        $axisTypeCompany = ['1', '1', '1', '1', '1', '1', '1'];
        $fqBindings = [[
            ':companyIds0' => 1,
            ':companyIds1' => 2,
            ':companyIds2' => 3,
            ':startDate' => '19900614',
            ':endDate' => '19900615',
            ':progStartDate' => '199006130000000000',
            ':progEndDate' => '199006169999999999',
        ], [
            ':companyIds0' => 1,
            ':companyIds1' => 2,
            ':companyIds2' => 3,
            ':startDate' => '19900614',
            ':endDate' => '19900615',
            ':progStartDate' => '199006130000000000',
            ':progEndDate' => '199006169999999999',
        ], [
            ':companyIds0' => 1,
            ':companyIds1' => 2,
            ':companyIds2' => 3,
            ':startDate' => '19900614',
            ':endDate' => '19900615',
            ':progStartDate' => '199006130000000000',
            ':progEndDate' => '199006169999999999',
        ], [
            ':companyIds0' => 1,
            ':companyIds1' => 2,
            ':companyIds2' => 3,
            ':startDate' => '19900614',
            ':endDate' => '19900615',
            ':progStartDate' => '199006130000000000',
            ':progEndDate' => '199006169999999999',
        ], [
            ':companyIds0' => 1,
            ':companyIds1' => 2,
            ':companyIds2' => 3,
            ':startDate' => '19900614',
            ':endDate' => '19900615',
            ':progStartDate' => '199006130000000000',
            ':progEndDate' => '199006169999999999',
        ], [
            ':companyIds0' => 1,
            ':companyIds1' => 2,
            ':companyIds2' => 3,
            ':startDate' => '19900614',
            ':endDate' => '19900615',
            ':progStartDate' => '199006130000000000',
            ':progEndDate' => '199006169999999999',
        ], [
            ':companyIds0' => 1,
            ':companyIds1' => 2,
            ':companyIds2' => 3,
            ':startDate' => '19900614',
            ':endDate' => '19900615',
            ':progStartDate' => '199006130000000000',
            ':progEndDate' => '199006169999999999',
        ]];
        $listBinds = [
            [
                ':personal' => 'personal',
            ],
            [
                ':conv15SecFlag' => 1,
                ':personal' => 'personal',
            ],
            [
                ':conv15SecFlag' => 15,
                ':personal' => 'personal',
            ],
            [
                ':conv15SecFlag' => 15,
                ':condition_cross_code_tac' => 'condition_cross',
            ],
            [
            ],
            [
                ':conv15SecFlag' => 1,
            ],
            [
                ':conv15SecFlag' => 15,
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
                $regionId[$i],
                $division[$i],
                $codes[$i],
                $conditionCross[$i],
                $companyIds[$i],
                $productIds[$i],
                $cmIds[$i],
                $channels[$i],
                $conv15SecFlag[$i],
                $progIds[$i],
                $straddlingFlg[$i],
                $dataType[$i],
                $axisType[$i],
                $channelAxis[$i],
                $period[$i],
                $dataTypeFlags[$i],
                $axisTypeProduct[$i],
                $axisTypeCompany[$i],
                $fqBindings[$i],
                $listBinds[$i],
            ];
        }
    }

    /**
     * @test
     * @dataProvider commonCreateTempTablesDataProvider
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param string $cmType
     * @param string $cmSeconds
     * @param int $regionId
     * @param string $division
     * @param ?array $codes
     * @param array $conditionCross
     * @param array $companyIds
     * @param ?array $productIds
     * @param ?array $cmIds
     * @param array $channels
     * @param ?bool $conv15SecFlag
     * @param ?array $progIds
     * @param bool $straddlingFlg
     * @param array $dataType
     * @param string $axisType
     * @param ?string $channelAxis
     * @param string $period
     * @param array $dataTypeFlags
     * @param string $axisTypeProduct
     * @param string $axisTypeCompany
     * @param array $bindings
     * @param array $sampleBindings
     */
    public function commonCreateTempTables(String $startDate, String $endDate, String $startTime, String $endTime, String $cmType, String $cmSeconds, int $regionId, String $division, ?array $codes, array $conditionCross, array $companyIds, ?array $productIds, ?array $cmIds, array $channels, ?bool $conv15SecFlag, ?array $progIds, bool $straddlingFlg, array $dataType, string $axisType, ?string $channelAxis, string $period, array $dataTypeFlags, string $axisTypeProduct, string $axisTypeCompany, array $bindings, array $sampleBindings): void
    {
        // for cm_list
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once();
        // for analyze
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any())
            ->once();

        // for bindings
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('insertTemporaryTable')
            ->withArgs($this->bindAsserts($bindings, 'bindings'))
            ->once();

        if ($period !== 'cm') {
            // for date_list
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->once();

            // for adjust_cm_list
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->once();
        }

        if ($division === 'condition_cross') {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createConditionCrossSql')
                ->with(Mockery::any(), Mockery::any())
                ->andReturn('')
                ->once();
        } else {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('createCrossJoinWhereClause')
                ->with(Mockery::any(), Mockery::any(), Mockery::any(), Mockery::any())
                ->andReturn('')
                ->once();
        }

        if ($dataTypeFlags['isRt'] || $dataTypeFlags['isGross'] || $dataTypeFlags['isRtTotal']) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->withArgs($this->bindAsserts($sampleBindings, 'samples'))
                ->andReturn([])
                ->once();
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn([])
                ->once();
        }

        if ($dataTypeFlags['isTs'] || $dataTypeFlags['isGross'] || $dataTypeFlags['isRtTotal']) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->withArgs($this->bindAsserts($sampleBindings, 'samples'))
                ->andReturn([])
                ->once();
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->andReturn([])
                ->once();
        }

        $actual = $this->target->commonCreateTempTables($startDate, $endDate, $startTime, $endTime, $cmType, $cmSeconds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $conv15SecFlag, $progIds, $straddlingFlg, $dataType, $axisType, $channelAxis, $period, $dataTypeFlags, $axisTypeProduct, $axisTypeCompany);
    }

    public function commonCreateTempTablesDataProvider()
    {
        $cases = ['case1', 'case2', 'case3', 'case4', 'case5', 'case6', 'case7'];
        $startDate = ['19900614', '19900614', '19900614', '19900614', '19900614', '19900614', '19900614'];
        $endDate = ['19900615', '19900615', '19900615', '19900615', '19900615', '19900615', '19900615'];
        $startTime = ['050000', '100000', '120000', '120000', '050000', '100000', '120000'];
        $endTime = ['045959', '045959', '200000', '200000', '045959', '045959', '200000'];
        $cmType = ['1', '2', '', '', '1', '2', ''];
        $cmSeconds = ['2', '3', '', '', '2', '3', ''];
        $regionId = [1, 2, 1, 2, 1, 2, 1];
        $division = ['ga8', 'ga8', 'ga8', 'condition_cross', 'ga8', 'ga8', 'ga8'];
        $codes = [['personal'], ['personal'], ['personal'], [], ['household'], ['household'], ['household']];
        $conditionCross = [[], [], [], [], [], [], []];
        $companyIds = [[1, 2, 3], [1, 2, 3], [1, 2, 3], [1, 2, 3], [1, 2, 3], [1, 2, 3], [1, 2, 3]];
        $productIds = [[10, 11, 12], [], [], [], [10, 11, 12], [], []];
        $cmIds = [['20', '21', '22'], [], [], [], ['20', '21', '22'], [], []];
        $channels = [[1, 2, 3], [], [], [], [1, 2, 3], [], []];
        $conv15SecFlag = [null, true, false, false, null, true, false];
        $progIds = [['30', '31', '32'], [], [], [], ['30', '31', '32'], [], []];
        $straddlingFlg = [true, true, false, false, true, true, false];
        $dataType = [[], [], [], [], [], [], []];
        $axisType = ['1', '2', '', '', '1', '2', ''];
        $channelAxis = ['1', '0', '', '', '1', '0', ''];
        $period = ['cm', 'day', 'week', 'month', 'cm', 'day', ''];
        $dataTypeFlags = [
            ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            ['isRt' => false, 'isTs' => true, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            ['isRt' => false, 'isTs' => false, 'isGross' => true, 'isTotal' => false, 'isRtTotal' => false],
            ['isRt' => false, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => true],
            ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            ['isRt' => false, 'isTs' => true, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            ['isRt' => false, 'isTs' => false, 'isGross' => true, 'isTotal' => false, 'isRtTotal' => false],
        ];
        $axisTypeProduct = ['2', '2', '2', '2', '2', '2', '2'];
        $axisTypeCompany = ['1', '1', '1', '1', '1', '1', '1'];
        $bindings = [[
            ':startDate' => '19900614',
            ':endDate' => '19900615',
            ':channels0' => 1,
            ':channels1' => 2,
            ':channels2' => 3,
            ':companyIds0' => 1,
            ':companyIds1' => 2,
            ':companyIds2' => 3,
            ':productIds0' => 10,
            ':productIds1' => 11,
            ':productIds2' => 12,
            ':cmIds0' => '20',
            ':cmIds1' => '21',
            ':cmIds2' => '22',
            ':progIds0' => '30',
            ':progIds1' => '31',
            ':progIds2' => '32',
            ':regionId' => 1,
        ], [
            ':startDate' => '19900614',
            ':endDate' => '19900615',
            ':companyIds0' => 1,
            ':companyIds1' => 2,
            ':companyIds2' => 3,
            ':startTime' => '100000',
            ':endTime' => '045959',
            ':regionId' => 2,
        ], [
            ':startDate' => '19900614',
            ':endDate' => '19900615',
            ':companyIds0' => 1,
            ':companyIds1' => 2,
            ':companyIds2' => 3,
            ':startTime' => '120000',
            ':endTime' => '200000',
            ':regionId' => 1,
        ], [
            ':startDate' => '19900614',
            ':endDate' => '19900615',
            ':companyIds0' => 1,
            ':companyIds1' => 2,
            ':companyIds2' => 3,
            ':startTime' => '120000',
            ':endTime' => '200000',
            ':regionId' => 2,
        ], [
            ':startDate' => '19900614',
            ':endDate' => '19900615',
            ':channels0' => 1,
            ':channels1' => 2,
            ':channels2' => 3,
            ':companyIds0' => 1,
            ':companyIds1' => 2,
            ':companyIds2' => 3,
            ':productIds0' => 10,
            ':productIds1' => 11,
            ':productIds2' => 12,
            ':cmIds0' => '20',
            ':cmIds1' => '21',
            ':cmIds2' => '22',
            ':progIds0' => '30',
            ':progIds1' => '31',
            ':progIds2' => '32',
            ':regionId' => 1,
        ], [
            ':startDate' => '19900614',
            ':endDate' => '19900615',
            ':companyIds0' => 1,
            ':companyIds1' => 2,
            ':companyIds2' => 3,
            ':startTime' => '100000',
            ':endTime' => '045959',
            ':regionId' => 2,
        ], [
            ':startDate' => '19900614',
            ':endDate' => '19900615',
            ':companyIds0' => 1,
            ':companyIds1' => 2,
            ':companyIds2' => 3,
            ':startTime' => '120000',
            ':endTime' => '200000',
            ':regionId' => 1,
        ]];
        $sampleBindings = [
            [
                ':union_ga8_personal' => 'personal',
            ],
            [
                ':union_ga8_personal' => 'personal',
            ],
            [
                ':union_ga8_personal' => 'personal',
            ],
            [
                ':condition_cross_code' => 'condition_cross',
            ],
            [
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
                $regionId[$i],
                $division[$i],
                $codes[$i],
                $conditionCross[$i],
                $companyIds[$i],
                $productIds[$i],
                $cmIds[$i],
                $channels[$i],
                $conv15SecFlag[$i],
                $progIds[$i],
                $straddlingFlg[$i],
                $dataType[$i],
                $axisType[$i],
                $channelAxis[$i],
                $period[$i],
                $dataTypeFlags[$i],
                $axisTypeProduct[$i],
                $axisTypeCompany[$i],
                $bindings[$i],
                $sampleBindings[$i],
            ];
        }
    }

    /**
     * @test
     * @dataProvider getChartResultsDataProvider
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param string $cmType
     * @param string $cmSeconds
     * @param int $regionId
     * @param string $division
     * @param array $codes
     * @param array $conditionCross
     * @param array $companyIds
     * @param array $productIds
     * @param array $cmIds
     * @param array $channels
     * @param bool $conv15SecFlag
     * @param array $progIds
     * @param bool $straddlingFlg
     * @param array $dataType
     * @param array $reachAndFrequencyGroupingUnit
     * @param array $dataTypeFlags
     * @param array $cvBindings
     * @param array $viewBindings
     * @param array $bindings
     */
    public function getChartResults(String $startDate, String $endDate, String $startTime, String $endTime, String $cmType, String $cmSeconds, int $regionId, String $division, array $codes, array $conditionCross, array $companyIds, array $productIds, array $cmIds, array $channels, bool $conv15SecFlag, array $progIds, bool $straddlingFlg, array $dataType, array $reachAndFrequencyGroupingUnit, array $dataTypeFlags, array $cvBindings, array $viewBindings, array $bindings): void
    {
        if ($dataTypeFlags['isRt'] || $dataTypeFlags['isGross'] || $dataTypeFlags['isRtTotal']) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->once();
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('insertTemporaryTable')
                ->withArgs($this->bindAsserts($cvBindings, 'cvBindings'))
                ->once();
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->once();
        }

        if ($dataTypeFlags['isTs'] || $dataTypeFlags['isGross'] || $dataTypeFlags['isRtTotal']) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->once();
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('insertTemporaryTable')
                ->withArgs($this->bindAsserts($cvBindings, 'cvBindings'))
                ->once();
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->once();
        }

        if ($dataTypeFlags['isGross'] || $dataTypeFlags['isRtTotal']) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->once();
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('insertTemporaryTable')
                ->with(Mockery::any())
                ->once();
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->with(Mockery::any())
                ->once();
        }

        if ($dataTypeFlags['isRt'] || $dataTypeFlags['isTs'] || $dataTypeFlags['isGross']) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->withArgs($this->bindAsserts($viewBindings, 'viewBindings'))
                ->once();
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->withArgs($this->bindAsserts($viewBindings, 'viewBindings'))
                ->once();
        }

        if ($dataTypeFlags['isRtTotal']) {
            $this->target
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('select')
                ->withArgs($this->bindAsserts($viewBindings, 'viewBindings'))
                ->once();
        }

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($bindings, 'bindings'))
            ->once();

        $actual = $this->target->getChartResults($startDate, $endDate, $startTime, $endTime, $cmType, $cmSeconds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $conv15SecFlag, $progIds, $straddlingFlg, $dataType, $reachAndFrequencyGroupingUnit, $dataTypeFlags);
    }

    public function getChartResultsDataProvider()
    {
        $cases = ['case1', 'case2', 'case3', 'case4', 'case5', 'case6', 'case7', 'case8', 'case9', 'case10', 'case11'];
        $startDate = ['19900614', '19900614', '19900614', '19900614', '19900614', '19900614', '19900614', '19900614', '19900614', '19900614', '19900614'];
        $endDate = ['19900615', '19900615', '19900615', '19900615', '19900615', '19900615', '19900615', '19900615', '19900615', '19900615', '19900615'];
        $startTime = ['050000', '100000', '120000', '120000', '050000', '100000', '120000', '120000', '100000', '120000', '120000'];
        $endTime = ['045959', '045959', '200000', '200000', '045959', '045959', '200000', '200000', '045959', '200000', '200000'];
        $cmType = ['1', '2', '', '', '1', '2', '', '', '2', '', ''];
        $cmSeconds = ['2', '3', '', '', '2', '3', '', '', '3', '', ''];
        $regionId = [1, 2, 1, 2, 1, 2, 1, 1, 2, 1, 1];
        $division = ['ga8', 'ga8', 'ga8', 'condition_cross', 'ga8', 'ga8', 'ga8', 'ga8', 'condition_cross', 'condition_cross', 'condition_cross'];
        $codes = [['personal'], ['personal'], ['personal'], [], ['household'], ['household'], ['household'], ['personal'], [], [], []];
        $conditionCross = [[], [], [], [], [], [], [], [], [], [], []];
        $companyIds = [[1, 2, 3], [1, 2, 3], [1, 2, 3], [1, 2, 3], [1, 2, 3], [1, 2, 3], [1, 2, 3], [1, 2, 3], [1, 2, 3], [1, 2, 3], [1, 2, 3]];
        $productIds = [[10, 11, 12], [], [], [], [10, 11, 12], [], [], [], [], [], []];
        $cmIds = [['20', '21', '22'], [], [], [], ['20', '21', '22'], [], [], [], [], [], []];
        $channels = [[1, 2, 3], [], [], [], [1, 2, 3], [], [], [], [], [], []];
        $conv15SecFlag = [false, true, false, false, false, true, false, true, true, false, true];
        $progIds = [['30', '31', '32'], [], [], [], ['30', '31', '32'], [], [], [], [], [], []];
        $straddlingFlg = [true, true, false, false, true, true, false, true, true, false, true];
        $dataType = [[], [], [], [], [], [], [], [], [], [], []];
        $reachAndFrequencyGroupingUnit = [[3, 6, 8], [3, 6, 8], [3, 6, 8], [3, 6, 8], [3, 6, 8], [3, 6, 8], [3, 6, 8], [3, 6, 8], [3, 6, 8], [3, 6, 8], [3, 6, 8]];
        $dataTypeFlags = [
            ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            ['isRt' => false, 'isTs' => true, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            ['isRt' => false, 'isTs' => false, 'isGross' => true, 'isTotal' => false, 'isRtTotal' => false],
            ['isRt' => false, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => true], // condition_cross
            ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false], // households
            ['isRt' => false, 'isTs' => true, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false], // households
            ['isRt' => false, 'isTs' => false, 'isGross' => true, 'isTotal' => false, 'isRtTotal' => false], // households
            ['isRt' => false, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => true],
            ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            ['isRt' => false, 'isTs' => true, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            ['isRt' => false, 'isTs' => false, 'isGross' => true, 'isTotal' => false, 'isRtTotal' => false],
        ];
        $cvBindings = [[
            ':companyIds0' => 1,
            ':companyIds1' => 2,
            ':companyIds2' => 3,
            ':startDate' => '19900614',
            ':endDate' => '19900615',
            ':progStartDate' => '199006130000000000',
            ':progEndDate' => '199006169999999999',
        ], [
            ':companyIds0' => 1,
            ':companyIds1' => 2,
            ':companyIds2' => 3,
            ':startDate' => '19900614',
            ':endDate' => '19900615',
            ':progStartDate' => '199006130000000000',
            ':progEndDate' => '199006169999999999',
        ], [
            ':companyIds0' => 1,
            ':companyIds1' => 2,
            ':companyIds2' => 3,
            ':startDate' => '19900614',
            ':endDate' => '19900615',
            ':progStartDate' => '199006130000000000',
            ':progEndDate' => '199006169999999999',
        ], [
            ':companyIds0' => 1,
            ':companyIds1' => 2,
            ':companyIds2' => 3,
            ':startDate' => '19900614',
            ':endDate' => '19900615',
            ':progStartDate' => '199006130000000000',
            ':progEndDate' => '199006169999999999',
        ], [
            ':companyIds0' => 1,
            ':companyIds1' => 2,
            ':companyIds2' => 3,
            ':startDate' => '19900614',
            ':endDate' => '19900615',
            ':progStartDate' => '199006130000000000',
            ':progEndDate' => '199006169999999999',
        ], [
            ':companyIds0' => 1,
            ':companyIds1' => 2,
            ':companyIds2' => 3,
            ':startDate' => '19900614',
            ':endDate' => '19900615',
            ':progStartDate' => '199006130000000000',
            ':progEndDate' => '199006169999999999',
        ], [
            ':companyIds0' => 1,
            ':companyIds1' => 2,
            ':companyIds2' => 3,
            ':startDate' => '19900614',
            ':endDate' => '19900615',
            ':progStartDate' => '199006130000000000',
            ':progEndDate' => '199006169999999999',
        ], [
            ':companyIds0' => 1,
            ':companyIds1' => 2,
            ':companyIds2' => 3,
            ':startDate' => '19900614',
            ':endDate' => '19900615',
            ':progStartDate' => '199006130000000000',
            ':progEndDate' => '199006169999999999',
        ], [
            ':companyIds0' => 1,
            ':companyIds1' => 2,
            ':companyIds2' => 3,
            ':startDate' => '19900614',
            ':endDate' => '19900615',
            ':progStartDate' => '199006130000000000',
            ':progEndDate' => '199006169999999999',
        ], [
            ':companyIds0' => 1,
            ':companyIds1' => 2,
            ':companyIds2' => 3,
            ':startDate' => '19900614',
            ':endDate' => '19900615',
            ':progStartDate' => '199006130000000000',
            ':progEndDate' => '199006169999999999',
        ], [
            ':companyIds0' => 1,
            ':companyIds1' => 2,
            ':companyIds2' => 3,
            ':startDate' => '19900614',
            ':endDate' => '19900615',
            ':progStartDate' => '199006130000000000',
            ':progEndDate' => '199006169999999999',
        ]];
        $viewBindings = [[
            ':conv15SecFlag' => 15,
        ], [
            ':conv15SecFlag' => 1,
        ], [
            ':conv15SecFlag' => 15,
        ], [
            ':conv15SecFlag' => 15,
        ], [
            ':conv15SecFlag' => 15,
        ], [
            ':conv15SecFlag' => 1,
        ], [
            ':conv15SecFlag' => 15,
        ], [
            ':conv15SecFlag' => 1,
        ], [
            ':conv15SecFlag' => 1,
        ], [
            ':conv15SecFlag' => 15,
        ], [
            ':conv15SecFlag' => 1,
        ]];
        $bindings = [[
            ':personal' => 'personal',
        ], [
            ':personal' => 'personal',
        ], [
            ':personal' => 'personal',
        ], [
            ':condition_cross_code_tac' => 'condition_cross',
        ], [], [], [],
            [
                ':personal' => 'personal',
            ], [
                ':condition_cross_code_tac' => 'condition_cross',
            ], [
                ':condition_cross_code_tac' => 'condition_cross',
            ], [
                ':condition_cross_code_tac' => 'condition_cross',
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
                $regionId[$i],
                $division[$i],
                $codes[$i],
                $conditionCross[$i],
                $companyIds[$i],
                $productIds[$i],
                $cmIds[$i],
                $channels[$i],
                $conv15SecFlag[$i],
                $progIds[$i],
                $straddlingFlg[$i],
                $dataType[$i],
                $reachAndFrequencyGroupingUnit[$i],
                $dataTypeFlags[$i],
                $cvBindings[$i],
                $viewBindings[$i],
                $bindings[$i],
            ];
        }
    }

    /**
     * @test
     * @dataProvider getChartResultsRtTsDataProvider
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param string $cmType
     * @param string $cmSeconds
     * @param int $regionId
     * @param string $division
     * @param array $codes
     * @param array $conditionCross
     * @param array $companyIds
     * @param array $productIds
     * @param array $cmIds
     * @param array $channels
     * @param bool $conv15SecFlag
     * @param array $progIds
     * @param bool $straddlingFlg
     * @param array $dataType
     * @param array $reachAndFrequencyGroupingUnit
     * @param array $dataTypeFlags
     * @param array $cvBindings
     * @param array $viewBindings
     * @param array $bindings
     */
    public function getChartResultsRtTs(String $startDate, String $endDate, String $startTime, String $endTime, String $cmType, String $cmSeconds, int $regionId, String $division, array $codes, array $conditionCross, array $companyIds, array $productIds, array $cmIds, array $channels, bool $conv15SecFlag, array $progIds, bool $straddlingFlg, array $dataType, array $reachAndFrequencyGroupingUnit, array $dataTypeFlags, array $cvBindings, array $viewBindings, array $bindings): void
    {
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->once()->ordered();
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('insertTemporaryTable')
            ->withArgs($this->bindAsserts($cvBindings, 'cvBindings'))
            ->once()->ordered();
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->once()->ordered();
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('insertTemporaryTable')
            ->withArgs($this->bindAsserts($cvBindings, 'cvBindings'))
            ->once()->ordered();
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->once()->ordered();
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('insertTemporaryTable')
            ->once()->ordered();
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($viewBindings, 'viewBindings'))
            ->once()->ordered();
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($viewBindings, 'viewBindings'))
            ->once()->ordered();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($bindings, 'bindings'))
            ->once()->ordered();

        $actual = $this->target->getChartResults($startDate, $endDate, $startTime, $endTime, $cmType, $cmSeconds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $conv15SecFlag, $progIds, $straddlingFlg, $dataType, $reachAndFrequencyGroupingUnit, $dataTypeFlags);
    }

    public function getChartResultsRtTsDataProvider()
    {
        $cases = ['case1'];
        $startDate = ['19900614'];
        $endDate = ['19900615'];
        $startTime = ['050000'];
        $endTime = ['045959'];
        $cmType = ['1'];
        $cmSeconds = ['2'];
        $regionId = [1];
        $division = ['ga8'];
        $codes = [['personal']];
        $conditionCross = [[]];
        $companyIds = [[1, 2, 3]];
        $productIds = [[10, 11, 12]];
        $cmIds = [['20', '21', '22']];
        $channels = [[1, 2, 3]];
        $conv15SecFlag = [false];
        $progIds = [['30', '31', '32']];
        $straddlingFlg = [true];
        $dataType = [[]];
        $reachAndFrequencyGroupingUnit = [[3, 6, 8]];
        $dataTypeFlags = [
            ['isRt' => true, 'isTs' => true, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
        ];
        $cvBindings = [[
            ':companyIds0' => 1,
            ':companyIds1' => 2,
            ':companyIds2' => 3,
            ':startDate' => '19900614',
            ':endDate' => '19900615',
            ':progStartDate' => '199006130000000000',
            ':progEndDate' => '199006169999999999',
        ]];
        $viewBindings = [[
            ':conv15SecFlag' => 15,
        ]];
        $bindings = [[
            ':personal' => 'personal',
        ]];

        foreach ($cases as $i => $case) {
            yield $case => [
                $startDate[$i],
                $endDate[$i],
                $startTime[$i],
                $endTime[$i],
                $cmType[$i],
                $cmSeconds[$i],
                $regionId[$i],
                $division[$i],
                $codes[$i],
                $conditionCross[$i],
                $companyIds[$i],
                $productIds[$i],
                $cmIds[$i],
                $channels[$i],
                $conv15SecFlag[$i],
                $progIds[$i],
                $straddlingFlg[$i],
                $dataType[$i],
                $reachAndFrequencyGroupingUnit[$i],
                $dataTypeFlags[$i],
                $cvBindings[$i],
                $viewBindings[$i],
                $bindings[$i],
            ];
        }
    }

    /**
     * @test
     */
    public function getProductNames(): void
    {
        $expected = [];

        $companyId = '1';
        $productIds = [1, 2, 3];
        $bindings = [
            ':productIds0' => 1,
            ':productIds1' => 2,
            ':productIds2' => 3,
            ':companyId' => 1,
        ];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($bindings, 'bindings'))
            ->andReturn($expected)
            ->once();

        $actual = $this->target->getProductNames($companyId, $productIds);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider getCsvButtonInfoDataProvider
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param string $cmType
     * @param string $cmSeconds
     * @param int $regionId
     * @param string $division
     * @param ?array $codes
     * @param array $conditionCross
     * @param array $companyIds
     * @param ?array $productIds
     * @param ?array $cmIds
     * @param array $channels
     * @param ?bool $conv15SecFlag
     * @param ?array $progIds
     * @param bool $straddlingFlg
     * @param array $dataType
     * @param string $axisType
     * @param ?string $channelAxis
     * @param string $axisTypeProduct
     * @param string $axisTypeCompany
     * @param array $bindings
     */
    public function getCsvButtonInfo(String $startDate, String $endDate, String $startTime, String $endTime, String $cmType, String $cmSeconds, int $regionId, String $division, ?array $codes, array $conditionCross, array $companyIds, ?array $productIds, ?array $cmIds, array $channels, ?bool $conv15SecFlag, ?array $progIds, bool $straddlingFlg, array $dataType, string $axisType, ?string $channelAxis, string $axisTypeProduct, string $axisTypeCompany, array $bindings): void
    {
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($bindings, 'bindings'))
            ->once();

        $actual = $this->target->getCsvButtonInfo($startDate, $endDate, $startTime, $endTime, $cmType, $cmSeconds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $conv15SecFlag, $progIds, $straddlingFlg, $dataType, $axisType, $channelAxis, $axisTypeProduct, $axisTypeCompany);
    }

    public function getCsvButtonInfoDataProvider()
    {
        $cases = ['case1', 'case2', 'case3', 'case4'];
        $startDate = ['19900614', '19900614', '19900614', '19900614'];
        $endDate = ['19900615', '19900615', '19900615', '19900615'];
        $startTime = ['050000', '100000', '120000', '120000'];
        $endTime = ['045959', '045959', '200000', '200000'];
        $cmType = ['1', '2', '', ''];
        $cmSeconds = ['2', '3', '', ''];
        $regionId = [1, 2, 1, 1];
        $division = ['ga8', 'ga8', 'ga8', 'ga8'];
        $codes = [['personal'], ['personal'], ['personal'], ['personal']];
        $conditionCross = [[], [], [], []];
        $companyIds = [[1, 2, 3], [1, 2, 3], [1, 2, 3], [1, 2, 3]];
        $productIds = [[10, 11, 12], [], [], [10, 11, 12]];
        $cmIds = [['20', '21', '22'], [], [], []];
        $channels = [[1, 2, 3], [], [], []];
        $conv15SecFlag = [null, true, false, false];
        $progIds = [['30', '31', '32'], [], [], []];
        $straddlingFlg = [true, true, false, false];
        $dataType = [[], [], [], []];
        $axisType = ['1', '2', '', '2'];
        $channelAxis = ['1', '0', '1', '1'];
        $axisTypeProduct = ['2', '2', '2', '2'];
        $axisTypeCompany = ['1', '1', '1', '1'];
        $bindings = [[
            ':startDate' => '19900614',
            ':endDate' => '19900615',
            ':channels0' => 1,
            ':channels1' => 2,
            ':channels2' => 3,
            ':companyIds0' => 1,
            ':companyIds1' => 2,
            ':companyIds2' => 3,
            ':productIds0' => 10,
            ':productIds1' => 11,
            ':productIds2' => 12,
            ':cmIds0' => '20',
            ':cmIds1' => '21',
            ':cmIds2' => '22',
            ':progIds0' => '30',
            ':progIds1' => '31',
            ':progIds2' => '32',
            ':regionId' => 1,
        ], [
            ':startDate' => '19900614',
            ':endDate' => '19900615',
            ':companyIds0' => 1,
            ':companyIds1' => 2,
            ':companyIds2' => 3,
            ':startTime' => '100000',
            ':endTime' => '045959',
            ':regionId' => 2,
        ], [
            ':startDate' => '19900614',
            ':endDate' => '19900615',
            ':companyIds0' => 1,
            ':companyIds1' => 2,
            ':companyIds2' => 3,
            ':startTime' => '120000',
            ':endTime' => '200000',
            ':regionId' => 1,
        ], [
            ':startDate' => '19900614',
            ':endDate' => '19900615',
            ':companyIds0' => 1,
            ':companyIds1' => 2,
            ':companyIds2' => 3,
            ':productIds0' => 10,
            ':productIds1' => 11,
            ':productIds2' => 12,
            ':startTime' => '120000',
            ':endTime' => '200000',
            ':regionId' => 1,
        ]];

        foreach ($cases as $i => $case) {
            yield $case => [
                $startDate[$i],
                $endDate[$i],
                $startTime[$i],
                $endTime[$i],
                $cmType[$i],
                $cmSeconds[$i],
                $regionId[$i],
                $division[$i],
                $codes[$i],
                $conditionCross[$i],
                $companyIds[$i],
                $productIds[$i],
                $cmIds[$i],
                $channels[$i],
                $conv15SecFlag[$i],
                $progIds[$i],
                $straddlingFlg[$i],
                $dataType[$i],
                $axisType[$i],
                $channelAxis[$i],
                $axisTypeProduct[$i],
                $axisTypeCompany[$i],
                $bindings[$i],
            ];
        }
    }

    /**
     * @test
     * @dataProvider getCsvButtonInfoEmptyDataProvider
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param string $cmType
     * @param string $cmSeconds
     * @param int $regionId
     * @param string $division
     * @param ?array $codes
     * @param array $conditionCross
     * @param array $companyIds
     * @param ?array $productIds
     * @param ?array $cmIds
     * @param array $channels
     * @param ?bool $conv15SecFlag
     * @param ?array $progIds
     * @param bool $straddlingFlg
     * @param array $dataType
     * @param string $axisType
     * @param ?string $channelAxis
     * @param string $axisTypeProduct
     * @param string $axisTypeCompany
     */
    public function getCsvButtonInfoEmptyData(String $startDate, String $endDate, String $startTime, String $endTime, String $cmType, String $cmSeconds, int $regionId, String $division, ?array $codes, array $conditionCross, array $companyIds, ?array $productIds, ?array $cmIds, array $channels, ?bool $conv15SecFlag, ?array $progIds, bool $straddlingFlg, array $dataType, string $axisType, ?string $channelAxis, string $axisTypeProduct, string $axisTypeCompany): void
    {
        $expect = [];
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldNotReceive('select')
            ->once();

        $actual = $this->target->getCsvButtonInfo($startDate, $endDate, $startTime, $endTime, $cmType, $cmSeconds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $conv15SecFlag, $progIds, $straddlingFlg, $dataType, $axisType, $channelAxis, $axisTypeProduct, $axisTypeCompany);
        $this->assertEquals($expect, $actual);
    }

    public function getCsvButtonInfoEmptyDataProvider()
    {
        $cases = ['case1'];
        $startDate = ['19900614'];
        $endDate = ['19900615'];
        $startTime = ['050000'];
        $endTime = ['045959'];
        $cmType = ['1'];
        $cmSeconds = ['2'];
        $regionId = [1];
        $division = ['ga8'];
        $codes = [['personal']];
        $conditionCross = [[]];
        $companyIds = [[1, 2, 3]];
        $productIds = [[10, 11, 12]];
        $cmIds = [['20', '21', '22']];
        $channels = [[1, 2, 3]];
        $conv15SecFlag = [null];
        $progIds = [['30', '31', '32']];
        $straddlingFlg = [true];
        $dataType = [[]];
        $axisType = [''];
        $channelAxis = [''];
        $axisTypeProduct = ['2'];
        $axisTypeCompany = ['1'];

        foreach ($cases as $i => $case) {
            yield $case => [
                $startDate[$i],
                $endDate[$i],
                $startTime[$i],
                $endTime[$i],
                $cmType[$i],
                $cmSeconds[$i],
                $regionId[$i],
                $division[$i],
                $codes[$i],
                $conditionCross[$i],
                $companyIds[$i],
                $productIds[$i],
                $cmIds[$i],
                $channels[$i],
                $conv15SecFlag[$i],
                $progIds[$i],
                $straddlingFlg[$i],
                $dataType[$i],
                $axisType[$i],
                $channelAxis[$i],
                $axisTypeProduct[$i],
                $axisTypeCompany[$i],
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
