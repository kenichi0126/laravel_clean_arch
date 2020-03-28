<?php

namespace Tests\Unit\App\Http\UserInterfaces\Channels\Get;

use App\Http\UserInterfaces\Channels\Get\Request;
use Tests\Unit\App\Http\UserInterfaces\ValidationTestCase;

class ValidationTest extends ValidationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->target = new Request([
            'division' => 'ga8',
            'regionId' => 1,
            'withCommercials' => false,
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
                    'division' => 'ga8',
                    'regionId' => 1,
                    'withCommercials' => false,
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
                'division',
                [
                    'division' => null,
                ],
                'validation.required',
            ],
            [
                'division',
                [
                    'division' => '$&*(',
                ],
                'validation.alpha_num',
            ],
            [
                'regionId',
                [
                    'regionId' => 'aaaaa',
                ],
                'validation.integer',
            ],
            [
                'withCommercials',
                [
                    'withCommercials' => 'aaaaaa',
                ],
                'validation.boolean',
            ],
        ];
    }
}
