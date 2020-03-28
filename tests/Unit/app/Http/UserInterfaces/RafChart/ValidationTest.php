<?php

namespace Tests\Unit\App\Http\UserInterfaces\RafChart;

use App\Http\UserInterfaces\RafChart\Get\Request;
use Tests\Unit\App\Http\UserInterfaces\ValidationTestCase;

class ValidationTest extends ValidationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        \Auth
            ::shouldReceive('id')
                ->andReturn(1);

        $this->target = new Request([
            'startDateTime' => '2019-01-01 05:00:00',
            'endDateTime' => '2019-01-07 04:59:59',
        ]);
    }

    /**
     * @return array
     */
    public function dataValidationSuccess(): array
    {
        return [
            [
                [ // 企業指定
                    'startDateTime' => '2019-01-07 04:59:59',
                    'endDateTime' => '2019-01-07 04:59:59',
                    'dateRange' => 1000,
                    'companyIds' => [25],
                    'division' => 'ga8',
                    'dataType' => [0],
                    'regionId' => 1,
                ],
            ],
            [
                [ // 商品指定
                    'startDateTime' => '2019-01-07 04:59:59',
                    'endDateTime' => '2019-01-07 04:59:59',
                    'dateRange' => 1000,
                    'productIds' => [11],
                    'division' => 'ga8',
                    'dataType' => [0],
                    'regionId' => 1,
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function dataValidationError(): array
    {
        return [
            [
                'startDateTime',
                [
                    'startDateTime' => '',
                ],
                '検索開始日は必須です。',
            ],
            [
                'startDateTime',
                [
                    'startDateTime' => 'aaaaaa',
                ],
                'validation.date',
            ],
            [
                'endDateTime',
                [
                    'endDateTime' => '',
                ],
                '検索終了日は必須です。',
            ],
            [
                'endDateTime',
                [
                    'endDateTime' => 'aaaaaa',
                ],
                'validation.date',
            ],
            [
                'companyIds',
                [
                    'companyIds' => [],
                    'productIds' => [],
                ],
                '企業名または商品名を選択してください。',
            ],
            [
                'SearchableNumberOfDaysValidator',
                [
                    'SearchableNumberOfDaysValidator' => [
                        'configName' => 'RAF',
                        'division' => 'ga8',
                        'requestPeriod' => 1000,
                    ],
                ],
                '期間は93日以内で指定してください。',
            ],
            [
                'SearchableNumberOfDaysValidator',
                [
                    'SearchableNumberOfDaysValidator' => [
                        'configName' => 'RAF',
                        'division' => 'original',
                        'requestPeriod' => 1000,
                    ],
                ],
                '期間は93日以内で指定してください。',
            ],
            [
                'searchableBoundaryValidator',
                [
                    'searchableBoundaryValidator' => [
                            'startDateTime' => '2010-10-10 5:00:00',
                            'endDateTime' => '2010-10-11 5:00:00',
                            'dataType' => [0],
                            'regionId' => 1,
                        ],
                ],
                '期間は2013-12-30以降で指定してください。',
            ],
            [
                'searchableBoundaryValidator',
                [
                    'searchableBoundaryValidator' => [
                            'startDateTime' => '2010-10-10 5:00:00',
                            'endDateTime' => '2010-10-11 5:00:00',
                            'dataType' => [0],
                            'regionId' => 2,
                        ],
                ],
                '期間は2018-09-10以降で指定してください。',
            ],
            [
                'searchableBoundaryValidator',
                [
                    'searchableBoundaryValidator' => [
                        'startDateTime' => '2010-10-10 5:00:00',
                        'endDateTime' => '2010-10-11 5:00:00',
                        'dataType' => [1],
                        'regionId' => 1,
                    ],
                ],
                '期間は2013-12-30～放送日より7日前以上開けて指定してください。※タイムシフト／総合視聴率を含む場合',
            ],
            [
                'searchableBoundaryValidator',
                [
                    'searchableBoundaryValidator' => [
                        'startDateTime' => '2010-10-10 5:00:00',
                        'endDateTime' => '2010-10-11 5:00:00',
                        'dataType' => [1],
                        'regionId' => 2,
                    ],
                ],
                '期間は2018-09-10～放送日より7日前以上開けて指定してください。※タイムシフト／総合視聴率を含む場合',
            ],
        ];
    }
}
