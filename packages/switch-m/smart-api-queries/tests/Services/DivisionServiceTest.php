<?php

namespace Switchm\SmartApi\Queries\Tests\Services;

use Prophecy\Argument as arg;
use Prophecy\Prophecy\ObjectProphecy;
use Switchm\SmartApi\Queries\Dao\Rdb\DivisionDao;
use Switchm\SmartApi\Queries\Services\DivisionService;
use Switchm\SmartApi\Queries\Tests\TestCase;

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
    public function getCodeList掛け合わせ条件(): void
    {
        $this->divisionDao
            ->find(arg::cetera())
            ->willReturn(['baseDivision'])
            ->shouldNotBeCalled();

        $this->divisionDao
            ->findOriginalDiv(arg::cetera())
            ->willReturn(['originalDivision'])
            ->shouldNotBeCalled();

        $expected = [];

        $actual = $this->target->getCodeList('condition_cross', 1, 1, ['ga8', 'ga12']);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getCodeList基本5属性(): void
    {
        $this->divisionDao
            ->find(arg::cetera())
            ->willReturn(['baseDivision'])
            ->shouldBeCalled();

        $this->divisionDao
            ->findOriginalDiv(arg::cetera())
            ->willReturn(['originalDivision'])
            ->shouldNotBeCalled();

        $expected = ['baseDivision'];

        $actual = $this->target->getCodeList('ga8', 1, 1, ['ga8', 'ga12']);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getCodeListオリジナル属性(): void
    {
        $this->divisionDao
            ->find(arg::cetera())
            ->willReturn(['baseDivision'])
            ->shouldNotBeCalled();

        $this->divisionDao
            ->findOriginalDiv(arg::cetera())
            ->willReturn(['originalDivision'])
            ->shouldBeCalled();

        $expected = ['originalDivision'];

        $actual = $this->target->getCodeList('original', 1, 1, ['ga8', 'ga12']);

        $this->assertEquals($expected, $actual);
    }
}
