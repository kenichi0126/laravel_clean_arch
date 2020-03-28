<?php

namespace Tests\Unit\App\Http\UserInterfaces\SettingAttrDivs\Update;

use App\Http\UserInterfaces\SettingAttrDivs\Update\Request;
use Tests\Unit\App\Http\UserInterfaces\ValidationTestCase;

class ValidationTest extends ValidationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->target = new Request([
            'division' => '',
            'conditionCross' => [],
            'info' => [],
            'regionId' => 1,
            'sumpleName' => '',
            'code' => '',
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
                    'sumpleName' => 'aaa',
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
                'sumpleName',
                [
                    'sumpleName' => '',
                ],
                'サンプル名を入力してください。',
            ],
            [
                'sumpleName',
                [
                    'sumpleName' => 'aaaaaaaaaaaaaaaaaaaaa',
                ],
                'サンプル名は20文字以内で入力してください。',
            ],
            [
                'sumpleName',
                [
                    'sumpleName' => '"',
                ],
                'サンプル名にカンマとダブルクォーテーションは利用できません。',
            ],
        ];
    }
}
