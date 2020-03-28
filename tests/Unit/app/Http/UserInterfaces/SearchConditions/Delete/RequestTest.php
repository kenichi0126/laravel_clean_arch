<?php

namespace Tests\Unit\App\Http\UserInterfaces\SearchConditions\Delete;

use App\Http\UserInterfaces\SearchConditions\Delete\Request;
use Switchm\SmartApi\Components\SearchConditions\Delete\UseCases\InputData;
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
            0
        );

        $actual = $this->target->inputData();

        $this->assertEquals($expected, $actual);
    }
}
