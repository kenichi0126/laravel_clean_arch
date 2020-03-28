<?php

namespace Tests\Unit\App\Http\UserInterfaces\TimeBox\Get;

use App\Http\UserInterfaces\TimeBox\Get\Request;
use Smart2\CommandModel\Eloquent\Member;
use Switchm\SmartApi\Components\TimeBox\Get\UseCases\InputData;
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
            'regionId' => 1,
        ]);
        $m = new Member();
        \Auth
            ::shouldReceive('getUser')
                ->andReturn($m);

        $expected = new InputData(
            $this->target->input('regionId'),
            $m
        );

        $this->target->passedValidation();

        $actual = $this->target->inputData();

        $this->assertEquals($expected, $actual);
    }
}
