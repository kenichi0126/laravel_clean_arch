<?php

namespace Tests\Unit\App\Http\UserInterfaces\SampleCount\Get;

use App\Http\UserInterfaces\SampleCount\Get\Request;
use Tests\Unit\App\Http\UserInterfaces\ValidationTestCase;

class ValidationTest extends ValidationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->target = new Request([
            'info' => [[]],
            'conditionCross' => [[]],
            'regionId' => 1,
            'editFlg' => false,
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
                    'info' => [[]],
                    'conditionCross' => [[]],
                    'regionId' => 1,
                    'editFlg' => false,
                ],
            ],
            [
                [
                    'info' => [],
                    'conditionCross' => [[]],
                    'regionId' => 1,
                    'editFlg' => false,
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
                'info',
                [
                    'info' => 'a',
                ],
                'validation.array',
            ],
            [
                'conditionCross',
                [
                    'conditionCross' => [],
                ],
                'validation.required',
            ],
            [
                'conditionCross',
                [
                    'conditionCross' => 'a',
                ],
                'validation.array',
            ],
            [
                'regionId',
                [
                    'regionId' => null,
                ],
                'validation.required',
            ],
            [
                'regionId',
                [
                    'regionId' => 'a',
                ],
                'validation.integer',
            ],
        ];
    }
}
