<?php

namespace Switchm\SmartApi\Components\SearchConditions\Update\UseCases;

/**
 * Class InputData.
 */
final class InputData
{
    private $id;

    private $name;

    private $condition;

    /**
     * InputData constructor.
     * @param int $id
     * @param string $name
     * @param string $condition
     */
    public function __construct(int $id, string $name, string $condition)
    {
        $this->id = $id;
        $this->name = $name;
        $this->condition = $condition;
    }

    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function condition(): string
    {
        return $this->condition;
    }
}
