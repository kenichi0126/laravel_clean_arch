<?php

namespace Switchm\SmartApi\Components\SettingAttrDivs\Update\UseCases;

/**
 * Interface DataAccessInterface.
 */
interface DataAccessInterface
{
    /**
     * @param string $division
     * @param string $code
     * @param array $attributes
     * @return int
     */
    public function __invoke(string $division, string $code, array $attributes): int;
}
