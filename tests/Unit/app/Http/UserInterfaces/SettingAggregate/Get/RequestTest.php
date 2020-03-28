<?php

namespace Tests\Unit\App\Http\UserInterfaces\SettingAggregate\Get;

use App\Http\UserInterfaces\SettingAggregate\Get\Request;
use Switchm\SmartApi\Components\SettingAggregate\Get\UseCases\InputData;
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

        \Auth
            ::shouldReceive('id')
                ->andReturn(1);

        \UserInfo
            ::shouldReceive('execute')
                ->andReturn(new \stdClass());

        $this->target->merge([]);

        $expected = new InputData(new \stdClass());

        $this->target->passedValidation();

        $actual = $this->target->inputData();

        $this->assertEquals($expected, $actual);
    }
}
