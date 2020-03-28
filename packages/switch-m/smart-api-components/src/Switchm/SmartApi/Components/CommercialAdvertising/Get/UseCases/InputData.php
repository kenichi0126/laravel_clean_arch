<?php

namespace Switchm\SmartApi\Components\CommercialAdvertising\Get\UseCases;

use Carbon\Carbon;
use Switchm\SmartApi\Components\Common\DateTimeInputDataTrait;

class InputData
{
    use DateTimeInputDataTrait;

    private $companyIds;

    private $productIds;

    private $cmType;

    private $cmSeconds;

    private $progIds;

    private $regionId;

    private $cmIds;

    private $channels;

    private $heatMapRating;

    private $heatMapTciPersonal;

    private $heatMapTciHousehold;

    private $division;

    private $conditionCross;

    private $csvFlag;

    private $draw;

    private $code;

    private $userID;

    private $sampleCountMaxNumber;

    private $rdbDwhSearchPeriod;

    private $baseDivision;

    private $intervalHourly;

    private $intervalMinutes;

    private $sampleCodePrefix;

    private $sampleCodeNumberPrefix;

    private $selectedPersonalName;

    public function __construct(
        $startDateTime,
        $endDateTime,
        $companyIds,
        $productIds,
        $cmType,
        $cmSeconds,
        $progIds,
        $regionId,
        $cmIds,
        $channels,
        $heatMapRating,
        $heatMapTciPersonal,
        $heatMapTciHousehold,
        $division,
        $conditionCross,
        $csvFlag,
        $draw,
        $code,
        $userID,
        $sampleCountMaxNumber,
        $rdbDwhSearchPeriod,
        $baseDivision,
        $intervalHourly,
        $intervalMinutes,
        $sampleCodePrefix,
        $sampleCodeNumberPrefix,
        $selectedPersonalName
    ) {
        $this->startDateTime = Carbon::parse($startDateTime);
        $this->endDateTime = Carbon::parse($endDateTime);
        $this->companyIds = $companyIds;
        $this->productIds = $productIds;
        $this->cmType = $cmType;
        $this->cmSeconds = $cmSeconds;
        $this->progIds = $progIds;
        $this->regionId = $regionId;
        $this->cmIds = $cmIds;
        $this->channels = $channels;
        $this->heatMapRating = $heatMapRating;
        $this->heatMapTciPersonal = $heatMapTciPersonal;
        $this->heatMapTciHousehold = $heatMapTciHousehold;
        $this->division = $division;
        $this->conditionCross = $conditionCross;
        $this->csvFlag = $csvFlag;
        $this->draw = $draw;
        $this->code = $code;
        $this->userID = $userID;
        $this->sampleCountMaxNumber = $sampleCountMaxNumber;
        $this->rdbDwhSearchPeriod = $rdbDwhSearchPeriod;
        $this->baseDivision = $baseDivision;
        $this->intervalHourly = $intervalHourly;
        $this->intervalMinutes = $intervalMinutes;
        $this->sampleCodePrefix = $sampleCodePrefix;
        $this->sampleCodeNumberPrefix = $sampleCodeNumberPrefix;
        $this->selectedPersonalName = $selectedPersonalName;
    }

    public function companyIds()
    {
        return $this->companyIds;
    }

    public function productIds()
    {
        return $this->productIds;
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

    public function regionId()
    {
        return $this->regionId;
    }

    public function cmIds()
    {
        return $this->cmIds;
    }

    public function channels()
    {
        return $this->channels;
    }

    public function heatMapRating()
    {
        return $this->heatMapRating;
    }

    public function heatMapTciPersonal()
    {
        return $this->heatMapTciPersonal;
    }

    public function heatMapTciHousehold()
    {
        return $this->heatMapTciHousehold;
    }

    public function division()
    {
        return $this->division;
    }

    public function conditionCross()
    {
        return $this->conditionCross;
    }

    public function csvFlag()
    {
        return $this->csvFlag;
    }

    public function draw()
    {
        return $this->draw;
    }

    public function code()
    {
        return $this->code;
    }

    public function userID()
    {
        return $this->userID;
    }

    public function sampleCountMaxNumber()
    {
        return $this->sampleCountMaxNumber;
    }

    public function rdbDwhSearchPeriod()
    {
        return $this->rdbDwhSearchPeriod;
    }

    public function baseDivision()
    {
        return $this->baseDivision;
    }

    public function intervalHourly()
    {
        return $this->intervalHourly;
    }

    public function intervalMinutes()
    {
        return $this->intervalMinutes;
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
}
