<?php

namespace Switchm\SmartApi\Components\SettingAttrDivs\Create\UseCases;

/**
 * Interface DataAccessInterface.
 */
interface DataAccessInterface
{
    /**
     * @param array $attributes
     */
    public function __invoke(array $attributes): void;
}
