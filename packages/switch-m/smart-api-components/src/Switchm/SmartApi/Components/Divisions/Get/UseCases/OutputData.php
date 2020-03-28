<?php

namespace Switchm\SmartApi\Components\Divisions\Get\UseCases;

class OutputData
{
    private $divisions;

    private $divisionMaps;

    /**
     * OutputData constructor.
     * @param array $divisions
     * @param array $divisionMaps
     */
    public function __construct(array $divisions, array $divisionMaps)
    {
        $this->divisions = $divisions;
        $this->divisionMaps = $divisionMaps;
    }

    /**
     * @return array
     */
    public function divisions(): array
    {
        return $this->divisions;
    }

    /**
     * @return array
     */
    public function divisionMaps(): array
    {
        return $this->divisionMaps;
    }
}
