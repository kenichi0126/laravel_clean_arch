<?php

namespace Switchm\SmartApi\Components\Setting\Save\UseCases;

class InputData
{
    private $secFlag;

    private $division;

    private $codes;

    private $regionId;

    private $id;

    /**
     * InputData constructor.
     * @param int $secFlag
     * @param string $division
     * @param array $codes
     * @param int $regionId
     * @param int $id
     */
    public function __construct(int $secFlag, string $division, array $codes, int $regionId, int $id)
    {
        $this->secFlag = $secFlag;
        $this->division = $division;
        $this->codes = $codes;
        $this->regionId = $regionId;
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function secFlag(): int
    {
        return $this->secFlag;
    }

    /**
     * @return string
     */
    public function division(): string
    {
        return $this->division;
    }

    /**
     * @return array
     */
    public function codes(): array
    {
        return $this->codes;
    }

    /**
     * @return int
     */
    public function regionId(): int
    {
        return $this->regionId;
    }

    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }
}
