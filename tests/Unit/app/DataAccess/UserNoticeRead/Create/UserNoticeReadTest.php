<?php

namespace Tests\Unit\App\DataAccess\SystemNoticeRead\Create;

use App\DataAccess\UserNoticeRead\Create\DataAccess;
use App\DataProxy\UserNoticeRead;
use Tests\TestCase;

class UserNoticeReadTest extends TestCase
{
    private $target;

    private $userNoticeRead;

    /**
     * @test
     */
    public function __invoke(): void
    {
        $this->userNoticeRead->create(['notice_id' => 1, 'member_id' => 2, 'updated_at' => '2019-01-01 12:23:45'])->shouldBeCalled();
        $this->target->__invoke(1, 2, '2019-01-01 12:23:45');
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->userNoticeRead = $this->prophesize(UserNoticeRead::class);

        $this->target = new DataAccess($this->userNoticeRead->reveal());
    }
}
