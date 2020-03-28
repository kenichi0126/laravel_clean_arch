<?php

namespace Switchm\SmartApi\Components\ProgramTableDetail\Get\UseCases;

class OutputData
{
    private $data;

    private $headlines;

    public function __construct(?array $data, ?array $headlines)
    {
        $this->data = $data;
        $this->headlines = $headlines;
    }

    public function data(): ?array
    {
        return $this->data;
    }

    public function headlines(): ?array
    {
        return $this->headlines;
    }
}
