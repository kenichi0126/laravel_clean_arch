<?php

namespace Switchm\SmartApi\Components\CommercialList\Get\UseCases;

use Carbon\Carbon;
use Switchm\SmartApi\Components\Common\DateTimeInputDataTrait;

class InputData
{
    use DateTimeInputDataTrait;

    private $page;

    private $dateRange;

    private $cmType;

    private $cmSeconds;

    private $progIds;

    private $regionId;

    private $division;

    private $codes;

    private $conditionCross;

    private $companyIds;

    private $productIds;

    private $cmIds;

    private $channels;

    private $order;

    private $dispCount;

    private $conv_15_sec_flag;

    private $csvFlag;

    private $dataType;

    private $draw;

    private $user;

    private $userId;

    private $sampleCountMaxNumber;

    private $dataTypeFlags;

    private $baseDivision;

    private $codeNumber;

    private $sampleCodePrefix;

    private $sampleCodeNumberPrefix;

    private $selectedPersonalName;

    private $dataTypes;

    private $cmMaterialFlag;

    private $cmTypeFlag;

    /**
     * InputData constructor.
     * @param $startDateTime
     * @param $endDateTime
     * @param $page
     * @param $dateRange
     * @param $cmType
     * @param $cmSeconds
     * @param $progIds
     * @param $regionId
     * @param $division
     * @param $codes
     * @param $conditionCross
     * @param $companyIds
     * @param $productIds
     * @param $cmIds
     * @param $channels
     * @param $order
     * @param $dispCount
     * @param $conv_15_sec_flag
     * @param $csvFlag
     * @param $dataType
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
     * @param $cmMaterialFlag
     * @param $cmTypeFlag
     */
    public function __construct(
        $startDateTime,
        $endDateTime,
        $page,
        $dateRange,
        $cmType,
        $cmSeconds,
        $progIds,
        $regionId,
        $division,
        $codes,
        $conditionCross,
        $companyIds,
        $productIds,
        $cmIds,
        $channels,
        $order,
        $dispCount,
        $conv_15_sec_flag,
        $csvFlag,
        $dataType,
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
        $dataTypes,
        $cmMaterialFlag,
        $cmTypeFlag
    ) {
        $this->startDateTime = Carbon::parse($startDateTime);
        $this->endDateTime = Carbon::parse($endDateTime);
        $this->page = $page;
        $this->dateRange = $dateRange;
        $this->cmType = $cmType;
        $this->cmSeconds = $cmSeconds;
        $this->progIds = $progIds;
        $this->regionId = $regionId;
        $this->division = $division;
        $this->codes = $codes;
        $this->conditionCross = $conditionCross;
        $this->companyIds = $companyIds;
        $this->productIds = $productIds;
        $this->cmIds = $cmIds;
        $this->channels = $channels;
        $this->order = $order;
        $this->dispCount = $dispCount;
        $this->conv_15_sec_flag = $conv_15_sec_flag;
        $this->csvFlag = $csvFlag;
        $this->dataType = $dataType;
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
        $this->cmMaterialFlag = $cmMaterialFlag;
        $this->cmTypeFlag = $cmTypeFlag;
    }

    public function startDateTime(): Carbon
    {
        return $this->startDateTime;
    }

    public function endDateTime(): Carbon
    {
        return $this->endDateTime;
    }

    public function page()
    {
        return $this->page;
    }

    public function dataType()
    {
        return $this->dataType;
    }

    public function division()
    {
        return $this->division;
    }

    public function regionId()
    {
        return $this->regionId;
    }

    public function dateRange()
    {
        return $this->dateRange;
    }

    public function cmType()
    {
        return $this->cmType;
    }

    public function cmSeconds()
    {
        return $this->cmSeconds;
    }

    public function progIds()
    {
        return $this->progIds;
    }

    public function codes()
    {
        return $this->codes;
    }

    public function conditionCross()
    {
        return $this->conditionCross;
    }

    public function companyIds()
    {
        return $this->companyIds;
    }

    public function productIds()
    {
        return $this->productIds;
    }

    public function cmIds()
    {
        return $this->cmIds;
    }

    public function channels()
    {
        return $this->channels;
    }

    public function order()
    {
        return $this->order;
    }

    public function dispCount()
    {
        return $this->dispCount;
    }

    public function conv15SecFlag()
    {
        return $this->conv_15_sec_flag;
    }

    public function csvFlag()
    {
        return $this->csvFlag;
    }

    public function draw()
    {
        return $this->draw;
    }

    public function user()
    {
        return $this->user;
    }

    public function userId()
    {
        return $this->userId;
    }

    public function sampleCountMaxNumber()
    {
        return $this->sampleCountMaxNumber;
    }

    public function dataTypeFlags()
    {
        return $this->dataTypeFlags;
    }

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

    public function cmMaterialFlag()
    {
        return $this->cmMaterialFlag;
    }

    public function cmTypeFlag()
    {
        return $this->cmTypeFlag;
    }
}
