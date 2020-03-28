<?php

namespace Switchm\SmartApi\Components\RafChart\Get\UseCases;

class OutputData
{
    private $series;

    private $categories;

    private $average;

    private $overOne;

    private $grp;

    private $csvButtonInfo;

    private $header;

    /**
     * OutputData constructor.
     * @param array $series
     * @param array $categories
     * @param array $average
     * @param array $overOne
     * @param array $grp
     * @param array $csvButtonInfo
     * @param $header
     */
    public function __construct(array $series, array $categories, array $average, array $overOne, array $grp, array $csvButtonInfo, array $header)
    {
        $this->series = $series;
        $this->categories = $categories;
        $this->average = $average;
        $this->overOne = $overOne;
        $this->grp = $grp;
        $this->csvButtonInfo = $csvButtonInfo;
        $this->header = $header;
    }

    /**
     * @return mixed
     */
    public function series()
    {
        return $this->series;
    }

    /**
     * @return mixed
     */
    public function categories()
    {
        return $this->categories;
    }

    /**
     * @return mixed
     */
    public function average()
    {
        return $this->average;
    }

    /**
     * @return mixed
     */
    public function overOne()
    {
        return $this->overOne;
    }

    /**
     * @return mixed
     */
    public function grp()
    {
        return $this->grp;
    }

    /**
     * @return mixed
     */
    public function csvButtonInfo()
    {
        return $this->csvButtonInfo;
    }

    /**
     * @return mixed
     */
    public function header()
    {
        return $this->header;
    }
}
