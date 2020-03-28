<?php

namespace Switchm\SmartApi\Components\Tests\CommercialAdvertising\Get\UseCases;

use Prophecy\Argument as arg;
use ReflectionClass;
use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Components\CommercialAdvertising\Get\UseCases\InputData;
use Switchm\SmartApi\Components\CommercialAdvertising\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\CommercialAdvertising\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\CommercialAdvertising\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Common\CreateTableData;
use Switchm\SmartApi\Components\Common\Exceptions\SampleCountException;
use Switchm\SmartApi\Components\Common\RatingPoint;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Queries\Dao\Dwh\CommercialDao;
use Switchm\SmartApi\Queries\Services\DivisionService;
use Switchm\SmartApi\Queries\Services\ProductService;
use Switchm\SmartApi\Queries\Services\SampleService;

class InteractorTest extends TestCase
{
    private $productService;

    private $commercialDao;

    private $divisionService;

    private $sampleService;

    private $ratingPoint;

    private $createTableData;

    private $outputBoundary;

    private $searchConditionTextAppService;

    private $target;

    /**
     * @test
     * @throws SampleCountException
     */
    public function __invoke(): void
    {
        $input = new InputData(
            '2019-01-01 05:00:00',
            '2019-01-07 05:00:00',
            [],
            [],
            '',
            '',
            [],
            1,
            [],
            [],
            true,
            true,
            false,
            'ga8',
            [],
            '0',
            '',
            'personal',
            1,
            50,
            1,
            [
                'ga8',
                'ga12',
                'ga10s',
                'gm',
                'oc',
            ],
            100,
            60,
            'codePrefix',
            'codeNumberPrefix',
            'personalName',
        );

        $this->productService
            ->getCompanyIds(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->commercialDao
            ->searchAdvertising(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->ratingPoint
            ->initDate(arg::cetera())
            ->willReturn([])
            ->shouldBeCalledTimes(2);

        $this->ratingPoint
            ->getDateList(arg::cetera())
            ->willReturn([])
            ->shouldBeCalledTimes(2);

        $this->ratingPoint
            ->getChannelIds(arg::cetera())
            ->willReturn([])
            ->shouldBeCalledTimes(2);

        $this->ratingPoint
            ->getConditionCrossCount(arg::cetera())
            ->willReturn([])
            ->shouldBeCalledTimes(2);

        $this->ratingPoint
            ->getDataAndAlias(arg::cetera())
            ->willReturn([[], 'test'])
            ->shouldBeCalledTimes(2);

        $this->ratingPoint
            ->convertCsvData(arg::cetera())
            ->willReturn([])
            ->shouldBeCalledTimes(2);

        $this->createTableData
            ->__invoke(arg::cetera())
            ->willReturn([])
            ->shouldBeCalledTimes(2);

        $this->divisionService
            ->getCodeList(arg::cetera())
            ->willReturn([])
            ->shouldBeCalledTimes(2);

        $this->searchConditionTextAppService
            ->getRatingCsv(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled(2);

        $this->searchConditionTextAppService
            ->getAdvertisingHeader(arg::cetera())
            ->willReturn(['ok'])
            ->shouldBeCalled();

        $outputData = new OutputData([], [], '0', '', [[], []], '20190101050000', '20190107050000', ['ok']);

        $this->outputBoundary
            ->__invoke($outputData)->shouldBeCalled();

        $this->target->__invoke($input);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->productService = $this->prophesize(ProductService::class);
        $this->commercialDao = $this->prophesize(CommercialDao::class);
        $this->divisionService = $this->prophesize(DivisionService::class);
        $this->sampleService = $this->prophesize(SampleService::class);
        $this->ratingPoint = $this->prophesize(RatingPoint::class);
        $this->createTableData = $this->prophesize(CreateTableData::class);
        $this->outputBoundary = $this->prophesize(OutputBoundary::class);
        $this->searchConditionTextAppService = $this->prophesize(SearchConditionTextAppService::class);

        $this->target = new Interactor(
            $this->productService->reveal(),
            $this->commercialDao->reveal(),
            $this->divisionService->reveal(),
            $this->sampleService->reveal(),
            $this->ratingPoint->reveal(),
            $this->createTableData->reveal(),
            $this->outputBoundary->reveal(),
            $this->searchConditionTextAppService->reveal()
        );
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getHeader_For_List(): void
    {
        $csvFlag = '0';
        $params = [];

        $this->searchConditionTextAppService
            ->getAdvertisingHeader(arg::cetera())
            ->willReturn(['ok'])
            ->shouldBeCalled();

        $this->searchConditionTextAppService
            ->getAdvertisingCsv(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $expected = ['ok'];

        $reflection = new ReflectionClass($this->target);
        $method = $reflection->getMethod('getHeader');
        $method->setAccessible(true);

        $actual = $method->invoke($this->target, $csvFlag, $params);

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getHeader_For_Csv(): void
    {
        $csvFlag = '1';
        $params = [];

        $this->searchConditionTextAppService
            ->getAdvertisingHeader(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->searchConditionTextAppService
            ->getAdvertisingCsv(arg::cetera())
            ->willReturn(['ok'])
            ->shouldBeCalled();

        $expected = ['ok'];

        $reflection = new ReflectionClass($this->target);
        $method = $reflection->getMethod('getHeader');
        $method->setAccessible(true);

        $actual = $method->invoke($this->target, $csvFlag, $params);

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getRatingPoints_Personal_True(): void
    {
        $startDateTime = '2019-01-01 05:00:00';
        $endDateTime = '2019-01-07';
        $division = 'ga8';
        $code = 'personal';
        $conditionCross = [];
        $regionId = 1;
        $channels = [1, 2, 3, 4, 5];
        $rdbDwhSearchPeriod = 1;
        $sampleCountMaxNumber = 50;
        $dataTypeFlags = [];
        $userID = 1;

        $heatMapRating = true;
        $heatMapTciPersonal = true;
        $heatMapTciHousehold = false;
        $baseDivision = [
            'ga8',
            'ga12',
            'ga10s',
            'gm',
            'oc',
        ];
        $intervalHourly = 100;
        $intervalMinutes = 60;
        $codePrefix = 'codePrefix';
        $codeNumberPrefix = 'codeNumberPrefix';
        $selectedPersonalName = 'selectedPersonalName';

        $this->ratingPoint
            ->initDate(arg::cetera())
            ->willReturn([])
            ->shouldBeCalledTimes(2);

        $this->ratingPoint
            ->getDateList(arg::cetera())
            ->willReturn([])
            ->shouldBeCalledTimes(2);

        $this->ratingPoint
            ->getChannelIds(arg::cetera())
            ->willReturn([])
            ->shouldBeCalledTimes(2);

        $this->ratingPoint
            ->getConditionCrossCount(arg::cetera())
            ->willReturn([])
            ->shouldBeCalledTimes(2);

        $this->ratingPoint
            ->getDataAndAlias(arg::cetera())
            ->willReturn([[], 'test'])
            ->shouldBeCalledTimes(2);

        $this->ratingPoint
            ->convertCsvData(arg::cetera())
            ->willReturn([])
            ->shouldBeCalledTimes(2);

        $this->createTableData
            ->__invoke(arg::cetera())
            ->willReturn([])
            ->shouldBeCalledTimes(2);

        $this->divisionService
            ->getCodeList(arg::cetera())
            ->willReturn([])
            ->shouldBeCalledTimes(2);

        $this->searchConditionTextAppService
            ->getRatingCsv(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled(2);

        $expected = [[], []];

        $reflection = new ReflectionClass($this->target);
        $method = $reflection->getMethod('getRatingPoints');
        $method->setAccessible(true);

        $actual = $method->invoke(
            $this->target,
            $startDateTime,
            $endDateTime,
            $division,
            $code,
            $conditionCross,
            $regionId,
            $channels,
            $rdbDwhSearchPeriod,
            $sampleCountMaxNumber,
            $dataTypeFlags,
            $userID,
            $heatMapRating,
            $heatMapTciPersonal,
            $heatMapTciHousehold,
            $baseDivision,
            $intervalHourly,
            $intervalMinutes,
            $codePrefix,
            $codeNumberPrefix,
            $selectedPersonalName
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getRatingPoints_Household_True(): void
    {
        $startDateTime = '2019-01-01 05:00:00';
        $endDateTime = '2019-01-07';
        $division = 'ga8';
        $code = 'personal';
        $conditionCross = [];
        $regionId = 1;
        $channels = [1, 2, 3, 4, 5];
        $rdbDwhSearchPeriod = 1;
        $sampleCountMaxNumber = 50;
        $dataTypeFlags = [];
        $userID = 1;

        $heatMapRating = true;
        $heatMapTciPersonal = false;
        $heatMapTciHousehold = true;
        $baseDivision = [
            'ga8',
            'ga12',
            'ga10s',
            'gm',
            'oc',
        ];
        $intervalHourly = 100;
        $intervalMinutes = 60;
        $codePrefix = 'codePrefix';
        $codeNumberPrefix = 'codeNumberPrefix';
        $selectedPersonalName = 'selectedPersonalName';

        $this->ratingPoint
            ->initDate(arg::cetera())
            ->willReturn([])
            ->shouldBeCalledTimes(2);

        $this->ratingPoint
            ->getDateList(arg::cetera())
            ->willReturn([])
            ->shouldBeCalledTimes(2);

        $this->ratingPoint
            ->getChannelIds(arg::cetera())
            ->willReturn([])
            ->shouldBeCalledTimes(2);

        $this->ratingPoint
            ->getConditionCrossCount(arg::cetera())
            ->willReturn([])
            ->shouldBeCalledTimes(2);

        $this->ratingPoint
            ->getDataAndAlias(arg::cetera())
            ->willReturn([[], 'test'])
            ->shouldBeCalledTimes(2);

        $this->ratingPoint
            ->convertCsvData(arg::cetera())
            ->willReturn([])
            ->shouldBeCalledTimes(2);

        $this->createTableData
            ->__invoke(arg::cetera())
            ->willReturn([])
            ->shouldBeCalledTimes(2);

        $this->divisionService
            ->getCodeList(arg::cetera())
            ->willReturn([])
            ->shouldBeCalledTimes(2);

        $this->searchConditionTextAppService
            ->getRatingCsv(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled(2);

        $expected = [[], []];

        $reflection = new ReflectionClass($this->target);
        $method = $reflection->getMethod('getRatingPoints');
        $method->setAccessible(true);

        $actual = $method->invoke(
            $this->target,
            $startDateTime,
            $endDateTime,
            $division,
            $code,
            $conditionCross,
            $regionId,
            $channels,
            $rdbDwhSearchPeriod,
            $sampleCountMaxNumber,
            $dataTypeFlags,
            $userID,
            $heatMapRating,
            $heatMapTciPersonal,
            $heatMapTciHousehold,
            $baseDivision,
            $intervalHourly,
            $intervalMinutes,
            $codePrefix,
            $codeNumberPrefix,
            $selectedPersonalName
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getRatingPoints_All_False(): void
    {
        $startDateTime = '2019-01-01 05:00:00';
        $endDateTime = '2019-01-07';
        $division = 'ga8';
        $code = 'personal';
        $conditionCross = [];
        $regionId = 1;
        $channels = [1, 2, 3, 4, 5];
        $rdbDwhSearchPeriod = 1;
        $sampleCountMaxNumber = 50;
        $dataTypeFlags = [];
        $userID = 1;

        $heatMapRating = false;
        $heatMapTciPersonal = false;
        $heatMapTciHousehold = false;
        $baseDivision = [
            'ga8',
            'ga12',
            'ga10s',
            'gm',
            'oc',
        ];
        $intervalHourly = 100;
        $intervalMinutes = 60;
        $codePrefix = 'codePrefix';
        $codeNumberPrefix = 'codeNumberPrefix';
        $selectedPersonalName = 'selectedPersonalName';

        $this->ratingPoint
            ->initDate(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->ratingPoint
            ->getDateList(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->ratingPoint
            ->getChannelIds(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->ratingPoint
            ->getConditionCrossCount(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->ratingPoint
            ->getDataAndAlias(arg::cetera())
            ->willReturn([[], 'test'])
            ->shouldNotBeCalled();

        $this->ratingPoint
            ->convertCsvData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->createTableData
            ->__invoke(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->divisionService
            ->getCodeList(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->searchConditionTextAppService
            ->getRatingCsv(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $expected = [];

        $reflection = new ReflectionClass($this->target);
        $method = $reflection->getMethod('getRatingPoints');
        $method->setAccessible(true);

        $actual = $method->invoke(
            $this->target,
            $startDateTime,
            $endDateTime,
            $division,
            $code,
            $conditionCross,
            $regionId,
            $channels,
            $rdbDwhSearchPeriod,
            $sampleCountMaxNumber,
            $dataTypeFlags,
            $userID,
            $heatMapRating,
            $heatMapTciPersonal,
            $heatMapTciHousehold,
            $baseDivision,
            $intervalHourly,
            $intervalMinutes,
            $codePrefix,
            $codeNumberPrefix,
            $selectedPersonalName
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @dataProvider getRatingPointResultDataProvider
     * @param $code
     * @param $division
     * @param $channelType
     * @param $regionId
     * @throws \ReflectionException
     */
    public function getResult_test($code, $division, $channelType, $regionId): void
    {
        $this->ratingPoint
            ->initDate(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->ratingPoint
            ->getChannelIds(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->ratingPoint
            ->getDateList(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->ratingPoint
            ->getConditionCrossCount(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->ratingPoint
            ->getDataAndAlias(arg::cetera())
            ->willReturn([[], 'test'])
            ->shouldBeCalled();

        $this->createTableData
            ->__invoke(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->divisionService
            ->getCodeList(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->searchConditionTextAppService
            ->getRatingCsv(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->ratingPoint
            ->convertCsvData(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $input = [
            '2019-01-01',
            '2019-01-07',
            $regionId,
            [1],
            $channelType,
            $division,
            [],
            '1',
            $code,
            [0],
            'channelBy',
            'hourly',
            'hourly',
            20,
            50,
            [],
            1,
            [
                'ga8',
                'ga12',
                'ga10s',
                'gm',
                'oc',
            ],
            'viewing_rate',
            100,
            60,
            'codePrefix',
            'codeNumberPrefix',
            'personalName',
        ];

        $expected = [[], []];

        $reflection = new ReflectionClass($this->target);
        $method = $reflection->getMethod('getRatingPointResult');
        $method->setAccessible(true);

        $actual = $method->invoke($this->target, ...$input);

        $this->assertSame($expected, $actual);
    }

    public function getRatingPointResultDataProvider()
    {
        return [
            [
                /*code*/ 'personal',
                /*division*/ 'ga8',
                /*channelType*/ 'dt1',
                /*regionId*/ '1',
            ],
            [
                /*code*/ 'test',
                /*division*/ 'div',
                /*channelType*/ 'dt2',
                /*regionId*/ '1',
            ],
            [
                /*code*/ 'test',
                /*division*/ 'div',
                /*channelType*/ 'dt2',
                /*regionId*/ '2',
            ],
        ];
    }
}
