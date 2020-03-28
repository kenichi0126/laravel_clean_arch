<?php

namespace Switchm\SmartApi\Components\SearchConditions\Delete\UseCases;

/**
 * Class InputData.
 */
final class InputData
{
    private $id;

    /**
     * InputData constructor.
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }
}
