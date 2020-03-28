<?php

namespace Tests\Unit\App\Http\UserInterfaces\RankingCommercial\Get;

use App\Http\UserInterfaces\RankingCommercial\Get\Request;
use App\Rules\SearchableBoundaryValidator;
use App\Rules\SearchableNumberOfDaysValidator;
use Illuminate\Auth\AuthenticationException;
use Switchm\SmartApi\Components\Common\Exceptions\TrialException;
use Switchm\SmartApi\Components\RankingCommercial\Get\UseCases\InputData;
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
            'SearchableNumberOfDaysValidator' => new SearchableNumberOfDaysValidator(['RANKING_CM']),
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
                ->once();

        $this->target->merge([
            'startDateTime' => '2019-01-01 05:00:00',
            'endDateTime' => '2019-01-07 05:00:00',
            'page' => 1,
            'holiday' => 'true',
            'wdays' => [1],
            'division' => 'ga8',
            'dateRange' => 100,
            'dataType' => [0],
            'regionId' => 1,
            'cmType' => '',
            'codes' => [],
            'conditionCross' => [],
            'channels' => [1],
            'order' => [],
            'conv_15_sec_flag' => '1',
            'period' => 'period',
            'dispCount' => 20,
            'csvFlag' => 0,
            'cmLargeGenres' => [],
            'axisType' => '',
            'draw' => 1,
        ]);

        $expected = new InputData(
        // startDateTime
            '2019-01-01 05:00:00',
            // endDateTime
            '2019-01-07 05:00:00',
            // page
            1,
            // holiday
            'true',
            // wdays
            [1],
            // division
            'ga8',
            // dateRange
            100,
            // dataType
            [0],
            // regionId
            1,
            // cmType
            '',
            // codes
            [],
            // conditionCross
            [],
            // channels
            [1],
            // order
            [],
            // conv15SecFlag
            '1',
            // period
            'period',
            // dispCount
            20,
            // csvFlag
            0,
            // cmLargeGenres
            [],
            // axisType
            '',
            // draw
            1,
            // userId
            1,
            [71, 158, 223, 265, 318, 1033, 1332, 1872, 1889, 2143, 2451, 2617, 2855, 2857, 2859, 2863, 2865, 2930, 2939, 2942, 3110, 3213, 3245, 4025, 4574, 4826, 5256],
            '1',
            '2',
            [
                'ga8',
                'ga12',
                'ga10s',
                'gm',
                'oc',
            ]
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
