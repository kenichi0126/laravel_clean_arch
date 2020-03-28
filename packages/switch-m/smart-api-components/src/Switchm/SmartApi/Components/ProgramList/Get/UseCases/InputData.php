<?php

namespace Switchm\SmartApi\Components\ProgramList\Get\UseCases;

use Carbon\Carbon;
use Switchm\SmartApi\Components\Common\DateTimeInputDataTrait;

class InputData
{
    use DateTimeInputDataTrait;

    private $digitalAndBs;

    private $digitalKanto;

    private $bs1;

    private $bs2;

    private $holiday;

    private $dataType;

    private $wdays;

    private $genres;

    private $programNames;

    private $order;

    private $dispCount;

    private $dateRange;

    private $page;

    private $regionId;

    private $division;

    private $conditionCross;

    private $csvFlag;

    private $draw;

    private $codes;

    private $dataTypeFlags;

    private $userId;

    private $hasPermission;

    private $baseDivision;

    private $sampleCountMaxNumber;

    private $dataTypeConst;

    private $prefixes;

    private $selectedPersonalName;

    private $codeNumber;

    public function __construct(
        $startDateTime,
        $endDateTime,
        $digitalAndBs,
        $digitalKanto,
        $bs1,
        $bs2,
        $holiday,
        $dataType,
        $wdays,
        $genres,
        $programNames,
        $order,
        $dispCount,
        $dateRange,
        $page,
        $regionId,
        $division,
        $conditionCross,
        $csvFlag,
        $draw,
        $codes,
        $dataTypeFlags,
        $userId,
        $hasPermission,
        $baseDivision,
        $sampleCountMaxNumber,
        $dataTypeConst,
        $prefixes,
        $selectedPersonalName,
        $codeNumber
    ) {
        $this->startDateTime = Carbon::parse($startDateTime);
        $this->endDateTime = Carbon::parse($endDateTime);
        $this->digitalAndBs = $digitalAndBs;
        $this->digitalKanto = $digitalKanto;
        $this->bs1 = $bs1;
        $this->bs2 = $bs2;
        $this->holiday = $holiday;
        $this->dataType = $dataType;
        $this->wdays = $wdays;
        $this->genres = $genres;
        $this->programNames = $programNames;
        $this->order = $order;
        $this->dispCount = $dispCount;
        $this->dateRange = $dateRange;
        $this->page = $page;
        $this->regionId = $regionId;
        $this->division = $division;
        $this->conditionCross = $conditionCross;
        $this->csvFlag = $csvFlag;
        $this->draw = $draw;
        $this->codes = $codes;
        $this->dataTypeFlags = $dataTypeFlags;
        $this->userId = $userId;
        $this->hasPermission = $hasPermission;
        $this->baseDivision = $baseDivision;
        $this->sampleCountMaxNumber = $sampleCountMaxNumber;
        $this->dataTypeConst = $dataTypeConst;
        $this->prefixes = $prefixes;
        $this->selectedPersonalName = $selectedPersonalName;
        $this->codeNumber = $codeNumber;
    }

    public function digitalAndBs()
    {
        return $this->digitalAndBs;
    }

    public function digitalKanto()
    {
        return $this->digitalKanto;
    }

    public function bs1()
    {
        return $this->bs1;
    }

    public function bs2()
    {
        return $this->bs2;
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

    public function programNames()
    {
        return $this->programNames;
    }

    public function order()
    {
        return $this->order;
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

    public function dataTypeFlags()
    {
        return $this->dataTypeFlags;
    }

    public function userId()
    {
        return $this->userId;
    }

    public function hasPermission()
    {
        return $this->hasPermission;
    }

    public function baseDivision()
    {
        return $this->baseDivision;
    }

    public function sampleCountMaxNumber()
    {
        return $this->sampleCountMaxNumber;
    }

    public function dataTypeConst()
    {
        return $this->dataTypeConst;
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
