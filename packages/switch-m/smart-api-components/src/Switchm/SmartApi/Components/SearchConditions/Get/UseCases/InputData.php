<?php

namespace Switchm\SmartApi\Components\SearchConditions\Get\UseCases;

/**
 * Class InputData.
 */
final class InputData
{
    private $regionId;

    private $memberId;

    private $orderColumn;

    private $orderDirection;

    private $permissionRankingCommercial;

    private $permissionTimeShifting;

    private $permissionBsInfo;

    private $permissionCmMaterials;

    private $permissionTimeSpot;

    private $permissionMultipleCondition;

    /**
     * InputData constructor.
     * @param int $regionId
     * @param int $memberId
     * @param string $orderColumn
     * @param string $orderDirection
     * @param bool $permissionRankingCommercial
     * @param bool $permissionTimeShifting
     * @param bool $permissionBsInfo
     * @param bool $permissionCmMaterials
     * @param bool $permissionTimeSpot
     * @param bool $permissionMultipleCondition
     */
    public function __construct(
        int $regionId,
        int $memberId,
        string $orderColumn,
        string $orderDirection,
        bool $permissionRankingCommercial,
        bool $permissionTimeShifting,
        bool $permissionBsInfo,
        bool $permissionCmMaterials,
        bool $permissionTimeSpot,
        bool $permissionMultipleCondition
    ) {
        $this->regionId = $regionId;
        $this->memberId = $memberId;
        $this->orderColumn = $orderColumn;
        $this->orderDirection = $orderDirection;
        $this->permissionRankingCommercial = $permissionRankingCommercial;
        $this->permissionTimeShifting = $permissionTimeShifting;
        $this->permissionBsInfo = $permissionBsInfo;
        $this->permissionCmMaterials = $permissionCmMaterials;
        $this->permissionTimeSpot = $permissionTimeSpot;
        $this->permissionMultipleCondition = $permissionMultipleCondition;
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
    public function memberId(): int
    {
        return $this->memberId;
    }

    /**
     * @return string
     */
    public function orderColumn(): string
    {
        return $this->orderColumn;
    }

    /**
     * @return string
     */
    public function orderDirection(): string
    {
        return $this->orderDirection;
    }

    /**
     * @return bool
     */
    public function permissionRankingCommercial(): bool
    {
        return $this->permissionRankingCommercial;
    }

    /**
     * @return bool
     */
    public function permissionTimeShifting(): bool
    {
        return $this->permissionTimeShifting;
    }

    /**
     * @return bool
     */
    public function permissionBsInfo(): bool
    {
        return $this->permissionBsInfo;
    }

    /**
     * @return bool
     */
    public function permissionCmMaterials(): bool
    {
        return $this->permissionCmMaterials;
    }

    /**
     * @return bool
     */
    public function permissionTimeSpot(): bool
    {
        return $this->permissionTimeSpot;
    }

    /**
     * @return bool
     */
    public function permissionMultipleCondition(): bool
    {
        return $this->permissionMultipleCondition;
    }
}
