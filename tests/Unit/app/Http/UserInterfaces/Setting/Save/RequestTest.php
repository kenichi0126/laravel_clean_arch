<?php

namespace Tests\Unit\App\Http\UserInterfaces\Setting\Save;

use App\Http\UserInterfaces\Setting\Save\Request;
use Switchm\SmartApi\Components\Setting\Save\UseCases\InputData;
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
            'secFlag' => 'required|int',
            'division' => 'required|string',
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
        $expected = [
            'division.required' => '属性は必須です。',
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
            'secFlag' => 1,
            'division' => '',
            'codes' => [],
            'regionId' => 1,
        ]);

        $expected = new InputData(
            1,
            '',
            [],
            1,
            1
        );

        $this->target->passedValidation();

        $actual = $this->target->inputData();

        $this->assertEquals($expected, $actual);
    }
}
