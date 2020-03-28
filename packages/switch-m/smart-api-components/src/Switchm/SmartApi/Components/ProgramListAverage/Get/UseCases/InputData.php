<?php

namespace Switchm\SmartApi\Components\ProgramListAverage\Get\UseCases;

class InputData
{
    private $averageType;

    private $codes;

    private $conditionCross;

    private $dataType;

    private $digitalAndBs;

    private $division;

    private $progIds;

    private $regionId;

    private $timeBoxIds;

    private $baseDivision;

    private $dataTypeFlags;

    private $dataTypeConst;

    private $prefixes;

    private $selectedPersonalName;

    private $codeNumber;

    public function __construct(
        $averageType,
        $codes,
        $conditionCross,
        $dataType,
        $digitalAndBs,
        $division,
        $progIds,
        $regionId,
        $timeBoxIds,
        $baseDivision,
        $dataTypeFlags,
        $dataTypeConst,
        $prefixes,
        $selectedPersonalName,
        $codeNumber
    ) {
        $this->averageType = $averageType;
        $this->codes = $codes;
        $this->conditionCross = $conditionCross;
        $this->dataType = $dataType;
        $this->digitalAndBs = $digitalAndBs;
        $this->division = $division;
        $this->progIds = $progIds;
        $this->regionId = $regionId;
        $this->timeBoxIds = $timeBoxIds;
        $this->baseDivision = $baseDivision;
        $this->dataTypeFlags = $dataTypeFlags;
        $this->dataTypeConst = $dataTypeConst;
        $this->prefixes = $prefixes;
        $this->selectedPersonalName = $selectedPersonalName;
        $this->codeNumber = $codeNumber;
    }

    public function averageType()
    {
        return $this->averageType;
    }

    public function codes()
    {
        return $this->codes;
    }

    public function conditionCross()
    {
        return $this->conditionCross;
    }

    public function dataType()
    {
        return $this->dataType;
    }

    public function digitalAndBs()
    {
        return $this->digitalAndBs;
    }

    public function division()
    {
        return $this->division;
    }

    public function progIds()
    {
        return $this->progIds;
    }

    public function regionId()
    {
        return $this->regionId;
    }

    public function timeBoxIds()
    {
        return $this->timeBoxIds;
    }

    public function baseDivision()
    {
        return $this->baseDivision;
    }

    public function dataTypeFlags()
    {
        return $this->dataTypeFlags;
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
