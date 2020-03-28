<?php

namespace Switchm\SmartApi\Components\Tests\Common;

use Carbon\Carbon;
use Prophecy\Argument as arg;
use Switchm\SmartApi\Components\Common\CreateTableData;
use Switchm\SmartApi\Components\Common\Exceptions\SampleCountException;
use Switchm\SmartApi\Components\Common\RatingPoint;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Queries\Dao\Dwh\PerHourlyDao;
use Switchm\SmartApi\Queries\Dao\Rdb\HourlyReportDao;
use Switchm\SmartApi\Queries\Services\HolidayService;
use Switchm\SmartApi\Queries\Services\SampleService;

class RatingPointTest extends TestCase
{
    private $perHourlyDao;

    private $rdbHourlyDao;

    private $hourlyReportDao;

    private $sampleService;

    private $holidayService;

    private $createTableData;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->perHourlyDao = $this->prophesize(PerHourlyDao::class);
        $this->rdbHourlyDao = $this->prophesize(\Switchm\SmartApi\Queries\Dao\Rdb\PerHourlyDao::class);
        $this->hourlyReportDao = $this->prophesize(HourlyReportDao::class);
        $this->sampleService = $this->prophesize(SampleService::class);
        $this->holidayService = $this->prophesize(HolidayService::class);
        $this->createTableData = $this->prophesize(CreateTableData::class);

        $this->target = new RatingPoint(
            $this->perHourlyDao->reveal(),
            $this->rdbHourlyDao->reveal(),
            $this->hourlyReportDao->reveal(),
            $this->sampleService->reveal(),
            $this->holidayService->reveal(),
            $this->createTableData->reveal()
        );
    }

    /**
     * @test
     * @dataProvider getDateListProvider
     * @param $csvFlag
     */
    public function getDateList($csvFlag): void
    {
        $this->holidayService
            ->getDateList(arg::cetera())
            ->willReturn([[], [], []])
            ->shouldBeCalled();

        $expected = [[], [], []];

        $weekStartDateTime = new Carbon('2019-01-01 05:00:00');
        $weekEndDateTime = new Carbon('2019-01-07 04:59:59');

        $actual = $this->target->getDateList($weekStartDateTime, $weekEndDateTime, $csvFlag);

        $this->assertSame($expected, $actual);
    }

    public function getDateListProvider()
    {
        return [
            ['0'],
            ['1'],
        ];
    }

    /**
     * @test
     * @dataProvider initDateDataProvider
     * @param $startDateTime
     * @param $endDateTime
     * @param $hour
     * @param $expected
     */
    public function testInitDate($startDateTime, $endDateTime, $hour, $expected): void
    {
        $actual = $this->target->initDate($startDateTime, $endDateTime, $hour);
        $this->assertEquals($expected, $actual);
    }

    public function initDateDataProvider()
    {
        return [
            [
                '2019-01-01 05:00:00',
                '2019-01-07 04:59:59',
                'hourly',
                [
                    new Carbon('2019-01-01 05:00:00'),
                    new Carbon('2019-01-08 04:00:00'),
                    new Carbon('2018-12-31 05:00:00'),
                    new Carbon('2019-01-14 04:00:00.999999'),
                ],
            ],
            [
                '2019-01-01 05:00:00',
                '2019-01-07 04:59:59',
                1,
                [
                    new Carbon('2019-01-02 01:00:00'),
                    new Carbon('2019-01-08 01:59:00'),
                    new Carbon('2018-12-31 00:00:00'),
                    new Carbon('2019-01-13 23:59:59.999999'),
                ],
            ],
            [
                '2019-01-01 05:00:00',
                '2019-01-07 04:59:59',
                10,
                [
                    new Carbon('2019-01-01 10:00:00'),
                    new Carbon('2019-01-07 10:59:00'),
                    new Carbon('2018-12-31 00:00:00'),
                    new Carbon('2019-01-13 23:59:59.999999'),
                ],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider getChannelIdsDataProvider
     * @param string $channelType
     * @param int $regionId
     * @param null|array $channels
     * @param $expected
     */
    public function testGetChannelIds(string $channelType, int $regionId, ?array $channels, $expected): void
    {
        $actual = $this->target->getChannelIds($channelType, $regionId, $channels);
        $this->assertSame($expected, $actual);
    }

    public function getChannelIdsDataProvider()
    {
        return [
            [
                'advertising',
                1,
                [],
                [],
            ],
            [
                'dt1',
                1,
                [],
                [1, 3, 4, 5, 6, 7],
            ],
            [
                'dt2',
                1,
                [],
                [2, 8, 9, 10, 11, 12, 13, 14],
            ],
            [
                'bs1',
                1,
                [],
                [15, 16, 17, 18],
            ],
            [
                'bs2',
                1,
                [],
                [19, 20, 21, 22],
            ],
            [
                'bs1',
                1,
                [],
                [15, 16, 17, 18],
            ],
            [
                'bs3',
                1,
                [],
                [23, 24, 25],
            ],
            [
                'summary',
                1,
                [],
                [-10, -11, -12],
            ],
            [
                'dt1',
                2,
                [],
                [44, 46, 47, 48, 49, 50],
            ],
            [
                'dt2',
                2,
                [],
                [45, 51, 52, 53, 54, 55],
            ],
            [
                'summary',
                2,
                [],
                [-10, -11, -12],
            ],
        ];
    }

    /**
     * @test
     * @throws SampleCountException
     */
    public function getConditionCrossCount_divisionがcondition_crossではない場合(): void
    {
        $this->sampleService
            ->getConditionCrossCount(arg::cetera())
            ->willReturn()
            ->shouldNotBeCalled();

        $expected = [0, 0];

        $division = 'ga8';
        $conditionCross = [];
        $startDate = '2019-01-01';
        $endDate = '2019-01-07';
        $regionId = 1;
        $sampleCountMaxNumber = 50;
        $dataTypeFlags = ['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false];

        $actual = $this->target->getConditionCrossCount(
            $division,
            $conditionCross,
            $startDate,
            $endDate,
            $regionId,
            $sampleCountMaxNumber,
            $dataTypeFlags
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @dataProvider notExceptionGetConditionCrossCountDataProvider
     * @param $providedDataTypeFlags
     * @param $providedExpected
     * @throws SampleCountException
     */
    public function getConditionCrossCount_divisionがcondition_crossかつcntが50以上の場合($providedDataTypeFlags, $providedExpected): void
    {
        $this->sampleService
            ->getConditionCrossCount(arg::cetera())
            ->willReturn(50)
            ->shouldBeCalled();

        $division = 'condition_cross';
        $conditionCross = [];
        $startDate = '2019-01-01';
        $endDate = '2019-01-07';
        $regionId = 1;
        $sampleCountMaxNumber = 50;
        $dataTypeFlags = $providedDataTypeFlags;

        $expected = $providedExpected;

        $actual = $this->target->getConditionCrossCount(
            $division,
            $conditionCross,
            $startDate,
            $endDate,
            $regionId,
            $sampleCountMaxNumber,
            $dataTypeFlags
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array
     */
    public function notExceptionGetConditionCrossCountDataProvider()
    {
        return [
            'リアルタイム' => [['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false], [50, 0]],
            'タイムシフト' => [['isRt' => false, 'isTs' => true, 'isGross' => true, 'isTotal' => true, 'isRtTotal' => true], [0, 50]],
        ];
    }

    /**
     * @test
     * @dataProvider exceptionGetConditionCrossCountDataProvider
     * @param $providedDataTypeFlags
     * @throws SampleCountException
     */
    public function getConditionCrossCount_divisionがcondition_crossかつかつcntが50未満($providedDataTypeFlags): void
    {
        $this->expectException(SampleCountException::class);

        $this->sampleService
            ->getConditionCrossCount(arg::cetera())
            ->willReturn(49)
            ->shouldBeCalled();

        $division = 'condition_cross';
        $conditionCross = [];
        $startDate = '2019-01-01';
        $endDate = '2019-01-07';
        $regionId = 1;
        $sampleCountMaxNumber = 50;
        $dataTypeFlags = $providedDataTypeFlags;

        $this->target->getConditionCrossCount(
            $division,
            $conditionCross,
            $startDate,
            $endDate,
            $regionId,
            $sampleCountMaxNumber,
            $dataTypeFlags
        );
    }

    /**
     * @return array
     */
    public function exceptionGetConditionCrossCountDataProvider()
    {
        return [
            'リアルタイム' => [['isRt' => true, 'isTs' => false, 'isGross' => false, 'isTotal' => false, 'isRtTotal' => false]],
            'タイムシフト' => [['isRt' => false, 'isTs' => true, 'isGross' => true, 'isTotal' => true, 'isRtTotal' => true]],
        ];
    }

    /**
     * @test
     */
    public function getDataAndAlias_dataDivisionがviewing_rateかつ掛け合わせ条件の場合(): void
    {
        $this->rdbHourlyDao
            ->getConditionCrossRatingData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getConditionCrossRatingData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbHourlyDao
            ->getRatingData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getRatingData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbHourlyDao
            ->getConditionCrossShareData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getConditionCrossShareData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbHourlyDao
            ->getShareData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getShareData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbHourlyDao
            ->getConditionCrossTargetData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getConditionCrossTargetData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbHourlyDao
            ->getTargetData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getTargetData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $dataDivision = 'viewing_rate';
        $isCrossCondition = true;
        $isOriginal = true;
        $params = [new Carbon(), new Carbon(), [], '', [], 'ga8', ['personal'], '', [], false, 1, [], [], 100, 60, 'codePrefix', 'codeNumberPrefix', 'selectedPersonalname'];

        $expected = [[], 'viewing_rate'];

        $actual = $this->target->getDataAndAlias($dataDivision, $isCrossCondition, $isOriginal, $params);

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function getDataAndAlias_dataDivisionがviewing_rateかつNot掛け合わせ条件の場合(): void
    {
        $this->rdbHourlyDao
            ->getConditionCrossRatingData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getConditionCrossRatingData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbHourlyDao
            ->getRatingData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getRatingData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbHourlyDao
            ->getConditionCrossShareData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getConditionCrossShareData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbHourlyDao
            ->getShareData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getShareData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbHourlyDao
            ->getConditionCrossTargetData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getConditionCrossTargetData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbHourlyDao
            ->getTargetData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getTargetData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $dataDivision = 'viewing_rate';
        $isCrossCondition = false;
        $isOriginal = false;
        $params = [new Carbon(), new Carbon(), [], '', [], 'ga8', 'personal', '', [], false, 1, [], [], 100, 60, 'codePrefix', 'codeNumberPrefix', 'selectedPersonalname'];

        $expected = [[], 'viewing_rate'];

        $actual = $this->target->getDataAndAlias($dataDivision, $isCrossCondition, $isOriginal, $params);

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function getDataAndAlias_dataDivisionがviewing_rate_shareかつ掛け合わせ条件の場合(): void
    {
        $this->rdbHourlyDao
            ->getConditionCrossRatingData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getConditionCrossRatingData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbHourlyDao
            ->getRatingData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getRatingData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbHourlyDao
            ->getConditionCrossShareData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getConditionCrossShareData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbHourlyDao
            ->getShareData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getShareData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbHourlyDao
            ->getConditionCrossTargetData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getConditionCrossTargetData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbHourlyDao
            ->getTargetData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getTargetData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $dataDivision = 'viewing_rate_share';
        $isCrossCondition = true;
        $isOriginal = true;
        $params = [new Carbon(), new Carbon(), [], '', [], 'ga8', ['personal'], '', [], false, 1, [], [], 100, 60, 'codePrefix', 'codeNumberPrefix', 'selectedPersonalname'];

        $expected = [[], 'share'];

        $actual = $this->target->getDataAndAlias($dataDivision, $isCrossCondition, $isOriginal, $params);

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function getDataAndAlias_dataDivisionがviewing_rate_shareかつNot掛け合わせ条件の場合(): void
    {
        $this->rdbHourlyDao
            ->getConditionCrossRatingData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getConditionCrossRatingData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbHourlyDao
            ->getRatingData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getRatingData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbHourlyDao
            ->getConditionCrossShareData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getConditionCrossShareData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbHourlyDao
            ->getShareData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getShareData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbHourlyDao
            ->getConditionCrossTargetData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getConditionCrossTargetData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbHourlyDao
            ->getTargetData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getTargetData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $dataDivision = 'viewing_rate_share';
        $isCrossCondition = false;
        $isOriginal = false;
        $params = [new Carbon(), new Carbon(), [], '', [], 'ga8', 'personal', '', [], false, 1, [], [], 100, 60, 'codePrefix', 'codeNumberPrefix', 'selectedPersonalname'];

        $expected = [[], 'share'];

        $actual = $this->target->getDataAndAlias($dataDivision, $isCrossCondition, $isOriginal, $params);

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function getDataAndAlias_dataDivisionがtarget_content_personalかつ掛け合わせ条件の場合(): void
    {
        $this->rdbHourlyDao
            ->getConditionCrossRatingData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getConditionCrossRatingData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbHourlyDao
            ->getRatingData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getRatingData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbHourlyDao
            ->getConditionCrossShareData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getConditionCrossShareData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbHourlyDao
            ->getShareData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getShareData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbHourlyDao
            ->getConditionCrossTargetData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getConditionCrossTargetData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbHourlyDao
            ->getTargetData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getTargetData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $dataDivision = 'target_content_personal';
        $isCrossCondition = true;
        $isOriginal = true;
        $params = [new Carbon(), new Carbon(), [], '', [], 'ga8', ['personal'], '', [], false, 1, [], [], 100, 60, 'codePrefix', 'codeNumberPrefix', 'selectedPersonalname'];

        $expected = [[], 'target_viewing_rate'];

        $actual = $this->target->getDataAndAlias($dataDivision, $isCrossCondition, $isOriginal, $params);

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function getDataAndAlias_dataDivisionがtarget_content_householdかつNot掛け合わせ条件の場合(): void
    {
        $this->rdbHourlyDao
            ->getConditionCrossRatingData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getConditionCrossRatingData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbHourlyDao
            ->getRatingData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getRatingData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbHourlyDao
            ->getConditionCrossShareData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getConditionCrossShareData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbHourlyDao
            ->getShareData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getShareData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbHourlyDao
            ->getConditionCrossTargetData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getConditionCrossTargetData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->rdbHourlyDao
            ->getTargetData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->perHourlyDao
            ->getTargetData(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $dataDivision = 'target_content_household';
        $isCrossCondition = false;
        $isOriginal = false;
        $params = [new Carbon(), new Carbon(), [], '', [], 'ga8', 'personal', '', [], false, 1, [], [], 100, 60, 'codePrefix', 'codeNumberPrefix', 'selectedPersonalname'];

        $expected = [[], 'target_viewing_rate'];

        $actual = $this->target->getDataAndAlias($dataDivision, $isCrossCondition, $isOriginal, $params);

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function getConditionCrossRatingData_Rdb(): void
    {
        $this->rdbHourlyDao
            ->getConditionCrossRatingData(arg::cetera())
            ->shouldBeCalled()
            ->willReturn([]);

        $this->perHourlyDao
            ->getConditionCrossRatingData(arg::cetera())
            ->shouldNotBeCalled()
            ->willReturn([]);

        $expected = [];

        $startDateTime = new Carbon();
        $endDateTime = new Carbon();
        $period['isRdb'] = true;
        $period['isDwh'] = false;
        $channelType = '';
        $channelIds = [];
        $division = '';
        $code = null;
        $dataDivision = '';
        $conditionCross = [];
        $isOriginal = true;
        $regionId = 1;
        $dataType = [];
        $dataTypeFlags = [];
        $intervalHourly = 100;
        $intervalMinutes = 60;
        $codePrefix = 'codePrefix';
        $codeNumberPrefix = 'codeNumberPrefix';
        $selectedPersonalName = 'selectedPersonalName';

        $actual = $this->target->getConditionCrossRatingData(
            $startDateTime,
            $endDateTime,
            $period,
            $channelType,
            $channelIds,
            $division,
            $code,
            $dataDivision,
            $conditionCross,
            $isOriginal,
            $regionId,
            $dataType,
            $dataTypeFlags,
            $intervalHourly,
            $intervalMinutes,
            $codePrefix,
            $codeNumberPrefix,
            $selectedPersonalName
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function getConditionCrossRatingData_Dwh(): void
    {
        $this->rdbHourlyDao
            ->getConditionCrossRatingData(arg::cetera())
            ->shouldNotBeCalled()
            ->willReturn([]);

        $this->perHourlyDao
            ->getConditionCrossRatingData(arg::cetera())
            ->shouldBeCalled()
            ->willReturn([]);

        $expected = [];

        $startDateTime = new Carbon();
        $endDateTime = new Carbon();
        $period['isRdb'] = false;
        $period['isDwh'] = true;
        $channelType = '';
        $channelIds = [];
        $division = '';
        $code = null;
        $dataDivision = '';
        $conditionCross = [];
        $isOriginal = true;
        $regionId = 1;
        $dataType = [];
        $dataTypeFlags = [];
        $intervalHourly = 100;
        $intervalMinutes = 60;
        $codePrefix = 'codePrefix';
        $codeNumberPrefix = 'codeNumberPrefix';
        $selectedPersonalName = 'selectedPersonalName';

        $actual = $this->target->getConditionCrossRatingData(
            $startDateTime,
            $endDateTime,
            $period,
            $channelType,
            $channelIds,
            $division,
            $code,
            $dataDivision,
            $conditionCross,
            $isOriginal,
            $regionId,
            $dataType,
            $dataTypeFlags,
            $intervalHourly,
            $intervalMinutes,
            $codePrefix,
            $codeNumberPrefix,
            $selectedPersonalName
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getRatingData_Rdb(): void
    {
        $this->rdbHourlyDao
            ->getRatingData(arg::cetera())
            ->shouldBeCalled()
            ->willReturn([]);

        $this->perHourlyDao
            ->getRatingData(arg::cetera())
            ->shouldNotBeCalled()
            ->willReturn([]);

        $expected = [];

        $startDateTime = new Carbon();
        $endDateTime = new Carbon();
        $period['isRdb'] = true;
        $period['isDwh'] = false;
        $channelType = '';
        $channelIds = [];
        $division = '';
        $code = '';
        $dataDivision = '';
        $conditionCross = [];
        $isOriginal = true;
        $regionId = 1;
        $dataType = [];
        $dataTypeFlags = [];
        $intervalHourly = 100;
        $intervalMinutes = 60;
        $codePrefix = 'codePrefix';
        $codeNumberPrefix = 'codeNumberPrefix';
        $selectedPersonalName = 'selectedPersonalName';

        $actual = $this->target->getRatingData(
            $startDateTime,
            $endDateTime,
            $period,
            $channelType,
            $channelIds,
            $division,
            $code,
            $dataDivision,
            $conditionCross,
            $isOriginal,
            $regionId,
            $dataType,
            $dataTypeFlags,
            $intervalHourly,
            $intervalMinutes,
            $codePrefix,
            $codeNumberPrefix,
            $selectedPersonalName
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getRatingData_Dwh(): void
    {
        $this->rdbHourlyDao
            ->getRatingData(arg::cetera())
            ->shouldNotBeCalled()
            ->willReturn([]);

        $this->perHourlyDao
            ->getRatingData(arg::cetera())
            ->shouldBeCalled()
            ->willReturn([]);

        $expected = [];

        $startDateTime = new Carbon();
        $endDateTime = new Carbon();
        $period['isRdb'] = false;
        $period['isDwh'] = true;
        $channelType = '';
        $channelIds = [];
        $division = '';
        $code = '';
        $dataDivision = '';
        $conditionCross = [];
        $isOriginal = true;
        $regionId = 1;
        $dataType = [];
        $dataTypeFlags = [];
        $intervalHourly = 100;
        $intervalMinutes = 60;
        $codePrefix = 'codePrefix';
        $codeNumberPrefix = 'codeNumberPrefix';
        $selectedPersonalName = 'selectedPersonalName';

        $actual = $this->target->getRatingData(
            $startDateTime,
            $endDateTime,
            $period,
            $channelType,
            $channelIds,
            $division,
            $code,
            $dataDivision,
            $conditionCross,
            $isOriginal,
            $regionId,
            $dataType,
            $dataTypeFlags,
            $intervalHourly,
            $intervalMinutes,
            $codePrefix,
            $codeNumberPrefix,
            $selectedPersonalName
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getConditionCrossShareData_Rdb(): void
    {
        $this->rdbHourlyDao
            ->getConditionCrossShareData(arg::cetera())
            ->shouldBeCalled()
            ->willReturn([]);

        $this->perHourlyDao
            ->getConditionCrossShareData(arg::cetera())
            ->shouldNotBeCalled()
            ->willReturn([]);

        $expected = [];

        $startDateTime = new Carbon();
        $endDateTime = new Carbon();
        $period['isRdb'] = true;
        $period['isDwh'] = false;
        $channelType = '';
        $channelIds = [];
        $division = '';
        $code = null;
        $dataDivision = '';
        $conditionCross = [];
        $isOriginal = true;
        $regionId = 1;
        $dataType = [];
        $dataTypeFlags = [];
        $intervalHourly = 100;
        $intervalMinutes = 60;
        $codePrefix = 'codePrefix';
        $codeNumberPrefix = 'codeNumberPrefix';
        $selectedPersonalName = 'selectedPersonalName';

        $actual = $this->target->getConditionCrossShareData(
            $startDateTime,
            $endDateTime,
            $period,
            $channelType,
            $channelIds,
            $division,
            $code,
            $dataDivision,
            $conditionCross,
            $isOriginal,
            $regionId,
            $dataType,
            $dataTypeFlags,
            $intervalHourly,
            $intervalMinutes,
            $codePrefix,
            $codeNumberPrefix,
            $selectedPersonalName
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function getConditionCrossShareData_Dwh(): void
    {
        $this->rdbHourlyDao
            ->getConditionCrossShareData(arg::cetera())
            ->shouldNotBeCalled()
            ->willReturn([]);

        $this->perHourlyDao
            ->getConditionCrossShareData(arg::cetera())
            ->shouldBeCalled()
            ->willReturn([]);

        $expected = [];

        $startDateTime = new Carbon();
        $endDateTime = new Carbon();
        $period['isRdb'] = false;
        $period['isDwh'] = true;
        $channelType = '';
        $channelIds = [];
        $division = '';
        $code = null;
        $dataDivision = '';
        $conditionCross = [];
        $isOriginal = true;
        $regionId = 1;
        $dataType = [];
        $dataTypeFlags = [];
        $intervalHourly = 100;
        $intervalMinutes = 60;
        $codePrefix = 'codePrefix';
        $codeNumberPrefix = 'codeNumberPrefix';
        $selectedPersonalName = 'selectedPersonalName';

        $actual = $this->target->getConditionCrossShareData(
            $startDateTime,
            $endDateTime,
            $period,
            $channelType,
            $channelIds,
            $division,
            $code,
            $dataDivision,
            $conditionCross,
            $isOriginal,
            $regionId,
            $dataType,
            $dataTypeFlags,
            $intervalHourly,
            $intervalMinutes,
            $codePrefix,
            $codeNumberPrefix,
            $selectedPersonalName
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getShareData_Rdb(): void
    {
        $this->rdbHourlyDao
            ->getShareData(arg::cetera())
            ->shouldBeCalled()
            ->willReturn([]);

        $this->perHourlyDao
            ->getShareData(arg::cetera())
            ->shouldNotBeCalled()
            ->willReturn([]);

        $expected = [];

        $startDateTime = new Carbon();
        $endDateTime = new Carbon();
        $period['isRdb'] = true;
        $period['isDwh'] = false;
        $channelType = '';
        $channelIds = [];
        $division = '';
        $code = '';
        $dataDivision = '';
        $conditionCross = [];
        $isOriginal = true;
        $regionId = 1;
        $dataType = [];
        $dataTypeFlags = [];
        $intervalHourly = 100;
        $intervalMinutes = 60;
        $codePrefix = 'codePrefix';
        $codeNumberPrefix = 'codeNumberPrefix';
        $selectedPersonalName = 'selectedPersonalName';

        $actual = $this->target->getShareData(
            $startDateTime,
            $endDateTime,
            $period,
            $channelType,
            $channelIds,
            $division,
            $code,
            $dataDivision,
            $conditionCross,
            $isOriginal,
            $regionId,
            $dataType,
            $dataTypeFlags,
            $intervalHourly,
            $intervalMinutes,
            $codePrefix,
            $codeNumberPrefix,
            $selectedPersonalName
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getShareData_Dwh(): void
    {
        $this->rdbHourlyDao
            ->getShareData(arg::cetera())
            ->shouldNotBeCalled()
            ->willReturn([]);

        $this->perHourlyDao
            ->getShareData(arg::cetera())
            ->shouldBeCalled()
            ->willReturn([]);

        $expected = [];

        $startDateTime = new Carbon();
        $endDateTime = new Carbon();
        $period['isRdb'] = false;
        $period['isDwh'] = true;
        $channelType = '';
        $channelIds = [];
        $division = '';
        $code = '';
        $dataDivision = '';
        $conditionCross = [];
        $isOriginal = true;
        $regionId = 1;
        $dataType = [];
        $dataTypeFlags = [];
        $intervalHourly = 100;
        $intervalMinutes = 60;
        $codePrefix = 'codePrefix';
        $codeNumberPrefix = 'codeNumberPrefix';
        $selectedPersonalName = 'selectedPersonalName';

        $actual = $this->target->getShareData(
            $startDateTime,
            $endDateTime,
            $period,
            $channelType,
            $channelIds,
            $division,
            $code,
            $dataDivision,
            $conditionCross,
            $isOriginal,
            $regionId,
            $dataType,
            $dataTypeFlags,
            $intervalHourly,
            $intervalMinutes,
            $codePrefix,
            $codeNumberPrefix,
            $selectedPersonalName
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getConditionCrossTargetData_Rdb(): void
    {
        $this->rdbHourlyDao
            ->getConditionCrossTargetData(arg::cetera())
            ->shouldBeCalled()
            ->willReturn([]);

        $this->perHourlyDao
            ->getConditionCrossTargetData(arg::cetera())
            ->shouldNotBeCalled()
            ->willReturn([]);

        $expected = [];

        $startDateTime = new Carbon();
        $endDateTime = new Carbon();
        $period['isRdb'] = true;
        $period['isDwh'] = false;
        $channelType = '';
        $channelIds = [];
        $division = '';
        $code = null;
        $dataDivision = '';
        $conditionCross = [];
        $isOriginal = true;
        $regionId = 1;
        $dataType = [];
        $dataTypeFlags = [];
        $intervalHourly = 100;
        $intervalMinutes = 60;
        $codePrefix = 'codePrefix';
        $codeNumberPrefix = 'codeNumberPrefix';
        $selectedPersonalName = 'selectedPersonalName';

        $actual = $this->target->getConditionCrossTargetData(
            $startDateTime,
            $endDateTime,
            $period,
            $channelType,
            $channelIds,
            $division,
            $code,
            $dataDivision,
            $conditionCross,
            $isOriginal,
            $regionId,
            $dataType,
            $dataTypeFlags,
            $intervalHourly,
            $intervalMinutes,
            $codePrefix,
            $codeNumberPrefix,
            $selectedPersonalName
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function getConditionCrossTargetData_Dwh(): void
    {
        $this->rdbHourlyDao
            ->getConditionCrossTargetData(arg::cetera())
            ->shouldNotBeCalled()
            ->willReturn([]);

        $this->perHourlyDao
            ->getConditionCrossTargetData(arg::cetera())
            ->shouldBeCalled()
            ->willReturn([]);

        $expected = [];

        $startDateTime = new Carbon();
        $endDateTime = new Carbon();
        $period['isRdb'] = false;
        $period['isDwh'] = true;
        $channelType = '';
        $channelIds = [];
        $division = '';
        $code = null;
        $dataDivision = '';
        $conditionCross = [];
        $isOriginal = true;
        $regionId = 1;
        $dataType = [];
        $dataTypeFlags = [];
        $intervalHourly = 100;
        $intervalMinutes = 60;
        $codePrefix = 'codePrefix';
        $codeNumberPrefix = 'codeNumberPrefix';
        $selectedPersonalName = 'selectedPersonalName';

        $actual = $this->target->getConditionCrossTargetData(
            $startDateTime,
            $endDateTime,
            $period,
            $channelType,
            $channelIds,
            $division,
            $code,
            $dataDivision,
            $conditionCross,
            $isOriginal,
            $regionId,
            $dataType,
            $dataTypeFlags,
            $intervalHourly,
            $intervalMinutes,
            $codePrefix,
            $codeNumberPrefix,
            $selectedPersonalName
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getTargetData_Rdb(): void
    {
        $this->rdbHourlyDao
            ->getTargetData(arg::cetera())
            ->shouldBeCalled()
            ->willReturn([]);

        $this->perHourlyDao
            ->getTargetData(arg::cetera())
            ->shouldNotBeCalled()
            ->willReturn([]);

        $expected = [];

        $startDateTime = new Carbon();
        $endDateTime = new Carbon();
        $period['isRdb'] = true;
        $period['isDwh'] = false;
        $channelType = '';
        $channelIds = [];
        $division = '';
        $code = '';
        $dataDivision = '';
        $conditionCross = [];
        $isOriginal = true;
        $regionId = 1;
        $dataType = [];
        $dataTypeFlags = [];
        $intervalHourly = 100;
        $intervalMinutes = 60;
        $codePrefix = 'codePrefix';
        $codeNumberPrefix = 'codeNumberPrefix';
        $selectedPersonalName = 'selectedPersonalName';

        $actual = $this->target->getTargetData(
            $startDateTime,
            $endDateTime,
            $period,
            $channelType,
            $channelIds,
            $division,
            $code,
            $dataDivision,
            $conditionCross,
            $isOriginal,
            $regionId,
            $dataType,
            $dataTypeFlags,
            $intervalHourly,
            $intervalMinutes,
            $codePrefix,
            $codeNumberPrefix,
            $selectedPersonalName
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getTargetData_Dwh(): void
    {
        $this->rdbHourlyDao
            ->getTargetData(arg::cetera())
            ->shouldNotBeCalled()
            ->willReturn([]);

        $this->perHourlyDao
            ->getTargetData(arg::cetera())
            ->shouldBeCalled()
            ->willReturn([]);

        $expected = [];

        $startDateTime = new Carbon();
        $endDateTime = new Carbon();
        $period['isRdb'] = false;
        $period['isDwh'] = true;
        $channelType = '';
        $channelIds = [];
        $division = '';
        $code = '';
        $dataDivision = '';
        $conditionCross = [];
        $isOriginal = true;
        $regionId = 1;
        $dataType = [];
        $dataTypeFlags = [];
        $intervalHourly = 100;
        $intervalMinutes = 60;
        $codePrefix = 'codePrefix';
        $codeNumberPrefix = 'codeNumberPrefix';
        $selectedPersonalName = 'selectedPersonalName';

        $actual = $this->target->getTargetData(
            $startDateTime,
            $endDateTime,
            $period,
            $channelType,
            $channelIds,
            $division,
            $code,
            $dataDivision,
            $conditionCross,
            $isOriginal,
            $regionId,
            $dataType,
            $dataTypeFlags,
            $intervalHourly,
            $intervalMinutes,
            $codePrefix,
            $codeNumberPrefix,
            $selectedPersonalName
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getLimit_diffが0以外(): void
    {
        $this->hourlyReportDao
            ->latest(arg::cetera())
            ->shouldNotBeCalled()
            ->willReturn(new \stdClass());

        $expected = null;

        $regionId = 1;
        $startDate = '2019-01-01';
        $endDate = '2019-01-31';

        $actual = $this->target->getLimit($regionId, $startDate, $endDate);

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @dataProvider getLimit_diffが0DataProvider
     * @param $regionId
     * @param $startDate
     * @param $endDate
     * @param $latest
     * @param $expected
     */
    public function getLimit_diffが0($regionId, $startDate, $endDate, $latest, $expected): void
    {
        $this->hourlyReportDao
            ->latest(arg::cetera())
            ->shouldBeCalled()
            ->willReturn($latest);

        $actual = $this->target->getLimit($regionId, $startDate, $endDate);

        $this->assertEquals($expected, $actual);
    }

    public function getLimit_diffが0DataProvider()
    {
        $latest = new \stdClass();
        $latest->datetime = '2019-01-01';

        return [
            [
                1,
                '2019-01-01',
                '2019-01-07',
                null,
                null,
            ],
            [
                1,
                '2019-01-01',
                '2019-01-07',
                new \stdClass(),
                null,
            ],
            [
                1,
                '2019-01-01',
                '2019-01-07',
                $latest,
                new Carbon('2019-01-01'),
            ],
        ];
    }

    /**
     * @test
     * @dataProvider convertCsvDataDataProvider
     * @param $convertData
     * @param $displayType
     * @param $channelIds
     * @param $dateList
     * @param $expected
     */
    public function convertCsvData($convertData, $displayType, $channelIds, $dateList, $expected): void
    {
        $actual = $this->target->convertCsvData($convertData, $displayType, $channelIds, $dateList);

        $this->assertSame($expected, $actual);
    }

    public function convertCsvDataDataProvider()
    {
        return [
            [
                [['dummy1'], ['dummy2'], ['dummy3']],
                'dateBy',
                [],
                [['dummyDate1'], ['dummyDate2'], ['dummyDate3']],
                [['dummy1'], ['dummy2'], ['dummy3']],
            ],
            [
                [['dummy1'], ['dummy2'], ['dummy3']],
                'dateBy',
                [1, 2, 3, 4, 5],
                [['dummyDate1'], ['dummyDate2'], ['dummyDate3']],
                [['dummy1', '', '', ''], ['dummy2', '', '', ''], ['dummy3', '', '', '']],
            ],
            [
                [['dummy1'], ['dummy2']],
                'notDateBy',
                [1],
                [['dummyDate1'], ['dummyDate2']],
                [[null, null, null, ''], [null, null, null, '']],
            ],
        ];
    }
}
