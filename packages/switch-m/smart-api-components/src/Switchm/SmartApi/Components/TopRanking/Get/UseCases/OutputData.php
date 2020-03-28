<?php

namespace Switchm\SmartApi\Components\TopRanking\Get\UseCases;

class OutputData
{
    private $program;

    private $company_cm;

    private $product_cm;

    private $programDate;

    private $cmDate;

    private $programPhNumbers;

    private $cmPhNumbers;

    /**
     * OutputData constructor.
     * @param $program
     * @param $company_cm
     * @param $product_cm
     * @param $programDate
     * @param $cmDate
     * @param $programPhNumbers
     * @param $cmPhNumbers
     */
    public function __construct(
        $program,
        $company_cm,
        $product_cm,
        $programDate,
        $cmDate,
        $programPhNumbers,
        $cmPhNumbers
    ) {
        $this->program = $program;
        $this->company_cm = $company_cm;
        $this->product_cm = $product_cm;
        $this->programDate = $programDate;
        $this->cmDate = $cmDate;
        $this->programPhNumbers = $programPhNumbers;
        $this->cmPhNumbers = $cmPhNumbers;
    }

    public function program(): array
    {
        return $this->program;
    }

    public function company_cm(): array
    {
        return $this->company_cm;
    }

    public function product_cm(): array
    {
        return $this->product_cm;
    }

    public function programDate(): string
    {
        return $this->programDate;
    }

    public function cmDate(): string
    {
        return $this->cmDate;
    }

    public function programPhNumbers(): array
    {
        return $this->programPhNumbers;
    }

    public function cmPhNumbers(): array
    {
        return $this->cmPhNumbers;
    }
}
