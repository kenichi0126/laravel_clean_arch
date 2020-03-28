<?php

namespace Switchm\SmartApi\Components\ProgramTableDetail\Get\UseCases;

use Switchm\SmartApi\Components\Common\DateTimeInputDataTrait;

class InputData
{
    use DateTimeInputDataTrait;

    private $regionId;

    private $division;

    private $progId;

    private $timeBoxId;

    private $subDate;

    private $boundary;

    /**
     * @param $regionId
     * @param $division
     * @param $progId
     * @param $timeBoxId
     * @param $subDate
     * @param $boundary
     */
    public function __construct(
        $regionId,
        $division,
        $progId,
        $timeBoxId,
        $subDate,
        $boundary
    ) {
        $this->regionId = $regionId;
        $this->division = $division;
        $this->progId = $progId;
        $this->timeBoxId = $timeBoxId;
        $this->subDate = $subDate;
        $this->boundary = $boundary;
    }

    public function regionId()
    {
        return $this->regionId;
    }

    public function division()
    {
        return $this->division;
    }

    public function progId()
    {
        return $this->progId;
    }

    public function timeBoxId()
    {
        return $this->timeBoxId;
    }

    public function subDate()
    {
        return $this->subDate;
    }

    public function boundary()
    {
        return $this->boundary;
    }
}
