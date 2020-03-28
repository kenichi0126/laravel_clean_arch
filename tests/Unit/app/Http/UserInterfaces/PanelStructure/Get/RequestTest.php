<?php

namespace Tests\Unit\App\Http\UserInterfaces\PanelStructure\Get;

use App\Http\UserInterfaces\PanelStructure\Get\Request;
use Switchm\SmartApi\Components\PanelStructure\Get\UseCases\InputData;
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
        $user->id = 1;

        \UserInfo
            ::shouldReceive('execute')
                ->andReturn($user)
                ->once();

        $this->target->merge([
            'division' => 'ga8',
            'regionId' => 1,
        ]);

        $expected = new InputData(
            $this->target->input('division'),
            $this->target->input('regionId'),
            true,
            1
        );

        $this->target->passedValidation();

        $actual = $this->target->inputData();

        $this->assertEquals($expected, $actual);
    }
}
