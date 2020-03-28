<?php

namespace Switchm\SmartApi\Components\SearchConditions\Create\UseCases;

/**
 * Class OutputData.
 */
final class OutputData
{
    private $result;

    /**
     * OutputData constructor.
     * @param bool $result
     */
    public function __construct(bool $result)
    {
        $this->result = $result;
    }

    /**
     * @return bool
     */
    public function result(): bool
    {
        return $this->result;
    }
}
