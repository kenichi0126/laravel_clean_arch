<?php

namespace Switchm\SmartApi\Queries\Dao\Rdb;

class NoticeDao extends Dao
{
    /**
     * @param int $memberId
     * @return array
     */
    public function searchSystemNotice(int $memberId): array
    {
        $query =
            'select ' .
            '    sn.id, sn.subject, sn.body, sn.imp_level, sn.notice_start, ' .
            '    case when exists( ' .
            '        select notice_id from system_notices_read snr ' .
            '        where sn.id = snr.notice_id and snr.member_id = :member_id) then 1 else 0 end as read ' .
            'from system_notices sn ' .
            'where sn.notice_start <= clock_timestamp() ' .
            '  and ((sn.notice_end is null) or (sn.notice_end >= clock_timestamp())) ' .
            'order by notice_start desc limit 50';

        return $this->select($query, [':member_id' => $memberId]);
    }

    public function searchUserNotice(int $memberId): array
    {
        $query =
            'select ' .
            '    un.id, un.subject, un.body, un.imp_level, un.notice_start, ' .
            '    case when exists( ' .
            '        select notice_id from user_notices_read unr ' .
            '        where un.id = unr.notice_id and un.member_id = :member_id and unr.member_id = :member_id) then 1 else 0 end as read ' .
            'from user_notices un ' .
            'where un.notice_start <= clock_timestamp() ' .
            '  and ((un.notice_end is null) or (un.notice_end >= clock_timestamp())) ' .
            '  and ( un.member_id = :member_id ) ' .
            'order by notice_start desc limit 50';

        return $this->select($query, [':member_id' => $memberId]);
    }

    public function searchSystemNoticeRead(int $noticeId, int $memberId): array
    {
        $query = 'select notice_id from system_notices_read where notice_id = :notice_id and member_id = :member_id';
        return $this->select($query, [
            ':notice_id' => $noticeId,
            ':member_id' => $memberId,
        ]);
    }

    public function searchUserNoticeRead(int $noticeId, int $memberId): array
    {
        $query = 'select notice_id from user_notices_read where notice_id = :notice_id and member_id = :member_id';
        return $this->select($query, [
            ':notice_id' => $noticeId,
            ':member_id' => $memberId,
        ]);
    }
}
