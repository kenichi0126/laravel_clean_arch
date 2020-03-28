<?php

namespace Tests\Unit\App\Http\UserInterfaces\CmMaterials\Get;

use App\Http\UserInterfaces\CmMaterials\Get\Request;
use Switchm\SmartApi\Components\CmMaterials\Get\UseCases\InputData;
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
            'product_ids' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'start_time_hour' => 'integer',
            'start_time_min' => 'integer',
            'end_time_hour' => 'integer',
            'end_time_min' => 'integer',
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
            'product_ids' => [1],
            'start_date' => '2019-01-01 05:00:00',
            'end_date' => '2019-01-01 05:00:00',
            'start_time_hour' => 5,
            'start_time_min' => 10,
            'end_time_hour' => 5,
            'end_time_min' => 10,
            'regionId' => 1,
            'channels' => [1],
            'cmType' => 0,
            'cmSeconds' => 1,
            'companyIds' => [1],
            'progIds' => [1],
        ]);

        $expected = new InputData(
            [1],
            '2019-01-01 05:00:00',
            '2019-01-01 05:00:00',
            5,
            10,
            5,
            10,
            1,
            [1],
            0,
            1,
            [1],
            [1]
        );

        $this->target->passedValidation();

        $actual = $this->target->inputData();

        $this->assertEquals($expected, $actual);
    }
}
