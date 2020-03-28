<?php

namespace Switchm\SmartApi\Components\Tests\TopRanking\Get\UseCases;

use Carbon\Carbon;
use Prophecy\Argument as arg;
use ReflectionClass;
use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Components\TopRanking\Get\UseCases\InputData;
use Switchm\SmartApi\Components\TopRanking\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\TopRanking\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\TopRanking\Get\UseCases\OutputData;
use Switchm\SmartApi\Queries\Dao\Dwh\TopDao;

class InteractorTest extends TestCase
{
    private $dwhTopDao;

    private $searchConditionTextAppService;

    private $outputBoundary;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2019, 1, 1, 5, 0, 0));

        $this->dwhTopDao = $this->prophesize(TopDao::class);
        $this->searchConditionTextAppService = $this->prophesize(SearchConditionTextAppService::class);
        $this->outputBoundary = $this->prophesize(OutputBoundary::class);

        $this->target = new Interactor($this->dwhTopDao->reveal(), $this->searchConditionTextAppService->reveal(), $this->outputBoundary->reveal());
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    /**
     * @test
     * @dataProvider getRankingsDataProvider
     * @param int $day
     * @param array $expected
     * @throws \ReflectionException
     */
    public function getRankings(int $day, array $expected): void
    {
        Carbon::setTestNow(Carbon::create(2019, 1, $day, 5, 0, 0));

        $reflection = new ReflectionClass($this->target);
        $method = $reflection->getMethod('getRankings');
        $method->setAccessible(true);

        $this->dwhTopDao
            ->findProgramRanking(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->searchConditionTextAppService
            ->getPersonalHouseholdNumbers(arg::cetera())
            ->willReturn([])
            ->shouldBeCalledTimes(2);

        $this->dwhTopDao
            ->findCmRankingOfCompany(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->dwhTopDao
            ->findCmRankingOfProduct(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $actual = $method->invoke($this->target, 1, [3, 4, 5, 6, 7], 1, [1, 2, 3]);

        $this->assertSame($expected, $actual);
    }

    public function getRankingsDataProvider()
    {
        return [
            [1, ['program' => [], 'company_cm' => [], 'product_cm' => [], 'programDate' => '2018年12月24日～2018年12月30日', 'cmDate' => '2018年11月', 'programPhNumbers' => [], 'cmPhNumbers' => []]],
            [10, ['program' => [], 'company_cm' => [], 'product_cm' => [], 'programDate' => '2018年12月31日～2019年01月06日', 'cmDate' => '2018年12月', 'programPhNumbers' => [], 'cmPhNumbers' => []]],
        ];
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param $regionId
     */
    public function invoke_nodata($regionId): void
    {
        $this->dwhTopDao
            ->findProgramRanking(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->searchConditionTextAppService
            ->getPersonalHouseholdNumbers(arg::cetera())
            ->willReturn([])
            ->shouldBeCalledTimes(4);

        $this->dwhTopDao
            ->findCmRankingOfCompany(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->dwhTopDao
            ->findCmRankingOfProduct(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $input = new InputData(
            $regionId,
            1,
            [1, 2, 3]
        );

        $this->target->__invoke($input);

        $outputData = new OutputData([], [], [], '2018年12月24日～2018年12月30日', '2018年11月', [], []);

        $this->outputBoundary->__invoke($outputData)->shouldBeCalled();
        $this->target->__invoke($input);
    }

    public function dataProvider()
    {
        return [
            [
                1,
            ],
            [
                2,
            ],
        ];
    }

    /**
     * @test
     * @param $regionId
     */
    public function invoke(): void
    {
        $this->dwhTopDao
            ->findProgramRanking(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->searchConditionTextAppService
            ->getPersonalHouseholdNumbers(arg::cetera())
            ->willReturn([1, 2])
            ->shouldBeCalledTimes(4);

        $this->dwhTopDao
            ->findCmRankingOfCompany(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->dwhTopDao
            ->findCmRankingOfProduct(arg::cetera())
            ->willReturn([[1], [2]])
            ->shouldBeCalled();

        $input = new InputData(
            1,
            1,
            [1, 2, 3]
        );

        $this->target->__invoke($input);

        $outputData = new OutputData([], [], [[1, 'company_name' => ''], [2, 'company_name' => '']], '2018年12月24日～2018年12月30日', '2018年11月', ['1', '2'], ['1', '2']);

        $this->outputBoundary->__invoke($outputData)->shouldBeCalled();
        $this->target->__invoke($input);
    }
}
