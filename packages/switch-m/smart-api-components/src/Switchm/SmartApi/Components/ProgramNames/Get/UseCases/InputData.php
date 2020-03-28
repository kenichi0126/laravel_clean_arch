<?php

namespace Switchm\SmartApi\Components\ProgramNames\Get\UseCases;

use Carbon\Carbon;
use Switchm\SmartApi\Components\Common\DateTimeInputDataTrait;

class InputData
{
    use DateTimeInputDataTrait;

    private $programName;

    private $channels;

    private $digitalAndBs;

    private $programFlag;

    private $digitalKanto;

    private $bs1;

    private $bs2;

    private $cmType;

    private $cmSeconds;

    private $productIds;

    private $companies;

    private $regionId;

    private $dataType;

    private $programIds;

    private $wdays;

    private $holiday;

    /**
     * @param $startDateTime
     * @param $endDateTime
     * @param $programName
     * @param $channels
     * @param $digitalAndBs
     * @param $programFlag
     * @param $digitalKanto
     * @param $bs1
     * @param $bs2
     * @param $cmType
     * @param $cmSeconds
     * @param $productIds
     * @param $companies
     * @param $regionId
     * @param $dataType
     * @param $programIds
     * @param $wdays
     * @param $holiday
     */
    public function __construct(
        $startDateTime,
        $endDateTime,
        $programName,
        $channels,
        $digitalAndBs,
        $programFlag,
        $digitalKanto,
        $bs1,
        $bs2,
        $cmType,
        $cmSeconds,
        $productIds,
        $companies,
        $regionId,
        $dataType,
        $programIds,
        $wdays,
        $holiday
    ) {
        $this->startDateTime = Carbon::parse($startDateTime);
        $this->endDateTime = Carbon::parse($endDateTime);
        $this->programName = $programName;
        $this->channels = $channels;
        $this->digitalAndBs = $digitalAndBs;
        $this->programFlag = $programFlag;
        $this->digitalKanto = $digitalKanto;
        $this->bs1 = $bs1;
        $this->bs2 = $bs2;
        $this->cmType = $cmType;
        $this->cmSeconds = $cmSeconds;
        $this->productIds = $productIds;
        $this->companies = $companies;
        $this->regionId = $regionId;
        $this->dataType = $dataType;
        $this->programIds = $programIds;
        $this->wdays = $wdays;
        $this->holiday = $holiday;
    }

    /**
     * @return mixed
     */
    public function programName()
    {
        return $this->programName;
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
    public function digitalAndBs()
    {
        return $this->digitalAndBs;
    }

    /**
     * @return mixed
     */
    public function programFlag()
    {
        return $this->programFlag;
    }

    /**
     * @return mixed
     */
    public function digitalKanto()
    {
        return $this->digitalKanto;
    }

    /**
     * @return mixed
     */
    public function bs1()
    {
        return $this->bs1;
    }

    /**
     * @return mixed
     */
    public function bs2()
    {
        return $this->bs2;
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
    public function productIds()
    {
        return $this->productIds;
    }

    /**
     * @return mixed
     */
    public function companies()
    {
        return $this->companies;
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
    public function dataType()
    {
        return $this->dataType;
    }

    /**
     * @return mixed
     */
    public function programIds()
    {
        return $this->programIds;
    }

    /**
     * @return mixed
     */
    public function wdays()
    {
        return $this->wdays;
    }

    /**
     * @return mixed
     */
    public function holiday()
    {
        return $this->holiday;
    }
}
