<?php

namespace Switchm\SmartApi\Queries\Tests\Services;

use Prophecy\Argument as arg;
use Prophecy\Prophecy\ObjectProphecy;
use Switchm\SmartApi\Queries\Dao\Rdb\SampleDao;
use Switchm\SmartApi\Queries\Services\HolidayService;
use Switchm\SmartApi\Queries\Services\SampleService;
use Switchm\SmartApi\Queries\Tests\TestCase;

class SampleServiceTest extends TestCase
{
    /**
     * @var ObjectProphecy
     */
    private $sampleDao;

    /**
     * @var HolidayService
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->sampleDao = $this->prophesize(SampleDao::class);
        $this->target = new SampleService($this->sampleDao->reveal());
    }

    /**
     * @test
     */
    public function getConditionCrossCount(): void
    {
        $this->sampleDao
            ->getCrossConditionCount(arg::cetera())
            ->willReturn((object) ['cnt' => 1])
            ->shouldBeCalled();

        $expected = 1;

        $actual = $this->target->getConditionCrossCount(['conditionCross'], '20190101', '20190107', 1, true);

        $this->assertEquals($expected, $actual);
    }
}
