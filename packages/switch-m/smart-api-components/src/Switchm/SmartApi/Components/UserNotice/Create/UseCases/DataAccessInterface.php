<?php

namespace Switchm\SmartApi\Components\UserNotice\Create\UseCases;

/**
 * Interface DataAccessInterface.
 */
interface DataAccessInterface
{
    /**
     * @param int $noticeId
     * @param int $memberId
     * @param string $updatedAt
     * @return mixed
     */
    public function __invoke(int $noticeId, int $memberId, string $updatedAt): void;
}
