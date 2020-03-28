<?php

namespace Switchm\SmartApi\Components\Top\Get\UseCases;

class InputData
{
    private $regionId;

    private $channelColors;

    private $channelColorsKansai;

    /**
     * TopInputData constructor.
     * @param int $regionId
     * @param array $channelColors
     * @param array $channelColorsKansai
     */
    public function __construct(int $regionId, array $channelColors, array $channelColorsKansai)
    {
        $this->regionId = $regionId;
        $this->channelColors = $channelColors;
        $this->channelColorsKansai = $channelColorsKansai;
    }

    /**
     * @return int
     */
    public function regionId(): int
    {
        return $this->regionId;
    }

    /**
     * @return array
     */
    public function channelColors(): array
    {
        return $this->channelColors;
    }

    /**
     * @return array
     */
    public function channelColorsKansai(): array
    {
        return $this->channelColorsKansai;
    }
}
