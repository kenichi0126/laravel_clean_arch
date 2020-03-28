<?php

namespace Switchm\SmartApi\Components\RafChart\Get\UseCases;

use Carbon\Carbon;
use Switchm\SmartApi\Components\Common\DateTimeInputDataTrait;

class InputData
{
    use DateTimeInputDataTrait;

    protected $startDateTime;

    protected $endDateTime;

    protected $dataType;

    protected $dateRange;

    protected $regionId;

    protected $division;

    protected $conditionCross;

    protected $csvFlag;

    protected $codes;

    protected $channels;

    protected $axisType;

    protected $channelAxis;

    protected $cmIds;

    protected $cmSeconds;

    protected $cmType;

    protected $codeNames;

    protected $companyIds;

    protected $conv15SecFlag;

    protected $period;

    protected $productIds;

    protected $progIds;

    protected $reachAndFrequencyGroupingUnit;

    protected $dataTypeFlags;

    protected $axisTypeProduct;

    protected $productAxisLimit;

    protected $userId;

    protected $axisTypeCompany;

    protected $baseDivision;

    public function __construct(
        $startDateTime,
        $endDateTime,
        $dataType,
        $dateRange,
        $regionId,
        $division,
        $conditionCross,
        $csvFlag,
        $codes,
        $channels,
        $axisType,
        $channelAxis,
        $cmIds,
        $cmSeconds,
        $cmType,
        $codeNames,
        $companyIds,
        $conv15SecFlag,
        $period,
        $productIds,
        $progIds,
        $reachAndFrequencyGroupingUnit,
        $dataTypeFlags,
        $axisTypeProduct,
        $productAxisLimit,
        $userId,
        $axisTypeCompany,
        $baseDivision
    ) {
        $this->startDateTime = Carbon::parse($startDateTime);
        $this->endDateTime = Carbon::parse($endDateTime);

        $this->dataType = $dataType;
        $this->dateRange = $dateRange;
        $this->regionId = $regionId;
        $this->division = $division;
        $this->conditionCross = $conditionCross;
        $this->csvFlag = $csvFlag;
        $this->codes = $codes;
        $this->channels = $channels;
        $this->axisType = $axisType;
        $this->channelAxis = $channelAxis;
        $this->cmIds = $cmIds;
        $this->cmSeconds = $cmSeconds;
        $this->cmType = $cmType;
        $this->codeNames = $codeNames;
        $this->companyIds = $companyIds;
        $this->conv15SecFlag = $conv15SecFlag;
        $this->period = $period;
        $this->productIds = $productIds;
        $this->progIds = $progIds;
        $this->reachAndFrequencyGroupingUnit = $reachAndFrequencyGroupingUnit;
        $this->dataTypeFlags = $dataTypeFlags;
        $this->axisTypeProduct = $axisTypeProduct;
        $this->productAxisLimit = $productAxisLimit;
        $this->userId = $userId;
        $this->axisTypeCompany = $axisTypeCompany;
        $this->baseDivision = $baseDivision;
    }

    public function dataType()
    {
        return $this->dataType;
    }

    public function dateRange()
    {
        return $this->dateRange;
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

    public function codes()
    {
        return $this->codes;
    }

    public function channels()
    {
        return $this->channels;
    }

    public function axisType(): string
    {
        return $this->axisType;
    }

    public function channelAxis()
    {
        return $this->channelAxis;
    }

    public function cmIds()
    {
        return $this->cmIds;
    }

    public function cmSeconds()
    {
        return $this->cmSeconds;
    }

    public function cmType()
    {
        return $this->cmType;
    }

    public function codeNames()
    {
        return $this->codeNames;
    }

    public function companyIds()
    {
        return $this->companyIds;
    }

    public function period()
    {
        return $this->period;
    }

    public function productIds()
    {
        return $this->productIds;
    }

    public function conv15SecFlag()
    {
        return $this->conv15SecFlag;
    }

    public function progIds()
    {
        return $this->progIds;
    }

    public function reachAndFrequencyGroupingUnit()
    {
        return $this->reachAndFrequencyGroupingUnit;
    }

    public function dataTypeFlags()
    {
        return $this->dataTypeFlags;
    }

    public function axisTypeProduct(): string
    {
        return $this->axisTypeProduct;
    }

    public function productAxisLimit()
    {
        return $this->productAxisLimit;
    }

    public function userId()
    {
        return $this->userId;
    }

    public function axisTypeCompany()
    {
        return $this->axisTypeCompany;
    }

    public function baseDivision(): array
    {
        return $this->baseDivision;
    }
}
