<?php

namespace Switchm\SmartApi\Components\CommercialAdvertising\Get\UseCases;

final class OutputData
{
    private $data;

    private $channels;

    private $csvFlag;

    private $draw;

    private $rp;

    private $startDateTimeShort;

    private $endDateTimeShort;

    private $header;

    public function __construct($data, $channels, $csvFlag, $draw, $rp, $startDateTimeShort, $endDateTimeShort, $header)
    {
        $this->data = $data;
        $this->channels = $channels;
        $this->csvFlag = $csvFlag;
        $this->draw = $draw;
        $this->rp = $rp;
        $this->startDateTimeShort = $startDateTimeShort;
        $this->endDateTimeShort = $endDateTimeShort;
        $this->header = $header;
    }

    public function data()
    {
        return $this->data;
    }

    public function channels()
    {
        return $this->channels;
    }

    public function csvFlag()
    {
        return $this->csvFlag;
    }

    public function draw()
    {
        return $this->draw;
    }

    public function rp()
    {
        return $this->rp;
    }

    public function startDateTimeShort()
    {
        return $this->startDateTimeShort;
    }

    public function endDateTimeShort()
    {
        return $this->endDateTimeShort;
    }

    public function header()
    {
        return $this->header;
    }
}
