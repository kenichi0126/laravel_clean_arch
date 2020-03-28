<?php

namespace Tests\Unit\App\Http\UserInterfaces\SettingAttrDivsOrder\Update;

use App\Http\UserInterfaces\SettingAttrDivsOrder\Update\Request;
use Tests\Unit\App\Http\UserInterfaces\ValidationTestCase;

class ValidationTest extends ValidationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->target = new Request([
            'divisions' => [],
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
                    'divisions' => [[]],
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
                'divisions',
                [
                    'divisions' => null,
                ],
                '属性は必須です。',
            ],
            [
                'divisions',
                [
                    'divisions' => 'a',
                ],
                'validation.array',
            ],
        ];
    }
}
