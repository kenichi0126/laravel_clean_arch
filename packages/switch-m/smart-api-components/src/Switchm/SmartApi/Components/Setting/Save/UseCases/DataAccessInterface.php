<?php

namespace Switchm\SmartApi\Components\Setting\Save\UseCases;

/**
 * Interface DataAccessInterface.
 */
interface DataAccessInterface
{
    /**
     * @param int $memberId
     * @param array $attributes
     * @return bool
     */
    public function __invoke(int $memberId, array $attributes): bool;
}
