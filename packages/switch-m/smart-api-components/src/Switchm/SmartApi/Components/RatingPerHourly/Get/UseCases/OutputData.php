<?php

namespace Switchm\SmartApi\Components\RatingPerHourly\Get\UseCases;

class OutputData
{
    private $data;

    private $draw;

    private $cnt;

    private $dateList;

    private $channelType;

    private $displayType;

    private $aggregateType;

    private $startDateShort;

    private $endDateShort;

    private $header;

    public function __construct(
        $data,
        $draw,
        $cnt,
        $dateList,
        $channelType,
        $displayType,
        $aggregateType,
        $startDateShort,
        $endDateShort,
        $header
    ) {
        $this->data = $data;
        $this->draw = $draw;
        $this->cnt = $cnt;
        $this->dateList = $dateList;
        $this->channelType = $channelType;
        $this->displayType = $displayType;
        $this->aggregateType = $aggregateType;
        $this->startDateShort = $startDateShort;
        $this->endDateShort = $endDateShort;
        $this->header = $header;
    }

    public function data()
    {
        return $this->data;
    }

    public function draw()
    {
        return $this->draw;
    }

    public function cnt()
    {
        return $this->cnt;
    }

    public function dateList()
    {
        return $this->dateList;
    }

    public function channelType()
    {
        return $this->channelType;
    }

    public function displayType()
    {
        return $this->displayType;
    }

    public function aggregateType()
    {
        return $this->aggregateType;
    }

    public function startDateShort()
    {
        return $this->startDateShort;
    }

    public function endDateShort()
    {
        return $this->endDateShort;
    }

    public function header()
    {
        return $this->header;
    }
}
