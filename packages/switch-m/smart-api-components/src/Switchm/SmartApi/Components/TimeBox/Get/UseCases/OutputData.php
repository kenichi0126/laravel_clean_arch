<?php

namespace Switchm\SmartApi\Components\TimeBox\Get\UseCases;

class OutputData
{
    private $id;

    private $regionId;

    private $startDate;

    private $duration;

    private $version;

    private $startedAt;

    private $endedAt;

    private $panelersNumber;

    private $householdsNumber;

    /**
     * OutputData constructor.
     * @param $id
     * @param $regionId
     * @param $startDate
     * @param $duration
     * @param $version
     * @param $startedAt
     * @param $endedAt
     * @param $panelersNumber
     * @param $householdsNumber
     */
    public function __construct(
        $id,
        $regionId,
        $startDate,
        $duration,
        $version,
        $startedAt,
        $endedAt,
        $panelersNumber,
        $householdsNumber
    ) {
        $this->id = $id;
        $this->regionId = $regionId;
        $this->startDate = $startDate;
        $this->duration = $duration;
        $this->version = $version;
        $this->startedAt = $startedAt;
        $this->endedAt = $endedAt;
        $this->panelersNumber = $panelersNumber;
        $this->householdsNumber = $householdsNumber;
    }

    /**
     * @return mixed
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function regionId()
    {
        return $this->regionId;
    }

    public function startDate()
    {
        return $this->startDate;
    }

    public function duration()
    {
        return $this->duration;
    }

    public function version()
    {
        return $this->version;
    }

    public function startedAt()
    {
        return $this->startedAt;
    }

    public function endedAt()
    {
        return $this->endedAt;
    }

    public function panelersNumber()
    {
        return $this->panelersNumber;
    }

    public function householdsNumber()
    {
        return $this->householdsNumber;
    }
}
