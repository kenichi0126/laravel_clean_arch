<?php

namespace Switchm\SmartApi\Components\Tests\RafCsv\UseCases;

use Smart2\Application\Services\SearchConditionTextAppService;
use Smart2\QueryModel\Service\SearchConditionTextService;
use Switchm\SmartApi\Components\Common\Exceptions\RafCsvProductAxisException;
use Switchm\SmartApi\Components\Common\Exceptions\SampleCountException;
use Switchm\SmartApi\Components\RafCsv\Get\UseCases\InputData;
use Switchm\SmartApi\Components\RafCsv\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\RafCsv\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\RafCsv\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Queries\Dao\Dwh\RafDao;
use Switchm\SmartApi\Queries\Services\DivisionService;
use Switchm\SmartApi\Queries\Services\ProductService;
use Switchm\SmartApi\Queries\Services\SampleService;

class InteractorTest extends TestCase
{
    private $rafDao;

    private $rdbRafDao;

    private $productService;

    private $divisionService;

    private $sampleService;

    private $searchConditionTextService;

    private $searchConditionTextAppService;

    private $outputBoundary;

    private $target;

    private $requests;

    private $companyIds;

    private $data;

    private $dataType;

    private $results;

    public function setUp(): void
    {
        parent::setUp();
        $this->rafDao = $this->prophesize(RafDao::class);
        $this->rdbRafDao = $this->prophesize(\Switchm\SmartApi\Queries\Dao\Rdb\RafDao::class);
        $this->productService = $this->prophesize(ProductService::class);
        $this->divisionService = $this->prophesize(DivisionService::class);
        $this->sampleService = $this->prophesize(SampleService::class);
        $this->searchConditionTextService = $this->prophesize(SearchConditionTextService::class);
        $this->searchConditionTextAppService = $this->prophesize(SearchConditionTextAppService::class);
        $this->outputBoundary = $this->prophesize(OutputBoundary::class);

        $this->target = new Interactor(
            $this->rafDao->reveal(),
            $this->rdbRafDao->reveal(),
            $this->productService->reveal(),
            $this->divisionService->reveal(),
            $this->sampleService->reveal(),
            $this->searchConditionTextAppService->reveal(),
            $this->searchConditionTextService->reveal(),
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
            'dataType' => [0],
            'dateRange' => 7,
            'csvFlag' => '1',
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
            'csvFlag' => '1',
        ];

        // mocks
        $this->companyIds = [3];

        $this->data = (object) [
            'division' => 'ga12',
            'codes' => ['personal', 'fc', 'household'],
            'dataType' => [0],
            'axisType' => 0,
            'companyIds' => [3],
            'productIds' => [],
            'regionId' => 1,
            'channels' => [3, 4, 5, 6, 7],
            'channelAxis' => 0,
            'period' => 'day',
            'codeList' => [
                    '0' => (object)
                    [
                        'division' => 'ga12',
                        'code' => 'fc',
                        'division_name' => '性・年齢12区分',
                        'name' => 'FC',
                        'division_order' => 102,
                        'display_order' => 1,
                    ],

                    '1' => (object)
                    [
                        'division' => 'ga12',
                        'code' => 'ft',
                        'division_name' => '性・年齢12区分',
                        'name' => 'FT',
                        'division_order' => 102,
                        'display_order' => 2,
                    ],

                    '2' => (object)
                    [
                        'division' => 'ga12',
                        'code' => 'f1',
                        'division_name' => '性・年齢12区分',
                        'name' => 'F1',
                        'division_order' => 102,
                        'display_order' => 3,
                    ],

                    '3' => (object)
                    [
                        'division' => 'ga12',
                        'code' => 'f2',
                        'division_name' => '性・年齢12区分',
                        'name' => 'F2',
                        'division_order' => 102,
                        'display_order' => 4,
                    ],

                    '4' => (object)
                    [
                        'division' => 'ga12',
                        'code' => 'f3y',
                        'division_name' => '性・年齢12区分',
                        'name' => 'F3-',
                        'division_order' => 102,
                        'display_order' => 5,
                    ],

                    '5' => (object)
                    [
                        'division' => 'ga12',
                        'code' => 'f3o',
                        'division_name' => '性・年齢12区分',
                        'name' => 'F3+',
                        'division_order' => 102,
                        'display_order' => 6,
                    ],

                    '6' => (object)
                    [
                        'division' => 'ga12',
                        'code' => 'mc',
                        'division_name' => '性・年齢12区分',
                        'name' => 'MC',
                        'division_order' => 102,
                        'display_order' => 7,
                    ],

                    '7' => (object)
                    [
                        'division' => 'ga12',
                        'code' => 'mt',
                        'division_name' => '性・年齢12区分',
                        'name' => 'MT',
                        'division_order' => 102,
                        'display_order' => 8,
                    ],

                    '8' => (object)
                    [
                        'division' => 'ga12',
                        'code' => 'm1',
                        'division_name' => '性・年齢12区分',
                        'name' => 'M1',
                        'division_order' => 102,
                        'display_order' => 9,
                    ],

                    '9' => (object)
                    [
                        'division' => 'ga12',
                        'code' => 'm2',
                        'division_name' => '性・年齢12区分',
                        'name' => 'M2',
                        'division_order' => 102,
                        'display_order' => 10,
                    ],

                    '10' => (object)
                    [
                        'division' => 'ga12',
                        'code' => 'm3y',
                        'division_name' => '性・年齢12区分',
                        'name' => 'M3-',
                        'division_order' => 102,
                        'display_order' => 11,
                    ],

                    '11' => (object)
                    [
                        'division' => 'ga12',
                        'code' => 'm3o',
                        'division_name' => '性・年齢12区分',
                        'name' => 'M3+',
                        'division_order' => 102,
                        'display_order' => 12,
                    ],
                ],
            'axisTypeCompany' => '1',
            'axisTypeProduct' => '2',
            'length' => 40000,
        ];

        $this->dataType = [
            'personal' => [
                    'name' => '個人計',
                    'description' => '４才以上男女',
                ],
            'fc' => [
                    'name' => 'FC',
                    'description' => '女性子供',
                ],
            'household' => [
                    'name' => '世帯',
                    'description' => '',
                ],
        ];

        $this->results = [
            '0' => (object)
            [
                'row_number' => 1,
                'date' => '2019-07-17 00:00:00',
                'company_id' => 0,
                'company_name' => '',
                'product_id' => 0,
                'product_name' => '',
                'channel_id' => 0,
                'channel_name' => '',
                'code' => 'household',
                'grp_summary' => 579.2,
                'time_group_1_grp' => 88.7,
                'time_group_2_grp' => 105,
                'time_group_3_grp' => 180.9,
                'time_group_4_grp' => 121.6,
                'time_group_5_grp' => 32.6,
                'time_group_6_grp' => 50.5,
                'freq_1' => 9,
                'freq_2' => 8.6,
                'freq_3' => 5.6,
                'freq_4' => 5.5,
                'freq_5' => 6.6,
                'freq_6' => 3.9,
                'freq_7' => 4,
                'freq_8' => 3.3,
                'freq_9' => 2.9,
                'freq_10' => 2.8,
                'freq_11' => 2,
                'freq_12' => 1.4,
                'freq_13' => 1.1,
                'freq_14' => 0.7,
                'freq_15' => 1.3,
                'freq_16' => 1,
                'freq_17' => 0.7,
                'freq_18' => 0.7,
                'freq_19' => 0.8,
                'freq_20' => 6.4,
                'reach_1' => 68.1,
                'reach_2' => 59.1,
                'reach_3' => 50.5,
                'reach_4' => 44.9,
                'reach_5' => 39.5,
                'reach_6' => 32.9,
                'reach_7' => 29.1,
                'reach_8' => 25.1,
                'reach_9' => 21.8,
                'reach_10' => 18.9,
                'reach_avg' => 8.4,
                'rt_number' => 2178,
                'next' => 'fc',
            ],

            '1' => (object)
            [
                'row_number' => 2,
                'date' => '2019-07-17 00:00:00',
                'company_id' => 0,
                'company_name' => '',
                'product_id' => 0,
                'product_name' => '',
                'channel_id' => 0,
                'channel_name' => '',
                'code' => 'fc',
                'grp_summary' => 297.5,
                'time_group_1_grp' => 47.1,
                'time_group_2_grp' => 52,
                'time_group_3_grp' => 85.3,
                'time_group_4_grp' => 68.1,
                'time_group_5_grp' => 18.3,
                'time_group_6_grp' => 26.6,
                'freq_1' => 7.7,
                'freq_2' => 6.8,
                'freq_3' => 3.9,
                'freq_4' => 4.3,
                'freq_5' => 3.7,
                'freq_6' => 2.7,
                'freq_7' => 2.3,
                'freq_8' => 1.8,
                'freq_9' => 1.5,
                'freq_10' => 1.2,
                'freq_11' => 1,
                'freq_12' => 0.7,
                'freq_13' => 0.6,
                'freq_14' => 0.3,
                'freq_15' => 0.6,
                'freq_16' => 0.4,
                'freq_17' => 0.3,
                'freq_18' => 0.3,
                'freq_19' => 0.3,
                'freq_20' => 2.8,
                'reach_1' => 43.3,
                'reach_2' => 35.6,
                'reach_3' => 28.8,
                'reach_4' => 24.8,
                'reach_5' => 20.6,
                'reach_6' => 16.9,
                'reach_7' => 14.2,
                'reach_8' => 11.9,
                'reach_9' => 10.1,
                'reach_10' => 8.6,
                'reach_avg' => 6.8,
                'rt_number' => 5662,
                'next' => 'personal',
            ],
            '2' => (object)
            [
                'row_number' => 3,
                'date' => '2019-07-17 00:00:00',
                'company_id' => 0,
                'company_name' => '',
                'product_id' => 0,
                'product_name' => '',
                'channel_id' => 0,
                'channel_name' => '',
                'code' => 'personal',
                'grp_summary' => 297.5,
                'time_group_1_grp' => 47.1,
                'time_group_2_grp' => 52,
                'time_group_3_grp' => 85.3,
                'time_group_4_grp' => 68.1,
                'time_group_5_grp' => 18.3,
                'time_group_6_grp' => 26.6,
                'freq_1' => 7.7,
                'freq_2' => 6.8,
                'freq_3' => 3.9,
                'freq_4' => 4.3,
                'freq_5' => 3.7,
                'freq_6' => 2.7,
                'freq_7' => 2.3,
                'freq_8' => 1.8,
                'freq_9' => 1.5,
                'freq_10' => 1.2,
                'freq_11' => 1,
                'freq_12' => 0.7,
                'freq_13' => 0.6,
                'freq_14' => 0.3,
                'freq_15' => 0.6,
                'freq_16' => 0.4,
                'freq_17' => 0.3,
                'freq_18' => 0.3,
                'freq_19' => 0.3,
                'freq_20' => 2.8,
                'reach_1' => 43.3,
                'reach_2' => 35.6,
                'reach_3' => 28.8,
                'reach_4' => 24.8,
                'reach_5' => 20.6,
                'reach_6' => 16.9,
                'reach_7' => 14.2,
                'reach_8' => 11.9,
                'reach_9' => 10.1,
                'reach_10' => 8.6,
                'reach_avg' => 6.8,
                'rt_number' => 5662,
                'next' => '',
            ],
        ];
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

        $this->divisionService->getCodeList($inputData->division(), $inputData->regionId(), $inputData->userId(), $inputData->baseDivision())->shouldBeCalled()->willReturn(['codeList']);

        $searchConditionParams = [
            $inputData->startDate(),
            $inputData->endDate(),
            $inputData->startTimeShort(),
            $inputData->endTimeShort(),
            $inputData->cmType(),
            $inputData->cmSeconds(),
            $inputData->progIds(),
            $inputData->regionId(),
            [3],
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
        $this->searchConditionTextAppService->getRafCsv(...$searchConditionParams)->shouldBeCalled()->willReturn(['header']);

        $this->rafDao->commonCreateTempTables(...array_merge($params, [
            $inputData->axisType(),
            $inputData->channelAxis(),
            $inputData->period(),
            $inputData->dataTypeFlags(),
            $inputData->axisTypeProduct(),
            $inputData->axisTypeCompany(),
        ]))->shouldBeCalled();

        $this->rafDao->createCsvTempTable(...array_merge($params, [
            $inputData->axisType(),
            $inputData->channelAxis(),
            $inputData->period(),
            $inputData->dataTypeFlags(),
            $inputData->axisTypeProduct(),
            $inputData->axisTypeCompany(),
        ]))->shouldBeCalled();

        $data = (object) [
            'division' => $inputData->division(),
            'codes' => $inputData->codes(),
            'dataType' => $inputData->dataType(),
            'axisType' => $inputData->axisType(),
            'companyIds' => $this->companyIds,
            'productIds' => $inputData->productIds(),
            'regionId' => $inputData->regionId(),
            'channels' => $inputData->channels(),
            'channelAxis' => $inputData->channelAxis(),
            'period' => $inputData->period(),
            'codeList' => ['codeList'],
            'axisTypeCompany' => $inputData->axisTypeCompany(),
            'axisTypeProduct' => $inputData->axisTypeProduct(),
            'length' => 40000,
        ];

        $this->outputBoundary->__invoke(new OutputData($inputData->division(), $inputData->startDateShort(), $inputData->endDateShort(), ['header'], [$this->target, 'csvGenerator'], $data))->shouldBeCalled();

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
        $this->divisionService->getCodeList($inputData->division(), $inputData->regionId(), $inputData->userId(), $inputData->baseDivision())->shouldBeCalled()->willReturn(['codeList']);

        $searchConditionParams = [
            $inputData->startDate(),
            $inputData->endDate(),
            $inputData->startTimeShort(),
            $inputData->endTimeShort(),
            $inputData->cmType(),
            $inputData->cmSeconds(),
            $inputData->progIds(),
            $inputData->regionId(),
            [3],
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
        $this->searchConditionTextAppService->getRafCsv(...$searchConditionParams)->shouldBeCalled()->willReturn(['header']);

        $this->rafDao->commonCreateTempTables(...array_merge($params, [
            $inputData->axisType(),
            $inputData->channelAxis(),
            $inputData->period(),
            $inputData->dataTypeFlags(),
            $inputData->axisTypeProduct(),
            $inputData->axisTypeCompany(),
        ]))->shouldBeCalled();

        $this->rafDao->createCsvTempTable(...array_merge($params, [
            $inputData->axisType(),
            $inputData->channelAxis(),
            $inputData->period(),
            $inputData->dataTypeFlags(),
            $inputData->axisTypeProduct(),
            $inputData->axisTypeCompany(),
        ]))->shouldBeCalled();

        $data = (object) [
            'division' => $inputData->division(),
            'codes' => $inputData->codes(),
            'dataType' => $inputData->dataType(),
            'axisType' => $inputData->axisType(),
            'companyIds' => $this->companyIds,
            'productIds' => $inputData->productIds(),
            'regionId' => $inputData->regionId(),
            'channels' => $inputData->channels(),
            'channelAxis' => $inputData->channelAxis(),
            'period' => $inputData->period(),
            'codeList' => ['codeList'],
            'axisTypeCompany' => $inputData->axisTypeCompany(),
            'axisTypeProduct' => $inputData->axisTypeProduct(),
            'length' => 40000,
        ];

        $this->outputBoundary->__invoke(new OutputData($inputData->division(), $inputData->startDateShort(), $inputData->endDateShort(), ['header'], [$this->target, 'csvGenerator'], $data))->shouldBeCalled();

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
        $this->divisionService->getCodeList($inputData->division(), $inputData->regionId(), $inputData->userId(), $inputData->baseDivision())->shouldBeCalled()->willReturn(['codeList']);

        $searchConditionParams = [
            $inputData->startDate(),
            $inputData->endDate(),
            $inputData->startTimeShort(),
            $inputData->endTimeShort(),
            $inputData->cmType(),
            $inputData->cmSeconds(),
            $inputData->progIds(),
            $inputData->regionId(),
            [3],
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
        $this->searchConditionTextAppService->getRafCsv(...$searchConditionParams)->shouldBeCalled()->willReturn(['header']);

        $this->rafDao->commonCreateTempTables(...array_merge($params, [
            $inputData->axisType(),
            $inputData->channelAxis(),
            $inputData->period(),
            $inputData->dataTypeFlags(),
            $inputData->axisTypeProduct(),
            $inputData->axisTypeCompany(),
        ]))->shouldBeCalled();

        $this->rafDao->createCsvTempTable(...array_merge($params, [
            $inputData->axisType(),
            $inputData->channelAxis(),
            $inputData->period(),
            $inputData->dataTypeFlags(),
            $inputData->axisTypeProduct(),
            $inputData->axisTypeCompany(),
        ]))->shouldBeCalled();

        $data = (object) [
            'division' => $inputData->division(),
            'codes' => $inputData->codes(),
            'dataType' => $inputData->dataType(),
            'axisType' => $inputData->axisType(),
            'companyIds' => $this->companyIds,
            'productIds' => $inputData->productIds(),
            'regionId' => $inputData->regionId(),
            'channels' => $inputData->channels(),
            'channelAxis' => $inputData->channelAxis(),
            'period' => $inputData->period(),
            'codeList' => ['codeList'],
            'axisTypeCompany' => $inputData->axisTypeCompany(),
            'axisTypeProduct' => $inputData->axisTypeProduct(),
            'length' => 40000,
        ];

        $this->outputBoundary->__invoke(new OutputData($inputData->division(), $inputData->startDateShort(), $inputData->endDateShort(), ['header'], [$this->target, 'csvGenerator'], $data))->shouldBeCalled();

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
    public function csvGenerator集計軸すべて(): void
    {
        $expect = [
            ['企業名:', '花王'],
            ['商品名:', 'すべて'],
            ['放送:', '全局_'],
            ['', '', '個人計', 'FC', '世帯'],
            ['', '', '４才以上男女', '女4〜12才', ''],
            ['集計期間内有効サンプル数', '', 5662, 5662, 2178],
            ['時間帯別 GRP　(%)', '', '', '', ''],
            ['', '05:00～07:59', 47.1, 47.1, 88.7],
            ['', '08:00～11:59', 52, 52, 105],
            ['', '12:00～17:59', 85.3, 85.3, 180.9],
            ['', '18:00～22:59', 68.1, 68.1, 121.6],
            ['', '23:00～23:59', 18.3, 18.3, 32.6],
            ['', '24:00～28:59', 26.6, 26.6, 50.5],
            ['延視聴率 GRP計　(%)', '', 297.5, 297.5, 579.2],
            ['累積到達率　Reach　(%)', '', 43.3, 43.3, 68.1],
            ['', '1回以上', 43.3, 43.3, 68.1],
            ['', '2回以上', 35.6, 35.6, 59.1],
            ['', '3回以上', 28.8, 28.8, 50.5],
            ['', '4回以上', 24.8, 24.8, 44.9],
            ['', '5回以上', 20.6, 20.6, 39.5],
            ['', '6回以上', 16.9, 16.9, 32.9],
            ['', '7回以上', 14.2, 14.2, 29.1],
            ['', '8回以上', 11.9, 11.9, 25.1],
            ['', '9回以上', 10.1, 10.1, 21.8],
            ['', '10回以上', 8.6, 8.6, 18.9],
            ['平均視聴回数　(回)', '', 6.8, 6.8, 8.4],
            ['視聴の分布　frequency (%)', '', '', '', ''],
            ['', '1回', 7.7, 7.7, 9],
            ['', '2回', 6.8, 6.8, 8.6],
            ['', '3回', 3.9, 3.9, 5.6],
            ['', '4回', 4.3, 4.3, 5.5],
            ['', '5回', 3.7, 3.7, 6.6],
            ['', '6回', 2.7, 2.7, 3.9],
            ['', '7回', 2.3, 2.3, 4],
            ['', '8回', 1.8, 1.8, 3.3],
            ['', '9回', 1.5, 1.5, 2.9],
            ['', '10回', 1.2, 1.2, 2.8],
            ['', '11回', 1, 1, 2],
            ['', '12回', 0.7, 0.7, 1.4],
            ['', '13回', 0.6, 0.6, 1.1],
            ['', '14回', 0.3, 0.3, 0.7],
            ['', '15回', 0.6, 0.6, 1.3],
            ['', '16回', 0.4, 0.4, 1],
            ['', '17回', 0.3, 0.3, 0.7],
            ['', '18回', 0.3, 0.3, 0.7],
            ['', '19回', 0.3, 0.3, 0.8],
            ['', '20回以上', 2.8, 2.8, 6.4],
            [],
            [],
            ['企業名:', '花王'],
            ['商品名:', 'すべて'],
            ['放送:', '全局_'],
            ['集計区分：', '個人計'],
            ['集計条件：', '４才以上男女'],
            ['集計期間内有効サンプル数：', 5662],
            [],
            ['', '検索期間TO', '2019年07月17日（Wed）'],
            ['時間帯別 GRP　(%)', '', ''],
            ['', '05:00～07:59', 47.1],
            ['', '08:00～11:59', 52],
            ['', '12:00～17:59', 85.3],
            ['', '18:00～22:59', 68.1],
            ['', '23:00～23:59', 18.3],
            ['', '24:00～28:59', 26.6],
            ['延視聴率 GRP計　(%)', '', 297.5],
            ['累積到達率　Reach　(%)', '', 43.3],
            ['', '1回以上', 43.3],
            ['', '2回以上', 35.6],
            ['', '3回以上', 28.8],
            ['', '4回以上', 24.8],
            ['', '5回以上', 20.6],
            ['', '6回以上', 16.9],
            ['', '7回以上', 14.2],
            ['', '8回以上', 11.9],
            ['', '9回以上', 10.1],
            ['', '10回以上', 8.6],
            ['平均視聴回数　(回)', '', 6.8],
            ['視聴の分布　frequency (%)', '', ''],
            ['', '1回', 7.7],
            ['', '2回', 6.8],
            ['', '3回', 3.9],
            ['', '4回', 4.3],
            ['', '5回', 3.7],
            ['', '6回', 2.7],
            ['', '7回', 2.3],
            ['', '8回', 1.8],
            ['', '9回', 1.5],
            ['', '10回', 1.2],
            ['', '11回', 1],
            ['', '12回', 0.7],
            ['', '13回', 0.6],
            ['', '14回', 0.3],
            ['', '15回', 0.6],
            ['', '16回', 0.4],
            ['', '17回', 0.3],
            ['', '18回', 0.3],
            ['', '19回', 0.3],
            ['', '20回以上', 2.8],
            [],
            [],
            ['企業名:', '花王'],
            ['商品名:', 'すべて'],
            ['放送:', '全局_'],
            ['集計区分：', 'FC'],
            ['集計条件：', '女4〜12才'],
            ['集計期間内有効サンプル数：', 5662],
            [],
            ['', '検索期間TO', '2019年07月17日（Wed）'],
            ['時間帯別 GRP　(%)', '', ''],
            ['', '05:00～07:59', 47.1],
            ['', '08:00～11:59', 52],
            ['', '12:00～17:59', 85.3],
            ['', '18:00～22:59', 68.1],
            ['', '23:00～23:59', 18.3],
            ['', '24:00～28:59', 26.6],
            ['延視聴率 GRP計　(%)', '', 297.5],
            ['累積到達率　Reach　(%)', '', 43.3],
            ['', '1回以上', 43.3],
            ['', '2回以上', 35.6],
            ['', '3回以上', 28.8],
            ['', '4回以上', 24.8],
            ['', '5回以上', 20.6],
            ['', '6回以上', 16.9],
            ['', '7回以上', 14.2],
            ['', '8回以上', 11.9],
            ['', '9回以上', 10.1],
            ['', '10回以上', 8.6],
            ['平均視聴回数　(回)', '', 6.8],
            ['視聴の分布　frequency (%)', '', ''],
            ['', '1回', 7.7],
            ['', '2回', 6.8],
            ['', '3回', 3.9],
            ['', '4回', 4.3],
            ['', '5回', 3.7],
            ['', '6回', 2.7],
            ['', '7回', 2.3],
            ['', '8回', 1.8],
            ['', '9回', 1.5],
            ['', '10回', 1.2],
            ['', '11回', 1],
            ['', '12回', 0.7],
            ['', '13回', 0.6],
            ['', '14回', 0.3],
            ['', '15回', 0.6],
            ['', '16回', 0.4],
            ['', '17回', 0.3],
            ['', '18回', 0.3],
            ['', '19回', 0.3],
            ['', '20回以上', 2.8],
            [],
            [],
            ['企業名:', '花王'],
            ['商品名:', 'すべて'],
            ['放送:', '全局_'],
            ['集計区分：', '世帯'],
            ['集計条件：', ''],
            ['集計期間内有効サンプル数：', 2178],
            [],
            ['', '検索期間TO', '2019年07月17日（Wed）'],
            ['時間帯別 GRP　(%)', '', ''],
            ['', '05:00～07:59', 88.7],
            ['', '08:00～11:59', 105],
            ['', '12:00～17:59', 180.9],
            ['', '18:00～22:59', 121.6],
            ['', '23:00～23:59', 32.6],
            ['', '24:00～28:59', 50.5],
            ['延視聴率 GRP計　(%)', '', 579.2],
            ['累積到達率　Reach　(%)', '', 68.1],
            ['', '1回以上', 68.1],
            ['', '2回以上', 59.1],
            ['', '3回以上', 50.5],
            ['', '4回以上', 44.9],
            ['', '5回以上', 39.5],
            ['', '6回以上', 32.9],
            ['', '7回以上', 29.1],
            ['', '8回以上', 25.1],
            ['', '9回以上', 21.8],
            ['', '10回以上', 18.9],
            ['平均視聴回数　(回)', '', 8.4],
            ['視聴の分布　frequency (%)', '', ''],
            ['', '1回', 9],
            ['', '2回', 8.6],
            ['', '3回', 5.6],
            ['', '4回', 5.5],
            ['', '5回', 6.6],
            ['', '6回', 3.9],
            ['', '7回', 4],
            ['', '8回', 3.3],
            ['', '9回', 2.9],
            ['', '10回', 2.8],
            ['', '11回', 2],
            ['', '12回', 1.4],
            ['', '13回', 1.1],
            ['', '14回', 0.7],
            ['', '15回', 1.3],
            ['', '16回', 1],
            ['', '17回', 0.7],
            ['', '18回', 0.7],
            ['', '19回', 0.8],
            ['', '20回以上', 6.4],
            [],
            [],
        ];

        $this->searchConditionTextService->convertDataDivisionText($this->data->dataType)->willReturn($this->dataType)->shouldBeCalled();
        $this->rafDao->selectTempResultsForCsv(40000, 0)->willReturn($this->results)->shouldBeCalled();
        $this->searchConditionTextService->convertCompanyNames([3])->willReturn(['企業名:', '花王'])->shouldBeCalled();
        $this->searchConditionTextService->convertProductNames([])->willReturn(['商品名:', 'すべて'])->shouldBeCalled();
        $this->searchConditionTextService->convertChannels([3, 4, 5, 6, 7], 1)->willReturn(['放送:', '全局'])->shouldBeCalled();
        $ret = [];

        foreach ($this->target->csvGenerator($this->data) as $body) {
            $ret = array_merge($ret, $body);
        }
        $this->assertSame($expect, $ret);
    }

    /**
     * @test
     */
    public function csvGenerator企業別(): void
    {
        $expect = [
            ['企業名:', '花王'],
            ['商品名:', '指定なし'],
            ['放送:', '全局_'],
            ['', '', '個人計', 'FC', '世帯'],
            ['', '', '４才以上男女', 'FC', ''],
            ['集計期間内有効サンプル数', '', 5662, 5662, 2178],
            ['時間帯別 GRP　(%)', '', '', '', ''],
            ['', '05:00～07:59', 47.1, 47.1, 88.7],
            ['', '08:00～11:59', 52, 52, 105],
            ['', '12:00～17:59', 85.3, 85.3, 180.9],
            ['', '18:00～22:59', 68.1, 68.1, 121.6],
            ['', '23:00～23:59', 18.3, 18.3, 32.6],
            ['', '24:00～28:59', 26.6, 26.6, 50.5],
            ['延視聴率 GRP計　(%)', '', 297.5, 297.5, 579.2],
            ['累積到達率　Reach　(%)', '', 43.3, 43.3, 68.1],
            ['', '1回以上', 43.3, 43.3, 68.1],
            ['', '2回以上', 35.6, 35.6, 59.1],
            ['', '3回以上', 28.8, 28.8, 50.5],
            ['', '4回以上', 24.8, 24.8, 44.9],
            ['', '5回以上', 20.6, 20.6, 39.5],
            ['', '6回以上', 16.9, 16.9, 32.9],
            ['', '7回以上', 14.2, 14.2, 29.1],
            ['', '8回以上', 11.9, 11.9, 25.1],
            ['', '9回以上', 10.1, 10.1, 21.8],
            ['', '10回以上', 8.6, 8.6, 18.9],
            ['平均視聴回数　(回)', '', 6.8, 6.8, 8.4],
            ['視聴の分布　frequency (%)', '', '', '', ''],
            ['', '1回', 7.7, 7.7, 9],
            ['', '2回', 6.8, 6.8, 8.6],
            ['', '3回', 3.9, 3.9, 5.6],
            ['', '4回', 4.3, 4.3, 5.5],
            ['', '5回', 3.7, 3.7, 6.6],
            ['', '6回', 2.7, 2.7, 3.9],
            ['', '7回', 2.3, 2.3, 4],
            ['', '8回', 1.8, 1.8, 3.3],
            ['', '9回', 1.5, 1.5, 2.9],
            ['', '10回', 1.2, 1.2, 2.8],
            ['', '11回', 1, 1, 2],
            ['', '12回', 0.7, 0.7, 1.4],
            ['', '13回', 0.6, 0.6, 1.1],
            ['', '14回', 0.3, 0.3, 0.7],
            ['', '15回', 0.6, 0.6, 1.3],
            ['', '16回', 0.4, 0.4, 1],
            ['', '17回', 0.3, 0.3, 0.7],
            ['', '18回', 0.3, 0.3, 0.7],
            ['', '19回', 0.3, 0.3, 0.8],
            ['', '20回以上', 2.8, 2.8, 6.4],
            [],
            [],
            ['企業名:', '花王'],
            ['商品名:', '指定なし'],
            ['放送:', '全局_'],
            ['集計区分：', '個人計'],
            ['集計条件：', '４才以上男女'],
            ['集計期間内有効サンプル数：', 5662],
            [],
            ['', '検索期間TO', '2019年07月17日（Wed）'],
            ['時間帯別 GRP　(%)', '', ''],
            ['', '05:00～07:59', 47.1],
            ['', '08:00～11:59', 52],
            ['', '12:00～17:59', 85.3],
            ['', '18:00～22:59', 68.1],
            ['', '23:00～23:59', 18.3],
            ['', '24:00～28:59', 26.6],
            ['延視聴率 GRP計　(%)', '', 297.5],
            ['累積到達率　Reach　(%)', '', 43.3],
            ['', '1回以上', 43.3],
            ['', '2回以上', 35.6],
            ['', '3回以上', 28.8],
            ['', '4回以上', 24.8],
            ['', '5回以上', 20.6],
            ['', '6回以上', 16.9],
            ['', '7回以上', 14.2],
            ['', '8回以上', 11.9],
            ['', '9回以上', 10.1],
            ['', '10回以上', 8.6],
            ['平均視聴回数　(回)', '', 6.8],
            ['視聴の分布　frequency (%)', '', ''],
            ['', '1回', 7.7],
            ['', '2回', 6.8],
            ['', '3回', 3.9],
            ['', '4回', 4.3],
            ['', '5回', 3.7],
            ['', '6回', 2.7],
            ['', '7回', 2.3],
            ['', '8回', 1.8],
            ['', '9回', 1.5],
            ['', '10回', 1.2],
            ['', '11回', 1],
            ['', '12回', 0.7],
            ['', '13回', 0.6],
            ['', '14回', 0.3],
            ['', '15回', 0.6],
            ['', '16回', 0.4],
            ['', '17回', 0.3],
            ['', '18回', 0.3],
            ['', '19回', 0.3],
            ['', '20回以上', 2.8],
            [],
            [],
            ['企業名:', '花王'],
            ['商品名:', '指定なし'],
            ['放送:', '全局_'],
            ['集計区分：', 'FC'],
            ['集計条件：', 'FC'],
            ['集計期間内有効サンプル数：', 5662],
            [],
            ['', '検索期間TO', '2019年07月17日（Wed）'],
            ['時間帯別 GRP　(%)', '', ''],
            ['', '05:00～07:59', 47.1],
            ['', '08:00～11:59', 52],
            ['', '12:00～17:59', 85.3],
            ['', '18:00～22:59', 68.1],
            ['', '23:00～23:59', 18.3],
            ['', '24:00～28:59', 26.6],
            ['延視聴率 GRP計　(%)', '', 297.5],
            ['累積到達率　Reach　(%)', '', 43.3],
            ['', '1回以上', 43.3],
            ['', '2回以上', 35.6],
            ['', '3回以上', 28.8],
            ['', '4回以上', 24.8],
            ['', '5回以上', 20.6],
            ['', '6回以上', 16.9],
            ['', '7回以上', 14.2],
            ['', '8回以上', 11.9],
            ['', '9回以上', 10.1],
            ['', '10回以上', 8.6],
            ['平均視聴回数　(回)', '', 6.8],
            ['視聴の分布　frequency (%)', '', ''],
            ['', '1回', 7.7],
            ['', '2回', 6.8],
            ['', '3回', 3.9],
            ['', '4回', 4.3],
            ['', '5回', 3.7],
            ['', '6回', 2.7],
            ['', '7回', 2.3],
            ['', '8回', 1.8],
            ['', '9回', 1.5],
            ['', '10回', 1.2],
            ['', '11回', 1],
            ['', '12回', 0.7],
            ['', '13回', 0.6],
            ['', '14回', 0.3],
            ['', '15回', 0.6],
            ['', '16回', 0.4],
            ['', '17回', 0.3],
            ['', '18回', 0.3],
            ['', '19回', 0.3],
            ['', '20回以上', 2.8],
            [],
            [],
            ['企業名:', '花王'],
            ['商品名:', '指定なし'],
            ['放送:', '全局_'],
            ['集計区分：', '世帯'],
            ['集計条件：', ''],
            ['集計期間内有効サンプル数：', 2178],
            [],
            ['', '検索期間TO', '2019年07月17日（Wed）'],
            ['時間帯別 GRP　(%)', '', ''],
            ['', '05:00～07:59', 88.7],
            ['', '08:00～11:59', 105],
            ['', '12:00～17:59', 180.9],
            ['', '18:00～22:59', 121.6],
            ['', '23:00～23:59', 32.6],
            ['', '24:00～28:59', 50.5],
            ['延視聴率 GRP計　(%)', '', 579.2],
            ['累積到達率　Reach　(%)', '', 68.1],
            ['', '1回以上', 68.1],
            ['', '2回以上', 59.1],
            ['', '3回以上', 50.5],
            ['', '4回以上', 44.9],
            ['', '5回以上', 39.5],
            ['', '6回以上', 32.9],
            ['', '7回以上', 29.1],
            ['', '8回以上', 25.1],
            ['', '9回以上', 21.8],
            ['', '10回以上', 18.9],
            ['平均視聴回数　(回)', '', 8.4],
            ['視聴の分布　frequency (%)', '', ''],
            ['', '1回', 9],
            ['', '2回', 8.6],
            ['', '3回', 5.6],
            ['', '4回', 5.5],
            ['', '5回', 6.6],
            ['', '6回', 3.9],
            ['', '7回', 4],
            ['', '8回', 3.3],
            ['', '9回', 2.9],
            ['', '10回', 2.8],
            ['', '11回', 2],
            ['', '12回', 1.4],
            ['', '13回', 1.1],
            ['', '14回', 0.7],
            ['', '15回', 1.3],
            ['', '16回', 1],
            ['', '17回', 0.7],
            ['', '18回', 0.7],
            ['', '19回', 0.8],
            ['', '20回以上', 6.4],
            [],
            [],
        ];

        $this->data->division = 'oc';
        $this->results[0]->company_id = 3;
        $this->results[0]->company_name = '花王';
        $this->results[1]->company_id = 3;
        $this->results[1]->company_name = '花王';
        $this->results[2]->company_id = 3;
        $this->results[2]->company_name = '花王';

        $this->data->axisType = '1';
        $this->searchConditionTextService->convertDataDivisionText($this->data->dataType)->willReturn($this->dataType)->shouldBeCalled();
        $this->rafDao->selectTempResultsForCsv(40000, 0)->willReturn($this->results)->shouldBeCalled();
        $this->searchConditionTextService->convertChannels([3, 4, 5, 6, 7], 1)->willReturn(['放送:', '全局'])->shouldBeCalled();
        $ret = [];

        foreach ($this->target->csvGenerator($this->data) as $body) {
            $ret = array_merge($ret, $body);
        }
        $this->assertSame($expect, $ret);
    }

    /**
     * @test
     */
    public function csvGenerator集計軸商品別(): void
    {
        $expect = [
            ['企業名:', '花王'],
            ['商品名:', 'キッチン泡ハイター'],
            ['放送:', '全局_'],
            ['', '', '個人計', 'FC', '世帯'],
            ['', '', '４才以上男女', '女4〜12才', ''],
            ['集計期間内有効サンプル数', '', 5662, 5662, 2178],
            ['時間帯別 GRP　(%)', '', '', '', ''],
            ['', '05:00～07:59', 47.1, 47.1, 88.7],
            ['', '08:00～11:59', 52, 52, 105],
            ['', '12:00～17:59', 85.3, 85.3, 180.9],
            ['', '18:00～22:59', 68.1, 68.1, 121.6],
            ['', '23:00～23:59', 18.3, 18.3, 32.6],
            ['', '24:00～28:59', 26.6, 26.6, 50.5],
            ['延視聴率 GRP計　(%)', '', 297.5, 297.5, 579.2],
            ['累積到達率　Reach　(%)', '', 43.3, 43.3, 68.1],
            ['', '1回以上', 43.3, 43.3, 68.1],
            ['', '2回以上', 35.6, 35.6, 59.1],
            ['', '3回以上', 28.8, 28.8, 50.5],
            ['', '4回以上', 24.8, 24.8, 44.9],
            ['', '5回以上', 20.6, 20.6, 39.5],
            ['', '6回以上', 16.9, 16.9, 32.9],
            ['', '7回以上', 14.2, 14.2, 29.1],
            ['', '8回以上', 11.9, 11.9, 25.1],
            ['', '9回以上', 10.1, 10.1, 21.8],
            ['', '10回以上', 8.6, 8.6, 18.9],
            ['平均視聴回数　(回)', '', 6.8, 6.8, 8.4],
            ['視聴の分布　frequency (%)', '', '', '', ''],
            ['', '1回', 7.7, 7.7, 9],
            ['', '2回', 6.8, 6.8, 8.6],
            ['', '3回', 3.9, 3.9, 5.6],
            ['', '4回', 4.3, 4.3, 5.5],
            ['', '5回', 3.7, 3.7, 6.6],
            ['', '6回', 2.7, 2.7, 3.9],
            ['', '7回', 2.3, 2.3, 4],
            ['', '8回', 1.8, 1.8, 3.3],
            ['', '9回', 1.5, 1.5, 2.9],
            ['', '10回', 1.2, 1.2, 2.8],
            ['', '11回', 1, 1, 2],
            ['', '12回', 0.7, 0.7, 1.4],
            ['', '13回', 0.6, 0.6, 1.1],
            ['', '14回', 0.3, 0.3, 0.7],
            ['', '15回', 0.6, 0.6, 1.3],
            ['', '16回', 0.4, 0.4, 1],
            ['', '17回', 0.3, 0.3, 0.7],
            ['', '18回', 0.3, 0.3, 0.7],
            ['', '19回', 0.3, 0.3, 0.8],
            ['', '20回以上', 2.8, 2.8, 6.4],
            [],
            [],
            ['企業名:', '花王'],
            ['商品名:', 'キッチン泡ハイター'],
            ['放送:', '全局_'],
            ['集計区分：', '個人計'],
            ['集計条件：', '４才以上男女'],
            ['集計期間内有効サンプル数：', 5662],
            [],
            ['', '検索期間TO', '2019年07月17日（Wed）'],
            ['時間帯別 GRP　(%)', '', ''],
            ['', '05:00～07:59', 47.1],
            ['', '08:00～11:59', 52],
            ['', '12:00～17:59', 85.3],
            ['', '18:00～22:59', 68.1],
            ['', '23:00～23:59', 18.3],
            ['', '24:00～28:59', 26.6],
            ['延視聴率 GRP計　(%)', '', 297.5],
            ['累積到達率　Reach　(%)', '', 43.3],
            ['', '1回以上', 43.3],
            ['', '2回以上', 35.6],
            ['', '3回以上', 28.8],
            ['', '4回以上', 24.8],
            ['', '5回以上', 20.6],
            ['', '6回以上', 16.9],
            ['', '7回以上', 14.2],
            ['', '8回以上', 11.9],
            ['', '9回以上', 10.1],
            ['', '10回以上', 8.6],
            ['平均視聴回数　(回)', '', 6.8],
            ['視聴の分布　frequency (%)', '', ''],
            ['', '1回', 7.7],
            ['', '2回', 6.8],
            ['', '3回', 3.9],
            ['', '4回', 4.3],
            ['', '5回', 3.7],
            ['', '6回', 2.7],
            ['', '7回', 2.3],
            ['', '8回', 1.8],
            ['', '9回', 1.5],
            ['', '10回', 1.2],
            ['', '11回', 1],
            ['', '12回', 0.7],
            ['', '13回', 0.6],
            ['', '14回', 0.3],
            ['', '15回', 0.6],
            ['', '16回', 0.4],
            ['', '17回', 0.3],
            ['', '18回', 0.3],
            ['', '19回', 0.3],
            ['', '20回以上', 2.8],
            [],
            [],
            ['企業名:', '花王'],
            ['商品名:', 'キッチン泡ハイター'],
            ['放送:', '全局_'],
            ['集計区分：', 'FC'],
            ['集計条件：', '女4〜12才'],
            ['集計期間内有効サンプル数：', 5662],
            [],
            ['', '検索期間TO', '2019年07月17日（Wed）'],
            ['時間帯別 GRP　(%)', '', ''],
            ['', '05:00～07:59', 47.1],
            ['', '08:00～11:59', 52],
            ['', '12:00～17:59', 85.3],
            ['', '18:00～22:59', 68.1],
            ['', '23:00～23:59', 18.3],
            ['', '24:00～28:59', 26.6],
            ['延視聴率 GRP計　(%)', '', 297.5],
            ['累積到達率　Reach　(%)', '', 43.3],
            ['', '1回以上', 43.3],
            ['', '2回以上', 35.6],
            ['', '3回以上', 28.8],
            ['', '4回以上', 24.8],
            ['', '5回以上', 20.6],
            ['', '6回以上', 16.9],
            ['', '7回以上', 14.2],
            ['', '8回以上', 11.9],
            ['', '9回以上', 10.1],
            ['', '10回以上', 8.6],
            ['平均視聴回数　(回)', '', 6.8],
            ['視聴の分布　frequency (%)', '', ''],
            ['', '1回', 7.7],
            ['', '2回', 6.8],
            ['', '3回', 3.9],
            ['', '4回', 4.3],
            ['', '5回', 3.7],
            ['', '6回', 2.7],
            ['', '7回', 2.3],
            ['', '8回', 1.8],
            ['', '9回', 1.5],
            ['', '10回', 1.2],
            ['', '11回', 1],
            ['', '12回', 0.7],
            ['', '13回', 0.6],
            ['', '14回', 0.3],
            ['', '15回', 0.6],
            ['', '16回', 0.4],
            ['', '17回', 0.3],
            ['', '18回', 0.3],
            ['', '19回', 0.3],
            ['', '20回以上', 2.8],
            [],
            [],
            ['企業名:', '花王'],
            ['商品名:', 'キッチン泡ハイター'],
            ['放送:', '全局_'],
            ['集計区分：', '世帯'],
            ['集計条件：', ''],
            ['集計期間内有効サンプル数：', 2178],
            [],
            ['', '検索期間TO', '2019年07月17日（Wed）'],
            ['時間帯別 GRP　(%)', '', ''],
            ['', '05:00～07:59', 88.7],
            ['', '08:00～11:59', 105],
            ['', '12:00～17:59', 180.9],
            ['', '18:00～22:59', 121.6],
            ['', '23:00～23:59', 32.6],
            ['', '24:00～28:59', 50.5],
            ['延視聴率 GRP計　(%)', '', 579.2],
            ['累積到達率　Reach　(%)', '', 68.1],
            ['', '1回以上', 68.1],
            ['', '2回以上', 59.1],
            ['', '3回以上', 50.5],
            ['', '4回以上', 44.9],
            ['', '5回以上', 39.5],
            ['', '6回以上', 32.9],
            ['', '7回以上', 29.1],
            ['', '8回以上', 25.1],
            ['', '9回以上', 21.8],
            ['', '10回以上', 18.9],
            ['平均視聴回数　(回)', '', 8.4],
            ['視聴の分布　frequency (%)', '', ''],
            ['', '1回', 9],
            ['', '2回', 8.6],
            ['', '3回', 5.6],
            ['', '4回', 5.5],
            ['', '5回', 6.6],
            ['', '6回', 3.9],
            ['', '7回', 4],
            ['', '8回', 3.3],
            ['', '9回', 2.9],
            ['', '10回', 2.8],
            ['', '11回', 2],
            ['', '12回', 1.4],
            ['', '13回', 1.1],
            ['', '14回', 0.7],
            ['', '15回', 1.3],
            ['', '16回', 1],
            ['', '17回', 0.7],
            ['', '18回', 0.7],
            ['', '19回', 0.8],
            ['', '20回以上', 6.4],
            [],
            [],
        ];

        $this->results[0]->company_id = 3;
        $this->results[0]->company_name = '花王';
        $this->results[0]->product_id = 222;
        $this->results[0]->product_name = 'キッチン泡ハイター';
        $this->results[1]->company_id = 3;
        $this->results[1]->company_name = '花王';
        $this->results[1]->product_id = 222;
        $this->results[1]->product_name = 'キッチン泡ハイター';
        $this->results[2]->company_id = 3;
        $this->results[2]->company_name = '花王';
        $this->results[2]->product_id = 222;
        $this->results[2]->product_name = 'キッチン泡ハイター';

        $this->data->axisType = '2';
        $this->data->productIds = [222];
        $this->searchConditionTextService->convertDataDivisionText($this->data->dataType)->willReturn($this->dataType)->shouldBeCalled();
        $this->rafDao->selectTempResultsForCsv(40000, 0)->willReturn($this->results)->shouldBeCalled();
        $this->searchConditionTextService->convertChannels([3, 4, 5, 6, 7], 1)->willReturn(['放送:', '全局'])->shouldBeCalled();
        $ret = [];

        foreach ($this->target->csvGenerator($this->data) as $body) {
            $ret = array_merge($ret, $body);
        }
        $this->assertSame($expect, $ret);
    }

    /**
     * @test
     */
    public function csvGenerator集計軸放送局別(): void
    {
        $expect = [
            ['企業名:', '花王'],
            ['商品名:', '指定なし'],
            ['放送:', '日本テレビ_リアルタイム'],
            ['', '', '個人計'],
            ['', '', '４才以上男女'],
            ['集計期間内有効サンプル数', '', 5662],
            ['時間帯別 GRP　(%)', '', ''],
            ['', '05:00～07:59', 47.1],
            ['', '08:00～11:59', 52],
            ['', '12:00～17:59', 85.3],
            ['', '18:00～22:59', 68.1],
            ['', '23:00～23:59', 18.3],
            ['', '24:00～28:59', 26.6],
            ['延視聴率 GRP計　(%)', '', 297.5],
            ['累積到達率　Reach　(%)', '', 43.3],
            ['', '1回以上', 43.3],
            ['', '2回以上', 35.6],
            ['', '3回以上', 28.8],
            ['', '4回以上', 24.8],
            ['', '5回以上', 20.6],
            ['', '6回以上', 16.9],
            ['', '7回以上', 14.2],
            ['', '8回以上', 11.9],
            ['', '9回以上', 10.1],
            ['', '10回以上', 8.6],
            ['平均視聴回数　(回)', '', 6.8],
            ['視聴の分布　frequency (%)', '', ''],
            ['', '1回', 7.7],
            ['', '2回', 6.8],
            ['', '3回', 3.9],
            ['', '4回', 4.3],
            ['', '5回', 3.7],
            ['', '6回', 2.7],
            ['', '7回', 2.3],
            ['', '8回', 1.8],
            ['', '9回', 1.5],
            ['', '10回', 1.2],
            ['', '11回', 1],
            ['', '12回', 0.7],
            ['', '13回', 0.6],
            ['', '14回', 0.3],
            ['', '15回', 0.6],
            ['', '16回', 0.4],
            ['', '17回', 0.3],
            ['', '18回', 0.3],
            ['', '19回', 0.3],
            ['', '20回以上', 2.8],
            [],
            [],
            ['企業名:', '花王'],
            ['商品名:', '指定なし'],
            ['放送:', '日本テレビ_リアルタイム'],
            ['集計区分：', '個人計'],
            ['集計条件：', '４才以上男女'],
            ['集計期間内有効サンプル数：', 5662],
            [],
            ['', '検索期間TO', '2019年07月17週'],
            ['時間帯別 GRP　(%)', '', ''],
            ['', '05:00～07:59', 47.1],
            ['', '08:00～11:59', 52],
            ['', '12:00～17:59', 85.3],
            ['', '18:00～22:59', 68.1],
            ['', '23:00～23:59', 18.3],
            ['', '24:00～28:59', 26.6],
            ['延視聴率 GRP計　(%)', '', 297.5],
            ['累積到達率　Reach　(%)', '', 43.3],
            ['', '1回以上', 43.3],
            ['', '2回以上', 35.6],
            ['', '3回以上', 28.8],
            ['', '4回以上', 24.8],
            ['', '5回以上', 20.6],
            ['', '6回以上', 16.9],
            ['', '7回以上', 14.2],
            ['', '8回以上', 11.9],
            ['', '9回以上', 10.1],
            ['', '10回以上', 8.6],
            ['平均視聴回数　(回)', '', 6.8],
            ['視聴の分布　frequency (%)', '', ''],
            ['', '1回', 7.7],
            ['', '2回', 6.8],
            ['', '3回', 3.9],
            ['', '4回', 4.3],
            ['', '5回', 3.7],
            ['', '6回', 2.7],
            ['', '7回', 2.3],
            ['', '8回', 1.8],
            ['', '9回', 1.5],
            ['', '10回', 1.2],
            ['', '11回', 1],
            ['', '12回', 0.7],
            ['', '13回', 0.6],
            ['', '14回', 0.3],
            ['', '15回', 0.6],
            ['', '16回', 0.4],
            ['', '17回', 0.3],
            ['', '18回', 0.3],
            ['', '19回', 0.3],
            ['', '20回以上', 2.8],
            [],
            [],
            ['企業名:', '花王'],
            ['商品名:', '指定なし'],
            ['放送:', '朝日テレビ_リアルタイム'],
            ['', '', '個人計'],
            ['', '', '４才以上男女'],
            ['集計期間内有効サンプル数', '', 5662],
            ['時間帯別 GRP　(%)', '', ''],
            ['', '05:00～07:59', 47.1],
            ['', '08:00～11:59', 52],
            ['', '12:00～17:59', 85.3],
            ['', '18:00～22:59', 68.1],
            ['', '23:00～23:59', 18.3],
            ['', '24:00～28:59', 26.6],
            ['延視聴率 GRP計　(%)', '', 297.5],
            ['累積到達率　Reach　(%)', '', 60.3],
            ['', '1回以上', 60.3],
            ['', '2回以上', 35.6],
            ['', '3回以上', 28.8],
            ['', '4回以上', 24.8],
            ['', '5回以上', 20.6],
            ['', '6回以上', 16.9],
            ['', '7回以上', 14.2],
            ['', '8回以上', 11.9],
            ['', '9回以上', 10.1],
            ['', '10回以上', 8.6],
            ['平均視聴回数　(回)', '', 6.8],
            ['視聴の分布　frequency (%)', '', ''],
            ['', '1回', 7.7],
            ['', '2回', 6.8],
            ['', '3回', 3.9],
            ['', '4回', 4.3],
            ['', '5回', 3.7],
            ['', '6回', 2.7],
            ['', '7回', 2.3],
            ['', '8回', 1.8],
            ['', '9回', 1.5],
            ['', '10回', 1.2],
            ['', '11回', 1],
            ['', '12回', 0.7],
            ['', '13回', 0.6],
            ['', '14回', 0.3],
            ['', '15回', 0.6],
            ['', '16回', 0.4],
            ['', '17回', 0.3],
            ['', '18回', 0.3],
            ['', '19回', 0.3],
            ['', '20回以上', 2.8],
            [],
            [],
            ['企業名:', '花王'],
            ['商品名:', '指定なし'],
            ['放送:', '朝日テレビ_リアルタイム'],
            ['集計区分：', '個人計'],
            ['集計条件：', '４才以上男女'],
            ['集計期間内有効サンプル数：', 5662],
            [],
            ['', '検索期間TO', '2019年07月17週'],
            ['時間帯別 GRP　(%)', '', ''],
            ['', '05:00～07:59', 47.1],
            ['', '08:00～11:59', 52],
            ['', '12:00～17:59', 85.3],
            ['', '18:00～22:59', 68.1],
            ['', '23:00～23:59', 18.3],
            ['', '24:00～28:59', 26.6],
            ['延視聴率 GRP計　(%)', '', 297.5],
            ['累積到達率　Reach　(%)', '', 60.3],
            ['', '1回以上', 60.3],
            ['', '2回以上', 35.6],
            ['', '3回以上', 28.8],
            ['', '4回以上', 24.8],
            ['', '5回以上', 20.6],
            ['', '6回以上', 16.9],
            ['', '7回以上', 14.2],
            ['', '8回以上', 11.9],
            ['', '9回以上', 10.1],
            ['', '10回以上', 8.6],
            ['平均視聴回数　(回)', '', 6.8],
            ['視聴の分布　frequency (%)', '', ''],
            ['', '1回', 7.7],
            ['', '2回', 6.8],
            ['', '3回', 3.9],
            ['', '4回', 4.3],
            ['', '5回', 3.7],
            ['', '6回', 2.7],
            ['', '7回', 2.3],
            ['', '8回', 1.8],
            ['', '9回', 1.5],
            ['', '10回', 1.2],
            ['', '11回', 1],
            ['', '12回', 0.7],
            ['', '13回', 0.6],
            ['', '14回', 0.3],
            ['', '15回', 0.6],
            ['', '16回', 0.4],
            ['', '17回', 0.3],
            ['', '18回', 0.3],
            ['', '19回', 0.3],
            ['', '20回以上', 2.8],
            [],
            [],
        ];

        $results = [
            '0' => (object)
            [
                'row_number' => 1,
                'date' => '2019-07-17 00:00:00',
                'company_id' => 0,
                'company_name' => '',
                'product_id' => 0,
                'product_name' => '',
                'channel_id' => 3,
                'channel_name' => '日本テレビ',
                'code' => 'personal',
                'grp_summary' => 297.5,
                'time_group_1_grp' => 47.1,
                'time_group_2_grp' => 52,
                'time_group_3_grp' => 85.3,
                'time_group_4_grp' => 68.1,
                'time_group_5_grp' => 18.3,
                'time_group_6_grp' => 26.6,
                'freq_1' => 7.7,
                'freq_2' => 6.8,
                'freq_3' => 3.9,
                'freq_4' => 4.3,
                'freq_5' => 3.7,
                'freq_6' => 2.7,
                'freq_7' => 2.3,
                'freq_8' => 1.8,
                'freq_9' => 1.5,
                'freq_10' => 1.2,
                'freq_11' => 1,
                'freq_12' => 0.7,
                'freq_13' => 0.6,
                'freq_14' => 0.3,
                'freq_15' => 0.6,
                'freq_16' => 0.4,
                'freq_17' => 0.3,
                'freq_18' => 0.3,
                'freq_19' => 0.3,
                'freq_20' => 2.8,
                'reach_1' => 43.3,
                'reach_2' => 35.6,
                'reach_3' => 28.8,
                'reach_4' => 24.8,
                'reach_5' => 20.6,
                'reach_6' => 16.9,
                'reach_7' => 14.2,
                'reach_8' => 11.9,
                'reach_9' => 10.1,
                'reach_10' => 8.6,
                'reach_avg' => 6.8,
                'rt_number' => 5662,
                'next' => '4',
            ],
            '1' => (object)
            [
                'row_number' => 1,
                'date' => '2019-07-17 00:00:00',
                'company_id' => 0,
                'company_name' => '',
                'product_id' => 0,
                'product_name' => '',
                'channel_id' => 4,
                'channel_name' => '朝日テレビ',
                'code' => 'personal',
                'grp_summary' => 297.5,
                'time_group_1_grp' => 47.1,
                'time_group_2_grp' => 52,
                'time_group_3_grp' => 85.3,
                'time_group_4_grp' => 68.1,
                'time_group_5_grp' => 18.3,
                'time_group_6_grp' => 26.6,
                'freq_1' => 7.7,
                'freq_2' => 6.8,
                'freq_3' => 3.9,
                'freq_4' => 4.3,
                'freq_5' => 3.7,
                'freq_6' => 2.7,
                'freq_7' => 2.3,
                'freq_8' => 1.8,
                'freq_9' => 1.5,
                'freq_10' => 1.2,
                'freq_11' => 1,
                'freq_12' => 0.7,
                'freq_13' => 0.6,
                'freq_14' => 0.3,
                'freq_15' => 0.6,
                'freq_16' => 0.4,
                'freq_17' => 0.3,
                'freq_18' => 0.3,
                'freq_19' => 0.3,
                'freq_20' => 2.8,
                'reach_1' => 60.3,
                'reach_2' => 35.6,
                'reach_3' => 28.8,
                'reach_4' => 24.8,
                'reach_5' => 20.6,
                'reach_6' => 16.9,
                'reach_7' => 14.2,
                'reach_8' => 11.9,
                'reach_9' => 10.1,
                'reach_10' => 8.6,
                'reach_avg' => 6.8,
                'rt_number' => 5662,
                'next' => '',
            ],
        ];

        $this->data->channelAxis = '1';
        $this->data->channelIds = [3, 4];
        $this->data->codes = ['personal'];
        $this->data->period = 'week';
        $this->searchConditionTextService->convertDataDivisionText($this->data->dataType)->willReturn(['データ種別:', 'リアルタイム'])->shouldBeCalled();
        $this->rafDao->selectTempResultsForCsv(40000, 0)->willReturn($results)->shouldBeCalled();
        $this->searchConditionTextService->convertCompanyNames([3])->willReturn(['企業名:', '花王'])->shouldBeCalled();
        $this->searchConditionTextService->convertProductNames([])->willReturn(['商品名:', '指定なし'])->shouldBeCalled();
        $ret = [];

        foreach ($this->target->csvGenerator($this->data) as $body) {
            $ret = array_merge($ret, $body);
        }
        $this->assertSame($expect, $ret);
    }

    /**
     * @test
     */
    public function csvGenerator掛け合わせ企業別(): void
    {
        $expect = [
            ['企業名:', '花王'],
            ['商品名:', 'リセッシュ'],
            ['放送:', '全局_リアルタイム'],
            ['', '', '掛け合わせ条件', '世帯'],
            ['', '', '', ''],
            ['集計期間内有効サンプル数', '', 5662, 2178],
            ['時間帯別 GRP　(%)', '', '', ''],
            ['', '05:00～07:59', 47.1, 88.7],
            ['', '08:00～11:59', 52, 105],
            ['', '12:00～17:59', 85.3, 180.9],
            ['', '18:00～22:59', 68.1, 121.6],
            ['', '23:00～23:59', 18.3, 32.6],
            ['', '24:00～28:59', 26.6, 50.5],
            ['延視聴率 GRP計　(%)', '', 297.5, 579.2],
            ['累積到達率　Reach　(%)', '', 43.3, 68.1],
            ['', '1回以上', 43.3, 68.1],
            ['', '2回以上', 35.6, 59.1],
            ['', '3回以上', 28.8, 50.5],
            ['', '4回以上', 24.8, 44.9],
            ['', '5回以上', 20.6, 39.5],
            ['', '6回以上', 16.9, 32.9],
            ['', '7回以上', 14.2, 29.1],
            ['', '8回以上', 11.9, 25.1],
            ['', '9回以上', 10.1, 21.8],
            ['', '10回以上', 8.6, 18.9],
            ['平均視聴回数　(回)', '', 6.8, 8.4],
            ['視聴の分布　frequency (%)', '', '', ''],
            ['', '1回', 7.7, 9],
            ['', '2回', 6.8, 8.6],
            ['', '3回', 3.9, 5.6],
            ['', '4回', 4.3, 5.5],
            ['', '5回', 3.7, 6.6],
            ['', '6回', 2.7, 3.9],
            ['', '7回', 2.3, 4],
            ['', '8回', 1.8, 3.3],
            ['', '9回', 1.5, 2.9],
            ['', '10回', 1.2, 2.8],
            ['', '11回', 1, 2],
            ['', '12回', 0.7, 1.4],
            ['', '13回', 0.6, 1.1],
            ['', '14回', 0.3, 0.7],
            ['', '15回', 0.6, 1.3],
            ['', '16回', 0.4, 1],
            ['', '17回', 0.3, 0.7],
            ['', '18回', 0.3, 0.7],
            ['', '19回', 0.3, 0.8],
            ['', '20回以上', 2.8, 6.4],
            [],
            [],
            ['企業名:', '花王'],
            ['商品名:', 'リセッシュ'],
            ['放送:', '全局_リアルタイム'],
            ['集計区分：', '掛け合わせ条件'],
            ['集計条件：', ''],
            ['集計期間内有効サンプル数：', 5662],
            [],
            ['', '検索期間TO', '2019年07月16日（Tue）24時00分00秒'],
            ['時間帯別 GRP　(%)', '', ''],
            ['', '05:00～07:59', 47.1],
            ['', '08:00～11:59', 52],
            ['', '12:00～17:59', 85.3],
            ['', '18:00～22:59', 68.1],
            ['', '23:00～23:59', 18.3],
            ['', '24:00～28:59', 26.6],
            ['延視聴率 GRP計　(%)', '', 297.5],
            ['累積到達率　Reach　(%)', '', 43.3],
            ['', '1回以上', 43.3],
            ['', '2回以上', 35.6],
            ['', '3回以上', 28.8],
            ['', '4回以上', 24.8],
            ['', '5回以上', 20.6],
            ['', '6回以上', 16.9],
            ['', '7回以上', 14.2],
            ['', '8回以上', 11.9],
            ['', '9回以上', 10.1],
            ['', '10回以上', 8.6],
            ['平均視聴回数　(回)', '', 6.8],
            ['視聴の分布　frequency (%)', '', ''],
            ['', '1回', 7.7],
            ['', '2回', 6.8],
            ['', '3回', 3.9],
            ['', '4回', 4.3],
            ['', '5回', 3.7],
            ['', '6回', 2.7],
            ['', '7回', 2.3],
            ['', '8回', 1.8],
            ['', '9回', 1.5],
            ['', '10回', 1.2],
            ['', '11回', 1],
            ['', '12回', 0.7],
            ['', '13回', 0.6],
            ['', '14回', 0.3],
            ['', '15回', 0.6],
            ['', '16回', 0.4],
            ['', '17回', 0.3],
            ['', '18回', 0.3],
            ['', '19回', 0.3],
            ['', '20回以上', 2.8],
            [],
            [],
            ['企業名:', '花王'],
            ['商品名:', 'リセッシュ'],
            ['放送:', '全局_リアルタイム'],
            ['集計区分：', '世帯'],
            ['集計条件：', ''],
            ['集計期間内有効サンプル数：', 2178],
            [],
            ['', '検索期間TO', '2019年07月16日（Tue）24時00分00秒'],
            ['時間帯別 GRP　(%)', '', ''],
            ['', '05:00～07:59', 88.7],
            ['', '08:00～11:59', 105],
            ['', '12:00～17:59', 180.9],
            ['', '18:00～22:59', 121.6],
            ['', '23:00～23:59', 32.6],
            ['', '24:00～28:59', 50.5],
            ['延視聴率 GRP計　(%)', '', 579.2],
            ['累積到達率　Reach　(%)', '', 68.1],
            ['', '1回以上', 68.1],
            ['', '2回以上', 59.1],
            ['', '3回以上', 50.5],
            ['', '4回以上', 44.9],
            ['', '5回以上', 39.5],
            ['', '6回以上', 32.9],
            ['', '7回以上', 29.1],
            ['', '8回以上', 25.1],
            ['', '9回以上', 21.8],
            ['', '10回以上', 18.9],
            ['平均視聴回数　(回)', '', 8.4],
            ['視聴の分布　frequency (%)', '', ''],
            ['', '1回', 9],
            ['', '2回', 8.6],
            ['', '3回', 5.6],
            ['', '4回', 5.5],
            ['', '5回', 6.6],
            ['', '6回', 3.9],
            ['', '7回', 4],
            ['', '8回', 3.3],
            ['', '9回', 2.9],
            ['', '10回', 2.8],
            ['', '11回', 2],
            ['', '12回', 1.4],
            ['', '13回', 1.1],
            ['', '14回', 0.7],
            ['', '15回', 1.3],
            ['', '16回', 1],
            ['', '17回', 0.7],
            ['', '18回', 0.7],
            ['', '19回', 0.8],
            ['', '20回以上', 6.4],
            [],
            [],
        ];
        $results = [
            '0' => (object)
            [
                'row_number' => 1,
                'date' => '2019-07-17 00:00:00',
                'company_id' => 3,
                'company_name' => '花王',
                'product_id' => 0,
                'product_name' => '',
                'channel_id' => 0,
                'channel_name' => '',
                'code' => 'household',
                'grp_summary' => 579.2,
                'time_group_1_grp' => 88.7,
                'time_group_2_grp' => 105,
                'time_group_3_grp' => 180.9,
                'time_group_4_grp' => 121.6,
                'time_group_5_grp' => 32.6,
                'time_group_6_grp' => 50.5,
                'freq_1' => 9,
                'freq_2' => 8.6,
                'freq_3' => 5.6,
                'freq_4' => 5.5,
                'freq_5' => 6.6,
                'freq_6' => 3.9,
                'freq_7' => 4,
                'freq_8' => 3.3,
                'freq_9' => 2.9,
                'freq_10' => 2.8,
                'freq_11' => 2,
                'freq_12' => 1.4,
                'freq_13' => 1.1,
                'freq_14' => 0.7,
                'freq_15' => 1.3,
                'freq_16' => 1,
                'freq_17' => 0.7,
                'freq_18' => 0.7,
                'freq_19' => 0.8,
                'freq_20' => 6.4,
                'reach_1' => 68.1,
                'reach_2' => 59.1,
                'reach_3' => 50.5,
                'reach_4' => 44.9,
                'reach_5' => 39.5,
                'reach_6' => 32.9,
                'reach_7' => 29.1,
                'reach_8' => 25.1,
                'reach_9' => 21.8,
                'reach_10' => 18.9,
                'reach_avg' => 8.4,
                'rt_number' => 2178,
                'next' => 'condition_cross',
            ],
            '1' => (object)
            [
                'row_number' => 3,
                'date' => '2019-07-17 00:00:00',
                'company_id' => 3,
                'company_name' => '花王',
                'product_id' => 0,
                'product_name' => '',
                'channel_id' => 0,
                'channel_name' => '',
                'code' => 'condition_cross',
                'grp_summary' => 297.5,
                'time_group_1_grp' => 47.1,
                'time_group_2_grp' => 52,
                'time_group_3_grp' => 85.3,
                'time_group_4_grp' => 68.1,
                'time_group_5_grp' => 18.3,
                'time_group_6_grp' => 26.6,
                'freq_1' => 7.7,
                'freq_2' => 6.8,
                'freq_3' => 3.9,
                'freq_4' => 4.3,
                'freq_5' => 3.7,
                'freq_6' => 2.7,
                'freq_7' => 2.3,
                'freq_8' => 1.8,
                'freq_9' => 1.5,
                'freq_10' => 1.2,
                'freq_11' => 1,
                'freq_12' => 0.7,
                'freq_13' => 0.6,
                'freq_14' => 0.3,
                'freq_15' => 0.6,
                'freq_16' => 0.4,
                'freq_17' => 0.3,
                'freq_18' => 0.3,
                'freq_19' => 0.3,
                'freq_20' => 2.8,
                'reach_1' => 43.3,
                'reach_2' => 35.6,
                'reach_3' => 28.8,
                'reach_4' => 24.8,
                'reach_5' => 20.6,
                'reach_6' => 16.9,
                'reach_7' => 14.2,
                'reach_8' => 11.9,
                'reach_9' => 10.1,
                'reach_10' => 8.6,
                'reach_avg' => 6.8,
                'rt_number' => 5662,
                'next' => '',
            ],
        ];

        $this->data->division = 'condition_cross';
        $this->data->productIds = [333];
        $this->data->axisType = '1';
        $this->data->period = 'cm';
        $this->searchConditionTextService->convertDataDivisionText($this->data->dataType)->willReturn(['データ種別：', 'リアルタイム'])->shouldBeCalled();
        $this->rafDao->getProductNames('3', [333])->willReturn([(object) ['name' => 'リセッシュ']])->shouldBeCalled();
        $this->rafDao->selectTempResultsForCsv(40000, 0)->willReturn($results)->shouldBeCalled();
        $this->searchConditionTextService->convertChannels([3, 4, 5, 6, 7], 1)->willReturn(['放送:', '全局'])->shouldBeCalled();
        $ret = [];

        foreach ($this->target->csvGenerator($this->data) as $body) {
            $ret = array_merge($ret, $body);
        }
        $this->assertSame($expect, $ret);
    }

    /**
     * @test
     */
    public function csvGenerator0件の場合(): void
    {
        $expect = [];

        $this->data->axisType = '1';
        $this->data->codes = ['fc'];
        $this->searchConditionTextService->convertDataDivisionText($this->data->dataType)->willReturn($this->dataType)->shouldBeCalled();
        $this->rafDao->selectTempResultsForCsv(40000, 0)->willReturn([])->shouldBeCalled();
        $ret = [];

        foreach ($this->target->csvGenerator($this->data) as $body) {
            $ret = array_merge($ret, $body);
        }
        $this->assertSame($expect, $ret);
    }

    /**
     * @test
     */
    public function csvGenerator商品別月単位カスタム区分(): void
    {
        $expect = [
            ['企業名:', '花王'],
            ['商品名:', 'リセッシュ'],
            ['放送:', '全局_リアルタイム'],
            ['', '', 'カスタム１'],
            ['', '', ''],
            ['集計期間内有効サンプル数', '', 5662],
            ['時間帯別 GRP　(%)', '', ''],
            ['', '05:00～07:59', 47.1],
            ['', '08:00～11:59', 52],
            ['', '12:00～17:59', 85.3],
            ['', '18:00～22:59', 68.1],
            ['', '23:00～23:59', 18.3],
            ['', '24:00～28:59', 26.6],
            ['延視聴率 GRP計　(%)', '', 297.5],
            ['累積到達率　Reach　(%)', '', 43.3],
            ['', '1回以上', 43.3],
            ['', '2回以上', 35.6],
            ['', '3回以上', 28.8],
            ['', '4回以上', 24.8],
            ['', '5回以上', 20.6],
            ['', '6回以上', 16.9],
            ['', '7回以上', 14.2],
            ['', '8回以上', 11.9],
            ['', '9回以上', 10.1],
            ['', '10回以上', 8.6],
            ['平均視聴回数　(回)', '', 6.8],
            ['視聴の分布　frequency (%)', '', ''],
            ['', '1回', 7.7],
            ['', '2回', 6.8],
            ['', '3回', 3.9],
            ['', '4回', 4.3],
            ['', '5回', 3.7],
            ['', '6回', 2.7],
            ['', '7回', 2.3],
            ['', '8回', 1.8],
            ['', '9回', 1.5],
            ['', '10回', 1.2],
            ['', '11回', 1],
            ['', '12回', 0.7],
            ['', '13回', 0.6],
            ['', '14回', 0.3],
            ['', '15回', 0.6],
            ['', '16回', 0.4],
            ['', '17回', 0.3],
            ['', '18回', 0.3],
            ['', '19回', 0.3],
            ['', '20回以上', 2.8],
            [],
            [],
            ['企業名:', '花王'],
            ['商品名:', 'リセッシュ'],
            ['放送:', '全局_リアルタイム'],
            ['集計区分：', 'カスタム１'],
            ['集計条件：', ''],
            ['集計期間内有効サンプル数：', 5662],
            [],
            ['', '検索期間TO', '2019年07月', '2019年08月'],
            ['時間帯別 GRP　(%)', '', '', ''],
            ['', '05:00～07:59', 47.1, 47.1],
            ['', '08:00～11:59', 52, 52],
            ['', '12:00～17:59', 85.3, 85.3],
            ['', '18:00～22:59', 68.1, 68.1],
            ['', '23:00～23:59', 18.3, 18.3],
            ['', '24:00～28:59', 26.6, 26.6],
            ['延視聴率 GRP計　(%)', '', 297.5, 297.5],
            ['累積到達率　Reach　(%)', '', 43.3, 43.3],
            ['', '1回以上', 43.3, 43.3],
            ['', '2回以上', 35.6, 35.6],
            ['', '3回以上', 28.8, 28.8],
            ['', '4回以上', 24.8, 24.8],
            ['', '5回以上', 20.6, 20.6],
            ['', '6回以上', 16.9, 16.9],
            ['', '7回以上', 14.2, 14.2],
            ['', '8回以上', 11.9, 11.9],
            ['', '9回以上', 10.1, 10.1],
            ['', '10回以上', 8.6, 8.6],
            ['平均視聴回数　(回)', '', 6.8, 6.8],
            ['視聴の分布　frequency (%)', '', '', ''],
            ['', '1回', 7.7, 7.7],
            ['', '2回', 6.8, 6.8],
            ['', '3回', 3.9, 3.9],
            ['', '4回', 4.3, 4.3],
            ['', '5回', 3.7, 3.7],
            ['', '6回', 2.7, 2.7],
            ['', '7回', 2.3, 2.3],
            ['', '8回', 1.8, 1.8],
            ['', '9回', 1.5, 1.5],
            ['', '10回', 1.2, 1.2],
            ['', '11回', 1, 1],
            ['', '12回', 0.7, 0.7],
            ['', '13回', 0.6, 0.6],
            ['', '14回', 0.3, 0.3],
            ['', '15回', 0.6, 0.6],
            ['', '16回', 0.4, 0.4],
            ['', '17回', 0.3, 0.3],
            ['', '18回', 0.3, 0.3],
            ['', '19回', 0.3, 0.3],
            ['', '20回以上', 2.8, 2.8],
            [],
            [],
        ];

        $resultsFirst = [
            '0' => (object)
            [
                'row_number' => 1,
                'date' => '2019-07-17 00:00:00',
                'company_id' => 3,
                'company_name' => '花王',
                'product_id' => 222,
                'product_name' => 'リセッシュ',
                'channel_id' => 0,
                'channel_name' => '全局',
                'code' => 'custom909_202001141928381',
                'grp_summary' => 297.5,
                'time_group_1_grp' => 47.1,
                'time_group_2_grp' => 52,
                'time_group_3_grp' => 85.3,
                'time_group_4_grp' => 68.1,
                'time_group_5_grp' => 18.3,
                'time_group_6_grp' => 26.6,
                'freq_1' => 7.7,
                'freq_2' => 6.8,
                'freq_3' => 3.9,
                'freq_4' => 4.3,
                'freq_5' => 3.7,
                'freq_6' => 2.7,
                'freq_7' => 2.3,
                'freq_8' => 1.8,
                'freq_9' => 1.5,
                'freq_10' => 1.2,
                'freq_11' => 1,
                'freq_12' => 0.7,
                'freq_13' => 0.6,
                'freq_14' => 0.3,
                'freq_15' => 0.6,
                'freq_16' => 0.4,
                'freq_17' => 0.3,
                'freq_18' => 0.3,
                'freq_19' => 0.3,
                'freq_20' => 2.8,
                'reach_1' => 43.3,
                'reach_2' => 35.6,
                'reach_3' => 28.8,
                'reach_4' => 24.8,
                'reach_5' => 20.6,
                'reach_6' => 16.9,
                'reach_7' => 14.2,
                'reach_8' => 11.9,
                'reach_9' => 10.1,
                'reach_10' => 8.6,
                'reach_avg' => 6.8,
                'rt_number' => 5662,
                'next' => '222',
            ],
        ];
        $resultsSeconds = [
            '0' => (object)
            [
                'row_number' => 1,
                'date' => '2019-08-17 00:00:00',
                'company_id' => 3,
                'company_name' => '花王',
                'product_id' => 222,
                'product_name' => '',
                'channel_id' => 0,
                'channel_name' => '全局',
                'code' => 'custom909_202001141928381',
                'grp_summary' => 297.5,
                'time_group_1_grp' => 47.1,
                'time_group_2_grp' => 52,
                'time_group_3_grp' => 85.3,
                'time_group_4_grp' => 68.1,
                'time_group_5_grp' => 18.3,
                'time_group_6_grp' => 26.6,
                'freq_1' => 7.7,
                'freq_2' => 6.8,
                'freq_3' => 3.9,
                'freq_4' => 4.3,
                'freq_5' => 3.7,
                'freq_6' => 2.7,
                'freq_7' => 2.3,
                'freq_8' => 1.8,
                'freq_9' => 1.5,
                'freq_10' => 1.2,
                'freq_11' => 1,
                'freq_12' => 0.7,
                'freq_13' => 0.6,
                'freq_14' => 0.3,
                'freq_15' => 0.6,
                'freq_16' => 0.4,
                'freq_17' => 0.3,
                'freq_18' => 0.3,
                'freq_19' => 0.3,
                'freq_20' => 2.8,
                'reach_1' => 43.3,
                'reach_2' => 35.6,
                'reach_3' => 28.8,
                'reach_4' => 24.8,
                'reach_5' => 20.6,
                'reach_6' => 16.9,
                'reach_7' => 14.2,
                'reach_8' => 11.9,
                'reach_9' => 10.1,
                'reach_10' => 8.6,
                'reach_avg' => 6.8,
                'rt_number' => 5662,
                'next' => '',
            ],
        ];

        $this->data->axisType = '2';
        $this->data->codes = ['custom909_202001141928381'];
        $this->data->period = 'month';
        $this->data->division = 'custom909';
        $this->data->codeList = [
            (object) [
                'division' => 'custom909',
                'code' => 'custom909_202001141928381',
                'division_name' => 'カスタム１',
                'name' => 'カスタム１',
                'division_order' => '1',
                'display_order' => '1',
            ],
        ];
        $this->data->dataType = [
            'custom909_202001141928381' => [
                'name' => 'カスタム１',
                'description' => '',
            ],
        ];
        $this->data->length = 1;
        $this->searchConditionTextService->convertDataDivisionText($this->data->dataType)->willReturn(['データ種別:', 'リアルタイム'])->shouldBeCalled();
        $this->rafDao->selectTempResultsForCsv(1, 0)->willReturn($resultsFirst);
        $this->rafDao->selectTempResultsForCsv(1, 1)->willReturn($resultsSeconds);
        $this->rafDao->selectTempResultsForCsv(1, 2)->willReturn([]);
        $this->searchConditionTextService->convertChannels([3, 4, 5, 6, 7], 1)->willReturn(['放送:', '全局'])->shouldBeCalled();

        $ret = [];

        foreach ($this->target->csvGenerator($this->data) as $body) {
            $ret = array_merge($ret, $body);
        }
        $this->assertSame($expect, $ret);
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
            $inputData->baseDivision(),
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
            '2',
            1,
            30,
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
            1,
            30,
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
