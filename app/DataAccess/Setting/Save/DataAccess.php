<?php

namespace App\DataAccess\Setting\Save;

use App\DataProxy\MemberSystemSettingsInterface as MemberSystemSettings;
use Switchm\SmartApi\Components\Setting\Save\UseCases\DataAccessInterface;

/**
 * Class DataAccess.
 */
final class DataAccess implements DataAccessInterface
{
    private $memberSystemSettings;

    /**
     * DataAccess constructor.
     * @param MemberSystemSettings $memberSystemSettings
     */
    public function __construct(MemberSystemSettings $memberSystemSettings)
    {
        $this->memberSystemSettings = $memberSystemSettings;
    }

    /**
     * @param int $memberId
     * @param array $attributes
     * @return bool
     */
    public function __invoke(int $memberId, array $attributes): bool
    {
        return $this->memberSystemSettings->saveByMemberId($memberId, $attributes);
    }
}
