<?php

namespace Switchm\SmartApi\Components\SettingAttrDivs\Delete\UseCases;

class InputData
{
    private $division;

    private $code;

    /**
     * SettingAttrDivsInputData constructor.
     * @param string $division
     * @param null|string $code
     */
    public function __construct(string $division, ?string $code)
    {
        $this->division = $division;
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function division(): string
    {
        return $this->division;
    }

    /**
     * @return string
     */
    public function code(): string
    {
        return $this->code;
    }
}
