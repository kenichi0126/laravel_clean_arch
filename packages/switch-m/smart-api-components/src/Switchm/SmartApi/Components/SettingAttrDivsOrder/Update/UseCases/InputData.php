<?php

namespace Switchm\SmartApi\Components\SettingAttrDivsOrder\Update\UseCases;

class InputData
{
    private $divisions;

    /**
     * SettingAttrDivsOrderInputData constructor.
     * @param array $divisions
     */
    public function __construct(array $divisions)
    {
        $this->divisions = $divisions;
    }

    /**
     * @return array
     */
    public function divisions(): array
    {
        return $this->divisions;
    }
}
