<?php

namespace Tests\Unit\App\Http\UserInterfaces\ProgramListAverage\Get;

use App\Http\UserInterfaces\ProgramListAverage\Get\Request;
use Switchm\SmartApi\Components\ProgramListAverage\Get\UseCases\InputData;
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
            'averageType' => 'simple',
            'codes' => ['ga8'],
            'conditionCross' => [],
            'dataType' => [0],
            'digitalAndBs' => 'digital',
            'division' => 'ga8',
            'progIds' => [],
            'regionId' => 1,
            'timeBoxIds' => [1],
        ]);
        $dataTypeFlags = ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false];
        $dataTypeConst = [
            'rt' => \Config::get('const.DATA_TYPE_NUMBER.REALTIME'),
            'ts' => \Config::get('const.DATA_TYPE_NUMBER.TIMESHIFT'),
            'total' => \Config::get('const.DATA_TYPE_NUMBER.TOTAL'),
            'gross' => \Config::get('const.DATA_TYPE_NUMBER.GROSS'),
            'rtTotal' => \Config::get('const.DATA_TYPE_NUMBER.RT_TOTAL'),
        ];
        $prefixes = [
            'code' => \Config::get('const.SAMPLE_CODE_PREFIX'),
            'number' => \Config::get('const.SAMPLE_CODE_NUMBER_PREFIX'),
        ];
        $expected = new InputData(
            $this->target->input('averageType'),
            $this->target->input('codes'),
            $this->target->input('conditionCross'),
            $this->target->input('dataType'),
            $this->target->input('digitalAndBs'),
            $this->target->input('division'),
            $this->target->input('progIds'),
            $this->target->input('regionId'),
            $this->target->input('timeBoxIds'),
            ['ga8', 'ga12', 'ga10s', 'gm', 'oc'],
            $dataTypeFlags,
            $dataTypeConst,
            $prefixes,
            \Config::get('const.SAMPLE_SELECTED_PERSONAL_NAME'),
            \Config::get('const.MAX_CODE_NUMBER')
        );

        $this->target->passedValidation();

        $actual = $this->target->inputData();

        $this->assertEquals($expected, $actual);
    }
}
