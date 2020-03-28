<?php

namespace Smart2\QueryModel\Service;

use Carbon\Carbon;
use Prophecy\Argument as arg;
use Prophecy\Prophecy\ObjectProphecy;
use Switchm\SmartApi\Queries\Dao\Rdb\SearchConditionTextDao;
use Tests\TestCase;

class SearchConditionTextServiceTest extends TestCase
{
    /**
     * @var ObjectProphecy
     */
    private $searchConditionTextDao;

    /**
     * @var SearchConditionTextService
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->searchConditionTextDao = $this->prophesize(SearchConditionTextDao::class);
        $this->target = new SearchConditionTextService($this->searchConditionTextDao->reveal());
    }

    /**
     * @test
     */
    public function convertCompanyNames(): void
    {
        $expected = [
            ['SMART  -  CM出稿数'],
            ['検索期間:', '2019年10月10日(木) 5:00～2019年10月10日(木) 28:59'],
            ['放送:', ''],
            ['視聴率表示:', 'OFF'],
            ['含有率表示:', 'OFF'],
            ['企業名:', '指定なし'],
            ['商品名:', '指定なし'],
            ['CM秒数:', '全CM'],
            ['単位:', '本'],
            ['レポート作成日時:', Carbon::now()->isoFormat('YYYY年MM月DD日(ddd) H:mm')],
            ['データ提供元:', 'Switch Media Lab, Inc.'],
            [],
            [],
            [],
            [''],
            [],
        ];

        \Auth::shouldReceive('user->hasPermission')
            ->andReturn(false);

        /*
        $this->searchConditionTextDao
            ->getCrossConditionCount(arg::cetera())
            ->willReturn($expected)
            ->shouldBeCalled();
        */

        $actual = $this->target->getAdvertising(
            '2019-10-10',
            '2019-10-10',
            '0500',
            '2859',
            'cmtype',
            1,
            null,
            1,
            [],
            [],
            [],
            [],
            false,
            'false',
            'false'
        );

        $this->assertEquals($expected, $actual);
    }
}
