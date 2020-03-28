<?php

namespace Smart2\QueryModel\Service;

use Prophecy\Argument as arg;
use Prophecy\Prophecy\ObjectProphecy;
use Switchm\SmartApi\Queries\Dao\Rdb\DivisionDao;
use Tests\TestCase;

class DivisionServiceTest extends TestCase
{
    /**
     * @var ObjectProphecy
     */
    private $divisionDao;

    /**
     * @var DivisionService
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->divisionDao = $this->prophesize(DivisionDao::class);
        $this->target = new DivisionService($this->divisionDao->reveal());
    }

    /**
     * @test
     */
    public function getCodeListConditionCross(): void
    {
        $expected = [];

        $actual = $this->target->getCodeList('condition_cross', 1, 1);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getCodeListBasicDivision(): void
    {
        $expected = [];

        $this->divisionDao
            ->find(arg::cetera())
            ->willReturn($expected)
            ->shouldBeCalled();

        $actual = $this->target->getCodeList('ga8', 1, 1);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getCodeListOriginalDivision(): void
    {
        $expected = [];

        $this->divisionDao
            ->findOriginalDiv(arg::cetera())
            ->willReturn($expected)
            ->shouldBeCalled();

        $actual = $this->target->getCodeList('', 1, 1);

        $this->assertEquals($expected, $actual);
    }
}
