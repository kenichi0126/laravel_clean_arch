<?php

namespace Switchm\SmartApi\Components\Channels\Get\UseCases;

use Switchm\SmartApi\Components\Common\DateTimeInputDataTrait;

class InputData
{
    use DateTimeInputDataTrait;

    private $division;

    private $regionId;

    private $withCommercials;

    public function __construct($division, $regionId, $withCommercials)
    {
        $this->division = $division;
        $this->regionId = $regionId;
        $this->withCommercials = $withCommercials;
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
    public function withCommercials()
    {
        return $this->withCommercials;
    }
}
