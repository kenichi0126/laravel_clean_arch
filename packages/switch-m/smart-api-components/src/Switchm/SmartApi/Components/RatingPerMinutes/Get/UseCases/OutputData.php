<?php

namespace Switchm\SmartApi\Components\RatingPerMinutes\Get\UseCases;

class OutputData
{
    private $data;

    private $draw;

    private $recordsFiltered;

    private $recordsTotal;

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
        $recordsFiltered,
        $recordsTotal,
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
        $this->recordsFiltered = $recordsFiltered;
        $this->recordsTotal = $recordsTotal;
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

    public function recordsFiltered()
    {
        return $this->recordsFiltered;
    }

    public function recordsTotal()
    {
        return $this->recordsTotal;
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
