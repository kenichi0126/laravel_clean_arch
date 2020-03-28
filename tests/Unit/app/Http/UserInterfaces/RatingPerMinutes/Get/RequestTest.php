<?php

namespace Tests\Unit\App\Http\UserInterfaces\RatingPerMinutes\Get;

use App\Http\UserInterfaces\RatingPerMinutes\Get\Request;
use Carbon\Carbon;
use Switchm\SmartApi\Components\Common\Exceptions\TrialException;
use Switchm\SmartApi\Components\RatingPerMinutes\Get\UseCases\InputData;
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
            'division' => 'in:ga8,ga10s,ga12,gm,oc',
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
            'division.in' => '基本属性サンプルのみ選択可能です。',
        ];

        $actual = $this->target->messages();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws TrialException
     */
    public function passedValidation(): void
    {
        Carbon::setTestNow(new Carbon('2019-01-01 10:00:00'));

        \Auth
            ::shouldReceive('id')
                ->andReturn(1)
                ->once();

        $user = new class {
            public function isDuringTrial()
            {
                return true;
            }
        };

        \Auth
            ::shouldReceive('getUser')
                ->andReturn($user)
                ->once();

        $this->assertNull($this->target->inputData());

        $this->target->merge([
            'startDateTime' => '2019-01-01 05:00:00',
            'endDateTime' => '2019-01-31 04:59:00',
            'regionId' => 1,
            'channels' => [],
            'channelType' => 'dt1',
            'division' => 'ga8',
            'conditionCross' => [],
            'csvFlag' => '0',
            'draw' => '1',
            'code' => 'personal',
            'dataDivision' => 'viewing_rate',
            'dataType' => [0],
            'displayType' => 'channelBy',
            'aggregateType' => '6',
            'hour' => '6',
        ]);

        $expected = new InputData(
            $this->target->input('startDateTime'),
            $this->target->input('endDateTime'),
            $this->target->input('regionId'),
            $this->target->input('channels'),
            $this->target->input('channelType'),
            $this->target->input('division'),
            $this->target->input('conditionCross'),
            $this->target->input('csvFlag'),
            $this->target->input('draw'),
            $this->target->input('code'),
            $this->target->input('dataDivision'),
            $this->target->input('dataType'),
            $this->target->input('displayType'),
            $this->target->input('aggregateType'),
            $this->target->input('hour'),
            50,
            1,
            ['rdbStartDate' => new Carbon('2019-01-01 05:00:00'), 'rdbEndDate' => new Carbon('2019-01-31 04:59:00'), 'dwhStartDate' => new Carbon('2019-01-01 05:00:00'), 'dwhEndDate' => new Carbon('2019-01-31 04:59:00'), 'isDwh' => false, 'isRdb' => true],
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
                ->andReturn($user);

        $this->target->passedValidation();
    }
}
