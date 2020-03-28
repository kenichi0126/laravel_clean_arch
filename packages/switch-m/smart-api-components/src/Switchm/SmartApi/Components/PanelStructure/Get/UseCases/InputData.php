<?php

namespace Switchm\SmartApi\Components\PanelStructure\Get\UseCases;

use Switchm\SmartApi\Components\Common\DateTimeInputDataTrait;

class InputData
{
    use DateTimeInputDataTrait;

    private $division;

    private $regionId;

    private $isBaseFiveDivision;

    private $userId;

    /**
     * @param $division
     * @param $regionId
     * @param $isBaseFiveDivision
     * @param $userID
     */
    public function __construct(
        $division,
        $regionId,
        $isBaseFiveDivision,
        $userID
    ) {
        $this->division = $division;
        $this->regionId = $regionId;
        $this->isBaseFiveDivision = $isBaseFiveDivision;
        $this->userId = $userID;
    }

    /**
     * @return mixed
     */
    public function division()
    {
        return $this->division;
    }

    /**
     * @return mixed
     */
    public function regionId()
    {
        return $this->regionId;
    }

    /**
     * @return mixed
     */
    public function isBaseFiveDivision()
    {
        return $this->isBaseFiveDivision;
    }

    /**
     * @return mixed
     */
    public function userId()
    {
        return $this->userId;
    }
}
