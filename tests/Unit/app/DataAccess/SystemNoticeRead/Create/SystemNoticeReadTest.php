<?php

namespace Tests\Unit\App\DataAccess\SystemNoticeRead\Create;

use App\DataAccess\SystemNoticeRead\Create\DataAccess;
use App\DataProxy\SystemNoticeRead;
use Tests\TestCase;

class SystemNoticeReadTest extends TestCase
{
    private $target;

    private $systemNoticeRead;

    /**
     * @test
     */
    public function __invoke(): void
    {
        $this->systemNoticeRead->create(['notice_id' => 1, 'member_id' => 2, 'updated_at' => '2019-01-01 12:23:45'])->shouldBeCalled();
        $this->target->__invoke(1, 2, '2019-01-01 12:23:45');
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->systemNoticeRead = $this->prophesize(SystemNoticeRead::class);

        $this->target = new DataAccess($this->systemNoticeRead->reveal());
    }
}
