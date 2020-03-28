<?php

namespace Smart2\Application\Services\Notices;

// TODO: 別サービスに依存されているので、一旦残しておく
class NoticesService extends Service
{
    public function __construct()
    {
    }

    public function __invoke(int $memberId): array
    {
        $systemNotices = $this->getSystemNotice($memberId);
        $userNotices = $this->getUserNotice($memberId);
        return ['sn' => $systemNotices,
                  'un' => $userNotices,
                  'sn_unread_cnt' => $this->getUnreadCount($systemNotices),
                  'un_unread_cnt' => $this->getUnreadCount($userNotices), ];
    }
}
