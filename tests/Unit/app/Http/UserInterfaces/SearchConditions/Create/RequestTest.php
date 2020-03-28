<?php

namespace Tests\Unit\App\Http\UserInterfaces\SearchConditions\Create;

use App\Http\UserInterfaces\SearchConditions\Create\Request;
use Switchm\SmartApi\Components\SearchConditions\Create\UseCases\InputData;
use Tests\TestCase;

/**
 * Class RequestTest.
 */
final class RequestTest extends TestCase
{
    private $target;

    public function setUp(): void
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
            'regionId' => 'required|integer',
            'name' => 'required|max:50',
            'routeName' => 'required|max:255',
            'condition' => 'required',
        ];
        $actual = $this->target->rules();
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function messages(): void
    {
        $expected = [
            'regionId.required' => 'リージョンIDは必須です。',
            'regionId.integer' => 'リージョンIDは数値です。',
            'name.required' => '名前は必須です。',
            'name.max' => '名前は最大50文字までです。',
            'routeName.required' => 'ルート名は必須です。',
            'routeName.max' => 'ルート名は最大255文字までです。',
            'condition.required' => '条件は必須です。',
        ];
        $actual = $this->target->messages();
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function passedValidation(): void
    {
        \Auth::shouldReceive('id')->andReturn(1)->once();

        $this->target->passedValidation();

        $expected = new InputData(
            1,
            1,
            'test',
            'main.test.test',
            '{\"test\": \"test\"}'
        );

        $actual = $this->target->inputData();

        $this->assertEquals($expected, $actual);
    }
}
