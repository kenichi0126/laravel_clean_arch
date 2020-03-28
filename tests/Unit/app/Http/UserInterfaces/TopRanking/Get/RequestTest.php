<?php

namespace Tests\Unit\App\Http\UserInterfaces\TopRanking\Get;

use App\Http\UserInterfaces\TopRanking\Get\Request;
use Switchm\SmartApi\Components\TopRanking\Get\UseCases\InputData;
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
        \Auth::
            shouldReceive('id')
                ->andReturn(1)
                ->once();

        $user = new \stdClass();
        $user->conv_15_sec_flag = 'true';

        \UserInfo::
            shouldReceive('execute')
                ->andReturn($user)
                ->once();

        $this->assertNull($this->target->inputData());

        $this->target->merge([
            'regionId' => 1,
        ]);

        $expected = new InputData(
            $this->target->input('regionId'),
            $user->conv_15_sec_flag,
            [71, 158, 223, 265, 318, 1033, 1332, 1872, 1889, 2143, 2451, 2617, 2855, 2857, 2859, 2863, 2865, 2930, 2939, 2942, 3110, 3213, 3245, 4025, 4574, 4826, 5256]
        );

        $this->target->passedValidation();

        $actual = $this->target->inputData();

        $this->assertEquals($expected, $actual);
    }
}
