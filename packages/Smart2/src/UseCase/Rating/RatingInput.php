<?php

namespace Smart2\UseCase\Rating;

use Carbon\Carbon;
use Switchm\SmartApi\Components\Common\DateTimeInputDataTrait;

class RatingInput
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

    private $dateRange;

    private $dataDivision;

    private $dataType;

    private $displayType;

    private $aggregateType;

    private $advertising;

    private $hour;

    // startDateTime, endDateTime, regionId, channels, channelType, division, conditionCross, csvFlag, draw, code,
    // dateRange, dataDivision, dataType, displayType, aggregateType, advertising

    // # perminiutes
    // hour

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
        $dateRange,
        $dataDivision,
        $dataType,
        $displayType,
        $aggregateType,
        $advertising,
        $hour
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
        $this->dateRange = $dateRange;
        $this->dataDivision = $dataDivision;
        $this->dataType = $dataType;
        $this->displayType = $displayType;
        $this->aggregateType = $aggregateType;
        $this->advertising = $advertising;
        $this->hour = $hour;

        $this->validation();
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

    public function dateRange()
    {
        return $this->dateRange;
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

    public function advertising()
    {
        return $this->advertising;
    }

    public function hour()
    {
        return $this->hour;
    }

    private function validation(): void
    {
        searchPeriodValidation('RATING_POINTS', $this->division(), $this->dateRange());
        searchRangeValidation($this->startDateTime(), $this->endDateTime(), $this->dataType(), $this->regionId());
    }
}
