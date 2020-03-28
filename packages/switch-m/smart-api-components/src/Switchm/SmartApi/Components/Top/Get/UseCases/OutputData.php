<?php

namespace Switchm\SmartApi\Components\Top\Get\UseCases;

class OutputData
{
    private $date;

    private $programs;

    private $charts;

    private $categories;

    private $phNumbers;

    /**
     * OutputData constructor.
     * @param $date
     * @param $programs
     * @param $charts
     * @param $categories
     * @param $phNumbers
     */
    public function __construct($date, $programs, $charts, $categories, $phNumbers)
    {
        $this->date = $date;
        $this->programs = $programs;
        $this->charts = $charts;
        $this->categories = $categories;
        $this->phNumbers = $phNumbers;
    }

    public function date(): string
    {
        return $this->date;
    }

    public function programs(): array
    {
        return $this->programs;
    }

    public function charts(): array
    {
        return $this->charts;
    }

    public function categories(): array
    {
        return $this->categories;
    }

    public function phNumbers(): array
    {
        return $this->phNumbers;
    }
}
