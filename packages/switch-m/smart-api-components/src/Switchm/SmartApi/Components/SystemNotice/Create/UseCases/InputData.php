<?php

namespace Switchm\SmartApi\Components\SystemNotice\Create\UseCases;

class InputData
{
    private $noticeId;

    private $memberId;

    /**
     * NoticeReadSystemNoticeInputData constructor.
     * @param $noticeId
     * @param $memberId
     */
    public function __construct($noticeId, $memberId)
    {
        $this->noticeId = $noticeId;
        $this->memberId = $memberId;
    }

    /**
     * @return mixed
     */
    public function noticeId()
    {
        return $this->noticeId;
    }

    /**
     * @return mixed
     */
    public function memberId()
    {
        return $this->memberId;
    }
}
