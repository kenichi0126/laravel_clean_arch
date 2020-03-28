<?php

namespace Tests\Unit\App\Http\UserInterfaces\Setting\Save;

use App\Http\UserInterfaces\Setting\Save\Request;
use Tests\Unit\App\Http\UserInterfaces\ValidationTestCase;

class ValidationTest extends ValidationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->target = new Request([
            'secFlag' => 1,
            'division' => '',
            'codes' => [],
            'regionId' => 1,
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
                    'secFlag' => 1,
                    'division' => 'test',
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
                'secFlag',
                [
                    'secFlag' => null,
                ],
                'validation.required',
            ],
            [
                'secFlag',
                [
                    'secFlag' => 'a',
                ],
                'validation.integer',
            ],
            [
                'division',
                [
                    'division' => '',
                ],
                '属性は必須です。',
            ],
            [
                'division',
                [
                    'division' => 1,
                ],
                'validation.string',
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
