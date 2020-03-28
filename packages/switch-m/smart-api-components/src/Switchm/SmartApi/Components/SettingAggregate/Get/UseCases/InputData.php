<?php

namespace Switchm\SmartApi\Components\SettingAggregate\Get\UseCases;

class InputData
{
    private $userInfo;

    /**
     * SettingAggregateInputData constructor.
     * @param \stdClass $userInfo
     */
    public function __construct(\stdClass $userInfo)
    {
        $this->userInfo = $userInfo;
    }

    /**
     * @return \stdClass
     */
    public function userInfo(): \stdClass
    {
        return $this->userInfo;
    }
}
