<?php

namespace Switchm\SmartApi\Components\ProgramMultiChannelProfile\Get\UseCases;

class OutputData
{
    private $data;

    private $startDateShort;

    private $endDateShort;

    private $header;

    /**
     * OutputData constructor.
     * @param array $data
     * @param string $startDateShort
     * @param string $endDateShort
     * @param $header
     */
    public function __construct(array $data, string $startDateShort, string $endDateShort, array $header)
    {
        $this->data = $data;
        $this->startDateShort = $startDateShort;
        $this->endDateShort = $endDateShort;
        $this->header = $header;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function startDateShort(): string
    {
        return $this->startDateShort;
    }

    public function endDateShort(): string
    {
        return $this->endDateShort;
    }

    public function header(): array
    {
        return $this->header;
    }
}
