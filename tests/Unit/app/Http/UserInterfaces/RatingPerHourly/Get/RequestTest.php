<?php

namespace Tests\Unit\App\Http\UserInterfaces\RatingPerHourly\Get;

use App\Http\UserInterfaces\RatingPerHourly\Get\Request;
use App\Rules\SearchableBoundaryValidator;
use App\Rules\SearchableNumberOfDaysValidator;
use Carbon\Carbon;
use Switchm\SmartApi\Components\Common\Exceptions\TrialException;
use Switchm\SmartApi\Components\RatingPerHourly\Get\UseCases\InputData;
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
            'SearchableNumberOfDaysValidator' => new SearchableNumberOfDaysValidator(['RATING_POINTS']),
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
     * @throws TrialException
     */
    public function passedValidation(): void
    {
        \Auth
            ::shouldReceive('id')
                ->andReturn(1);

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
                ->once();

        $this->target->merge([
            'startDateTime' => '2019-01-01 05:00:00',
            'endDateTime' => '2019-01-07 05:00:00',
            'regionId' => 1,
            'channels' => [1],
            'channelType' => '1',
            'division' => 'ga8',
            'conditionCross' => [],
            'csvFlag' => 0,
            'draw' => 1,
            'code' => 'personal',
            'dataType' => [0],
            'displayType' => '',
            'aggregateType' => '',
            'hour' => '',
        ]);

        $expected = new InputData(
            // startDateTime
            '2019-01-01 05:00:00',
            // endDateTime
            '2019-01-07 05:00:00',
            // regionId
            1,
            // channels
            [1],
            // channelType
            '1',
            // division
            'ga8',
            // conditionCross
            [],
            // csvFlag
            0,
            // draw
            1,
            // code
            'personal',
            // dataDivision
            null,
            // dataType
            [0],
            // displayType
            '',
            // aggregateType
            '',
            // hour
            '',
            // maxNumber
            50,
            // dataTypeFlags
            ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            // userId
            1,
            // rdbDwhSearchPeriod
            [
                'rdbStartDate' => new Carbon('2019-01-01 05:00:00'),
                'rdbEndDate' => new Carbon('2019-01-07 05:00:00'),
                'dwhStartDate' => new Carbon('2019-01-01 05:00:00'),
                'dwhEndDate' => new Carbon('2019-01-07 05:00:00'),
                'isDwh' => true,
                'isRdb' => false,
            ],
            [
                'ga8',
                'ga12',
                'ga10s',
                'gm',
                'oc',
            ],
            \Config::get('const.RATING_POINTS_LATEST_DATE_TIME_INTERVAL.PER_HOURLY'),
            \Config::get('const.RATING_POINTS_LATEST_DATE_TIME_INTERVAL.PER_MINUTES'),
            \Config::get('const.SAMPLE_CODE_PREFIX'),
            \Config::get('const.SAMPLE_CODE_NUMBER_PREFIX'),
            \Config::get('const.SAMPLE_SELECTED_PERSONAL_NAME')
        );

        $this->target->passedValidation();
        $actual = $this->target->inputData();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @throws TrialException
     */
    public function passedValidation_Exception(): void
    {
        $this->expectException(TrialException::class);

        \Auth
            ::shouldReceive('id')
                ->andReturn(1);

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
