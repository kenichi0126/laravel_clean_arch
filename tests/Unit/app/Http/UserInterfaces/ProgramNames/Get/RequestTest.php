<?php

namespace Tests\Unit\App\Http\UserInterfaces\ProgramNames\Get;

use App\Http\UserInterfaces\ProgramNames\Get\Request;
use App\Rules\SearchableBoundaryValidator;
use Switchm\SmartApi\Components\ProgramNames\Get\UseCases\InputData;
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

        $this->assertNull($this->target->input('regionId'));
        $this->assertSame(
            ['startDateTime' => null, 'endDateTime' => null, 'dataType' => null, 'regionId' => null],
            $this->target->input('searchableBoundaryValidator')
        );
    }

    /**
     * @test
     */
    public function passedValidation(): void
    {
        $this->assertNull($this->target->inputData());

        $this->target->merge([
            'startDateTime' => '2019-01-01 05:00:00',
            'endDateTime' => '2019-01-31 04:59:00',
            'programName' => '世界',
            'channels' => [],
            'digitalAndBs' => 'digital',
            'programFlag' => true,
            'digitalKanto' => [1, 2, 3, 4, 5, 6, 7],
            'bs1' => [15, 16, 17, 18, 19, 20, 21],
            'bs2' => [22, 23, 24, 25],
            'cmtype' => 0,
            'cmSeconds' => '1',
            'productIds' => [],
            'companies' => [],
            'regionId' => 1,
            'dataType' => [0],
            'programIds' => [],
            'wdays' => ['1', '2', '3', '4', '5', '6', '0'],
            'holiday' => true,
        ]);

        $expected = new InputData(
            $this->target->input('startDateTime'),
            $this->target->input('endDateTime'),
            $this->target->input('programName'),
            $this->target->input('channels'),
            $this->target->input('digitalAndBs'),
            $this->target->input('programFlag'),
            $this->target->input('digitalKanto'),
            $this->target->input('bs1'),
            $this->target->input('bs2'),
            $this->target->input('cmType'),
            $this->target->input('cmSeconds'),
            $this->target->input('productIds'),
            $this->target->input('companies'),
            $this->target->input('regionId'),
            $this->target->input('dataType'),
            $this->target->input('programIds'),
            $this->target->input('wdays'),
            $this->target->input('holiday')
        );

        $this->target->passedValidation();

        $actual = $this->target->inputData();

        $this->assertEquals($expected, $actual);
    }
}
