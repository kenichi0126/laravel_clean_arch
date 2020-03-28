<?php

namespace Switchm\SmartApi\Components\CmMaterials\Get\UseCases;

class InputData
{
    private $productIds;

    private $startDate;

    private $endDate;

    private $startTimeHour;

    private $startTimeMin;

    private $endTimeHour;

    private $endTimeMin;

    private $regionId;

    private $channels;

    private $cmType;

    private $cmSeconds;

    private $companyIds;

    private $progIds;

    public function __construct(
        $productIds,
        $startDate,
        $endDate,
        $startTimeHour,
        $startTimeMin,
        $endTimeHour,
        $endTimeMin,
        $regionId,
        $channels,
        $cmType,
        $cmSeconds,
        $companyIds,
        $progIds
    ) {
        $this->productIds = $productIds;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->startTimeHour = $startTimeHour;
        $this->startTimeMin = $startTimeMin;
        $this->endTimeHour = $endTimeHour;
        $this->endTimeMin = $endTimeMin;
        $this->regionId = $regionId;
        $this->channels = $channels;
        $this->cmType = $cmType;
        $this->cmSeconds = $cmSeconds;
        $this->companyIds = $companyIds;
        $this->progIds = $progIds;
    }

    public function productIds()
    {
        return $this->productIds;
    }

    public function startDate()
    {
        return $this->startDate;
    }

    public function endDate()
    {
        return $this->endDate;
    }

    public function startTimeHour()
    {
        return $this->startTimeHour;
    }

    public function startTimeMin()
    {
        return $this->startTimeMin;
    }

    public function endTimeHour()
    {
        return $this->endTimeHour;
    }

    public function endTimeMin()
    {
        return $this->endTimeMin;
    }

    public function regionId()
    {
        return $this->regionId;
    }

    public function channels()
    {
        return $this->channels;
    }

    public function cmType()
    {
        return $this->cmType;
    }

    public function cmSeconds()
    {
        return $this->cmSeconds;
    }

    public function companyIds()
    {
        return $this->companyIds;
    }

    public function progIds()
    {
        return $this->progIds;
    }
}
