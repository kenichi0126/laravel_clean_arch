<?php

namespace Switchm\SmartApi\Components\SampleCount\Get\UseCases;

class InputData
{
    private $info;

    private $conditionCross;

    private $regionId;

    private $editFlg;

    /**
     * SampleCountGetSampleCountInputData constructor.
     * @param array $info
     * @param array $conditionCross
     * @param int $regionId
     * @param null|bool $editFlg
     */
    public function __construct(array $info, array $conditionCross, int $regionId, ?bool $editFlg)
    {
        $this->info = $info;
        $this->conditionCross = $conditionCross;
        $this->regionId = $regionId;
        $this->editFlg = $editFlg;
    }

    /**
     * @return array
     */
    public function info(): array
    {
        return $this->info;
    }

    /**
     * @return array
     */
    public function conditionCross(): array
    {
        return $this->conditionCross;
    }

    /**
     * @return int
     */
    public function regionId(): int
    {
        return $this->regionId;
    }

    /**
     * @return null|bool
     */
    public function editFlg(): ?bool
    {
        return $this->editFlg;
    }
}
