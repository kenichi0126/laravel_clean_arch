<?php

namespace Switchm\SmartApi\Components\SearchConditions\Create\UseCases;

/**
 * Interface DataAccessInterface.
 */
interface DataAccessInterface
{
    /**
     * @param int $memberId
     * @param int $regionId
     * @param string $name
     * @param string $routeName
     * @param string $condition
     */
    public function __invoke(int $memberId, int $regionId, string $name, string $routeName, string $condition): void;
}
