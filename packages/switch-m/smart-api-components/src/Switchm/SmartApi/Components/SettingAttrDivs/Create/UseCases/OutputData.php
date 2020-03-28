<?php

namespace Switchm\SmartApi\Components\SettingAttrDivs\Create\UseCases;

class OutputData
{
    private $isSuccess;

    /**
     * OutputData constructor.
     * @param $isSuccess
     */
    public function __construct(bool $isSuccess)
    {
        $this->isSuccess = $isSuccess;
    }

    public function isSuccess(): bool
    {
        return $this->isSuccess;
    }
}
