<?php

namespace Switchm\SmartApi\Components\SettingAttrDivs\Update\UseCases;

class OutputData
{
    private $result;

    /**
     * OutputData constructor.
     * @param $result
     */
    public function __construct(int $result)
    {
        $this->result = $result;
    }

    public function result(): int
    {
        return $this->result;
    }
}
