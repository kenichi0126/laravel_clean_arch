<?php

namespace Switchm\SmartApi\Components\ProgramPeriodAverage\Get\UseCases;

class OutputData
{
    private $data;

    private $draw;

    private $recordsFiltered;

    private $recordsTotal;

    private $startDateShort;

    private $endDateShort;

    private $header;

    public function __construct($data, $draw, $recordsFiltered, $recordsTotal, $startDateShort, $endDateShort, $header)
    {
        $this->data = $data;
        $this->draw = $draw;
        $this->recordsFiltered = $recordsFiltered;
        $this->recordsTotal = $recordsTotal;
        $this->startDateShort = $startDateShort;
        $this->endDateShort = $endDateShort;
        $this->header = $header;
    }

    /**
     * @return mixed
     */
    public function data()
    {
        return $this->data;
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
    public function recordsFiltered()
    {
        return $this->recordsFiltered;
    }

    /**
     * @return mixed
     */
    public function recordsTotal()
    {
        return $this->recordsTotal;
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

    /**
     * @return mixed
     */
    public function header()
    {
        return $this->header;
    }
}
