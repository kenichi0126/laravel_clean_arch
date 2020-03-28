<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Dwh;

use Mockery;
use Switchm\SmartApi\Queries\Dao\Dwh\RankingDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class RankingDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = Mockery::mock(RankingDao::class, [])->makePartial();
    }

    /**
     * @test
     * @dataProvider searchCommercialDataProvider
     * @param string $startDate
     * @param string $endDate
     * @param string $startTime
     * @param string $endTime
     * @param null|array $wdays
     * @param bool $holiday
     * @param null|string $cmType
     * @param int $regionId
     * @param string $division
     * @param null|array $codes
     * @param null|array $conditionCross
     * @param array $channels
     * @param null|array $order
     * @param null|bool $conv_15_sec_flag
     * @param string $period
     * @param bool $straddlingFlg
     * @param int $length
     * @param int $page
     * @param string $csvFlag
     * @param array $dataType
     * @param null|array $cmLargeGenres
     * @param string $axisType
     * @param array $exclusionCompanyIds
     * @param string $axisTypeCompany
     * @param string $axisTypeProduct
     * @param \stdClass $selectOne
     * @param array $expectBinds
     */
    public function searchCommercial(String $startDate, String $endDate, String $startTime, String $endTime, ?array $wdays, bool $holiday, ?String $cmType, int $regionId, String $division, ?array $codes, ?array $conditionCross, array $channels, ?array $order, ?bool $conv_15_sec_flag, string $period, bool $straddlingFlg, int $length, int $page, string $csvFlag, array $dataType, ?array $cmLargeGenres, string $axisType, array $exclusionCompanyIds, string $axisTypeCompany, string $axisTypeProduct, \stdClass $selectOne, array $expectBinds): void
    {
        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createTimeBoxCaseClause')
            ->with($startDate, $endDate, $regionId, 'cr.started_at')
            ->andReturns('')
            ->once()->ordered();
        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createTimeBoxCaseClause')
            ->with($startDate, $endDate, $regionId, 'tcr.started_at')
            ->andReturns('')
            ->once()->ordered();
        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createTimeBoxCaseClause')
            ->with($startDate, $endDate, $regionId, 'cr.started_at')
            ->andReturns('')
            ->once()->ordered();

        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($expectBinds, 'select'))
            ->once()->ordered();
        // common
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->withArgs($this->bindAsserts($expectBinds, 'selectOne'))
            ->andReturn($selectOne)
            ->once()->ordered();

        $actual = $this->target->searchCommercial($startDate, $endDate, $startTime, $endTime, $wdays, $holiday, $cmType, $regionId, $division, $codes, $conditionCross, $channels, $order, $conv_15_sec_flag, $period, $straddlingFlg, $length, $page, $csvFlag, $dataType, $cmLargeGenres, $axisType, $exclusionCompanyIds, $axisTypeCompany, $axisTypeProduct);
    }

    public function searchCommercialDataProvider()
    {
        $cases = ['case1', 'case2', 'case3', 'case4', 'case5', 'case6', 'case7'];
        $startDate = ['19900614', '19900614', '19900614', '19900614', '19900614', '19900614', '19900614'];
        $endDate = ['19900615', '19900615', '19900615', '19900615', '19900615', '19900615', '19900615'];
        $startTime = ['050000', '100000', '120000', '120000', '050000', '100000', '120000'];
        $endTime = ['045959', '045959', '200000', '200000', '045959', '045959', '200000'];
        $wdays = [[1], [1], [1], [1], [1], [1], [1]];
        $holiday = [true, false, true, false, true, false, true];
        $cmType = ['1', '2', '', '', '1', '2', ''];
        $regionId = [1, 2, 1, 2, 1, 2, 1];
        $division = ['ga8', 'ga8', 'ga8', 'condition_cross', 'ga8', 'ga8', 'ga8'];
        $codes = [['personal'], ['personal', 'c1'], ['personal'], [], ['household'], ['household'], ['household']];
        $conditionCross = [[], [], [], [], [], [], []];
        $channels = [[1, 2, 3], [], [], [], [1, 2, 3], [], []];
        $order = [[['column' => 'rank', 'dir' => 'asc']], [], [], [], [], [], []];
        $conv15SecFlag = [null, true, false, false, null, true, false];
        $period = ['', '', '', '', '', '', ''];
        $straddlingFlg = [true, true, false, false, true, true, false];
        $length = [30, 1000, 30, 30, 30, 30, 30];
        $page = [0, 1, 2, 3, 4, 5, 6];
        $csvFlag = ['1', '0', '1', '0', '1', '0', '1'];
        $dataType = [[], [], [], [], [], [], []];
        $cmLargeGenres = [[], [3], [], [3], [], [3], []];
        $axisType = ['1', '2', '', '', '1', '2', ''];
        $exclusionCompanyIds = [[71], [71], [71], [71], [71], [71], [71]];
        $axisTypeProduct = ['2', '2', '2', '2', '2', '2', '2'];
        $axisTypeCompany = ['1', '1', '1', '1', '1', '1', '1'];
        $selectOne = [(object) [], (object) [], (object) [], (object) [], (object) [], (object) [], (object) ['cnt' => '9999']];
        $expectBinds = [
            [
                ':division' => 'ga8',
                ':codes0' => 'personal',
                ':startDate' => '19900614',
                ':endDate' => '19900615',
                ':channels0' => 1,
                ':channels1' => 2,
                ':channels2' => 3,
                ':region_id' => 1,
                ':startTimestamp' => '1990-06-13 00:00:00',
                ':endTimestamp' => '1990-06-17 00:00:00',
                ':exclusionCompanyIds0' => 71,
                ':wdays0' => 1,
            ],
            [
                ':conv15SecFlag' => 1,
                ':division' => 'ga8',
                ':codes0' => 'personal',
                ':codes1' => 'c1',
                ':startDate' => '19900614',
                ':endDate' => '19900615',
                ':region_id' => 2,
                ':startTime' => '100000',
                ':endTime' => '045959',
                ':startTimestamp' => '1990-06-13 00:00:00',
                ':endTimestamp' => '1990-06-17 00:00:00',
                ':cmLargeGenres0' => 3,
                ':exclusionCompanyIds0' => 71,
                ':wdays0' => 1,
            ],
            [
                ':conv15SecFlag' => 15,
                ':division' => 'ga8',
                ':codes0' => 'personal',
                ':startDate' => '19900614',
                ':endDate' => '19900615',
                ':region_id' => 1,
                ':startTime' => '120000',
                ':endTime' => '200000',
                ':startTimestamp' => '1990-06-13 00:00:00',
                ':endTimestamp' => '1990-06-17 00:00:00',
                ':exclusionCompanyIds0' => 71,
                ':wdays0' => 1,
            ],
            [
                ':conv15SecFlag' => 15,
                ':startDate' => '19900614',
                ':endDate' => '19900615',
                ':region_id' => 2,
                ':startTime' => '120000',
                ':endTime' => '200000',
                ':startTimestamp' => '1990-06-13 00:00:00',
                ':endTimestamp' => '1990-06-17 00:00:00',
                ':cmLargeGenres0' => 3,
                ':exclusionCompanyIds0' => 71,
                ':wdays0' => 1,
            ],
            [
                ':division' => 'ga8',
                ':codes0' => 'household',
                ':startDate' => '19900614',
                ':endDate' => '19900615',
                ':channels0' => 1,
                ':channels1' => 2,
                ':channels2' => 3,
                ':region_id' => 1,
                ':startTimestamp' => '1990-06-13 00:00:00',
                ':endTimestamp' => '1990-06-17 00:00:00',
                ':exclusionCompanyIds0' => 71,
                ':wdays0' => 1,
            ],
            [
                ':conv15SecFlag' => 1,
                ':division' => 'ga8',
                ':codes0' => 'household',
                ':startDate' => '19900614',
                ':endDate' => '19900615',
                ':region_id' => 2,
                ':startTime' => '100000',
                ':endTime' => '045959',
                ':startTimestamp' => '1990-06-13 00:00:00',
                ':endTimestamp' => '1990-06-17 00:00:00',
                ':cmLargeGenres0' => 3,
                ':exclusionCompanyIds0' => 71,
                ':wdays0' => 1,
            ],
            [
                ':conv15SecFlag' => 15,
                ':division' => 'ga8',
                ':codes0' => 'household',
                ':startDate' => '19900614',
                ':endDate' => '19900615',
                ':region_id' => 1,
                ':startTime' => '120000',
                ':endTime' => '200000',
                ':startTimestamp' => '1990-06-13 00:00:00',
                ':endTimestamp' => '1990-06-17 00:00:00',
                ':exclusionCompanyIds0' => 71,
                ':wdays0' => 1,
            ],
        ];

        foreach ($cases as $i => $case) {
            yield $case => [
                $startDate[$i],
                $endDate[$i],
                $startTime[$i],
                $endTime[$i],
                $wdays[$i],
                $holiday[$i],
                $cmType[$i],
                $regionId[$i],
                $division[$i],
                $codes[$i],
                $conditionCross[$i],
                $channels[$i],
                $order[$i],
                $conv15SecFlag[$i],
                $period[$i],
                $straddlingFlg[$i],
                $length[$i],
                $page[$i],
                $csvFlag[$i],
                $dataType[$i],
                $cmLargeGenres[$i],
                $axisType[$i],
                $exclusionCompanyIds[$i],
                $axisTypeProduct[$i],
                $axisTypeCompany[$i],
                $selectOne[$i],
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
