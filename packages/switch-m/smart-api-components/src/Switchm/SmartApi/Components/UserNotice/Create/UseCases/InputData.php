<?php

namespace Switchm\SmartApi\Components\UserNotice\Create\UseCases;

class InputData
{
    private $noticeId;

    private $memberId;

    /**
     * InputData constructor.
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
     * @return int
     */
    public function memberId(): int
    {
        return $this->memberId;
    }
}
