<?php

namespace Switchm\SmartApi\Components\CompanyNames\Get\UseCases;

use Carbon\Carbon;
use Switchm\SmartApi\Components\Common\DateTimeInputDataTrait;

class InputData
{
    use DateTimeInputDataTrait;

    private $companyName;

    private $progIds;

    private $regionId;

    private $companyId;

    private $channels;

    private $cmType;

    private $cmSeconds;

    private $productIds;

    private $dataType;

    /**
     * @param $startDateTime
     * @param $endDateTime
     * @param $companyName
     * @param $progIds
     * @param $regionId
     * @param $companyId
     * @param $channels
     * @param $cmType
     * @param $cmSeconds
     * @param $productIds
     * @param $dataType
     */
    public function __construct(
        $startDateTime,
        $endDateTime,
        $companyName,
        $progIds,
        $regionId,
        $companyId,
        $channels,
        $cmType,
        $cmSeconds,
        $productIds,
        $dataType
    ) {
        $this->startDateTime = Carbon::parse($startDateTime);
        $this->endDateTime = Carbon::parse($endDateTime);
        $this->companyName = $companyName;
        $this->progIds = $progIds;
        $this->regionId = $regionId;
        $this->companyId = $companyId;
        $this->channels = $channels;
        $this->cmType = $cmType;
        $this->cmSeconds = $cmSeconds;
        $this->productIds = $productIds;
        $this->dataType = $dataType;
    }

    public function companyName()
    {
        return $this->companyName;
    }

    public function progIds()
    {
        return $this->progIds;
    }

    public function regionId()
    {
        return $this->regionId;
    }

    public function companyId()
    {
        return $this->companyId;
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

    public function productIds()
    {
        return $this->productIds;
    }

    public function dataType()
    {
        return $this->dataType;
    }
}
