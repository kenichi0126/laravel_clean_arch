<?php

namespace Switchm\SmartApi\Components\SettingAttrDivs\Get\UseCases;

class InputData
{
    private $regionId;

    private $id;

    /**
     * SettingAttrDivsInputData constructor.
     * @param int $regionId
     * @param int $id
     */
    public function __construct(int $regionId, int $id)
    {
        $this->regionId = $regionId;
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function regionId(): int
    {
        return $this->regionId;
    }

    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }
}
