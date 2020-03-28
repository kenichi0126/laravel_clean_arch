<?php

namespace Tests\Unit\App\Http\UserInterfaces\SettingAttrDivs\Update;

use App\Http\UserInterfaces\SettingAttrDivs\Update\Request;
use Switchm\SmartApi\Components\SettingAttrDivs\Update\UseCases\InputData;
use Tests\TestCase;

class RequestTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = new Request();
    }

    /**
     * @test
     */
    public function authorize(): void
    {
        $expected = true;
        $actual = $this->target->authorize();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function rules(): void
    {
        $expected = [
            'sumpleName' => 'required|max:20|str_in_comma_or_doublequote',
        ];

        $actual = $this->target->rules();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function messages(): void
    {
        $expected = [
            'sumpleName.required' => 'サンプル名を入力してください。',
            'sumpleName.max' => 'サンプル名は20文字以内で入力してください。',
            'sumpleName.str_in_comma_or_doublequote' => 'サンプル名にカンマとダブルクォーテーションは利用できません。',
        ];

        $actual = $this->target->messages();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function passedValidation(): void
    {
        $this->assertNull($this->target->inputData());

        $this->target->merge([
            'division' => '',
            'conditionCross' => [],
            'info' => [],
            'regionId' => 1,
            'sumpleName' => '',
            'code' => '',
        ]);

        $expected = new InputData(
            '',
            [],
            [],
            1,
            '',
            ''
        );

        $this->target->passedValidation();

        $actual = $this->target->inputData();

        $this->assertEquals($expected, $actual);
    }
}
