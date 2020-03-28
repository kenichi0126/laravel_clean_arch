<?php

namespace Switchm\SmartApi\Components\SearchConditions\Update\UseCases;

/**
 * Interface DataAccessInterface.
 */
interface DataAccessInterface
{
    /**
     * @param int $id
     * @param string $name
     * @param string $condition
     */
    public function __invoke(int $id, string $name, string $condition): void;
}
