<?php

namespace Switchm\SmartApi\Components\RatingPerMinutes\Get\UseCases;

use Carbon\Carbon;
use Switchm\SmartApi\Components\Common\DateTimeInputDataTrait;

class InputData
{
    use DateTimeInputDataTrait;

    private $regionId;

    private $channels;

    private $channelType;

    private $division;

    private $conditionCross;

    private $csvFlag;

    private $draw;

    private $code;

    private $dataDivision;

    private $dataType;

    private $displayType;

    private $aggregateType;

    private $hour;

    private $sampleCountMaxNumber;

    private $userId;

    private $rdbDwhSearchPeriod;

    private $baseDivision;

    private $intervalHourly;

    private $intervalMinutes;

    public function __construct(
        $startDateTime,
        $endDateTime,
        $regionId,
        $channels,
        $channelType,
        $division,
        $conditionCross,
        $csvFlag,
        $draw,
        $code,
        $dataDivision,
        $dataType,
        $displayType,
        $aggregateType,
        $hour,
        $sampleCountMaxNumber,
        $userId,
        $rdbDwhSearchPeriod,
        $baseDivision,
        $intervalHourly,
        $intervalMinutes
    ) {
        $this->startDateTime = Carbon::parse($startDateTime);
        $this->endDateTime = Carbon::parse($endDateTime);
        $this->regionId = $regionId;
        $this->channels = $channels;
        $this->channelType = $channelType;
        $this->division = $division;
        $this->conditionCross = $conditionCross;
        $this->csvFlag = $csvFlag;
        $this->draw = $draw;
        $this->code = $code;
        $this->dataDivision = $dataDivision;
        $this->dataType = $dataType;
        $this->displayType = $displayType;
        $this->aggregateType = $aggregateType;
        $this->hour = $hour;
        $this->sampleCountMaxNumber = $sampleCountMaxNumber;
        $this->userId = $userId;
        $this->rdbDwhSearchPeriod = $rdbDwhSearchPeriod;
        $this->baseDivision = $baseDivision;
        $this->intervalHourly = $intervalHourly;
        $this->intervalMinutes = $intervalMinutes;
    }

    public function regionId()
    {
        return $this->regionId;
    }

    public function channels()
    {
        return $this->channels;
    }

    public function channelType()
    {
        return $this->channelType;
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

    public function dataDivision()
    {
        return $this->dataDivision;
    }

    public function dataType()
    {
        return $this->dataType;
    }

    public function displayType()
    {
        return $this->displayType;
    }

    public function aggregateType()
    {
        return $this->aggregateType;
    }

    public function hour()
    {
        return $this->hour;
    }

    public function sampleCountMaxNumber()
    {
        return $this->sampleCountMaxNumber;
    }

    public function userId()
    {
        return $this->userId;
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
}
