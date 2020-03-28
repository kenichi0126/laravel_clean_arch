<?php

namespace Tests\Unit\App\Http\UserInterfaces\HourlyReport\Get;

use App\Http\UserInterfaces\HourlyReport\Get\Request;
use Tests\Unit\App\Http\UserInterfaces\ValidationTestCase;

class ValidationTest extends ValidationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->target = new Request([
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
                'regionId',
                [
                    'regionId' => null,
                ],
                'validation.required',
            ],
            [
                'regionId',
                [
                    'regionId' => 'aaaaaa',
                ],
                'validation.integer',
            ],
        ];
    }
}
