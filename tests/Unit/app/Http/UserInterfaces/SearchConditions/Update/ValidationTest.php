<?php

namespace Tests\Unit\App\Http\UserInterfaces\SearchConditions\Update;

use App\Http\UserInterfaces\SearchConditions\Update\Request;
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
            'name' => 'test',
            'condition' => '{\"test\": \"test\"}',
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
                    'name' => 'test',
                    'condition' => '{\"test\": \"test\"}',
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
            [
                'name',
                [
                    'name' => '',
                ],
                '名前は必須です。',
            ],
            [
                'name',
                [
                    'name' => '01234567890123456789012345678901234567890123456789'
                        . '0',
                ],
                '名前は最大50文字までです。',
            ],
            [
                'condition',
                [
                    'condition' => '',
                ],
                '条件は必須です。',
            ],
        ];
    }
}
