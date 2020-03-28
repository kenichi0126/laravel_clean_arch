<?php

namespace Tests\Unit\App\Http\UserInterfaces\RafChart;

use App\Http\UserInterfaces\RafChart\Get\Request;
use App\Rules\SearchableBoundaryValidator;
use App\Rules\SearchableNumberOfDaysValidator;
use Switchm\SmartApi\Components\Common\Exceptions\TrialException;
use Switchm\SmartApi\Components\RafChart\Get\UseCases\InputData;
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
            'companyIds' => 'required_without:productIds',
            'SearchableNumberOfDaysValidator' => new SearchableNumberOfDaysValidator(['RAF']),
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
        $expected = $this->target->merge([
            'startDateTime' => '2019-01-01 05:00:00',
            'endDateTime' => '2019-01-07 04:59:59',
            'division' => 'ga8',
            'dateRange' => 1,
            'dataType' => [0],
            'regionId' => 1,
        ]);

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
     * @dataProvider indexDataProvider
     * @param mixed $params
     */
    public function passedValidation($params): void
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
        };

        \Auth
            ::shouldReceive('getUser')
                ->andReturn($user);

        $this->target->merge($params);

        $expected = new InputData(
            $params['startDateTime'],
            $params['endDateTime'],
            $params['dataType'],
            $params['dateRange'],
            $params['regionId'],
            $params['division'],
            $params['conditionCross'],
            $params['csvFlag'],
            $params['codes'],
            $params['channels'],
            $params['axisType'],
            $params['channelAxis'],
            $params['cmIds'],
            $params['cmSeconds'],
            $params['cmType'],
            $params['codeNames'],
            $params['companyIds'],
            $params['conv_15_sec_flag'],
            $params['period'],
            $params['productIds'],
            $params['progIds'],
            $params['reachAndFrequencyGroupingUnit'],
            $params['dataTypeFlags'],
            $params['axisTypeProduct'],
            $params['axisLimit'],
            $params['userId'],
            $params['axisTypeCompany'],
            $params['baseDivision']
        );

        $this->target->passedValidation();

        $actual = $this->target->inputData();

        $this->assertEquals($expected, $actual);
    }

    public function indexDataProvider(): array
    {
        return [
            'params' => [
                [
                    'endDateTime' => '2019-06-13 04:59:00',
                    'startDateTime' => '2019-06-12 05:00:00',
                    'channels' => [
                        3,
                        4,
                        5,
                        6,
                        7,
                    ],
                    'companyIds' => [],
                    'cmType' => 0,
                    'cmSeconds' => 1,
                    'division' => 'ga8',
                    'codes' => [
                        'c',
                    ],
                    'conditionCross' => [
                        'gender' => [
                            '',
                        ],
                        'age' => [
                            'from' => 4,
                            'to' => 99,
                        ],
                        'occupation' => [
                            '',
                        ],
                        'married' => [
                            '',
                        ],
                        'dispOccupation' => [
                            '',
                        ],
                    ],
                    'reachAndFrequencyGroupingUnit' => [
                        3,
                        6,
                        9,
                    ],
                    'axisType' => 0,
                    'channelAxis' => 0,
                    'period' => 'day',
                    'codeNames' => [
                        [
                            'division' => 'ga8',
                            'code' => 'c',
                            'division_name' => '性・年齢8区分',
                            'name' => 'C',
                            'division_order' => 101,
                            'display_order' => 1,
                        ],
                    ],
                    'productIds' => [
                        52874,
                    ],
                    'cmIds' => [],
                    'regionId' => 1,
                    'conv_15_sec_flag' => 1,
                    'progIds' => [],
                    'dataType' => [
                        0,
                    ],
                    'dateRange' => 2,
                    'csvFlag' => 0,
                    'dataTypeFlags' => [
                        'isRt' => true,
                        'isTs' => false,
                        'isGross' => false,
                        'isTotal' => false,
                        'isRtTotal' => false,
                    ],
                    'axisTypeProduct' => 2,
                    'axisLimit' => 30,
                    'userId' => 1,
                    'axisTypeCompany' => '1',
                    'baseDivision' => [
                        'ga8',
                        'ga12',
                        'ga10s',
                        'gm',
                        'oc',
                    ],
                ], ], ];
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
