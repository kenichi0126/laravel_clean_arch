<?php

namespace Tests\Unit\App\Http\UserInterfaces\ProgramTableDetail\Get;

use App\Http\UserInterfaces\ProgramTableDetail\Get\Request;
use Switchm\SmartApi\Components\ProgramTableDetail\Get\UseCases\InputData;
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
            'progId' => 'required',
            'timeBoxId' => 'required',
            'division' => 'required',
            'regionId' => 'required',
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
            'regionId' => 1,
            'division' => 'ga8',
            'progId' => '123',
            'timeBoxId' => '123',
        ]);

        $expected = new InputData(
            $this->target->input('regionId'),
            $this->target->input('division'),
            $this->target->input('progId'),
            $this->target->input('timeBoxId'),
            2,
            0
        );

        $this->target->passedValidation();

        $actual = $this->target->inputData();

        $this->assertEquals($expected, $actual);
    }
}
