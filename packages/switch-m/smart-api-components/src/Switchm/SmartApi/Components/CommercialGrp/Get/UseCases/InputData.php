<?php

namespace Switchm\SmartApi\Components\CommercialGrp\Get\UseCases;

use Carbon\Carbon;
use Switchm\SmartApi\Components\Common\DateTimeInputDataTrait;

class InputData
{
    use DateTimeInputDataTrait;

    private $page;

    private $dataType;

    private $division;

    private $conditionCross;

    private $regionId;

    private $dateRange;

    private $productIds;

    private $companyIds;

    private $cmType;

    private $cmSeconds;

    private $progIds;

    private $codes;

    private $cmIds;

    private $channels;

    private $conv15SecFlag;

    private $period;

    private $allChannels;

    private $dispCount;

    private $csvFlag;

    private $draw;

    private $user;

    private $userId;

    private $sampleCountMaxNumber;

    private $dataTypeFlags;

    private $baseDivision;

    private $codeNumber;

    private $sampleCodePrefix;

    private $sampleCodeNumberPrefix;

    private $dataTypes;

    private $selectedPersonalName;

    /**
     * InputData constructor.
     * @param $startDateTime
     * @param $endDateTime
     * @param $page
     * @param $dataType
     * @param $division
     * @param $conditionCross
     * @param $regionId
     * @param $dateRange
     * @param $productIds
     * @param $companyIds
     * @param $cmType
     * @param $cmSeconds
     * @param $progIds
     * @param $codes
     * @param $cmIds
     * @param $channels
     * @param $conv15SecFlag
     * @param $period
     * @param $allChannels
     * @param $dispCount
     * @param $csvFlag
     * @param $draw
     * @param $user
     * @param $userId
     * @param $sampleCountMaxNumber
     * @param $dataTypeFlags
     * @param $baseDivision
     * @param $codeNumber
     * @param $sampleCodePrefix
     * @param $sampleCodeNumberPrefix
     * @param $selectedPersonalName
     * @param $dataTypes
     */
    public function __construct(
        $startDateTime,
        $endDateTime,
        $page,
        $dataType,
        $division,
        $conditionCross,
        $regionId,
        $dateRange,
        $productIds,
        $companyIds,
        $cmType,
        $cmSeconds,
        $progIds,
        $codes,
        $cmIds,
        $channels,
        $conv15SecFlag,
        $period,
        $allChannels,
        $dispCount,
        $csvFlag,
        $draw,
        $user,
        $userId,
        $sampleCountMaxNumber,
        $dataTypeFlags,
        $baseDivision,
        $codeNumber,
        $sampleCodePrefix,
        $sampleCodeNumberPrefix,
        $selectedPersonalName,
        $dataTypes
    ) {
        $this->startDateTime = Carbon::parse($startDateTime);
        $this->endDateTime = Carbon::parse($endDateTime);
        $this->page = $page;
        $this->dataType = $dataType;
        $this->division = $division;
        $this->conditionCross = $conditionCross;
        $this->regionId = $regionId;
        $this->dateRange = $dateRange;
        $this->productIds = $productIds;
        $this->companyIds = $companyIds;
        $this->cmType = $cmType;
        $this->cmSeconds = $cmSeconds;
        $this->progIds = $progIds;
        $this->codes = $codes;
        $this->cmIds = $cmIds;
        $this->channels = $channels;
        $this->conv15SecFlag = $conv15SecFlag;
        $this->period = $period;
        $this->allChannels = $allChannels;
        $this->dispCount = $dispCount;
        $this->csvFlag = $csvFlag;
        $this->draw = $draw;
        $this->user = $user;
        $this->userId = $userId;
        $this->sampleCountMaxNumber = $sampleCountMaxNumber;
        $this->dataTypeFlags = $dataTypeFlags;
        $this->baseDivision = $baseDivision;
        $this->codeNumber = $codeNumber;
        $this->sampleCodePrefix = $sampleCodePrefix;
        $this->sampleCodeNumberPrefix = $sampleCodeNumberPrefix;
        $this->selectedPersonalName = $selectedPersonalName;
        $this->dataTypes = $dataTypes;
    }

    /**
     * @return int
     */
    public function page(): int
    {
        return $this->page;
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
    public function division()
    {
        return $this->division;
    }

    /**
     * @return mixed
     */
    public function conditionCross()
    {
        return $this->conditionCross;
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
    public function dateRange()
    {
        return $this->dateRange;
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
    public function cmIds()
    {
        return $this->cmIds;
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
    public function companyIds()
    {
        return $this->companyIds;
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

    /**
     * @return mixed
     */
    public function codes()
    {
        return $this->codes;
    }

    /**
     * @return mixed
     */
    public function conv15SecFlag()
    {
        return $this->conv15SecFlag;
    }

    /**
     * @return mixed
     */
    public function period()
    {
        return $this->period;
    }

    /**
     * @return mixed
     */
    public function allChannels()
    {
        return $this->allChannels;
    }

    /**
     * @return mixed
     */
    public function dispCount()
    {
        return $this->dispCount;
    }

    /**
     * @return mixed
     */
    public function csvFlag()
    {
        return $this->csvFlag;
    }

    /**
     * @return mixed
     */
    public function draw()
    {
        return $this->draw;
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->user;
    }

    /**
     * @return mixed
     */
    public function userId()
    {
        return $this->userId;
    }

    /**
     * @return mixed
     */
    public function sampleCountMaxNumber()
    {
        return $this->sampleCountMaxNumber;
    }

    /**
     * @return mixed
     */
    public function dataTypeFlags()
    {
        return $this->dataTypeFlags;
    }

    /**
     * @return mixed
     */
    public function baseDivision()
    {
        return $this->baseDivision;
    }

    public function codeNumber()
    {
        return $this->codeNumber;
    }

    public function sampleCodePrefix()
    {
        return $this->sampleCodePrefix;
    }

    public function sampleCodeNumberPrefix()
    {
        return $this->sampleCodeNumberPrefix;
    }

    public function selectedPersonalName()
    {
        return $this->selectedPersonalName;
    }

    public function dataTypes()
    {
        return $this->dataTypes;
    }
}
