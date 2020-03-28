<?php

namespace Switchm\SmartApi\Components\ProductNames\Get\UseCases;

use Carbon\Carbon;
use Switchm\SmartApi\Components\Common\DateTimeInputDataTrait;

class InputData
{
    use DateTimeInputDataTrait;

    private $dataType;

    private $productName;

    private $companyIds;

    private $regionIds;

    private $productIds;

    private $channels;

    private $cmType;

    private $cmSeconds;

    private $progIds;

    /**
     * @param $startDateTime
     * @param $endDateTime
     * @param $dataType
     * @param $productName
     * @param $companyIds
     * @param $regionIds
     * @param $productIds
     * @param $channels
     * @param $cmType
     * @param $cmSeconds
     * @param $progIds
     */
    public function __construct(
        $startDateTime,
        $endDateTime,
        $dataType,
        $productName,
        $companyIds,
        $regionIds,
        $productIds,
        $channels,
        $cmType,
        $cmSeconds,
        $progIds
    ) {
        $this->startDateTime = Carbon::parse($startDateTime);
        $this->endDateTime = Carbon::parse($endDateTime);
        $this->dataType = $dataType;
        $this->productName = $productName;
        $this->companyIds = $companyIds;
        $this->regionIds = $regionIds;
        $this->productIds = $productIds;
        $this->channels = $channels;
        $this->cmType = $cmType;
        $this->cmSeconds = $cmSeconds;
        $this->progIds = $progIds;
    }

    /**
     * @return mixed
     */
    public function dataType()
    {
        return $this->dataType;
    }

    /**
     * @return mixed
     */
    public function productName()
    {
        return $this->productName;
    }

    /**
     * @return mixed
     */
    public function companyIds()
    {
        return $this->companyIds;
    }

    /**
     * @return mixed
     */
    public function regionIds()
    {
        return $this->regionIds;
    }

    /**
     * @return mixed
     */
    public function productIds()
    {
        return $this->productIds;
    }

    /**
     * @return mixed
     */
    public function channels()
    {
        return $this->channels;
    }

    /**
     * @return mixed
     */
    public function cmType()
    {
        return $this->cmType;
    }

    /**
     * @return mixed
     */
    public function cmSeconds()
    {
        return $this->cmSeconds;
    }

    /**
     * @return mixed
     */
    public function progIds()
    {
        return $this->progIds;
    }
}
