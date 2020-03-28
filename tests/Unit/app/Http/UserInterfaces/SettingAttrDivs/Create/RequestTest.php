<?php

namespace Tests\Unit\App\Http\UserInterfaces\SettingAttrDivs\Create;

use App\Http\UserInterfaces\SettingAttrDivs\Create\Request;
use Switchm\SmartApi\Components\SettingAttrDivs\Create\UseCases\InputData;
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

        \Auth
            ::shouldReceive('id')
                ->andReturn(1);

        $this->target->merge([
            'division' => '',
            'conditionCross' => [],
            'info' => [],
            'regionId' => 1,
            'sumpleName' => '',
        ]);

        $expected = new InputData(
            '',
            [],
            [],
            1,
            '',
            1
        );

        $this->target->passedValidation();

        $actual = $this->target->inputData();

        $this->assertEquals($expected, $actual);
    }
}
