<?php

namespace Switchm\SmartApi\Components\ProgramTable\Get\UseCases;

class OutputData
{
    private $data;

    private $draw;

    private $dateList;

    private $header;

    /**
     * OutputData constructor.
     * @param $data
     * @param $draw
     * @param $dateList
     * @param $header
     */
    public function __construct($data, $draw, $dateList, $header)
    {
        $this->data = $data;
        $this->draw = $draw;
        $this->dateList = $dateList;
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
    public function dateList()
    {
        return $this->dateList;
    }

    /**
     * @return mixed
     */
    public function header()
    {
        return $this->header;
    }
}
