<?php

namespace Switchm\SmartApi\Components\Tests\RatingPerHourly\Get\UseCases;

use Prophecy\Argument as arg;
use ReflectionClass;
use ReflectionException;
use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Components\Common\CreateTableData;
use Switchm\SmartApi\Components\Common\Exceptions\SampleCountException;
use Switchm\SmartApi\Components\Common\RatingPoint;
use Switchm\SmartApi\Components\RatingPerHourly\Get\UseCases\InputData;
use Switchm\SmartApi\Components\RatingPerHourly\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\RatingPerHourly\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\RatingPerHourly\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Queries\Services\DivisionService;

class InteractorTest extends TestCase
{
    private $divisionService;

    private $ratingPoint;

    private $createTableData;

    private $outputBoundary;

    private $searchConditionTextAppService;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->divisionService = $this->prophesize(DivisionService::class);
        $this->ratingPoint = $this->prophesize(RatingPoint::class);
        $this->createTableData = $this->prophesize(CreateTableData::class);
        $this->outputBoundary = $this->prophesize(OutputBoundary::class);
        $this->searchConditionTextAppService = $this->prophesize(SearchConditionTextAppService::class);

        $this->target = new Interactor(
            $this->divisionService->reveal(),
            $this->ratingPoint->reveal(),
            $this->createTableData->reveal(),
            $this->outputBoundary->reveal(),
            $this->searchConditionTextAppService->reveal()
        );
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function getHeader_csvFlagが0(): void
    {
        $this->searchConditionTextAppService
            ->getRatingCsv(arg::cetera())
            ->shouldNotBeCalled()
            ->willReturn([]);

        $this->searchConditionTextAppService
            ->getRatingHeader(arg::cetera())
            ->shouldBeCalled()
            ->willReturn([]);

        $reflection = new ReflectionClass($this->target);
        $method = $reflection->getMethod('getHeader');
        $method->setAccessible(true);

        $expected = [];

        $csvFlag = '0';
        $params = [];

        $actual = $method->invoke($this->target, $csvFlag, $params);

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function getHeader_csvFlagが1(): void
    {
        $this->searchConditionTextAppService
            ->getRatingCsv(arg::cetera())
            ->shouldBeCalled()
            ->willReturn([]);

        $this->searchConditionTextAppService
            ->getRatingHeader(arg::cetera())
            ->shouldNotBeCalled()
            ->willReturn([]);

        $reflection = new ReflectionClass($this->target);
        $method = $reflection->getMethod('getHeader');
        $method->setAccessible(true);

        $expected = [];

        $csvFlag = '1';
        $params = [];

        $actual = $method->invoke($this->target, $csvFlag, $params);

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function produceOutputData(): void
    {
        $reflection = new ReflectionClass($this->target);
        $method = $reflection->getMethod('produceOutputData');
        $method->setAccessible(true);

        $data = [];
        $draw = '';
        $cnt = 1;
        $dateList = [];
        $channelType = '';
        $displayType = '';
        $aggregateType = '';
        $startDateShort = '';
        $endDateShort = '';
        $header = '';

        $expected = new OutputData(
            $data,
            $draw,
            $cnt,
            $dateList,
            $channelType,
            $displayType,
            $aggregateType,
            $startDateShort,
            $endDateShort,
            $header
        );

        $actual = $method->invoke(
            $this->target,
            $data,
            $draw,
            $cnt,
            $dateList,
            $channelType,
            $displayType,
            $aggregateType,
            $startDateShort,
            $endDateShort,
            $header
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider invokeDataProvider
     * @param $code
     * @param $division
     * @param $channelType
     * @param $regionId
     * @param $csvFlag
     * @throws SampleCountException
     */
    public function invoke_list($code, $division, $channelType, $regionId, $csvFlag): void
    {
        $dateList = [
            [
                'carbon' => null,
                'date' => '',
                'holidayFlg' => false,
            ],
        ];

        $this->ratingPoint
            ->initDate(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->ratingPoint
            ->getDateList(arg::cetera())
            ->willReturn($dateList)
            ->shouldBeCalled();

        $this->ratingPoint
            ->getChannelIds(arg::cetera())
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

        $this->ratingPoint
            ->convertCsvData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

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
            ->shouldNotBeCalled();

        $this->searchConditionTextAppService
            ->getRatingHeader(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $outputData = new OutputData(
            [],
            '1',
            0,
            array_map(function ($v) {
                unset($v['carbon']);
                return $v;
            }, $dateList),
            $channelType,
            '1',
            'period',
            '20190101',
            '20190107',
            []
        );

        $this->outputBoundary->__invoke($outputData)->shouldBeCalled();

        $input = new InputData(
            '2019-01-01 05:00:00',
            '2019-01-07 04:59:59',
            $regionId,
            [1],
            $channelType,
            $division,
            [],
            $csvFlag,
            '1',
            $code,
            '',
            [0],
            '1',
            'period',
            '1',
            '0',
            [],
            1,
            [],
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
            'personalName'
        );

        $this->target->__invoke($input);
    }

    /**
     * @test
     * @throws SampleCountException
     */
    public function invoke_csv(): void
    {
        $code = 'personal';
        $division = 'ga8';
        $channelType = 'dt1';
        $regionId = '1';
        $csvFlag = '1';

        $this->ratingPoint
            ->initDate(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->ratingPoint
            ->getDateList(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->ratingPoint
            ->getChannelIds(arg::cetera())
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

        $this->ratingPoint
            ->convertCsvData(arg::cetera())
            ->willReturn([])
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

        $this->searchConditionTextAppService
            ->getRatingHeader(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();
        $outputData = new OutputData([], '1', 0, [], $channelType, '1', 'period', '20190101', '20190107', []);

        $this->outputBoundary->__invoke($outputData)->shouldBeCalled();

        $input = new InputData(
            '2019-01-01 05:00:00',
            '2019-01-07 04:59:59',
            $regionId,
            [1],
            $channelType,
            $division,
            [],
            $csvFlag,
            '1',
            $code,
            '',
            [0],
            '1',
            'period',
            '1',
            '0',
            [],
            1,
            [],
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

        $this->target->__invoke($input);
    }

    public function invokeDataProvider()
    {
        return [
            [
                /*code*/ 'personal',
                /*division*/ 'personal',
                /*channelType*/ 'dt1',
                /*regionId*/ '1',
                /*csvFlag*/ '0',
            ],
            [
                /*code*/ 'test',
                /*division*/ 'test',
                /*channelType*/ 'dt2',
                /*regionId*/ '1',
                /*csvFlag*/ '0',
            ],
            [
                /*code*/ 'test',
                /*division*/ 'test',
                /*channelType*/ 'dt2',
                /*regionId*/ '2',
                /*csvFlag*/ '0',
            ],
        ];
    }
}
