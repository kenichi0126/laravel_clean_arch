<?php

namespace Tests\Unit\App\Http\UserInterfaces\ProgramMultiChannelProfile\Get;

use App\Http\UserInterfaces\ProgramMultiChannelProfile\Get\Request;
use App\Rules\SearchableBoundaryValidator;
use Illuminate\Auth\AuthenticationException;
use Switchm\SmartApi\Components\Common\Exceptions\TrialException;
use Switchm\SmartApi\Components\ProgramMultiChannelProfile\Get\UseCases\InputData;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class RequestTest extends TestCase
{
    private $target;

    protected function setUp(): void
    {
        parent::setUp();

        \Auth
            ::shouldReceive('id')
                ->andReturn(1);

        $this->target = new Request([
            'startDateTime' => '2019-01-01 05:00:00',
            'endDateTime' => '2019-01-07 04:59:59',
            'dataType' => [0],
            'regionId' => 1,
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
            'regionId' => 'required',
            'startDateTime' => 'required|date',
            'endDateTime' => 'required|date',
            'progIds' => 'required',
            'timeBoxIds' => 'required',
            'sampleType' => 'required',
            'conditionCross' => 'required',
            'channelIds' => 'required',
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
        $temp = [
            'searchableBoundaryValidator' => [
                'startDateTime' => '2019-01-01 05:00:00',
                'endDateTime' => '2019-01-07 04:59:59',
                'dataType' => [0],
                'regionId' => 1,
            ],
        ];

        $expected = $this->target->merge($temp);

        $actual = (new Request([
            'startDateTime' => '2019-01-01 05:00:00',
            'endDateTime' => '2019-01-07 04:59:59',
            'dataType' => [0],
            'regionId' => 1,
        ]));
        $actual->prepareForValidation();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider passedValidation_no_exception_dataProvider
     * @param mixed $sampleType
     * @param mixed $isEnq
     * @throws AuthenticationException
     * @throws TrialException
     */
    public function passedValidation_no_exception($sampleType, $isEnq): void
    {
        $user = new class {
            public function hasPermission()
            {
                return true;
            }
        };

        \Auth
            ::shouldReceive('getUser')
                ->andReturn($user);

        $this->target->merge([
            'startDateTime' => '2019-01-01 05:00:00',
            'endDateTime' => '2019-01-07 05:00:00',
            'regionId' => 1,
            'progIds' => [1],
            'timeBoxIds' => [1],
            'division' => 'ga8',
            'conditionCross' => [],
            'codes' => [],
            'channelIds' => [1],
            'sampleType' => $sampleType,
        ]);

        $expected = new InputData(
            // startDateTime
            '2019-01-01 05:00:00',
            // endDateTime
            '2019-01-07 05:00:00',
            // regionId
            1,
            // progIDs
            [1],
            // timeBoxIds
            [1],
            // division
            'ga8',
            // conditionCross
            [],
            // codes
            [],
            // channelIds
            [1],
            // sampleType
            $sampleType,
            // isEnq
            $isEnq,
            // sampleCountMaxNumber
            50,
            \Config::get('const.ENQ_PROFILE_SAMPLE_THRESHOLD')
        );

        $this->target->passedValidation();
        $actual = $this->target->inputData();

        $this->assertEquals($expected, $actual);
    }

    public function passedValidation_no_exception_dataProvider()
    {
        return [
            ['1', false],
            ['3', true],
        ];
    }

    /**
     * @test
     * @dataProvider passedValidation_exception_dataProvider
     * @param mixed $regionId
     * @throws AuthenticationException
     * @throws TrialException
     */
    public function passedValidation_exception($regionId): void
    {
        $user = new class {
            public function hasPermission()
            {
                return false;
            }
        };

        \Auth
            ::shouldReceive('getUser')
                ->andReturn($user);

        $this->target->merge([
            'startDateTime' => '2019-01-01 05:00:00',
            'endDateTime' => '2019-01-07 05:00:00',
            'regionId' => $regionId,
            'progIds' => [1],
            'timeBoxIds' => [1],
            'division' => 'ga8',
            'conditionCross' => [],
            'codes' => [],
            'channelIds' => [1],
            'sampleType' => '3',
        ]);

        $this->expectException(NotFoundHttpException::class);

        $response = $this->target->passedValidation();
        $response->this->assertStatus(404);
    }

    public function passedValidation_exception_dataProvider()
    {
        return [
            [1],
            [2],
        ];
    }
}
