<?php

namespace Tests\Unit\App\Http\UserInterfaces\Channels\Get;

use App\Http\UserInterfaces\Channels\Get\Request;
use Switchm\SmartApi\Components\Channels\Get\UseCases\InputData;
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
            'division' => 'required|alpha_num',
            'regionId' => 'integer',
            'withCommercials' => 'boolean',
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
            'division' => 'ga8',
            'regionId' => 1,
            'withCommercials' => false,
        ]);

        $expected = new InputData(
            $this->target->input('division'),
            $this->target->input('regionId'),
            $this->target->input('withCommercials')
        );

        $this->target->passedValidation();

        $actual = $this->target->inputData();

        $this->assertEquals($expected, $actual);
    }
}
