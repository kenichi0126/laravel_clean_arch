<?php

namespace Switchm\SmartApi\Components\Tests\RankingCommercial\UseCases;

use Prophecy\Argument as arg;
use ReflectionClass;
use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Components\RankingCommercial\Get\UseCases\InputData;
use Switchm\SmartApi\Components\RankingCommercial\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\RankingCommercial\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\RankingCommercial\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Queries\Dao\Dwh\RankingDao;
use Switchm\SmartApi\Queries\Dao\Rdb\MdataCmGenreDao;
use Switchm\SmartApi\Queries\Services\DivisionService;

class InteractorTest extends TestCase
{
    private $rankingDao;

    private $mdataCmGenreDao;

    private $divisionService;

    private $presenter;

    private $searchConditionTextAppService;

    private $target;

    /**
     * @test
     */
    public function __invoke(): void
    {
        $this->divisionService
            ->getCodeList(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->rankingDao
            ->searchCommercial(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->searchConditionTextAppService
            ->getRankingCommercialCsv(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->searchConditionTextAppService
            ->getRankingCommercialHeader(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $outputData = new OutputData(null, '1', null, '20190101', '20190107', []);

        $this->presenter->__invoke($outputData)->shouldBeCalled();

        $input = new InputData(
            '2019-01-01 05:00:00',
            '2019-01-07 04:59:59',
            1,
            true,
            [1, 2, 3, 4, 5, 6, 0],
            'ga8',
            1,
            [0],
            1,
            '0',
            [],
            [],
            [1, 2, 3, 4, 5],
            [],
            '1',
            'period',
            20,
            '0',
            [],
            '',
            '1',
            1,
            [1],
            '2',
            '1',
            [
                'ga8',
                'ga12',
                'ga10s',
                'gm',
                'oc',
            ]
        );

        $this->target->__invoke($input);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->rankingDao = $this->prophesize(RankingDao::class);
        $this->mdataCmGenreDao = $this->prophesize(MdataCmGenreDao::class);
        $this->divisionService = $this->prophesize(DivisionService::class);
        $this->presenter = $this->prophesize(OutputBoundary::class);
        $this->searchConditionTextAppService = $this->prophesize(SearchConditionTextAppService::class);

        $this->target = new Interactor(
            $this->rankingDao->reveal(),
            $this->mdataCmGenreDao->reveal(),
            $this->divisionService->reveal(),
            $this->presenter->reveal(),
            $this->searchConditionTextAppService->reveal()
        );
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getParams(): void
    {
        $input = new InputData(
            '2019-01-01 05:00:00',
            '2019-01-07 04:59:59',
            1,
            true,
            [1, 2, 3, 4, 5, 6, 0],
            'ga8',
            1,
            [0],
            1,
            '0',
            [],
            [],
            [1, 2, 3, 4, 5],
            [],
            '1',
            'period',
            20,
            '1',
            '0',
            [],
            '1',
            1,
            [1],
            '2',
            '1',
            [
                'ga8',
                'ga12',
                'ga10s',
                'gm',
                'oc',
            ]
        );

        $expected = [
            '2019-01-01',
            '2019-01-07',
            '050000',
            '045959',
            [1, 2, 3, 4, 5, 6, 0],
            true,
            '0',
            1,
            'ga8',
            [],
            [],
            [1, 2, 3, 4, 5],
            [],
            '1',
            'period',
            true,
            20,
            1,
            '1',
            [0],
            '0',
            [],
            [1],
            '2',
            '1',
        ];

        $reflection = new ReflectionClass($this->target);
        $method = $reflection->getMethod('getParams');
        $method->setAccessible(true);

        $actual = $method->invoke($this->target, $input);

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getList(): void
    {
        $this->rankingDao
            ->searchCommercial(arg::cetera())
            ->willReturn([[], []])
            ->shouldBeCalled();

        $expected = [[], []];

        $reflection = new ReflectionClass($this->target);
        $method = $reflection->getMethod('getList');
        $method->setAccessible(true);

        $params = [
            '2019-01-01',
            '2019-01-07',
            '050000',
            '045959',
            [1, 2, 3, 4, 5, 6, 0],
            true,
            '0',
            1,
            'ga8',
            [],
            [],
            [1, 2, 3, 4, 5],
            [],
            '1',
            'period',
            true,
            20,
            1,
            '1',
            [0],
            [],
            '',
            [1],
            '2',
            '1',
        ];

        $actual = $method->invoke($this->target, $params);

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getHeader_List(): void
    {
        $this->searchConditionTextAppService
            ->getRankingCommercialHeader(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->searchConditionTextAppService
            ->getRankingCommercialCsv(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $expected = [];

        $reflection = new ReflectionClass($this->target);
        $method = $reflection->getMethod('getHeader');
        $method->setAccessible(true);

        $csvFlg = '0';
        $params = [];

        $actual = $method->invoke($this->target, $csvFlg, $params);

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getHeader_Csv(): void
    {
        $this->searchConditionTextAppService
            ->getRankingCommercialHeader(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->searchConditionTextAppService
            ->getRankingCommercialCsv(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $expected = [];

        $reflection = new ReflectionClass($this->target);
        $method = $reflection->getMethod('getHeader');
        $method->setAccessible(true);

        $csvFlg = '1';
        $params = [];

        $actual = $method->invoke($this->target, $csvFlg, $params);

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function produceOutputData(): void
    {
        $reflection = new ReflectionClass($this->target);
        $method = $reflection->getMethod('produceOutputData');
        $method->setAccessible(true);

        $list = [];
        $draw = [];
        $cnt = 0;
        $startDateShort = '20190101';
        $endDateShort = '20190107';
        $header = [];

        $expected = new OutputData(
            $list,
            $draw,
            $cnt,
            $startDateShort,
            $endDateShort,
            $header
        );
        $actual = $method->invoke($this->target, $list, $draw, $cnt, $startDateShort, $endDateShort, $header);
        $this->assertEquals($expected, $actual);
    }
}
