<?php

namespace Switchm\SmartApi\Components\Tests\SearchConditions\Create\UseCases;

use Prophecy\Argument as arg;
use Switchm\SmartApi\Components\SearchConditions\Create\UseCases\DataAccessInterface;
use Switchm\SmartApi\Components\SearchConditions\Create\UseCases\InputData;
use Switchm\SmartApi\Components\SearchConditions\Create\UseCases\Interactor;
use Switchm\SmartApi\Components\SearchConditions\Create\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\SearchConditions\Create\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Queries\Dao\Rdb\SearchConditionDao;

/**
 * Class InteractorTest.
 */
final class InteractorTest extends TestCase
{
    private $searchConditionDao;

    private $dataAccess;

    private $outputBoundary;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->searchConditionDao = $this->prophesize(SearchConditionDao::class);
        $this->dataAccess = $this->prophesize(DataAccessInterface::class);
        $this->outputBoundary = $this->prophesize(OutputBoundary::class);

        $this->target = new Interactor($this->searchConditionDao->reveal(), $this->dataAccess->reveal(), $this->outputBoundary->reveal());
    }

    /**
     * @test
     */
    public function invoke(): void
    {
        $input = new InputData(
            1,
            1,
            'test condition',
            'main.test.test.1',
            '{\"test\": \"test\"}'
        );

        $output = new OutputData(true);

        $this->searchConditionDao->countByMemberId(arg::cetera())->shouldBeCalled()->willReturn(29);

        $this->dataAccess->__invoke(arg::cetera())->shouldBeCalled();

        $this->outputBoundary->__invoke($output)->shouldBeCalled();

        $this->target->__invoke($input);
    }

    /**
     * @test
     */
    public function invoke_upper_error(): void
    {
        $input = new InputData(
            1,
            1,
            'test condition',
            'main.test.test.1',
            '{\"test\": \"test\"}'
        );

        $output = new OutputData(false);

        $this->searchConditionDao->countByMemberId(arg::cetera())->shouldBeCalled()->willReturn(30);

        $this->dataAccess->__invoke(arg::cetera())->shouldNotBeCalled();

        $this->outputBoundary->__invoke($output)->shouldBeCalled();

        $this->target->__invoke($input);
    }
}
