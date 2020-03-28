<?php

namespace App\DataAccess\SystemNoticeRead\Create;

use App\DataProxy\SystemNoticeReadInterface as SystemNoticeRead;
use Switchm\SmartApi\Components\SystemNotice\Create\UseCases\DataAccessInterface;

class DataAccess implements DataAccessInterface
{
    private $systemNoticeRead;

    public function __construct(SystemNoticeRead $systemNoticeRead)
    {
        $this->systemNoticeRead = $systemNoticeRead;
    }

    /**
     * @param int $noticeId
     * @param int $memberId
     * @param string $updatedAt
     * @return mixed
     */
    public function __invoke(int $noticeId, int $memberId, string $updatedAt): void
    {
        $this->systemNoticeRead->create(['notice_id' => $noticeId, 'member_id' => $memberId, 'updated_at' => $updatedAt]);
    }
}
