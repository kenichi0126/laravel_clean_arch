<?php

namespace Switchm\SmartApi\Components\SearchConditions\Delete\UseCases;

/**
 * Interface DataAccessInterface.
 */
interface DataAccessInterface
{
    /**
     * @param int $id
     */
    public function __invoke(int $id): void;
}
