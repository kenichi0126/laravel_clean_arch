<?php

namespace Tests\Unit\App\Http\UserInterfaces\SearchConditions\Delete;

use App\Http\UserInterfaces\SearchConditions\Delete\Request;
use Tests\Unit\App\Http\UserInterfaces\ValidationTestCase;

/**
 * Class ValidationTest.
 */
final class ValidationTest extends ValidationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->target = new Request([
            'id' => 0,
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
                    'id' => 0,
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
                'id',
                [
                    'id' => null,
                ],
                'IDは必須です。',
            ],
        ];
    }
}
