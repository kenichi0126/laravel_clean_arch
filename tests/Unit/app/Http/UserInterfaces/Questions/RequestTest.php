<?php

namespace Tests\Unit\App\Http\UserInterfaces\Questions;

use App\Http\UserInterfaces\Questions\Get\Request;
use Switchm\SmartApi\Components\Questions\Get\UseCases\InputData;
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
        $expected = [];
        $actual = $this->target->rules();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function messages(): void
    {
        $expected = [];
        $actual = $this->target->messages();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function passedValidation(): void
    {
        $this->target->merge(['keyword' => '車', 'qGroup' => '全選択', 'tag' => '全選択']);

        $this->assertNull($this->target->inputData());

        $expected = new InputData(
            $this->target->input('keyword'),
            $this->target->input('qGroup'),
            $this->target->input('tag')
        );

        $this->target->passedValidation();

        $actual = $this->target->inputData();

        $this->assertEquals($expected, $actual);
    }
}
