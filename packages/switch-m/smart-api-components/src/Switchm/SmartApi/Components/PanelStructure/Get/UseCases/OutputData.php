<?php

namespace Switchm\SmartApi\Components\PanelStructure\Get\UseCases;

class OutputData
{
    private $attrDivs;

    private $panelData;

    private $baseFiveDivisionFlag;

    /**
     * OutputData constructor.
     * @param array $attrDivs
     * @param array $panelData
     * @param bool $baseFiveDivisionFlag
     */
    public function __construct(array $attrDivs, array $panelData, bool $baseFiveDivisionFlag)
    {
        $this->attrDivs = $attrDivs;
        $this->panelData = $panelData;
        $this->baseFiveDivisionFlag = $baseFiveDivisionFlag;
    }

    /**
     * @return array
     */
    public function attrDivs(): array
    {
        return $this->attrDivs;
    }

    /**
     * @return array
     */
    public function panelData(): array
    {
        return $this->panelData;
    }

    /**
     * @return bool
     */
    public function baseFiveDivisionFlag(): bool
    {
        return $this->baseFiveDivisionFlag;
    }
}
