<?php

namespace Switchm\SmartApi\Components\Tests\Top\Get\UseCases;

use Carbon\Carbon;
use Prophecy\Argument as arg;
use ReflectionClass;
use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Components\Top\Get\UseCases\InputData;
use Switchm\SmartApi\Components\Top\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\Top\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\Top\Get\UseCases\OutputData;
use Switchm\SmartApi\Queries\Dao\Rdb\TopDao;

class InteractorTest extends TestCase
{
    private $topDao;

    private $searchConditionTextAppService;

    private $outputBoundary;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2019, 1, 1, 5, 0, 0));

        $this->topDao = $this->prophesize(TopDao::class);
        $this->searchConditionTextAppService = $this->prophesize(SearchConditionTextAppService::class);
        $this->outputBoundary = $this->prophesize(OutputBoundary::class);

        $this->target = new Interactor($this->topDao->reveal(), $this->searchConditionTextAppService->reveal(), $this->outputBoundary->reveal());
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    /**
     * @test
     * @dataProvider makeCategoriesDataProvider
     * @param int $param
     * @param array $expected
     * @throws \ReflectionException
     */
    public function makeCategories(int $param, array $expected): void
    {
        $reflection = new ReflectionClass($this->target);
        $method = $reflection->getMethod('makeCategories');
        $method->setAccessible(true);

        $actual = $method->invoke($this->target, $param);

        $this->assertSame($expected, $actual);
    }

    public function makeCategoriesDataProvider()
    {
        return [
            [1, [1, 0, 59, 58, 57, 56, 55, 54, 53, 52,  51, 50, 49, 48, 47, 46, 45, 44, 43, 42, 41, 40, 39, 38, 37, 36, 35, 34, 33, 32, 31, 30, 29, 28, 27, 26, 25, 24, 23, 22, 21, 20, 19, 18, 17, 16, 15, 14, 13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2]],
            [-1, [-1, 59, 58, 57, 56, 55, 54, 53, 52, 51, 50, 49, 48, 47, 46, 45, 44, 43, 42, 41, 40, 39, 38, 37, 36, 35, 34, 33, 32, 31, 30, 29, 28, 27, 26, 25, 24, 23, 22, 21, 20, 19, 18, 17, 16, 15, 14, 13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1]],
        ];
    }

    /**
     * @test
     * @dataProvider getLiveProgram_no_data_dataProvider
     * @param $regionId
     * @param $expected
     * @throws \ReflectionException
     */
    public function getLiveProgram_no_data($regionId, $expected): void
    {
        $reflection = new ReflectionClass($this->target);
        $method = $reflection->getMethod('getLiveProgram');
        $method->setAccessible(true);

        $this->topDao
            ->getTerms(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->searchConditionTextAppService
            ->getPersonalHouseholdNumbers(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->topDao
            ->findHourViewingRate(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $actual = $method->invoke($this->target, $regionId, [], [], []);

        $this->assertSame($expected, $actual);
    }

    public function getLiveProgram_no_data_dataProvider()
    {
        return [
            [1, [
                'date' => '',
                'programs' => [
                    ['channel_id' => 1, 'code_name' => 'NHK'],
                    ['channel_id' => 3, 'code_name' => 'NTV'],
                    ['channel_id' => 4, 'code_name' => 'EX'],
                    ['channel_id' => 5, 'code_name' => 'TBS'],
                    ['channel_id' => 6, 'code_name' => 'TX'],
                    ['channel_id' => 7, 'code_name' => 'CX'],
                ],
                'chart' => [],
                'categories' => [],
                'phNumbers' => [],
            ]],
            [2, [
                'date' => '',
                'programs' => [
                    ['channel_id' => 44, 'code_name' => 'NHKK'],
                    ['channel_id' => 46, 'code_name' => 'MBS'],
                    ['channel_id' => 47, 'code_name' => 'ABC'],
                    ['channel_id' => 48, 'code_name' => 'TVO'],
                    ['channel_id' => 49, 'code_name' => 'KTV'],
                    ['channel_id' => 50, 'code_name' => 'YTV'],
                ],
                'chart' => [],
                'categories' => [],
                'phNumbers' => [],
            ]],
        ];
    }

    /**
     * @test
     * @dataProvider getLiveProgram_termLoop_phNumberLoop_dataProvider
     * @param $regionId
     * @param $expected
     * @throws \ReflectionException
     */
    public function getLiveProgram_termLoop_phNumberLoop($regionId, $expected): void
    {
        $reflection = new ReflectionClass($this->target);
        $method = $reflection->getMethod('getLiveProgram');
        $method->setAccessible(true);

        $this->topDao
            ->getTerms(arg::cetera())
            ->willReturn([1, 2])
            ->shouldBeCalled();

        $this->searchConditionTextAppService
            ->getPersonalHouseholdNumbers(arg::cetera())
            ->willReturn([1, 2])
            ->shouldBeCalled();

        $this->topDao
            ->findHourViewingRate(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $actual = $method->invoke($this->target, $regionId, [], [], []);

        $this->assertSame($expected, $actual);
    }

    public function getLiveProgram_termLoop_phNumberLoop_dataProvider()
    {
        return [
            [1, [
                'date' => '',
                'programs' => [
                    ['channel_id' => 1, 'code_name' => 'NHK'],
                    ['channel_id' => 3, 'code_name' => 'NTV'],
                    ['channel_id' => 4, 'code_name' => 'EX'],
                    ['channel_id' => 5, 'code_name' => 'TBS'],
                    ['channel_id' => 6, 'code_name' => 'TX'],
                    ['channel_id' => 7, 'code_name' => 'CX'],
                ],
                'chart' => [],
                'categories' => [],
                'phNumbers' => ['1', '2'],
            ]],
            [2, [
                'date' => '',
                'programs' => [
                    ['channel_id' => 44, 'code_name' => 'NHKK'],
                    ['channel_id' => 46, 'code_name' => 'MBS'],
                    ['channel_id' => 47, 'code_name' => 'ABC'],
                    ['channel_id' => 48, 'code_name' => 'TVO'],
                    ['channel_id' => 49, 'code_name' => 'KTV'],
                    ['channel_id' => 50, 'code_name' => 'YTV'],
                ],
                'chart' => [],
                'categories' => [],
                'phNumbers' => ['1', '2'],
            ]],
        ];
    }

    /**
     * @test
     * @dataProvider getLiveProgram_TimeReportsPrepared_dataProvider
     * @param $regionId
     * @param $expected
     * @throws \ReflectionException
     */
    public function getLiveProgram_TimeReportsPrepared_phNumberLoop($regionId, $expected): void
    {
        $reflection = new ReflectionClass($this->target);
        $method = $reflection->getMethod('getLiveProgram');
        $method->setAccessible(true);

        $termList = new \stdClass();
        $termList->name = 'TimeReportsPrepared';
        $termList->datetime = '2019-01-01';

        $this->topDao
            ->getTerms(arg::cetera())
            ->willReturn([$termList])
            ->shouldBeCalled();

        $this->searchConditionTextAppService
            ->getPersonalHouseholdNumbers(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->topDao
            ->findHourViewingRate(arg::cetera())
            ->willReturn([['minute' => 1]])
            ->shouldBeCalled();

        $actual = $method->invoke($this->target, $regionId, [], [], []);

        $this->assertSame($expected, $actual);
    }

    public function getLiveProgram_TimeReportsPrepared_dataProvider()
    {
        return [
            [1, [
                'date' => '2019年01月01日（Tue）  00:01',
                'programs' => [
                    [
                        'channel_id' => 1,
                        'code_name' => 'NHK',
                        'viewing_rate' => null,
                        'color' => null,
                    ],
                    [
                        'channel_id' => 3,
                        'code_name' => 'NTV',
                        'viewing_rate' => null,
                        'color' => null,
                    ],
                    [
                        'channel_id' => 4,
                        'code_name' => 'EX',
                        'viewing_rate' => null,
                        'color' => null,
                    ],
                    [
                        'channel_id' => 5,
                        'code_name' => 'TBS',
                        'viewing_rate' => null,
                        'color' => null,
                    ],
                    [
                        'channel_id' => 6,
                        'code_name' => 'TX',
                        'viewing_rate' => null,
                        'color' => null,
                    ],
                    [
                        'channel_id' => 7,
                        'code_name' => 'CX',
                        'viewing_rate' => null,
                        'color' => null,
                    ],
                ],
                'chart' => [],
                'categories' => [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 0, 1],
                'phNumbers' => [],
            ]],
            [2, [
                'date' => '2019年01月01日（Tue）  00:01',
                'programs' => [
                    [
                        'channel_id' => 44,
                        'code_name' => 'NHKK',
                        'viewing_rate' => null,
                        'color' => null,
                    ],
                    [
                        'channel_id' => 46,
                        'code_name' => 'MBS',
                        'viewing_rate' => null,
                        'color' => null,
                    ],
                    [
                        'channel_id' => 47,
                        'code_name' => 'ABC',
                        'viewing_rate' => null,
                        'color' => null,
                    ],
                    [
                        'channel_id' => 48,
                        'code_name' => 'TVO',
                        'viewing_rate' => null,
                        'color' => null,
                    ],
                    [
                        'channel_id' => 49,
                        'code_name' => 'KTV',
                        'viewing_rate' => null,
                        'color' => null,
                    ],
                    [
                        'channel_id' => 50,
                        'code_name' => 'YTV',
                        'viewing_rate' => null,
                        'color' => null,
                    ],
                ],
                'chart' => [],
                'categories' => [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 0, 1],
                'phNumbers' => [],
            ]],
        ];
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getLiveProgram_programs_empty(): void
    {
        $regionId = 1;

        $reflection = new ReflectionClass($this->target);
        $method = $reflection->getMethod('getLiveProgram');
        $method->setAccessible(true);

        $termList = new \stdClass();
        $termList->name = 'TimeReportsPrepared';
        $termList->datetime = '2019-01-01';

        $this->topDao
            ->getTerms(arg::cetera())
            ->willReturn([$termList])
            ->shouldBeCalled();

        $this->searchConditionTextAppService
            ->getPersonalHouseholdNumbers(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->topDao
            ->findHourViewingRate(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $expected = ['date' => '', 'programs' => [], 'chart' => [], 'categories' => [], 'phNumbers' => []];

        $actual = $method->invoke($this->target, $regionId, [], [], []);

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @dataProvider getLiveProgram_ViewingRates_dataProvider
     * @param $regionId
     * @param $expected
     * @throws \ReflectionException
     */
    public function getLiveProgram_ViewingRates($regionId, $expected): void
    {
        $reflection = new ReflectionClass($this->target);
        $method = $reflection->getMethod('getLiveProgram');
        $method->setAccessible(true);

        $termList = new \stdClass();
        $termList->name = 'TimeReportsPrepared';
        $termList->datetime = '2019-01-01';

        $this->topDao
            ->getTerms(arg::cetera())
            ->willReturn([$termList])
            ->shouldBeCalled();

        $this->searchConditionTextAppService
            ->getPersonalHouseholdNumbers(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->topDao
            ->findHourViewingRate(arg::cetera())
            ->willReturn([['viewing_rate' => 1, 'channel_id' => 1, 'minute' => 1], ['viewing_rate' => 2, 'channel_id' => 2]])
            ->shouldBeCalled();

        $actual = $method->invoke($this->target, $regionId, [], [], []);

        $this->assertSame($expected, $actual);
    }

    public function getLiveProgram_ViewingRates_dataProvider()
    {
        return [
            [1, [
                'date' => '2019年01月01日（Tue）  00:01',
                'programs' => [
                    [
                        'channel_id' => 1,
                        'code_name' => 'NHK',
                        'viewing_rate' => 1,
                        'color' => null,
                    ],
                    [
                        'channel_id' => 3,
                        'code_name' => 'NTV',
                        'viewing_rate' => null,
                        'color' => null,
                    ],
                    [
                        'channel_id' => 4,
                        'code_name' => 'EX',
                        'viewing_rate' => null,
                        'color' => null,
                    ],
                    [
                        'channel_id' => 5,
                        'code_name' => 'TBS',
                        'viewing_rate' => null,
                        'color' => null,
                    ],
                    [
                        'channel_id' => 6,
                        'code_name' => 'TX',
                        'viewing_rate' => null,
                        'color' => null,
                    ],
                    [
                        'channel_id' => 7,
                        'code_name' => 'CX',
                        'viewing_rate' => null,
                        'color' => null,
                    ],
                ],
                'chart' => [
                    [
                        'name' => 'NHK',
                        'color' => null,
                        'data' => [
                            ['x' => null, 'y' => 1],
                        ],
                    ],
                    [
                        'name' => null,
                        'color' => null,
                        'data' => [
                            ['x' => null, 'y' => 2],
                        ],
                    ],
                ],
                'categories' => [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 0, 1],
                'phNumbers' => [],
            ]],
            [2, [
                'date' => '2019年01月01日（Tue）  00:01',
                'programs' => [
                    [
                        'channel_id' => 44,
                        'code_name' => 'NHKK',
                        'viewing_rate' => null,
                        'color' => null,
                    ],
                    [
                        'channel_id' => 46,
                        'code_name' => 'MBS',
                        'viewing_rate' => null,
                        'color' => null,
                    ],
                    [
                        'channel_id' => 47,
                        'code_name' => 'ABC',
                        'viewing_rate' => null,
                        'color' => null,
                    ],
                    [
                        'channel_id' => 48,
                        'code_name' => 'TVO',
                        'viewing_rate' => null,
                        'color' => null,
                    ],
                    [
                        'channel_id' => 49,
                        'code_name' => 'KTV',
                        'viewing_rate' => null,
                        'color' => null,
                    ],
                    [
                        'channel_id' => 50,
                        'code_name' => 'YTV',
                        'viewing_rate' => null,
                        'color' => null,
                    ],
                ],
                'chart' => [
                    [
                        'name' => null,
                        'color' => null,
                        'data' => [
                            ['x' => null, 'y' => 1],
                        ],
                    ],
                    [
                        'name' => null,
                        'color' => null,
                        'data' => [
                            ['x' => null, 'y' => 2],
                        ],
                    ],
                ],
                'categories' => [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 0, 1],
                'phNumbers' => [],
            ]],
        ];
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param $regionId
     * @param mixed $params
     */
    public function invoke($regionId, $params): void
    {
        $this->topDao
            ->getTerms(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->searchConditionTextAppService
            ->getPersonalHouseholdNumbers(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->topDao
            ->findHourViewingRate(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $input = new InputData(
            $regionId,
            [],
            []
        );

        $this->target->__invoke($input);

        $outputData = new OutputData(...$params);
        $this->outputBoundary->__invoke($outputData)->shouldBeCalled();
        $this->target->__invoke($input);
    }

    public function dataProvider()
    {
        return [
            [
                1,
                ['',
                 [['channel_id' => 1, 'code_name' => 'NHK'], ['channel_id' => 3, 'code_name' => 'NTV'], ['channel_id' => 4, 'code_name' => 'EX'], ['channel_id' => 5, 'code_name' => 'TBS'], ['channel_id' => 6, 'code_name' => 'TX'], ['channel_id' => 7, 'code_name' => 'CX']],
                 [],
                 [],
                 [], ],
            ],
            [
                2,
                ['',
                    [['channel_id' => 44, 'code_name' => 'NHKK'], ['channel_id' => 46, 'code_name' => 'MBS'], ['channel_id' => 47, 'code_name' => 'ABC'], ['channel_id' => 48, 'code_name' => 'TVO'], ['channel_id' => 49, 'code_name' => 'KTV'], ['channel_id' => 50, 'code_name' => 'YTV']],
                    [],
                    [],
                    [], ],
            ],
        ];
    }
}
