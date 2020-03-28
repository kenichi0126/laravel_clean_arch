<?php

namespace Tests\Unit\App\Http\UserInterfaces\CommercialAdvertising\Get;

use App\Http\UserInterfaces\CommercialAdvertising\Get\Request;
use App\Rules\SearchableBoundaryValidator;
use App\Rules\SearchableNumberOfDaysValidator;
use Carbon\Carbon;
use Switchm\SmartApi\Components\CommercialAdvertising\Get\UseCases\InputData;
use Switchm\SmartApi\Components\Common\Exceptions\TrialException;
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
     * @dataProvider ruleDataProvider
     * @param mixed $flag
     * @param mixed $configNames
     */
    public function rules($flag, $configNames): void
    {
        $this->target->merge(['heatMapRating' => $flag]);

        $expected = [
            'startDateTime' => 'required|date',
            'endDateTime' => 'required|date',
            'companyIds' => 'required_without:productIds|array',
            'SearchableNumberOfDaysValidator' => new SearchableNumberOfDaysValidator($configNames),
            'searchableBoundaryValidator' => new SearchableBoundaryValidator(),
        ];
        $actual = $this->target->rules();

        $this->assertEquals($expected, $actual);
    }

    public function ruleDataProvider()
    {
        return [
            [true, ['ADVERTISING', 'RATING_POINTS']],
            [false, ['ADVERTISING']],
        ];
    }

    /**
     * @test
     */
    public function messages(): void
    {
        $expected = [
            'startDateTime.required' => '検索開始日は必須です。',
            'endDateTime.required' => '検索終了日は必須です。',
            'companyIds.required_without' => '企業名または商品名を選択してください。',
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

        $actual = new Request([
            'startDateTime' => '2019-01-01 05:00:00',
            'endDateTime' => '2019-01-07 04:59:59',
            'division' => 'ga8',
            'dateRange' => 1,
            'dataType' => [0],
            'regionId' => 1,
        ]);
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
            'companyIds' => [],
            'productIds' => [],
            'cmType' => 0,
            'cmSeconds' => 0,
            'progIds' => [],
            'regionId' => 1,
            'cmIds' => [],
            'channels' => [1],
            'heatMapRating' => false,
            'heatMapTciPersonal' => false,
            'heatMapTciHousehold' => false,
            'division' => 'ga8',
            'conditionCross' => [],
            'csvFlag' => 0,
            'draw' => 1,
            'codes' => [],
        ]);

        $expected = new InputData(
            '2019-01-01 05:00:00',
            '2019-01-07 05:00:00',
            [],
            [],
            0,
            0,
            [],
            1,
            [],
            [1],
            false,
            false,
            false,
            'ga8',
            [],
            0,
            1,
            [],
            1,
            50,
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
