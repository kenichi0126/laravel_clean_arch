<?php

namespace Tests\Unit\App\Http\UserInterfaces\SearchConditions\Update;

use App\Http\UserInterfaces\SearchConditions\Update\Request;
use Switchm\SmartApi\Components\SearchConditions\Update\UseCases\InputData;
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
            'id' => 0,
            'name' => 'test',
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
            'id' => 'required',
            'name' => 'required|max:50',
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
            'id.required' => 'IDは必須です。',
            'name.required' => '名前は必須です。',
            'name.max' => '名前は最大50文字までです。',
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
        $this->target->passedValidation();

        $expected = new InputData(
            0,
            'test',
            '{\"test\": \"test\"}'
        );

        $actual = $this->target->inputData();

        $this->assertEquals($expected, $actual);
    }
}
