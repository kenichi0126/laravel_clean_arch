<?php

namespace Switchm\SmartApi\Components\Tests\CompanyNames\Get\UseCases;

use Prophecy\Argument as arg;
use Switchm\SmartApi\Components\CompanyNames\Get\UseCases\InputData;
use Switchm\SmartApi\Components\CompanyNames\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\CompanyNames\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\CompanyNames\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Queries\Dao\Rdb\CompanyNamesDao;

class InteractorTest extends TestCase
{
    private $companyNamesDao;

    private $outputBoundary;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->companyNamesDao = $this->prophesize(CompanyNamesDao::class);
        $this->outputBoundary = $this->prophesize(OutputBoundary::class);

        $this->target = new Interactor($this->companyNamesDao->reveal(), $this->outputBoundary->reveal());
    }

    /**
     * @test
     */
    public function invoke(): void
    {
        $this->companyNamesDao
            ->findForCondition(arg::cetera())
            ->willReturn(['data'])
            ->shouldBeCalled();

        $this->outputBoundary
            ->__invoke(new OutputData(['data']))
            ->willReturn()
            ->shouldBeCalled();

        $input = new InputData(
            '2019-01-01 05:00:00',
            '2019-01-07 04:59:59',
            'ソフトバンク',
            [],
            1,
            [],
            [3, 4, 5, 6, 7],
            0,
            1,
            [],
            [0]
        );

        $this->target->__invoke($input);
    }
}
