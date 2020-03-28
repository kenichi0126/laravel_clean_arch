<?php

namespace Switchm\SmartApi\Components\SearchConditions\Get\UseCases;

/**
 * Class OutputData.
 */
final class OutputData
{
    private $data;

    /**
     * OutputData constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function data(): array
    {
        return $this->data;
    }
}
