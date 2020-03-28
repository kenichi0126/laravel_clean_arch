<?php

namespace Switchm\SmartApi\Components\SearchConditions\Create\UseCases;

/**
 * Class InputData.
 */
final class InputData
{
    private $memberId;

    private $regionId;

    private $name;

    private $routeName;

    private $condition;

    /**
     * InputData constructor.
     * @param int $memberId
     * @param int $regionId
     * @param string $name
     * @param string $routeName
     * @param string $condition
     */
    public function __construct(int $memberId, int $regionId, string $name, string $routeName, string $condition)
    {
        $this->memberId = $memberId;
        $this->regionId = $regionId;
        $this->name = $name;
        $this->routeName = $routeName;
        $this->condition = $condition;
    }

    /**
     * @return int
     */
    public function memberId(): int
    {
        return $this->memberId;
    }

    /**
     * @return int
     */
    public function regionId(): int
    {
        return $this->regionId;
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
    public function routeName(): string
    {
        return $this->routeName;
    }

    /**
     * @return string
     */
    public function condition(): string
    {
        return $this->condition;
    }
}
