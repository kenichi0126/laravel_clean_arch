<?php

namespace Switchm\SmartApi\Components\SettingAttrDivs\Delete\UseCases;

/**
 * Interface DataAccessInterface.
 */
interface DataAccessInterface
{
    /**
     * @param string $division
     * @param string $code
     */
    public function __invoke(string $division, string $code): void;
}
