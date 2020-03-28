<?php

namespace Switchm\SmartApi\Components\Tests\SystemNotice\Get\UseCases;

use Carbon\Carbon;
use Switchm\SmartApi\Components\SystemNotice\Create\UseCases\DataAccessInterface;
use Switchm\SmartApi\Components\SystemNotice\Create\UseCases\InputData;
use Switchm\SmartApi\Components\SystemNotice\Create\UseCases\Interactor;
use Switchm\SmartApi\Components\SystemNotice\Create\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\SystemNotice\Create\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Queries\Dao\Rdb\NoticeDao;

class InteractorTest extends TestCase
{
    private $noticeDao;

    private $dataAccess;

    private $outputBoundary;

    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('1990-06-14 12:23:45');

        $this->noticeDao = $this->prophesize(NoticeDao::class);
        $this->dataAccess = $this->prophesize(DataAccessInterface::class);
        $this->outputBoundary = $this->prophesize(OutputBoundary::class);

        $this->target = new Interactor($this->noticeDao->reveal(), $this->dataAccess->reveal(), $this->outputBoundary->reveal());
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    /**
     * @test
     */
    public function invoke(): void
    {
        $this->noticeDao
            ->searchSystemNoticeRead(2, 1)
            ->willReturn([])
            ->shouldBeCalled();

        $input = new InputData(2, 1);

        $outputData = new OutputData();
        $this->dataAccess->__invoke(2, 1, '1990-06-14 12:23:45')->shouldBeCalled();
        $this->outputBoundary->__invoke($outputData)->shouldBeCalled();
        $this->target->__invoke($input);
    }

    /**
     * @test
     */
    public function invokeAlreadyRead(): void
    {
        $this->noticeDao
            ->searchSystemNoticeRead(2, 1)
            ->willReturn([1])
            ->shouldBeCalled();

        $input = new InputData(2, 1);

        $outputData = new OutputData();
        $this->dataAccess->__invoke(2, 1, '1990-06-14 12:23:45')->shouldNotBeCalled();
        $this->outputBoundary->__invoke($outputData)->shouldBeCalled();
        $this->target->__invoke($input);
    }
}
