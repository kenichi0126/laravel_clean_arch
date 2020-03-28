<?php

namespace Tests\Unit\App\Http\UserInterfaces\HourlyReport\Get;

use App\Http\UserInterfaces\HourlyReport\Get\Request;
use Switchm\SmartApi\Components\HourlyReport\Get\UseCases\InputData;
use Tests\TestCase;

class RequestTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = new Request(['regionId' => 1]);
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
        $expected = ['regionId' => 'required|integer'];

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
        $setting = (object) ['sponsor' => (object) ['sponsorTrial' => (object) ['settings' => []]]];

        \Auth
            ::shouldReceive('getUser')
                ->andReturn($setting)
                ->once();

        $expected = new InputData(1, []);

        $this->target->passedValidation();

        $actual = $this->target->inputData();

        $this->assertEquals($expected, $actual);
    }
}
