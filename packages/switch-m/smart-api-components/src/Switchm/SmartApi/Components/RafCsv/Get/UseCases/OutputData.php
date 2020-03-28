<?php

namespace Switchm\SmartApi\Components\RafCsv\Get\UseCases;

use stdClass;

class OutputData
{
    private $division;

    private $startDateShort;

    private $endDateShort;

    private $header;

    private $generator;

    private $data;

    /**
     * OutputData constructor.
     * @param string $division
     * @param string $startDateShort
     * @param string $endDateShort
     * @param array $header
     * @param array $generator
     * @param array $data
     */
    public function __construct(string $division, string $startDateShort, string $endDateShort, array $header, array $generator, stdClass $data)
    {
        $this->division = $division;
        $this->startDateShort = $startDateShort;
        $this->endDateShort = $endDateShort;
        $this->header = $header;
        $this->generator = $generator;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function division(): string
    {
        return $this->division;
    }

    /**
     * @return string
     */
    public function startDateShort(): string
    {
        return $this->startDateShort;
    }

    /**
     * @return string
     */
    public function endDateShort(): string
    {
        return $this->endDateShort;
    }

    /**
     * @return array
     */
    public function header(): array
    {
        return $this->header;
    }

    /**
     * @return mixed
     */
    public function generator()
    {
        return $this->generator;
    }

    /**
     * @return mixed
     */
    public function data()
    {
        return $this->data;
    }
}
