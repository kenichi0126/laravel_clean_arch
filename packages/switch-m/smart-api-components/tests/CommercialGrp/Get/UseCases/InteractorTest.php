<?php

namespace Switchm\SmartApi\Components\Tests\CommercialGrp\UseCases;

use Prophecy\Argument as arg;
use ReflectionException;
use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Components\CommercialGrp\Get\UseCases\InputData;
use Switchm\SmartApi\Components\CommercialGrp\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\CommercialGrp\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\CommercialGrp\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Common\Exceptions\SampleCountException;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Queries\Dao\Dwh\CommercialDao;
use Switchm\SmartApi\Queries\Services\DivisionService;
use Switchm\SmartApi\Queries\Services\ProductService;
use Switchm\SmartApi\Queries\Services\SampleService;

class InteractorTest extends TestCase
{
    private $commercialDao;

    private $rdbCommercialDao;

    private $divisionService;

    private $productService;

    private $sampleService;

    private $presenter;

    private $searchConditionTextAppService;

    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->commercialDao = $this->prophesize(CommercialDao::class);
        $this->rdbCommercialDao = $this->prophesize(\Switchm\SmartApi\Queries\Dao\Rdb\CommercialDao::class);
        $this->divisionService = $this->prophesize(DivisionService::class);
        $this->productService = $this->prophesize(ProductService::class);
        $this->sampleService = $this->prophesize(SampleService::class);
        $this->presenter = $this->prophesize(OutputBoundary::class);
        $this->searchConditionTextAppService = $this->prophesize(SearchConditionTextAppService::class);

        $this->target = new Interactor(
            $this->commercialDao->reveal(),
            $this->rdbCommercialDao->reveal(),
            $this->divisionService->reveal(),
            $this->productService->reveal(),
            $this->sampleService->reveal(),
            $this->presenter->reveal(),
            $this->searchConditionTextAppService->reveal()
        );
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function getConditionCrossCount_divisionがcondition_crossではない場合(): void
    {
        $this->sampleService
            ->getConditionCrossCount(arg::cetera())
            ->willReturn()
            ->shouldNotBeCalled();

        $reflection = new \ReflectionClass($this->target);

        $method = $reflection->getMethod('getConditionCrossCount');

        $method->setAccessible(true);

        $expected = [0, 0];

        $division = 'ga8';
        $conditionCross = [];
        $startDate = '2019-01-01';
        $endDate = '2019-01-07';
        $regionId = 1;
        $sampleCountMaxNumber = 50;
        $dataTypeFlags = ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false];

        $actual = $method->invoke(
            $this->target,
            $division,
            $conditionCross,
            $startDate,
            $endDate,
            $regionId,
            $sampleCountMaxNumber,
            $dataTypeFlags
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @dataProvider notExceptionGetConditionCrossCountDataProvider
     * @param $providedDataTypeFlags
     * @param $providedExpected
     * @throws ReflectionException
     */
    public function getConditionCrossCount_divisionがcondition_crossかつcntが50以上の場合($providedDataTypeFlags, $providedExpected): void
    {
        $this->sampleService
            ->getConditionCrossCount(arg::cetera())
            ->willReturn(50)
            ->shouldBeCalled();

        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('getConditionCrossCount');
        $method->setAccessible(true);

        $division = 'condition_cross';
        $conditionCross = [];
        $startDate = '2019-01-01';
        $endDate = '2019-01-07';
        $regionId = 1;
        $sampleCountMaxNumber = 50;
        $dataTypeFlags = $providedDataTypeFlags;

        $expected = $providedExpected;

        $actual = $method->invoke(
            $this->target,
            $division,
            $conditionCross,
            $startDate,
            $endDate,
            $regionId,
            $sampleCountMaxNumber,
            $dataTypeFlags
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array
     */
    public function notExceptionGetConditionCrossCountDataProvider()
    {
        return [
            'リアルタイム' => [['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false], [50, 0]],
            'タイムシフト' => [['isRt' => false, 'isTs' => true, 'isGross' => true, 'isTotal' => true, 'isRtTotal' => true], [0, 50]],
        ];
    }

    /**
     * @test
     * @dataProvider exceptionGetConditionCrossCountDataProvider
     * @param $providedDataTypeFlags
     * @throws ReflectionException
     */
    public function getConditionCrossCount_divisionがcondition_crossかつかつcntが50未満($providedDataTypeFlags): void
    {
        $this->expectException(SampleCountException::class);

        $this->sampleService
            ->getConditionCrossCount(arg::cetera())
            ->willReturn(49)
            ->shouldBeCalled();

        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('getConditionCrossCount');
        $method->setAccessible(true);

        $division = 'condition_cross';
        $conditionCross = [];
        $startDate = '2019-01-01';
        $endDate = '2019-01-07';
        $regionId = 1;
        $sampleCountMaxNumber = 50;
        $dataTypeFlags = $providedDataTypeFlags;

        $method->invoke(
            $this->target,
            $division,
            $conditionCross,
            $startDate,
            $endDate,
            $regionId,
            $sampleCountMaxNumber,
            $dataTypeFlags
        );
    }

    /**
     * @return array
     */
    public function exceptionGetConditionCrossCountDataProvider()
    {
        return [
            'リアルタイム' => [['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false]],
            'タイムシフト' => [['isRt' => false, 'isTs' => true, 'isGross' => true, 'isTotal' => true, 'isRtTotal' => true]],
        ];
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function getParams(): void
    {
        $this->productService
            ->getCompanyIds(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $input = new InputData(
            '2019-01-01 05:00:00',
            '2019-01-07 04:59:59',
            0,
            [0],
            'ga8',
            [],
            1,
            1,
            [],
            [25],
            null,
            null,
            null,
            ['personal'],
            null,
            [1, 2, 3, 4, 5],
            '1',
            'period',
            [],
            20,
            '0',
            1,
            null,
            1,
            50,
            ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            [],
            32,
            'code',
            'number',
            'selected_personal',
            [
                'REALTIME' => 0,
                'TIMESHIFT' => 1,
                'GROSS' => 2,
                'TOTAL' => 3,
                'RT_TOTAL' => 4,
            ]
        );

        $expected = [
            [
                '2019-01-01',
                '2019-01-07',
                '050000',
                '045959',
                null,
                null,
                null,
                1,
                'ga8',
                ['personal'],
                [],
                [],
                [],
                null,
                [1, 2, 3, 4, 5],
                '1',
                'period',
                [],
                true,
                20,
                0,
                '0',
                [0],
                32,
                'code',
                'number',
                'selected_personal',
                [
                    'REALTIME' => 0,
                    'TIMESHIFT' => 1,
                    'GROSS' => 2,
                    'TOTAL' => 3,
                    'RT_TOTAL' => 4,
                ],
                [
                    'isRt' => true,
                    'isTs' => false,
                    'isGross' => false,
                    'isTotal' => false,
                    'isRtTotal' => false,
                ],
            ],
            [
                '2019-01-01',
                '2019-01-07',
                '050000',
                '045959',
                null,
                null,
                null,
                1,
                'ga8',
                ['personal'],
                [],
                [],
                [],
                null,
                [1, 2, 3, 4, 5],
                '1',
                'period',
                [],
                true,
                20,
                0,
                '0',
                [0],
            ],
        ];

        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('getParams');
        $method->setAccessible(true);

        $actual = $method->invoke($this->target, $input);

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function getList_基本区分(): void
    {
        $this->divisionService
            ->getCodeList(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->rdbCommercialDao
            ->searchGrp(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->commercialDao
            ->searchGrpOriginalDivs(arg::cetera())
            ->willReturn()
            ->shouldNotBeCalled();

        $expected = [[], []];

        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('getList');
        $method->setAccessible(true);

        $division = 'ga8';
        $baseDivision = ['ga8', 'ga12', 'ga10s', 'gm', 'oc'];
        $regionId = 1;
        $userId = 1;
        $params = [
            '2019-01-01',
            '2019-01-07',
            '050000',
            '045959',
            null,
            null,
            null,
            1,
            'ga8',
            null,
            null,
            [],
            [],
            null,
            [],
            null,
            0,
            null,
            true,
            true,
            1,
            '1',
            [0],
            32,
            'code',
            'number',
            'selected_personal',
            [1],
            [
                'REALTIME' => 0,
                'TIMESHIFT' => 1,
                'GROSS' => 2,
                'TOTAL' => 3,
                'RT_TOTAL' => 4,
            ],
        ];

        $actual = $method->invoke($this->target, $division, $regionId, $userId, $baseDivision, ...$params);

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function getList_カスタム区分(): void
    {
        $this->divisionService
            ->getCodeList(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->rdbCommercialDao
            ->searchGrp(arg::cetera())
            ->willReturn()
            ->shouldNotBeCalled();

        $this->commercialDao
            ->searchGrpOriginalDivs(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $expected = [[], []];

        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('getList');
        $method->setAccessible(true);

        $division = 'original_division';
        $baseDivision = ['ga8', 'ga12', 'ga10s', 'gm', 'oc'];
        $regionId = 1;
        $userId = 1;
        $params = [
            '2019-01-01',
            '2019-01-07',
            '050000',
            '045959',
            null,
            null,
            null,
            1,
            'original_division',
            null,
            null,
            [],
            [],
            null,
            [],
            null,
            0,
            null,
            true,
            true,
            1,
            '1',
            [0],
            32,
            'code',
            'number',
            'selected_personal',
            [1],
            [
                'REALTIME' => 0,
                'TIMESHIFT' => 1,
                'GROSS' => 2,
                'TOTAL' => 3,
                'RT_TOTAL' => 4,
            ],
        ];

        $actual = $method->invoke($this->target, $division, $regionId, $userId, $baseDivision, ...$params);

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function getHeader_List(): void
    {
        $this->searchConditionTextAppService
            ->getGrpHeader(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->searchConditionTextAppService
            ->getGrpCsv(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $expected = [];

        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('getHeader');
        $method->setAccessible(true);

        $csvFlg = '0';
        $params = [];

        $actual = $method->invoke($this->target, $csvFlg, $params);

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function getHeader_Csv(): void
    {
        $this->searchConditionTextAppService
            ->getGrpHeader(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->searchConditionTextAppService
            ->getGrpCsv(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $expected = [];

        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('getHeader');
        $method->setAccessible(true);

        $csvFlg = '1';
        $params = [];

        $actual = $method->invoke($this->target, $csvFlg, $params);

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function produceOutputData(): void
    {
        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('produceOutputData');
        $method->setAccessible(true);

        $list = [];
        $draw = [];
        $division = 'ga8';
        $codes = [];
        $codeList = [];
        $period = 'period';
        $dataType = [0];
        $startDateShort = '20190101';
        $endDateShort = '20190107';
        $header = [];

        $expected = new OutputData(
            $list,
            $draw,
            $division,
            $codes,
            $codeList,
            $period,
            $dataType,
            $startDateShort,
            $endDateShort,
            $header
        );
        $actual = $method->invoke($this->target, $list, $draw, $division, $codes, $codeList, $period, $dataType, $startDateShort, $endDateShort, $header);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @throws SampleCountException
     */
    public function handle(): void
    {
        $this->sampleService
            ->getConditionCrossCount(arg::cetera())
            ->willReturn()
            ->shouldNotBeCalled();

        $this->productService
            ->getCompanyIds(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->divisionService
            ->getCodeList(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->rdbCommercialDao
            ->searchGrp(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->commercialDao
            ->searchGrpOriginalDivs(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->searchConditionTextAppService
            ->getGrpHeader(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->searchConditionTextAppService
            ->getGrpCsv(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $outputData = new OutputData([], 1, 'ga8', ['personal'], [], 'period', [0], '20190101', '20190107', []);

        $this->presenter->__invoke($outputData)->shouldBeCalled();

        $input = new InputData(
            '2019-01-01 05:00:00',
            '2019-01-07 04:59:59',
            0,
            [0],
            'ga8',
            [],
            1,
            1,
            [],
            [25],
            null,
            null,
            null,
            ['personal'],
            null,
            [1, 2, 3, 4, 5],
            '1',
            'period',
            '',
            20,
            '0',
            1,
            null,
            1,
            50,
            ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            [],
            32,
            'code',
            'number',
            'selected_personal',
            [
                'REALTIME' => 0,
                'TIMESHIFT' => 1,
                'GROSS' => 2,
                'TOTAL' => 3,
                'RT_TOTAL' => 4,
            ]
        );

        $this->target->__invoke($input);
    }
}
