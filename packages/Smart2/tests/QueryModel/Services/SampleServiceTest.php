<?php

namespace Smart2\QueryModel\Service;

use Prophecy\Argument as arg;
use Prophecy\Prophecy\ObjectProphecy;
use Smart2\QueryModel\Dao\ReadRdb\SampleDao;
use stdClass;
use Tests\TestCase;

class SampleServiceTest extends TestCase
{
    /**
     * @var ObjectProphecy
     */
    private $sampleDao;

    /**
     * @var SampleService
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
        $expected = new stdClass();
        $expected->cnt = 2;

        $this->sampleDao
            ->getCrossConditionCount(arg::cetera())
            ->willReturn($expected)
            ->shouldBeCalled();

        $actual = $this->target->getConditionCrossCount([], '', '', 1);

        $this->assertEquals($expected->cnt, $actual);
    }
}
