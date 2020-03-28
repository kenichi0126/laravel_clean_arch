<?php

namespace Switchm\SmartApi\Components\SettingAttrDivs\Get\UseCases;

class OutputData
{
    private $data;

    /**
     * OutputData constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function data(): array
    {
        return $this->data;
    }
}
