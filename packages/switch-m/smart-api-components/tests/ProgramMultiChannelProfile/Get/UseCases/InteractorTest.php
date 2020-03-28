<?php

namespace Switchm\SmartApi\Components\Tests\ProgramMultiChannelProfile\Get\UseCases;

use Prophecy\Argument as arg;
use Smart2\Application\Services\SearchConditionTextAppService;
use Switchm\SmartApi\Components\Common\Exceptions\SampleCountException;
use Switchm\SmartApi\Components\ProgramMultiChannelProfile\Get\UseCases\InputData;
use Switchm\SmartApi\Components\ProgramMultiChannelProfile\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\ProgramMultiChannelProfile\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\ProgramMultiChannelProfile\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Queries\Dao\Dwh\ProgramDao;
use Switchm\SmartApi\Queries\Services\SampleService;

class InteractorTest extends TestCase
{
    private $programDao;

    private $sampleService;

    private $searchConditionTextAppService;

    private $outputBoundary;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->programDao = $this->prophesize(ProgramDao::class);
        $this->sampleService = $this->prophesize(SampleService::class);
        $this->searchConditionTextAppService = $this->prophesize(SearchConditionTextAppService::class);
        $this->outputBoundary = $this->prophesize(OutputBoundary::class);

        $this->target = new Interactor(
            $this->programDao->reveal(),
            $this->sampleService->reveal(),
            $this->searchConditionTextAppService->reveal(),
            $this->outputBoundary->reveal()
        );
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function checkSampleCount_exception(): void
    {
        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('checkSampleCount');
        $method->setAccessible(true);

        $this->sampleService
            ->getConditionCrossCount(arg::cetera())
            ->willReturn(0)
            ->shouldBeCalled();

        $this->expectException(SampleCountException::class);

        $method->invoke(
            $this->target,
            50,
            [],
            '2019-01-01',
            '2019-01-07',
            1
        );
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function checkSampleCount_no_exception(): void
    {
        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('checkSampleCount');
        $method->setAccessible(true);

        $this->sampleService
            ->getConditionCrossCount(arg::cetera())
            ->willReturn(100)
            ->shouldBeCalled();

        $method->invoke(
            $this->target,
            50,
            [],
            '2019-01-01',
            '2019-01-07',
            1
        );
    }

    /**
     * @test
     * @throws SampleCountException
     */
    public function invoke_all_false(): void
    {
        $this->sampleService
            ->getConditionCrossCount(arg::cetera())
            ->willReturn(100)
            ->shouldNotBeCalled();

        $this->programDao
            ->createMultiChannelProfileTables(arg::cetera())
            ->shouldBeCalled();

        $this->programDao
            ->getDetailMultiChannelProfileResults(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->programDao
            ->getSelectedProgramsForProfile(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->programDao
            ->getHeaderProfileResults(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->searchConditionTextAppService
            ->getMultiChannelProfileCsv(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->searchConditionTextAppService
            ->getConvertedCrossConditionText(arg::cetera())
            ->willReturn('')
            ->shouldNotBeCalled();

        $this->outputBoundary
            ->__invoke(new OutputData([], '20190101', '20190107', []))
            ->willReturn()
            ->shouldBeCalled();

        $input = new InputData(
            // startDateTime
            '2019-01-01 05:00:00',
            // endDateTime
            '2019-01-07 05:00:00',
            // regionId
            1,
            // progIDs
            [1],
            // timeBoxIds
            [1],
            // division
            'ga8',
            // conditionCross
            [],
            // codes
            [],
            // channelIds
            [1],
            // sampleType
            '3',
            // isEnq
            true,
            // sampleCountMaxNumber
            50,
            100
        );

        $this->target->__invoke($input);
    }

    /**
     * @test
     * @throws SampleCountException
     */
    public function invoke_all_true(): void
    {
        $this->sampleService
            ->getConditionCrossCount(arg::cetera())
            ->willReturn(100)
            ->shouldBeCalled();

        $this->programDao
            ->createMultiChannelProfileTables(arg::cetera())
            ->shouldBeCalled();

        $this->programDao
            ->getDetailMultiChannelProfileResults(arg::cetera())
            ->willReturn([(object) ['option' => 'test']])
            ->shouldBeCalled();

        $this->programDao
            ->getSelectedProgramsForProfile(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->programDao
            ->getHeaderProfileResults(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->searchConditionTextAppService
            ->getMultiChannelProfileCsv(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->searchConditionTextAppService
            ->getConvertedCrossConditionText(arg::cetera())
            ->willReturn('')
            ->shouldBeCalled();

        $this->outputBoundary
            ->__invoke(new OutputData([(object) ['option' => '']], '20190101', '20190107', []))
            ->willReturn()
            ->shouldBeCalled();

        $input = new InputData(
        // startDateTime
            '2019-01-01 05:00:00',
            // endDateTime
            '2019-01-07 05:00:00',
            // regionId
            1,
            // progIDs
            [1],
            // timeBoxIds
            [1],
            // division
            'condition_cross',
            // conditionCross
            ['age' => '20'],
            // codes
            [],
            // channelIds
            [1],
            // sampleType
            '3',
            // isEnq
            true,
            // sampleCountMaxNumber
            50,
            100
        );

        $this->target->__invoke($input);
    }
}
