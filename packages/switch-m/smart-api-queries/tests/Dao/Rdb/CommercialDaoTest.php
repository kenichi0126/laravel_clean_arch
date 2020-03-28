<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Rdb;

use Mockery;
use Switchm\SmartApi\Queries\Dao\Rdb\CommercialDao;
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
     * @dataProvider searchListDataProvider
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
     * @param array $expectBinds
     * @param array $expectSelectOneBinds
     */
    public function searchList(String $startDate, String $endDate, String $startTime, String $endTime, ?String $cmType, ?String $cmSeconds, ?array $progIds, int $regionId, String $division, ?array $codes, ?array $conditionCross, ?array $companyIds, ?array $productIds, ?array $cmIds, array $channels, ?array $order, int $page, ?int $length, ?bool $conv_15_sec_flag, bool $straddlingFlg, ?array $codeList, string $csvFlag, array $dataType, bool $cmMaterialFlag, bool $cmTypeFlag, string $codeNumber, string $sampleCodePrefix, string $sampleCodeNumberPrefix, string $selectedPersonalName, array $dataTypes, array $dataTypeFlags, array $expectBinds, array $expectSelectOneBinds): void
    {
        $expected = ['list' => [], 'cnt' => 1];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('quote')
            ->with(Mockery::any());

        // expects
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createCommercialListWhere')
            ->with($startDate, $endDate, $cmType, $cmSeconds, $progIds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $straddlingFlg)
            ->andReturn([[10], [20], [30]])
            ->once();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createCrossJoinWhereClause')
            ->with($division, Mockery::any(), Mockery::any())
            ->once();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($expectBinds, 'select'))
            ->andReturn([])
            ->once();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->withArgs($this->bindAsserts($expectSelectOneBinds, 'selectOne'))
            ->andReturn((object) ['cnt' => 1])
            ->once();

        $actual = $this->target->searchList($startDate, $endDate, $startTime, $endTime, $cmType, $cmSeconds, $progIds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $order, $page, $length, $conv_15_sec_flag, $straddlingFlg, $codeList, $csvFlag, $dataType, $cmMaterialFlag, $cmTypeFlag, $codeNumber, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName, $dataTypes, $dataTypeFlags);

        $this->assertEquals($expected, $actual);
    }

    public function searchListDataProvider()
    {
        $cases = ['case1', 'case2', 'case3'];
        $startDate = ['19900614', '19900614', '19900614'];
        $endDate = ['19900615', '19900615', '19900615'];
        $startTime = ['050000', '100000', '120000'];
        $endTime = ['045959', '045959', '200000'];
        $cmType = ['1', '2', ''];
        $cmSeconds = ['2', '3', ''];
        $progIds = [[10], [10], [10], [10]];
        $regionId = [1, 2, 1];
        $division = ['ga8', 'ga8', 'ga8'];
        $codes = [['personal'], ['personal', 'c1', 'c2', 'household'], ['household']];
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
        $dataType = [[1], [1, 2, 3, 4, 5], [1]];
        $cmMaterialFlag = [true, false, true];
        $cmTypeFlag = [true, false, true];
        $codeNumber = ['', '', ''];
        $sampleCodePrefix = ['', '', ''];
        $sampleCodeNumberPrefix = ['', '', ''];
        $selectedPersonalName = ['', '', ''];
        $dataTypes = [['rt' => 1, 'ts' => 2, 'gross' => 3, 'total' => 4, 'rtTotal' => 5], ['rt' => 1, 'ts' => 2, 'gross' => 3, 'total' => 4, 'rtTotal' => 5], ['rt' => 1, 'ts' => 2, 'gross' => 3, 'total' => 4, 'rtTotal' => 5]];
        $dataTypeFlags = [[], [], []];
        $expectBinds = [
            [
                ':division' => 'ga8',
                ':codes0' => 'personal',
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
                ':time_box_ids0' => 10,
            ],
            [
                ':conv15SecFlag' => 15,
                ':division' => 'ga8',
                ':codes0' => 'personal',
                ':codes1' => 'c1',
                ':codes2' => 'c2',
                ':codes3' => 'household',
                ':startDate' => '19900614',
                ':endDate' => '19900615',
                ':progIds0' => '10',
                ':region_id' => 2,
                ':companyIds0' => 1,
                ':companyIds1' => 2,
                ':companyIds2' => 3,
                ':startTime' => '100000',
                ':endTime' => '045959',
                ':time_box_ids0' => 10,
                ':union_ga8_c1' => 'c1',
                ':union_ga8_c2' => 'c2',
            ],
            [
                ':conv15SecFlag' => 1,
                ':division' => 'ga8',
                ':codes0' => 'household',
                ':startDate' => '19900614',
                ':endDate' => '19900615',
                ':progIds0' => '10',
                ':region_id' => 1,
                ':companyIds0' => 1,
                ':companyIds1' => 2,
                ':companyIds2' => 3,
                ':startTime' => '120000',
                ':endTime' => '200000',
                ':time_box_ids0' => 10,
            ],
        ];
        $expectSelectOneBinds = [
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
                $dataType[$i],
                $cmMaterialFlag[$i],
                $cmTypeFlag[$i],
                $codeNumber[$i],
                $sampleCodePrefix[$i],
                $sampleCodeNumberPrefix[$i],
                $selectedPersonalName[$i],
                $dataTypes[$i],
                $dataTypeFlags[$i],
                $expectBinds[$i],
                $expectSelectOneBinds[$i],
            ];
        }
    }

    /**
     * @test
     * @dataProvider searchGrpDataProvider
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
     * @param array $expectBinds
     * @param string $period
     * @param ?string $allChannels
     */
    public function searchGrp(string $startDate, string $endDate, string $startTime, string $endTime, ?string $cmType, ?string $cmSeconds, ?array $progIds, int $regionId, string $division, ?array $codes, ?array $conditionCross, ?array $companyIds, ?array $productIds, ?array $cmIds, array $channels, ?bool $conv_15_sec_flag, string $period, ?string $allChannels, bool $straddlingFlg, int $length, int $page, string $csvFlag, array $dataType, string $codeNumber, string $sampleCodePrefix, string $sampleCodeNumberPrefix, string $selectedPersonalName, array $dataTypes, array $dataTypeFlags, array $expectBinds): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('quote')
            ->with(Mockery::any());

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createCommercialListWhere')
            ->with($startDate, $endDate, $cmType, $cmSeconds, $progIds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $straddlingFlg)
            ->andReturn([[10], [20], [30]])
            ->once();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createTimeBoxCaseClause')
            ->with($startDate, $endDate, $regionId, 'cr.started_at')
            ->once();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createTimeBoxCaseClause')
            ->with($startDate, $endDate, $regionId, 'tcr.started_at')
            ->once();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createCrossJoinWhereClause')
            ->with($division, Mockery::any(), Mockery::any())
            ->once();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($expectBinds, 'select'))
            ->andReturn([])
            ->once();

        $actual = $this->target->searchGrp($startDate, $endDate, $startTime, $endTime, $cmType, $cmSeconds, $progIds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $conv_15_sec_flag, $period, $allChannels, $straddlingFlg, $length, $page, $csvFlag, $dataType, $codeNumber, $sampleCodePrefix, $sampleCodeNumberPrefix, $selectedPersonalName, $dataTypes, $dataTypeFlags);

        $this->assertEquals($expected, $actual);
    }

    public function searchGrpDataProvider()
    {
        $cases = ['case1', 'case2', 'case3', 'case4'];
        $startDate = ['19900614', '19900614', '19900614', '19900614'];
        $endDate = ['19900615', '19900615', '19900615', '19900615'];
        $startTime = ['050000', '100000', '120000', '120000'];
        $endTime = ['045959', '045959', '200000', '200000'];
        $cmType = ['1', '2', '', ''];
        $cmSeconds = ['2', '3', '', ''];
        $progIds = [[10], [10], [10], [10], [10]];
        $regionId = [1, 2, 1, 1];
        $division = ['ga8', 'ga8', 'ga8', 'ga8'];
        $codes = [['personal'], ['personal', 'c1', 'c2', 'household'], ['household'], ['household']];
        $conditionCross = [[], [], [], []];
        $companyIds = [[1, 2, 3], [1, 2, 3], [1, 2, 3], [1, 2, 3]];
        $productIds = [[10, 11, 12], [], [], []];
        $cmIds = [['20', '21', '22'], [], [], []];
        $channels = [[1, 2, 3], [], [], []];
        $conv15SecFlag = [null, false, true, true];
        $period = ['period', 'day', 'week', 'month'];
        $allChannels = ['true', 'false', 'true', 'true'];
        $straddlingFlg = [true, true, false, false];
        $length = [30, 30, 30, 30];
        $page = [1, 2, 3, 4];
        $csvFlag = ['0', '0', '1', '1'];
        $dataType = [[1], [1, 2, 3, 4, 5], [1], [1]];
        $codeNumber = ['', '', '', ''];
        $sampleCodePrefix = ['', '', '', ''];
        $sampleCodeNumberPrefix = ['', '', '', ''];
        $selectedPersonalName = ['', '', '', ''];
        $dataTypes = [['rt' => 1, 'ts' => 2, 'gross' => 3, 'total' => 4, 'rtTotal' => 5], ['rt' => 1, 'ts' => 2, 'gross' => 3, 'total' => 4, 'rtTotal' => 5], ['rt' => 1, 'ts' => 2, 'gross' => 3, 'total' => 4, 'rtTotal' => 5], ['rt' => 1, 'ts' => 2, 'gross' => 3, 'total' => 4, 'rtTotal' => 5]];
        $dataTypeFlags = [[], [], [], []];
        $expectBinds = [
            [
                ':time_box_ids0' => 10,
                ':division' => 'ga8',
                ':codes0' => 'personal',
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
                ':from' => 1,
                ':to' => 30,
            ],
            [
                ':time_box_ids0' => 10,
                ':conv15SecFlag' => 15,
                ':division' => 'ga8',
                ':codes0' => 'personal',
                ':codes1' => 'c1',
                ':codes2' => 'c2',
                ':codes3' => 'household',
                ':startDate' => '19900614',
                ':endDate' => '19900615',
                ':progIds0' => '10',
                ':region_id' => 2,
                ':companyIds0' => 1,
                ':companyIds1' => 2,
                ':companyIds2' => 3,
                ':startTime' => '100000',
                ':endTime' => '045959',
                ':union_ga8_c1' => 'c1',
                ':union_ga8_c2' => 'c2',
                ':from' => 31,
                ':to' => 60,
            ],
            [
                ':time_box_ids0' => 10,
                ':conv15SecFlag' => 1,
                ':division' => 'ga8',
                ':codes0' => 'household',
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
                ':time_box_ids0' => 10,
                ':conv15SecFlag' => 1,
                ':division' => 'ga8',
                ':codes0' => 'household',
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
                $dataType[$i],
                $codeNumber[$i],
                $sampleCodePrefix[$i],
                $sampleCodeNumberPrefix[$i],
                $selectedPersonalName[$i],
                $dataTypes[$i],
                $dataTypeFlags[$i],
                $expectBinds[$i],
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
