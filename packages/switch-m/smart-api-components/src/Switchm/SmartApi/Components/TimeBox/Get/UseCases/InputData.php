<?php

namespace Switchm\SmartApi\Components\TimeBox\Get\UseCases;

class InputData
{
    private $regionId;

    private $trialSettings;

    public function __construct(
        $regionId,
        $trialSettings
    ) {
        $this->regionId = $regionId;
        $this->trialSettings = $trialSettings;
    }

    public function regionId()
    {
        return $this->regionId;
    }

    public function trialSettings()
    {
        return $this->trialSettings;
    }
}
