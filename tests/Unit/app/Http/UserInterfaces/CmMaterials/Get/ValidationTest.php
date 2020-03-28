<?php

namespace Tests\Unit\App\Http\UserInterfaces\CmMaterials\Get;

use App\Http\UserInterfaces\CmMaterials\Get\Request;
use Tests\Unit\App\Http\UserInterfaces\ValidationTestCase;

class ValidationTest extends ValidationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->target = new Request([
            'start_date' => '2019-01-01 05:00:00',
            'end_date' => '2019-01-07 04:59:59',
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
                    'product_ids' => [1],
                    'product_ids.test' => [2],
                    'start_date' => '2019-01-01 05:00:00',
                    'end_date' => '2019-01-07 04:59:59',
                    'start_time_hour' => 1,
                    'start_time_min' => 1,
                    'end_time_hour' => 1,
                    'end_time_min' => 1,
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
                'product_ids',
                [
                    'product_ids' => '',
                ],
                'validation.required',
            ],
            [
                'start_date',
                [
                    'start_date' => '',
                ],
                'validation.required',
            ],
            [
                'start_date',
                [
                    'start_date' => 'aaa',
                ],
                'validation.date',
            ],
            [
                'end_date',
                [
                    'end_date' => '',
                ],
                'validation.required',
            ],
            [
                'end_date',
                [
                    'end_date' => 'aaa',
                ],
                'validation.date',
            ],
            [
                'start_time_hour',
                [
                    'start_time_hour' => 'aaa',
                ],
                'validation.integer',
            ],
            [
                'start_time_min',
                [
                    'start_time_min' => 'aaa',
                ],
                'validation.integer',
            ],
            [
                'end_time_hour',
                [
                    'end_time_hour' => 'aaa',
                ],
                'validation.integer',
            ],
            [
                'end_time_min',
                [
                    'end_time_min' => 'aaa',
                ],
                'validation.integer',
            ],
        ];
    }
}
