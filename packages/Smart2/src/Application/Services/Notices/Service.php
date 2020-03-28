<?php

namespace Smart2\Application\Services\Notices;

use Switchm\SmartApi\Queries\Dao\Rdb\NoticeDao;

// TODO: 別サービスに依存されているので、一旦残しておく
class Service
{
    protected $noticeDao;

    /**
     * システム通知を取得.
     * @param mixed $memberId
     * @return object システム通知
     */
    protected function getSystemNotice($memberId)
    {
        $noticeDao = new NoticeDao();
        return $noticeDao->searchSystemNotice($memberId);
    }

    /**
     * ユーザー通知を取得.
     * @param mixed $memberId
     * @return object ユーザー通知
     */
    protected function getUserNotice($memberId)
    {
        $noticeDao = new NoticeDao();
        return $noticeDao->searchUserNotice($memberId);
    }

    /**
     * 未読件数の取得.
     * @param $param
     * @return int
     */
    protected function getUnreadCount($param)
    {
        $results = array_filter($param, function ($v) {
            return $v->read == 0;
        });
        return count($results);
    }
}
