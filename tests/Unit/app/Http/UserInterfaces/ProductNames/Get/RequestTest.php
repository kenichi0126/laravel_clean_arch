<?php

namespace Tests\Unit\App\Http\UserInterfaces\ProductNames\Get;

use App\Http\UserInterfaces\ProductNames\Get\Request;
use App\Rules\SearchableBoundaryValidator;
use Switchm\SmartApi\Components\ProductNames\Get\UseCases\InputData;
use Tests\TestCase;

class RequestTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = new Request([
            'startDateTime' => '2019-01-01 05:00:00',
            'endDateTime' => '2019-01-07 04:59:59',
            'dataType' => [0],
            'productName' => 'iPhone',
            'companyIds' => [],
            'regionIds' => [1],
            'productIds' => [],
            'channels' => [3, 4, 5, 6, 7],
            'cmType' => 0,
            'cmSeconds' => 1,
            'progIds' => [],
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
            'startDateTime' => 'required|date',
            'endDateTime' => 'required|date',
            'searchableBoundaryValidator' => new SearchableBoundaryValidator(),
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
            'startDateTime.required' => '検索開始日は必須です。',
            'endDateTime.required' => '検索終了日は必須です。',
        ];

        $actual = $this->target->messages();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function prepareForValidation(): void
    {
        $this->target->prepareForValidation();

        $this->assertSame([1], $this->target->input('regionIds'));
        $this->assertSame(
            ['startDateTime' => '2019-01-01 05:00:00', 'endDateTime' => '2019-01-07 04:59:59', 'dataType' => [0], 'regionId' => 1],
            $this->target->input('searchableBoundaryValidator')
        );
    }

    /**
     * @test
     */
    public function passedValidation(): void
    {
        $this->assertNull($this->target->inputData());

        $expected = new InputData(
            $this->target->input('startDateTime'),
            $this->target->input('endDateTime'),
            $this->target->input('dataType'),
            $this->target->input('productName'),
            $this->target->input('companyIds'),
            $this->target->input('regionIds'),
            $this->target->input('productIds'),
            $this->target->input('channels'),
            $this->target->input('cmType'),
            $this->target->input('cmSeconds'),
            $this->target->input('progIds')
        );

        $this->target->passedValidation();

        $actual = $this->target->inputData();

        $this->assertEquals($expected, $actual);
    }
}
