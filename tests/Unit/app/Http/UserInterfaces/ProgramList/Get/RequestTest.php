<?php

namespace Tests\Unit\App\Http\UserInterfaces\ProgramList\Get;

use App\Http\UserInterfaces\ProgramList\Get\Request;
use App\Rules\SearchableBoundaryValidator;
use App\Rules\SearchableNumberOfDaysValidator;
use Illuminate\Auth\AuthenticationException;
use Switchm\SmartApi\Components\Common\Exceptions\TrialException;
use Switchm\SmartApi\Components\ProgramList\Get\UseCases\InputData;
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
            'division' => 'ga8',
            'dateRange' => 1,
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
            'startDateTime' => 'required|date',
            'endDateTime' => 'required|date',
            'wdays' => 'required|array',
            'SearchableNumberOfDaysValidator' => new SearchableNumberOfDaysValidator(['PROGRAM_LIST']),
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
            'wdays.required' => '曜日の選択は必須です。',
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
            'SearchableNumberOfDaysValidator' => [
                'division' => 'ga8',
                'requestPeriod' => 1,
            ],
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
            'division' => 'ga8',
            'dateRange' => 1,
            'dataType' => [0],
            'regionId' => 1,
        ]));
        $actual->prepareForValidation();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @throws AuthenticationException
     * @throws TrialException
     */
    public function passedValidation(): void
    {
        \Auth
            ::shouldReceive('id')
                ->andReturn(1);

        \UserInfo
            ::shouldReceive('execute')
                ->andReturn(new \stdClass())
                ->once();

        $user = new class {
            public function isDuringTrial()
            {
                return true;
            }

            public function hasPermission()
            {
                return true;
            }
        };

        \Auth
            ::shouldReceive('getUser')
                ->andReturn($user)
                ->twice();

        $this->target->merge([
            'startDateTime' => '2019-01-01 05:00:00',
            'endDateTime' => '2019-01-07 05:00:00',
            'digitalAndBs' => 'digital',
            'digitalKanto' => [1],
            'bs1' => [98],
            'bs2' => [99],
            'holiday' => 'true',
            'dataType' => [0],
            'wdays' => [1],
            'genres' => [],
            'programNames' => ['test'],
            'order' => [],
            'dispCount' => 20,
            'dateRange' => 100,
            'page' => 1,
            'regionId' => 1,
            'division' => 'ga8',
            'conditionCross' => [],
            'csvFlag' => 0,
            'draw' => 1,
            'codes' => [],
        ]);

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
        // startDateTime
            '2019-01-01 05:00:00',
            // endDateTime
            '2019-01-07 05:00:00',
            // digitalAndBs
            'digital',
            // digitalKanto
            [1],
            // bs1
            [98],
            // bs2
            [99],
            // holiday
            'true',
            // dataType
            [0],
            // wdays
            [1],
            // genres
            [],
            // programNames
            ['test'],
            // order
            [],
            // dispCount
            20,
            // dateRange
            100,
            // page
            1,
            // regionId
            1,
            // division
            'ga8',
            // conditionCross
            [],
            // csvFlag
            0,
            // draw
            1,
            // codes
            [],
            // dataTypeFlag
            ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            // userId
            1,
            // hasPermission
            true,
            // BaseDivision
            ['ga8', 'ga12', 'ga10s', 'gm', 'oc'],
            // sampleMax
            50,
            // dataTypeConst
            $dataTypeConst,
            // prefixes
            $prefixes,
            \Config::get('const.SAMPLE_SELECTED_PERSONAL_NAME'),
            \Config::get('const.MAX_CODE_NUMBER')
        );

        $this->target->passedValidation();
        $actual = $this->target->inputData();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @throws AuthenticationException
     * @throws TrialException
     */
    public function passedValidation_Exception(): void
    {
        $this->expectException(TrialException::class);

        \Auth
            ::shouldReceive('id')
                ->andReturn(1);

        \UserInfo
            ::shouldReceive('execute')
                ->andReturn(new \stdClass())
                ->once();

        $user = new class {
            public $sponsor;

            public function isDuringTrial()
            {
                return false;
            }
        };
        $user->sponsor = new \stdClass();
        $user->sponsor->sponsorTrial = new \stdClass();
        $user->sponsor->sponsorTrial->settings = ['search_range' => ['start' => '2019-01-01', 'end' => '2019-01-07']];

        \Auth
            ::shouldReceive('getUser')
                ->andReturn($user)
                ->twice();

        $this->target->passedValidation();
    }
}
