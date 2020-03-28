<?php

namespace Switchm\SmartApi\Components\HourlyReport\Get\UseCases;

class InputData
{
    private $regionId;

    private $trialSettings;

    public function __construct(int $regionId, ?array $trialSettings)
    {
        $this->regionId = $regionId;
        $this->trialSettings = $trialSettings;
    }

    /**
     * @return int
     */
    public function regionId(): int
    {
        return $this->regionId;
    }

    /**
     * @return null|array
     */
    public function trialSettings(): ?array
    {
        return $this->trialSettings;
    }
}
