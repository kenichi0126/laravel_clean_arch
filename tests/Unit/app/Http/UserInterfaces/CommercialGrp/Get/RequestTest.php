<?php

namespace Tests\Unit\App\Http\UserInterfaces\CommercialGrp\Get;

use App\Http\UserInterfaces\CommercialGrp\Get\Request;
use App\Rules\SearchableBoundaryValidator;
use App\Rules\SearchableNumberOfDaysValidator;
use Illuminate\Auth\AuthenticationException;
use Switchm\SmartApi\Components\CommercialGrp\Get\UseCases\InputData;
use Switchm\SmartApi\Components\Common\Exceptions\TrialException;
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
            'companyIds' => 'required_without_all:progIds,productIds',
            'SearchableNumberOfDaysValidator' => new SearchableNumberOfDaysValidator(['CMGRP']),
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
            'companyIds.required_without_all' => '企業名、商品名または番組名を選択してください。',
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
                ->andReturn(null)
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
            'endDateTime' => '2019-01-07 04:59:59',
            'page' => 1,
            'dataType' => [0],
            'division' => 'ga8',
            'conditionCross' => [],
            'regionId' => 1,
            'dateRange' => 1,
            'productIds' => [],
            'companyIds' => [],
            'cmType' => [],
            'cmSeconds' => [],
            'progIds' => [],
            'codes' => ['personal'],
            'cmIds' => [],
            'channels' => [],
            'conv_15_sec_flag' => '1',
            'period' => 'period',
            'allChannels' => '0',
            'dispCount' => 20,
            'csvFlag' => '1',
            'draw' => 1,
        ]);

        $expected = new InputData(
        // startDateTime
            '2019-01-01 05:00:00',
            // endDateTime
            '2019-01-07 04:59:59',
            // page
            1,
            // dataType
            [0],
            // division
            'ga8',
            // conditionCross
            [],
            // regionId
            1,
            // dateRange
            1,
            // productIds
            [],
            // companyIds
            [],
            // cmType
            [],
            // cmSeconds
            [],
            // progIds
            [],
            // codes
            ['personal'],
            // cmIds
            [],
            // channels
            [],
            // conv15secFlag
            '1',
            // period
            'period',
            // allChannels
            '0',
            // dispCount
            20,
            // csvFlag
            '1',
            // draw
            1,
            // user
            $user,
            // userId
            1,
            // sampleCountMaxNumber
            50,
            // dataTypeFlags
            ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false],
            // baseDivision
            ['ga8', 'ga12', 'ga10s', 'gm', 'oc'],
            32,
            'code',
            'number',
            'selected_personal',
            [
                'REALTIME' => 0,
                'TIMESHIFT' => 1,
                'GROSS' => 2,
                'TOTAL' => 3,
                'RT_TOTAL' => 4,
            ]
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
