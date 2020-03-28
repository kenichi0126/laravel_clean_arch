<?php

namespace Tests\Unit\App\Http\UserInterfaces\SearchConditions\Create;

use App\Http\UserInterfaces\SearchConditions\Create\Request;
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
            'regionId' => 1,
            'name' => 'test',
            'routeName' => 'main.test.test',
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
                    'regionId' => 1,
                    'name' => 'test',
                    'routeName' => 'main.test.test',
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
                'regionId',
                [
                    'regionId' => '',
                ],
                'リージョンIDは必須です。',
            ],
            [
                'regionId',
                [
                    'regionId' => 'a',
                ],
                'リージョンIDは数値です。',
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
                'routeName',
                [
                    'routeName' => '',
                ],
                'ルート名は必須です。',
            ],
            [
                'routeName',
                [
                    'routeName' => '01234567890123456789012345678901234567890123456789'
                        . '01234567890123456789012345678901234567890123456789'
                        . '01234567890123456789012345678901234567890123456789'
                        . '01234567890123456789012345678901234567890123456789'
                        . '01234567890123456789012345678901234567890123456789'
                        . '012345',
                ],
                'ルート名は最大255文字までです。',
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
