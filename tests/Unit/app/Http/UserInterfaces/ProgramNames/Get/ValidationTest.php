<?php

namespace Tests\Unit\App\Http\UserInterfaces\ProgramNames\Get;

use App\Http\UserInterfaces\ProgramNames\Get\Request;
use Tests\Unit\App\Http\UserInterfaces\ValidationTestCase;

class ValidationTest extends ValidationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

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
                [
                    'startDateTime' => '2019-01-07 04:59:59',
                    'endDateTime' => '2019-01-07 04:59:59',
                    'dataType' => [0],
                    'regionIds' => [1],
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
