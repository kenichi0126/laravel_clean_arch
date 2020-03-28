<?php

namespace Switchm\SmartApi\Components\ProgramMultiChannelProfile\Get\UseCases;

use Carbon\Carbon;
use Switchm\SmartApi\Components\Common\DateTimeInputDataTrait;

class InputData
{
    use DateTimeInputDataTrait;

    private $regionId;

    private $progIds;

    private $timeBoxIds;

    private $division;

    private $conditionCross;

    private $codes;

    private $channelIds;

    private $sampleType;

    private $isEnq;

    private $sampleCountMaxNumber;

    private $ptThreshold;

    public function __construct(
        $startDateTime,
        $endDateTime,
        $regionId,
        $progIds,
        $timeBoxIds,
        $division,
        $conditionCross,
        $codes,
        $channelIds,
        $sampleType,
        $isEnq,
        $sampleCountMaxNumber,
        $ptThreshold
    ) {
        $this->startDateTime = Carbon::parse($startDateTime);
        $this->endDateTime = Carbon::parse($endDateTime);
        $this->regionId = $regionId;
        $this->progIds = $progIds;
        $this->timeBoxIds = $timeBoxIds;
        $this->division = $division;
        $this->conditionCross = $conditionCross;
        $this->codes = $codes;
        $this->channelIds = $channelIds;
        $this->sampleType = $sampleType;
        $this->isEnq = $isEnq;
        $this->sampleCountMaxNumber = $sampleCountMaxNumber;
        $this->ptThreshold = $ptThreshold;
    }

    public function regionId()
    {
        return $this->regionId;
    }

    public function progIds()
    {
        return $this->progIds;
    }

    public function timeBoxIds()
    {
        return $this->timeBoxIds;
    }

    public function division()
    {
        return $this->division;
    }

    public function conditionCross()
    {
        return $this->conditionCross;
    }

    public function codes()
    {
        return $this->codes;
    }

    public function channelIds()
    {
        return $this->channelIds;
    }

    public function sampleType()
    {
        return $this->sampleType;
    }

    public function isEnq()
    {
        return $this->isEnq;
    }

    public function sampleCountMaxNumber()
    {
        return $this->sampleCountMaxNumber;
    }

    public function ptThreshold()
    {
        return $this->ptThreshold;
    }
}
