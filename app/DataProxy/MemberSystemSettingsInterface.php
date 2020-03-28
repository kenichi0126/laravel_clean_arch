<?php

namespace App\DataProxy;

/**
 * Interface MemberSystemSettingsInterface.
 */
interface MemberSystemSettingsInterface
{
    /**
     * @param int $memberId
     * @param array $attributes
     * @return bool
     */
    public function saveByMemberId(int $memberId, array $attributes): bool;
}
