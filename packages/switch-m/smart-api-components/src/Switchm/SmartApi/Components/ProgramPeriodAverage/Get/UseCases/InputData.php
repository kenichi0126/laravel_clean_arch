<?php

namespace Switchm\SmartApi\Components\ProgramPeriodAverage\Get\UseCases;

use Carbon\Carbon;
use Switchm\SmartApi\Components\Common\DateTimeInputDataTrait;

class InputData
{
    use DateTimeInputDataTrait;

    private $holiday;

    private $dataType;

    private $wdays;

    private $genres;

    private $dispCount;

    private $dateRange;

    private $page;

    private $regionId;

    private $division;

    private $conditionCross;

    private $csvFlag;

    private $draw;

    private $codes;

    private $channels;

    private $programTypes;

    private $dispAverage;

    private $dataTypeFlags;

    private $baseDivision;

    private $sampleCountMaxNumber;

    private $userId;

    private $prefixes;

    private $selectedPersonalName;

    private $codeNumber;

    public function __construct(
        $startDateTime,
        $endDateTime,
        $holiday,
        $dataType,
        $wdays,
        $genres,
        $dispCount,
        $dateRange,
        $page,
        $regionId,
        $division,
        $conditionCross,
        $csvFlag,
        $draw,
        $codes,
        $channels,
        $programTypes,
        $dispAverage,
        $dataTypeFlags,
        $baseDivision,
        $sampleCountMaxNumber,
        $userId,
        $prefixes,
        $selectedPersonalName,
        $codeNumber
    ) {
        $this->startDateTime = Carbon::parse($startDateTime);
        $this->endDateTime = Carbon::parse($endDateTime);
        $this->holiday = $holiday;
        $this->dataType = $dataType;
        $this->wdays = $wdays;
        $this->genres = $genres;
        $this->dispCount = $dispCount;
        $this->dateRange = $dateRange;
        $this->page = $page;
        $this->regionId = $regionId;
        $this->division = $division;
        $this->conditionCross = $conditionCross;
        $this->csvFlag = $csvFlag;
        $this->draw = $draw;
        $this->codes = $codes;
        $this->channels = $channels;
        $this->programTypes = $programTypes;
        $this->dispAverage = $dispAverage;
        $this->dataTypeFlags = $dataTypeFlags;
        $this->baseDivision = $baseDivision;
        $this->sampleCountMaxNumber = $sampleCountMaxNumber;
        $this->userId = $userId;
        $this->prefixes = $prefixes;
        $this->selectedPersonalName = $selectedPersonalName;
        $this->codeNumber = $codeNumber;
    }

    public function holiday()
    {
        return $this->holiday;
    }

    public function dataType()
    {
        return $this->dataType;
    }

    public function wdays()
    {
        return $this->wdays;
    }

    public function genres()
    {
        return $this->genres;
    }

    public function dispCount()
    {
        return $this->dispCount;
    }

    public function dateRange()
    {
        return $this->dateRange;
    }

    public function page()
    {
        return $this->page;
    }

    public function regionId()
    {
        return $this->regionId;
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

    public function codes()
    {
        return $this->codes;
    }

    public function channels()
    {
        return $this->channels;
    }

    public function programTypes()
    {
        return $this->programTypes;
    }

    public function dispAverage()
    {
        return $this->dispAverage;
    }

    public function dataTypeFlags()
    {
        return $this->dataTypeFlags;
    }

    public function baseDivision()
    {
        return $this->baseDivision;
    }

    public function sampleCountMaxNumber()
    {
        return $this->sampleCountMaxNumber;
    }

    public function userId()
    {
        return $this->userId;
    }

    public function prefixes()
    {
        return $this->prefixes;
    }

    public function selectedPersonalName()
    {
        return $this->selectedPersonalName;
    }

    public function codeNumber()
    {
        return $this->codeNumber;
    }
}
