<?php

namespace Switchm\SmartApi\Components\SettingAttrDivs\Create\UseCases;

class InputData
{
    private $division;

    private $conditionCross;

    private $info;

    private $regionId;

    private $id;

    // TODO: UI側で修正したらこちらも修正する: $sumpleName →　$sampleName
    private $sumpleName;

    /**
     * SettingAttrDivsInputData constructor.
     * @param string $division
     * @param null|array $conditionCross
     * @param null|array $info
     * @param null|int $regionId
     * @param null|string $sumpleName
     * @param int $id
     */
    public function __construct(string $division, ?array $conditionCross, ?array $info, ?int $regionId, ?string $sumpleName, int $id)
    {
        $this->division = $division;
        $this->conditionCross = $conditionCross;
        $this->info = $info;
        $this->regionId = $regionId;
        $this->sumpleName = $sumpleName;
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function division(): string
    {
        return $this->division;
    }

    /**
     * @return null|array
     */
    public function conditionCross(): ?array
    {
        return $this->conditionCross;
    }

    /**
     * @return null|array
     */
    public function info(): ?array
    {
        return $this->info;
    }

    /**
     * @return null|int
     */
    public function regionId(): ?int
    {
        return $this->regionId;
    }

    /**
     * @return null|string
     */
    public function sumpleName(): ?string
    {
        return $this->sumpleName;
    }

    /**
     * @return id
     */
    public function id(): int
    {
        return $this->id;
    }
}
