<?php

namespace Switchm\SmartApi\Components\SettingAttrDivsOrder\Update\UseCases;

/**
 * Interface DataAccessInterface.
 */
interface DataAccessInterface
{
    /**
     * @param array $divisions
     */
    public function __invoke(array $divisions): void;
}
