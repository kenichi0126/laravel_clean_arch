<?php

namespace Switchm\SmartApi\Components\ProgramTable\Get\UseCases;

use Carbon\Carbon;
use Switchm\SmartApi\Components\Common\DateTimeInputDataTrait;

class InputData
{
    use DateTimeInputDataTrait;

    private $digitalAndBs;

    private $digitalKanto;

    private $bs1;

    private $bs2;

    private $regionId;

    private $division;

    private $conditionCross;

    private $draw;

    private $codes;

    private $channels;

    private $dispPeriod;

    private $baseDivision;

    private $period;

    private $userId;

    public function __construct(
        $startDateTime,
        $endDateTime,
        $digitalAndBs,
        $digitalKanto,
        $bs1,
        $bs2,
        $regionId,
        $division,
        $conditionCross,
        $draw,
        $codes,
        $channels,
        $dispPeriod,
        $baseDivision,
        $period,
        $userId
    ) {
        $this->startDateTime = Carbon::parse($startDateTime);
        $this->endDateTime = Carbon::parse($endDateTime);
        $this->digitalAndBs = $digitalAndBs;
        $this->digitalKanto = $digitalKanto;
        $this->bs1 = $bs1;
        $this->bs2 = $bs2;
        $this->regionId = $regionId;
        $this->division = $division;
        $this->conditionCross = $conditionCross;
        $this->draw = $draw;
        $this->codes = $codes;
        $this->channels = $channels;
        $this->dispPeriod = $dispPeriod;
        $this->baseDivision = $baseDivision;
        $this->period = $period;
        $this->userId = $userId;
    }

    public function digitalAndBs()
    {
        return $this->digitalAndBs;
    }

    public function digitalKanto()
    {
        return $this->digitalKanto;
    }

    public function bs1()
    {
        return $this->bs1;
    }

    public function bs2()
    {
        return $this->bs2;
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

    public function draw()
    {
        return $this->draw;
    }

    public function codes()
    {
        return $this->codes;
    }

    public function channels()
    {
        return $this->channels;
    }

    public function dispPeriod()
    {
        return $this->dispPeriod;
    }

    public function baseDivision()
    {
        return $this->baseDivision;
    }

    public function period()
    {
        return $this->period;
    }

    public function userId()
    {
        return $this->userId;
    }
}
