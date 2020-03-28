<?php

namespace Switchm\SmartApi\Components\CommercialList\Get\UseCases;

class OutputData
{
    private $list;

    private $draw;

    private $cnt;

    private $startDateShort;

    private $endDateShort;

    private $header;

    /**
     * OutputData constructor.
     * @param $list
     * @param $draw
     * @param $cnt
     * @param $startDateShort
     * @param $endDateShort
     * @param $header
     */
    public function __construct($list, $draw, $cnt, $startDateShort, $endDateShort, $header)
    {
        $this->list = $list;
        $this->draw = $draw;
        $this->cnt = $cnt;
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
    public function cnt()
    {
        return $this->cnt;
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
