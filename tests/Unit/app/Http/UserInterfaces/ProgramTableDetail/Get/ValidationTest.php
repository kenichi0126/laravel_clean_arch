<?php

namespace Tests\Unit\App\Http\UserInterfaces\ProgramTableDetail\Get;

use App\Http\UserInterfaces\ProgramTableDetail\Get\Request;
use Tests\Unit\App\Http\UserInterfaces\ValidationTestCase;

class ValidationTest extends ValidationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->target = new Request([
            'progId' => '12345',
            'timeBoxId' => '1',
            'division' => 'ga8',
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
                    'progId' => '12345',
                    'timeBoxId' => '1',
                    'division' => 'ga8',
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
                'progId',
                [
                    'progId' => '',
                ],
                'validation.required',
            ],
            [
                'timeBoxId',
                [
                    'timeBoxId' => '',
                ],
                'validation.required',
            ],
            [
                'division',
                [
                    'division' => '',
                ],
                'validation.required',
            ],
            [
                'regionId',
                [
                    'regionId' => '',
                ],
                'validation.required',
            ],
        ];
    }
}
