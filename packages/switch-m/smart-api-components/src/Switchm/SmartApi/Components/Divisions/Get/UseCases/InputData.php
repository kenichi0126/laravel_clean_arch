<?php

namespace Switchm\SmartApi\Components\Divisions\Get\UseCases;

class InputData
{
    private $menu;

    private $regionId;

    private $userInfo;

    private $hasCrossConditionPermission;

    public function __construct(string $menu, int $regionId, \stdClass $userInfo, bool $hasCrossConditionPermission)
    {
        $this->menu = $menu;
        $this->regionId = $regionId;
        $this->userInfo = $userInfo;
        $this->hasCrossConditionPermission = $hasCrossConditionPermission;
    }

    /**
     * @return string
     */
    public function menu(): string
    {
        return $this->menu;
    }

    /**
     * @return int
     */
    public function regionId(): int
    {
        return $this->regionId;
    }

    /**
     * @return \stdClass
     */
    public function userInfo()
    {
        return $this->userInfo;
    }

    /**
     * @return bool
     */
    public function hasCrossConditionPermission()
    {
        return $this->hasCrossConditionPermission;
    }
}
