<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Rdb;

use Mockery;
use Switchm\SmartApi\Queries\Dao\Rdb\RafDao;
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
     * @dataProvider getProductNumbersDataProvider
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
     * @param ?int $conv15SecFlag
     * @param ?array $progIds
     * @param bool $straddlingFlg
     * @param array $dataType
     * @param array $expectBinds
     */
    public function getProductNumbers(String $startDate, String $endDate, String $startTime, String $endTime, String $cmType, String $cmSeconds, int $regionId, String $division, ?array $codes, array $conditionCross, array $companyIds, ?array $productIds, ?array $cmIds, array $channels, ?bool $conv15SecFlag, ?array $progIds, bool $straddlingFlg, array $dataType, array $expectBinds): void
    {
        $expected = (object) [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->withArgs($this->bindAsserts($expectBinds, 'select'))
            ->andReturn($expected)
            ->once();

        $actual = $this->target->getProductNumbers($startDate, $endDate, $startTime, $endTime, $cmType, $cmSeconds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $conv15SecFlag, $progIds, $straddlingFlg, $dataType);

        $this->assertEquals($expected, $actual);
    }

    public function getProductNumbersDataProvider()
    {
        $cases = ['case1', 'case2', 'case3'];
        $startDate = ['19900614', '19900614', '19900614'];
        $endDate = ['19900615', '19900615', '19900615'];
        $startTime = ['050000', '100000', '120000'];
        $endTime = ['045959', '045959', '200000'];
        $cmType = ['1', '2', ''];
        $cmSeconds = ['2', '3', ''];
        $regionId = [1, 2, 1];
        $division = ['ga8', 'ga8', 'ga8'];
        $codes = [['personal'], ['personal'], ['personal']];
        $conditionCross = [[], [], []];
        $companyIds = [[1, 2, 3], [1, 2, 3], [1, 2, 3]];
        $productIds = [[10, 11, 12], [], []];
        $cmIds = [['20', '21', '22'], [], []];
        $channels = [[1, 2, 3], [], []];
        $conv15SecFlag = [null, true, false];
        $progIds = [['30', '31', '32'], [], []];
        $straddlingFlg = [true, true, false];
        $dataType = [[], [], []];
        $expectBinds = [[
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

        foreach ($cases as $i => $case) {
            yield [
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
