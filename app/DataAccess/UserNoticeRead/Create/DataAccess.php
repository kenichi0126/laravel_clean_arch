<?php

namespace App\DataAccess\UserNoticeRead\Create;

use App\DataProxy\UserNoticeReadInterface as UserNoticeRead;
use Switchm\SmartApi\Components\UserNotice\Create\UseCases\DataAccessInterface;

class DataAccess implements DataAccessInterface
{
    private $userNoticeRead;

    public function __construct(UserNoticeRead $userNoticeRead)
    {
        $this->userNoticeRead = $userNoticeRead;
    }

    /**
     * @param int $noticeId
     * @param int $memberId
     * @param string $updatedAt
     * @return mixed
     */
    public function __invoke(int $noticeId, int $memberId, string $updatedAt): void
    {
        $this->userNoticeRead->create(['notice_id' => $noticeId, 'member_id' => $memberId, 'updated_at' => $updatedAt]);
    }
}
