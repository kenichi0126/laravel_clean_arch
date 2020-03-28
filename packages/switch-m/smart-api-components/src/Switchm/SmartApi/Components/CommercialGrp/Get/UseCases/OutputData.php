<?php

namespace Switchm\SmartApi\Components\CommercialGrp\Get\UseCases;

class OutputData
{
    private $list;

    private $draw;

    private $division;

    private $codes;

    private $codeList;

    private $period;

    private $dataType;

    private $startDateShort;

    private $endDateShort;

    private $header;

    /**
     * OutputData constructor.
     * @param $list
     * @param $draw
     * @param $division
     * @param $codes
     * @param $codeList
     * @param $period
     * @param $dataType
     * @param $startDateShort
     * @param $endDateShort
     * @param $header
     */
    public function __construct($list, $draw, $division, $codes, $codeList, $period, $dataType, $startDateShort, $endDateShort, $header)
    {
        $this->list = $list;
        $this->draw = $draw;
        $this->division = $division;
        $this->codes = $codes;
        $this->codeList = $codeList;
        $this->period = $period;
        $this->dataType = $dataType;
        $this->startDateShort = $startDateShort;
        $this->endDateShort = $endDateShort;
        $this->header = $header;
    }

    /**
     * @return mixed
     */
    public function list()
    {
        return $this->list;
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
    public function division()
    {
        return $this->division;
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
    public function codeList()
    {
        return $this->codeList;
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
    public function dataType()
    {
        return $this->dataType;
    }

    /**
     * @return mixed
     */
    public function startDateShort()
    {
        return $this->startDateShort;
    }

    /**
     * @return mixed
     */
    public function endDateShort()
    {
        return $this->endDateShort;
    }

    public function header()
    {
        return $this->header;
    }
}
