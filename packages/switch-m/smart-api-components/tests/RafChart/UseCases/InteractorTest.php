<?php

namespace Switchm\SmartApi\Components\Tests\RafChart\UseCases;

use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Components\Common\Exceptions\RafCsvProductAxisException;
use Switchm\SmartApi\Components\Common\Exceptions\SampleCountException;
use Switchm\SmartApi\Components\RafChart\Get\UseCases\InputData;
use Switchm\SmartApi\Components\RafChart\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\RafChart\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\RafChart\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Queries\Dao\Dwh\RafDao;
use Switchm\SmartApi\Queries\Services\DivisionService;
use Switchm\SmartApi\Queries\Services\ProductService;
use Switchm\SmartApi\Queries\Services\SampleService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InteractorTest extends TestCase
{
    private $radDao;

    private $rdbRafDao;

    private $productService;

    private $divisionService;

    private $sampleService;

    private $searchConditionTextAppService;

    private $outputBoundary;

    private $target;

    private $requests;

    private $companyIds;

    private $codeNames;

    private $results;

    private $series;

    private $csvButtonInfo;

    private $categories;

    private $average;

    private $overOne;

    private $conditionCrossCodeNames;

    private $conditionCrossResults;

    private $conditionCrossSeries;

    private $conditionCrossCsvButtonInfo;

    private $conditionCrossCategories;

    private $conditionCrossAverage;

    private $conditionCrossOverOne;

    public function setUp(): void
    {
        parent::setUp();
        $this->radDao = $this->prophesize(RafDao::class);
        $this->rdbRafDao = $this->prophesize(\Switchm\SmartApi\Queries\Dao\Rdb\RafDao::class);
        $this->productService = $this->prophesize(ProductService::class);
        $this->divisionService = $this->prophesize(DivisionService::class);
        $this->sampleService = $this->prophesize(SampleService::class);
        $this->searchConditionTextAppService = $this->prophesize(SearchConditionTextAppService::class);
        $this->outputBoundary = $this->prophesize(OutputBoundary::class);

        $this->target = new Interactor(
            $this->radDao->reveal(),
            $this->rdbRafDao->reveal(),
            $this->productService->reveal(),
            $this->divisionService->reveal(),
            $this->sampleService->reveal(),
            $this->searchConditionTextAppService->reveal(),
            $this->outputBoundary->reveal()
        );

        $this->requests = [];
        $this->requests['basic'] = [
            'endDateTime' => '2019-06-11 04:59:00',
            'startDateTime' => '2019-06-05 05:00:00',
            'channels' => [3, 4, 5, 6, 7],
            'companyIds' => [3],
            'cmType' => '0',
            'cmSeconds' => '1',
            'division' => 'ga12',
            'codes' => [
                'personal',
                'fc',
                'household',
            ],
            'conditionCross' => [
                'gender' => [
                    '',
                ],
                'age' => [
                    'from' => 4,
                    'to' => 99,
                ],
                'occupation' => [
                    '',
                ],
                'married' => [
                    '',
                ],
                'dispOccupation' => [
                    '',
                ],
            ],
            'reachAndFrequencyGroupingUnit' => [3, 6, 9],
            'axisType' => 1,
            'channelAxis' => 1,
            'period' => 'week',
            'codeNames' => [
                [
                    'division' => 'personal',
                    'code' => 'personal',
                    'division_name' => '個人',
                    'name' => '個人',
                ],
                [
                    'division' => 'ga12',
                    'code' => 'fc',
                    'division_name' => '性・年齢12区分',
                    'name' => 'FC',
                    'division_order' => 102,
                    'display_order' => 1,
                ],
                [
                    'division' => 'household',
                    'code' => 'household',
                    'division_name' => '世帯',
                    'name' => '世帯',
                ],
            ],
            'productIds' => [],
            'cmIds' => [],
            'regionId' => 1,
            'conv_15_sec_flag' => true,
            'progIds' => [],
            'dataType' => [
                0,
            ],
            'dateRange' => 7,
            'csvFlag' => '0',
        ];

        $this->requests['condition_cross'] = [
            'endDateTime' => '2019-06-11 04:59:00',
            'startDateTime' => '2019-06-05 05:00:00',
            'channels' => [3, 4, 5, 6, 7],
            'companyIds' => [3],
            'cmType' => '0',
            'cmSeconds' => '1',
            'division' => 'condition_cross',
            'codes' => [],
            'conditionCross' => [
                'gender' => [
                    'f',
                ],
                'age' => [
                    'from' => 50,
                    'to' => 99,
                ],
                'occupation' => [
                    '',
                ],
                'married' => [
                    '',
                ],
                'dispOccupation' => [
                    '',
                ],
            ],
            'reachAndFrequencyGroupingUnit' => [3, 6, 9],
            'axisType' => 1,
            'channelAxis' => 1,
            'period' => 'week',
            'codeNames' => [],
            'productIds' => [],
            'cmIds' => [],
            'regionId' => 1,
            'conv_15_sec_flag' => true,
            'progIds' => [],
            'dataType' => [0],
            'dateRange' => 7,
            'csvFlag' => '0',
        ];

        // mocks
        $this->companyIds = [3];
        $this->codeNames = [
            [
                'division' => 'personal',
                'code' => 'personal',
                'division_name' => '個人',
                'name' => '個人',
            ],
            [
                'division' => 'ga12',
                'code' => 'fc',
                'division_name' => '性・年齢12区分',
                'name' => 'FC',
                'division_order' => 102,
                'display_order' => 1,
            ],
            [
                'division' => 'household',
                'code' => 'household',
                'division_name' => '世帯',
                'name' => '世帯',
            ],
        ];
        $this->results = [
            (object) [
                'code' => 'personal',
                'grp' => 2802.5,
                'freq_1' => 10.9,
                'freq_2' => 8.2,
                'freq_3' => 6.0,
                'freq_4' => 54.1,
                'average' => 31.1,
                'over_one' => 79.3,
                'number' => 5620,
            ],
            (object) [
                'code' => 'fc',
                'grp' => 1648.0,
                'freq_1' => 17.9,
                'freq_2' => 7.8,
                'freq_3' => 9.0,
                'freq_4' => 38.2,
                'average' => 19.9,
                'over_one' => 72.8,
                'number' => 346,
            ],
            (object) [
                'code' => 'household',
                'grp' => 5143.6,
                'freq_1' => 5.6,
                'freq_2' => 5.0,
                'freq_3' => 4.4,
                'freq_4' => 79.0,
                'average' => 48.3,
                'over_one' => 94.1,
                'number' => 2165,
            ],
        ];
        $this->series = [
            [
                'type' => 'column',
                'name' => '10回以上',
                'yAxis' => 0,
                'data' => [54.1, 38.2, 79],
                'tooltip' => ['valueSuffix' => ' %'],
            ],
            [
                'type' => 'column',
                'name' => '7回〜9回',
                'yAxis' => 0,
                'data' => [6, 9, 4.4],
                'tooltip' => ['valueSuffix' => ' %'],
            ],
            [
                'type' => 'column',
                'name' => '4回〜6回',
                'yAxis' => '0',
                'data' => [8.2, 7.8, 5],
                'tooltip' => ['valueSuffix' => ' %'],
            ],
            [
                'type' => 'column',
                'name' => '1回〜3回',
                'yAxis' => 0,
                'data' => [10.9, 17.9, 5.6],
                'tooltip' => ['valueSuffix' => ' %'],
            ],
            [
                'type' => 'column',
                'name' => '0回',
                'yAxis' => 0,
                'data' => [20.8, 27.1, 6],
                'tooltip' => ['valueSuffix' => ' %'],
            ],
            [
                'type' => 'line',
                'name' => 'GRP',
                'yAxis' => 1,
                'data' => [2802.5, 1648, 5143.6],
            ],
        ];
        $this->csvButtonInfo = [
            [
                'company_id' => 3,
                'company_name' => '花王',
                'channel_id' => 3,
                'channel_name' => '日本テレビ',
                'total_cnt' => 259,
                'total_duration' => 4245,
                'has_advertising' => 1,
            ],
            [
                'company_id' => 3,
                'company_name' => '花王',
                'channel_id' => 4,
                'channel_name' => 'テレビ朝日',
                'total_cnt' => 87,
                'total_duration' => 1395,
                'has_advertising' => 1,
            ],
            [
                'company_id' => 3,
                'company_name' => '花王',
                'channel_id' => 5,
                'channel_name' => 'TBS',
                'total_cnt' => 82,
                'total_duration' => 1380,
                'has_advertising' => 1,
            ],
            [
                'company_id' => 3,
                'company_name' => '花王',
                'channel_id' => 6,
                'channel_name' => 'テレビ東京',
                'total_cnt' => 27,
                'total_duration' => 450,
                'has_advertising' => 1,
            ],
            [
                'company_id' => 3,
                'company_name' => '花王',
                'channel_id' => 7,
                'channel_name' => 'フジテレビ',
                'total_cnt' => 262,
                'total_duration' => 4395,
                'has_advertising' => 1,
            ],
        ];
        $this->categories = ['個人', 'FC', '世帯'];
        $this->average = [31.1, 19.9, 48.3];
        $this->overOne = [79.3, 72.8, 94.1];

        // condition_cross
        $this->conditionCrossCodeNames = [
            [
                'code' => 'condition_cross',
                'name' => '掛け合わせ条件',
            ],
            [
                'code' => 'household',
                'name' => '世帯',
            ],
        ];
        $this->conditionCrossResults = [
            (object) [
                'code' => 'condition_cross',
                'grp' => 2802.5,
                'freq_1' => 10.9,
                'freq_2' => 8.2,
                'freq_3' => 6.0,
                'freq_4' => 54.1,
                'average' => 31.1,
                'over_one' => 79.3,
                'number' => 5620,
            ],
            (object) [
                'code' => 'household',
                'grp' => 5143.6,
                'freq_1' => 5.6,
                'freq_2' => 5.0,
                'freq_3' => 4.4,
                'freq_4' => 79.0,
                'average' => 48.3,
                'over_one' => 94.1,
                'number' => 2165,
            ],
        ];
        $this->conditionCrossSeries = [
            [
                'type' => 'column',
                'name' => '10回以上',
                'yAxis' => 0,
                'data' => [54.1, 79.0],
                'tooltip' => ['valueSuffix' => ' %'],
            ],
            [
                'type' => 'column',
                'name' => '7回〜9回',
                'yAxis' => 0,
                'data' => [6.0, 4.4],
                'tooltip' => ['valueSuffix' => ' %'],
            ],
            [
                'type' => 'column',
                'name' => '4回〜6回',
                'yAxis' => 0,
                'data' => [8.2, 5.0],
                'tooltip' => ['valueSuffix' => ' %'],
            ],
            [
                'type' => 'column',
                'name' => '1回〜3回',
                'yAxis' => 0,
                'data' => [10.9, 5.6],
                'tooltip' => ['valueSuffix' => ' %'],
            ],
            [
                'type' => 'column',
                'name' => '0回',
                'yAxis' => 0,
                'data' => [20.8, 6.0],
                'tooltip' => ['valueSuffix' => ' %'],
            ],
            [
                'type' => 'line',
                'name' => 'GRP',
                'yAxis' => 1,
                'data' => [2802.5, 5143.6],
            ],
        ];
        $this->conditionCrossCsvButtonInfo = [
            [
                'company_id' => 3,
                'company_name' => '花王',
                'channel_id' => 3,
                'channel_name' => '日本テレビ',
                'total_cnt' => 259,
                'total_duration' => 4245,
                'has_advertising' => 1,
            ],
            [
                'company_id' => 3,
                'company_name' => '花王',
                'channel_id' => 4,
                'channel_name' => 'テレビ朝日',
                'total_cnt' => 87,
                'total_duration' => 1395,
                'has_advertising' => 1,
            ],
            [
                'company_id' => 3,
                'company_name' => '花王',
                'channel_id' => 5,
                'channel_name' => 'TBS',
                'total_cnt' => 82,
                'total_duration' => 1380,
                'has_advertising' => 1,
            ],
            [
                'company_id' => 3,
                'company_name' => '花王',
                'channel_id' => 6,
                'channel_name' => 'テレビ東京',
                'total_cnt' => 27,
                'total_duration' => 450,
                'has_advertising' => 1,
            ],
            [
                'company_id' => 3,
                'company_name' => '花王',
                'channel_id' => 7,
                'channel_name' => 'フジテレビ',
                'total_cnt' => 262,
                'total_duration' => 4395,
                'has_advertising' => 1,
            ],
        ];
        $this->conditionCrossCategories = ['掛け合わせ条件', '世帯'];
        $this->conditionCrossAverage = [31.1, 48.3];
        $this->conditionCrossOverOne = [79.3, 94.1];
    }

    /**
     * @test
     */
    public function invoke基本属性正常系(): void
    {
        $inputData = $this->createInputData($this->requests['basic']);

        $params = [
            $inputData->startDate(),
            $inputData->endDate(),
            $inputData->startTimeShort(),
            $inputData->endTimeShort(),
            $inputData->cmType(),
            $inputData->cmSeconds(),
            $inputData->regionId(),
            $inputData->division(),
            $inputData->codes(),
            $inputData->conditionCross(),
            $this->companyIds,
            $inputData->productIds(),
            $inputData->cmIds(),
            $inputData->channels(),
            $inputData->conv15SecFlag(),
            $inputData->progIds(),
            $inputData->straddlingFlg(),
            $inputData->dataType(),
        ];

        $this->productService->getCompanyIds($inputData->productIds(), $inputData->companyIds())->willReturn($this->companyIds)->shouldBeCalled();
        $this->sampleService->getConditionCrossCount()->shouldNotBeCalled();
        $this->rdbRafDao->getProductNumbers()->willReturn(3)->shouldNotBeCalled();
        $this->radDao->commonCreateTempTables(...array_merge($params, [
            '0',
            '0',
            'cm',
            $inputData->dataTypeFlags(),
            $inputData->axisTypeProduct(),
            $inputData->axisTypeCompany(),
        ]))->shouldBeCalled();
        $this->radDao->getChartResults(...array_merge($params, [
            $inputData->reachAndFrequencyGroupingUnit(),
            $inputData->dataTypeFlags(),
        ]))->willReturn($this->results)->shouldBeCalled();
        $this->radDao->getCsvButtonInfo(...array_merge($params, [
            $inputData->axisType(),
            $inputData->channelAxis(),
            $inputData->axisTypeProduct(),
            $inputData->axisTypeCompany(),
        ]))->willReturn($this->csvButtonInfo)->shouldBeCalled();
        $this->divisionService->getCodeList($inputData->division(), $inputData->regionId(), $inputData->userId(), $inputData->baseDivision())->shouldBeCalled()->willReturn(['codeList']);
        $searchConditionParams = [
            $inputData->startDate(),
            $inputData->endDate(),
            $inputData->startTime(),
            $inputData->endTime(),
            $inputData->cmType(),
            $inputData->cmSeconds(),
            $inputData->progIds(),
            $inputData->regionId(),
            $inputData->companyIds(),
            $inputData->productIds(),
            $inputData->cmIds(),
            $inputData->channels(),
            $inputData->straddlingFlg(),
            $inputData->division(),
            $inputData->conditionCross(),
            ['codeList'],
            0,
            0,
            $inputData->conv15SecFlag(),
            $inputData->codes(),
            $inputData->dataType(),
            $inputData->csvFlag(),
            $inputData->period(),
        ];
        $this->searchConditionTextAppService->getRafList(...$searchConditionParams)->shouldBeCalled()->willReturn(['searchText']);
        $this->outputBoundary->__invoke(new OutputData($this->series, $this->categories, $this->average, $this->overOne, [2802.5, 1648, 5143.6], $this->csvButtonInfo, ['searchText']))->shouldBeCalled();

        $this->target->__invoke($inputData);
    }

    /**
     * @test
     */
    public function invoke掛け合わせ条件(): void
    {
        $inputData = $this->createInputData($this->requests['condition_cross']);

        $params = [
            $inputData->startDate(),
            $inputData->endDate(),
            $inputData->startTimeShort(),
            $inputData->endTimeShort(),
            $inputData->cmType(),
            $inputData->cmSeconds(),
            $inputData->regionId(),
            $inputData->division(),
            $inputData->codes(),
            $inputData->conditionCross(),
            $this->companyIds,
            $inputData->productIds(),
            $inputData->cmIds(),
            $inputData->channels(),
            $inputData->conv15SecFlag(),
            $inputData->progIds(),
            $inputData->straddlingFlg(),
            $inputData->dataType(),
        ];

        $this->productService->getCompanyIds($inputData->productIds(), $inputData->companyIds())->willReturn($this->companyIds)->shouldBeCalled();
        $this->sampleService->getConditionCrossCount($inputData->conditionCross(), $inputData->startDate(), $inputData->endDate(), $inputData->regionId(), true)->willReturn(100);
        $this->rdbRafDao->getProductNumbers()->willReturn(3)->shouldNotBeCalled();
        $this->radDao->commonCreateTempTables(...array_merge($params, [
            '0',
            '0',
            'cm',
            $inputData->dataTypeFlags(),
            $inputData->axisTypeProduct(),
            $inputData->axisTypeCompany(),
        ]))->shouldBeCalled();
        $this->radDao->getChartResults(...array_merge($params, [
            $inputData->reachAndFrequencyGroupingUnit(),
            $inputData->dataTypeFlags(),
        ]))->willReturn($this->conditionCrossResults)->shouldBeCalled();
        $this->radDao->getCsvButtonInfo(...array_merge($params, [
            $inputData->axisType(),
            $inputData->channelAxis(),
            $inputData->axisTypeProduct(),
            $inputData->axisTypeCompany(),
        ]))->willReturn($this->conditionCrossCsvButtonInfo)->shouldBeCalled();
        $this->divisionService->getCodeList($inputData->division(), $inputData->regionId(), $inputData->userId(), $inputData->baseDivision())->shouldBeCalled()->willReturn(['codeList']);
        $searchConditionParams = [
            $inputData->startDate(),
            $inputData->endDate(),
            $inputData->startTime(),
            $inputData->endTime(),
            $inputData->cmType(),
            $inputData->cmSeconds(),
            $inputData->progIds(),
            $inputData->regionId(),
            $inputData->companyIds(),
            $inputData->productIds(),
            $inputData->cmIds(),
            $inputData->channels(),
            $inputData->straddlingFlg(),
            $inputData->division(),
            $inputData->conditionCross(),
            ['codeList'],
            100,
            0,
            $inputData->conv15SecFlag(),
            $inputData->codes(),
            $inputData->dataType(),
            $inputData->csvFlag(),
            $inputData->period(),
        ];
        $this->searchConditionTextAppService->getRafList(...$searchConditionParams)->shouldBeCalled()->willReturn(['searchText']);
        $this->outputBoundary->__invoke(new OutputData($this->conditionCrossSeries, $this->conditionCrossCategories, $this->conditionCrossAverage, $this->conditionCrossOverOne, [2802.5, 5143.6], $this->conditionCrossCsvButtonInfo, ['searchText']))->shouldBeCalled();

        $this->target->__invoke($inputData);
    }

    /**
     * @test
     */
    public function invokeタイムシフト掛け合わせ条件(): void
    {
        $req = $this->requests['condition_cross'];
        $req['axisType'] = 2;
        $inputData = $this->createTimeShiftInputData($req);

        $params = [
            $inputData->startDate(),
            $inputData->endDate(),
            $inputData->startTimeShort(),
            $inputData->endTimeShort(),
            $inputData->cmType(),
            $inputData->cmSeconds(),
            $inputData->regionId(),
            $inputData->division(),
            $inputData->codes(),
            $inputData->conditionCross(),
            $this->companyIds,
            $inputData->productIds(),
            $inputData->cmIds(),
            $inputData->channels(),
            $inputData->conv15SecFlag(),
            $inputData->progIds(),
            $inputData->straddlingFlg(),
            $inputData->dataType(),
        ];

        $this->productService->getCompanyIds($inputData->productIds(), $inputData->companyIds())->willReturn($this->companyIds)->shouldBeCalled();
        $this->sampleService->getConditionCrossCount($inputData->conditionCross(), $inputData->startDate(), $inputData->endDate(), $inputData->regionId(), false)->willReturn(100);
        $productNumbers['number'] = 3;
        $this->rdbRafDao->getProductNumbers(...$params)->willReturn((object) $productNumbers)->shouldBeCalled();
        $this->radDao->commonCreateTempTables(...array_merge($params, [
            '0',
            '0',
            'cm',
            $inputData->dataTypeFlags(),
            $inputData->axisTypeProduct(),
            $inputData->axisTypeCompany(),
        ]))->shouldBeCalled();
        $this->radDao->getChartResults(...array_merge($params, [
            $inputData->reachAndFrequencyGroupingUnit(),
            $inputData->dataTypeFlags(),
        ]))->willReturn($this->conditionCrossResults)->shouldBeCalled();
        $this->radDao->getCsvButtonInfo(...array_merge($params, [
            $inputData->axisType(),
            $inputData->channelAxis(),
            $inputData->axisTypeProduct(),
            $inputData->axisTypeCompany(),
        ]))->willReturn($this->conditionCrossCsvButtonInfo)->shouldBeCalled();
        $this->divisionService->getCodeList($inputData->division(), $inputData->regionId(), $inputData->userId(), $inputData->baseDivision())->shouldBeCalled()->willReturn(['codeList']);
        $searchConditionParams = [
            $inputData->startDate(),
            $inputData->endDate(),
            $inputData->startTime(),
            $inputData->endTime(),
            $inputData->cmType(),
            $inputData->cmSeconds(),
            $inputData->progIds(),
            $inputData->regionId(),
            $inputData->companyIds(),
            $inputData->productIds(),
            $inputData->cmIds(),
            $inputData->channels(),
            $inputData->straddlingFlg(),
            $inputData->division(),
            $inputData->conditionCross(),
            ['codeList'],
            100,
            0,
            $inputData->conv15SecFlag(),
            $inputData->codes(),
            $inputData->dataType(),
            $inputData->csvFlag(),
            $inputData->period(),
        ];
        $this->searchConditionTextAppService->getRafList(...$searchConditionParams)->shouldBeCalled()->willReturn(['searchText']);
        $this->outputBoundary->__invoke(new OutputData($this->conditionCrossSeries, $this->conditionCrossCategories, $this->conditionCrossAverage, $this->conditionCrossOverOne, [2802.5, 5143.6], $this->conditionCrossCsvButtonInfo, ['searchText']))->shouldBeCalled();

        $this->target->__invoke($inputData);
    }

    /**
     * @test
     */
    public function invokeタイムシフト掛け合わせ条件商品数31以上(): void
    {
        $req = $this->requests['condition_cross'];
        $req['axisType'] = 2;
        $inputData = $this->createTimeShiftInputData($req);

        $params = [
            $inputData->startDate(),
            $inputData->endDate(),
            $inputData->startTimeShort(),
            $inputData->endTimeShort(),
            $inputData->cmType(),
            $inputData->cmSeconds(),
            $inputData->regionId(),
            $inputData->division(),
            $inputData->codes(),
            $inputData->conditionCross(),
            $this->companyIds,
            $inputData->productIds(),
            $inputData->cmIds(),
            $inputData->channels(),
            $inputData->conv15SecFlag(),
            $inputData->progIds(),
            $inputData->straddlingFlg(),
            $inputData->dataType(),
        ];

        $this->productService->getCompanyIds($inputData->productIds(), $inputData->companyIds())->willReturn($this->companyIds)->shouldBeCalled();
        $this->sampleService->getConditionCrossCount($inputData->conditionCross(), $inputData->startDate(), $inputData->endDate(), $inputData->regionId(), false)->willReturn(100);
        $productNumbers['number'] = 31;
        $this->rdbRafDao->getProductNumbers(...$params)->willReturn((object) $productNumbers)->shouldBeCalled();
        $this->expectException(RafCsvProductAxisException::class);
        $this->expectExceptionMessage('集計軸に商品別を指定する場合、商品の数が30以内になるように絞り込みをしてください。');
        $this->target->__invoke($inputData);
    }

    /**
     * @test
     */
    public function invoke件数0件(): void
    {
        $req = $this->requests['condition_cross'];
        $req['axisType'] = 2;
        $inputData = $this->createTimeShiftInputData($req);

        $params = [
            $inputData->startDate(),
            $inputData->endDate(),
            $inputData->startTimeShort(),
            $inputData->endTimeShort(),
            $inputData->cmType(),
            $inputData->cmSeconds(),
            $inputData->regionId(),
            $inputData->division(),
            $inputData->codes(),
            $inputData->conditionCross(),
            $this->companyIds,
            $inputData->productIds(),
            $inputData->cmIds(),
            $inputData->channels(),
            $inputData->conv15SecFlag(),
            $inputData->progIds(),
            $inputData->straddlingFlg(),
            $inputData->dataType(),
        ];

        $this->productService->getCompanyIds($inputData->productIds(), $inputData->companyIds())->willReturn($this->companyIds)->shouldBeCalled();
        $this->sampleService->getConditionCrossCount($inputData->conditionCross(), $inputData->startDate(), $inputData->endDate(), $inputData->regionId(), false)->willReturn(100);
        $productNumbers['number'] = 3;
        $this->rdbRafDao->getProductNumbers(...$params)->willReturn((object) $productNumbers)->shouldBeCalled();
        $this->radDao->commonCreateTempTables(...array_merge($params, [
            '0',
            '0',
            'cm',
            $inputData->dataTypeFlags(),
            $inputData->axisTypeProduct(),
            $inputData->axisTypeCompany(),
        ]))->shouldBeCalled();
        $this->radDao->getChartResults(...array_merge($params, [
            $inputData->reachAndFrequencyGroupingUnit(),
            $inputData->dataTypeFlags(),
        ]))->willReturn([])->shouldBeCalled();
        $this->expectException(NotFoundHttpException::class);

        $this->target->__invoke($inputData);
    }

    /**
     * @test
     */
    public function invoke件数サンプル数50未満(): void
    {
        $req = $this->requests['condition_cross'];
        $req['axisType'] = 2;
        $inputData = $this->createTimeShiftInputData($req);

        $params = [
            $inputData->startDate(),
            $inputData->endDate(),
            $inputData->startTimeShort(),
            $inputData->endTimeShort(),
            $inputData->cmType(),
            $inputData->cmSeconds(),
            $inputData->regionId(),
            $inputData->division(),
            $inputData->codes(),
            $inputData->conditionCross(),
            $this->companyIds,
            $inputData->productIds(),
            $inputData->cmIds(),
            $inputData->channels(),
            $inputData->conv15SecFlag(),
            $inputData->progIds(),
            $inputData->straddlingFlg(),
            $inputData->dataType(),
        ];

        $this->productService->getCompanyIds($inputData->productIds(), $inputData->companyIds())->willReturn($this->companyIds)->shouldBeCalled();
        $this->sampleService->getConditionCrossCount($inputData->conditionCross(), $inputData->startDate(), $inputData->endDate(), $inputData->regionId(), false)->willReturn(49);
        $this->expectException(SampleCountException::class);

        $this->target->__invoke($inputData);
    }

    private function createInputData($request)
    {
        return new InputData(
            $request['startDateTime'],
            $request['endDateTime'],
            $request['dataType'],
            $request['dateRange'],
            $request['regionId'],
            $request['division'],
            $request['conditionCross'],
            $request['csvFlag'],
            $request['codes'],
            $request['channels'],
            $request['axisType'],
            $request['channelAxis'],
            $request['cmIds'],
            $request['cmSeconds'],
            $request['cmType'],
            $request['codeNames'],
            $request['companyIds'],
            $request['conv_15_sec_flag'],
            $request['period'],
            $request['productIds'],
            $request['progIds'],
            $request['reachAndFrequencyGroupingUnit'],
            ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            2,
            30,
            1,
            1,
            [
                'ga8',
                'ga12',
                'ga10s',
                'gm',
                'oc',
            ]
        );
    }

    private function createTimeShiftInputData($request)
    {
        return new InputData(
            $request['startDateTime'],
            $request['endDateTime'],
            $request['dataType'],
            $request['dateRange'],
            $request['regionId'],
            $request['division'],
            $request['conditionCross'],
            $request['csvFlag'],
            $request['codes'],
            $request['channels'],
            $request['axisType'],
            $request['channelAxis'],
            $request['cmIds'],
            $request['cmSeconds'],
            $request['cmType'],
            $request['codeNames'],
            $request['companyIds'],
            $request['conv_15_sec_flag'],
            $request['period'],
            $request['productIds'],
            $request['progIds'],
            $request['reachAndFrequencyGroupingUnit'],
            ['isRt' => false, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => true],
            '2',
            30,
            1,
            1,
            [
                'ga8',
                'ga12',
                'ga10s',
                'gm',
                'oc',
            ]
        );
    }
}
