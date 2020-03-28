<?php

namespace Switchm\SmartApi\Components\Tests\Channels\Get\UseCases;

use Prophecy\Argument as arg;
use Switchm\SmartApi\Components\Channels\Get\UseCases\InputData;
use Switchm\SmartApi\Components\Channels\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\Channels\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\Channels\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Queries\Dao\Rdb\ChannelDao;

class InteractorTest extends TestCase
{
    private $channelDao;

    private $outputBoundary;

    private $target;

    /**
     * @test
     */
    public function __invoke_data_not_exist(): void
    {
        $this->channelDao
            ->search(arg::cetera())
            ->willReturn(['data'])
            ->shouldBeCalled();

        $this->outputBoundary
            ->__invoke(new OutputData(['data']))
            ->willReturn()
            ->shouldBeCalled();

        $userInfo = new \stdClass();
        $userInfo->id = 1;

        $inputData = new InputData('ga8', 1, false);

        $this->target->__invoke($inputData);
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->channelDao = $this->prophesize(ChannelDao::class);
        $this->outputBoundary = $this->prophesize(OutputBoundary::class);

        $this->target = new Interactor(
            $this->channelDao->reveal(),
            $this->outputBoundary->reveal()
        );
    }
}
