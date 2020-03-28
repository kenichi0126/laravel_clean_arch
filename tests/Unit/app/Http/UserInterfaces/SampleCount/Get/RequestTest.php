<?php

namespace Tests\Unit\App\Http\UserInterfaces\SampleCount\Get;

use App\Http\UserInterfaces\SampleCount\Get\Request;
use Switchm\SmartApi\Components\SampleCount\Get\UseCases\InputData;
use Tests\TestCase;

class RequestTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = new Request([
            'info' => [[]],
            'conditionCross' => [[]],
            'regionId' => 1,
            'editFlg' => false,
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
            'info' => 'array',
            'conditionCross' => 'required|array',
            'regionId' => 'required|int',
        ];

        $actual = $this->target->rules();

        $this->assertEquals($expected, $actual);
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
        $this->assertNull($this->target->inputData());

        $this->target->merge([
            'info' => [[]],
            'conditionCross' => [[]],
            'regionId' => 1,
            'editFlg' => false,
        ]);

        $expected = new InputData(
            [[]],
            [[]],
            1,
            false
        );

        $this->target->passedValidation();

        $actual = $this->target->inputData();

        $this->assertEquals($expected, $actual);
    }
}
