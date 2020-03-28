<?php

namespace Switchm\SmartApi\Components\RankingCommercial\Get\UseCases;

use Carbon\Carbon;
use Switchm\SmartApi\Components\Common\DateTimeInputDataTrait;

class InputData
{
    use DateTimeInputDataTrait;

    private $page;

    private $holiday;

    private $wdays;

    private $division;

    private $dateRange;

    private $dataType;

    private $regionId;

    private $cmType;

    private $cmSeconds;

    private $progIds;

    private $codes;

    private $conditionCross;

    private $companyIds;

    private $productIds;

    private $cmIds;

    private $channels;

    private $order;

    private $conv15SecFlag;

    private $period;

    private $dispCount;

    private $csvFlag;

    private $cmLargeGenres;

    private $axisType;

    private $draw;

    private $userId;

    private $broadcasterCompanyIds;

    private $axisTypeCompany;

    private $axisTypeProduct;

    private $baseDivision;

    /**
     * InputData constructor.
     * @param $startDateTime
     * @param $endDateTime
     * @param $page
     * @param $holiday
     * @param $wdays
     * @param $division
     * @param $dateRange
     * @param $dataType
     * @param $regionId
     * @param $cmType
     * @param $codes
     * @param $conditionCross
     * @param $channels
     * @param $order
     * @param $conv15SecFlag
     * @param $period
     * @param $dispCount
     * @param $csvFlag
     * @param $cmLargeGenres
     * @param $axisType
     * @param $draw
     * @param $userId
     * @param $broadcasterCompanyIds
     * @param $axisTypeCompany
     * @param $axisTypeProduct
     * @param $baseDivision
     */
    public function __construct(
        $startDateTime,
        $endDateTime,
        $page,
        $holiday,
        $wdays,
        $division,
        $dateRange,
        $dataType,
        $regionId,
        $cmType,
        $codes,
        $conditionCross,
        $channels,
        $order,
        $conv15SecFlag,
        $period,
        $dispCount,
        $csvFlag,
        $cmLargeGenres,
        $axisType,
        $draw,
        $userId,
        $broadcasterCompanyIds,
        $axisTypeCompany,
        $axisTypeProduct,
        $baseDivision
    ) {
        $this->startDateTime = Carbon::parse($startDateTime);
        $this->endDateTime = Carbon::parse($endDateTime);
        $this->page = $page;
        $this->holiday = $holiday;
        $this->wdays = $wdays;
        $this->division = $division;
        $this->dateRange = $dateRange;
        $this->dataType = $dataType;
        $this->regionId = $regionId;
        $this->cmType = $cmType;
        $this->codes = $codes;
        $this->conditionCross = $conditionCross;
        $this->channels = $channels;
        $this->order = $order;
        $this->conv15SecFlag = $conv15SecFlag;
        $this->period = $period;
        $this->dispCount = $dispCount;
        $this->csvFlag = $csvFlag;
        $this->cmLargeGenres = $cmLargeGenres;
        $this->axisType = $axisType;
        $this->draw = $draw;
        $this->userId = $userId;
        $this->broadcasterCompanyIds = $broadcasterCompanyIds;
        $this->axisTypeCompany = $axisTypeCompany;
        $this->axisTypeProduct = $axisTypeProduct;
        $this->baseDivision = $baseDivision;
    }

    public function page()
    {
        return $this->page;
    }

    //TODO: takata/UIを修正したのちに修正
    public function isHoliday()
    {
        return $this->holiday === true || $this->holiday === 'true';
    }

    public function wdays()
    {
        return $this->wdays;
    }

    public function division()
    {
        return $this->division;
    }

    public function dateRange()
    {
        return $this->dateRange;
    }

    public function dataType()
    {
        return $this->dataType;
    }

    public function regionId()
    {
        return $this->regionId;
    }

    public function cmType()
    {
        return $this->cmType;
    }

    public function codes()
    {
        return $this->codes;
    }

    public function conditionCross()
    {
        return $this->conditionCross;
    }

    public function channels()
    {
        return $this->channels;
    }

    public function order()
    {
        return $this->order;
    }

    public function conv15SecFlag()
    {
        return $this->conv15SecFlag;
    }

    public function period()
    {
        return $this->period;
    }

    public function dispCount()
    {
        return $this->dispCount;
    }

    public function csvFlag()
    {
        return $this->csvFlag;
    }

    public function cmLargeGenres()
    {
        return $this->cmLargeGenres;
    }

    public function axisType()
    {
        return $this->axisType;
    }

    public function draw()
    {
        return $this->draw;
    }

    public function userId()
    {
        return $this->userId;
    }

    public function broadcasterCompanyIds()
    {
        return $this->broadcasterCompanyIds;
    }

    public function axisTypeCompany()
    {
        return $this->axisTypeCompany;
    }

    public function axisTypeProduct()
    {
        return $this->axisTypeProduct;
    }

    public function baseDivision(): array
    {
        return $this->baseDivision;
    }
}
