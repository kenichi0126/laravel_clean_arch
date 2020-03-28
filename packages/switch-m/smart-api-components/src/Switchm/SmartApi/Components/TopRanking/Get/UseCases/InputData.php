<?php

namespace Switchm\SmartApi\Components\TopRanking\Get\UseCases;

class InputData
{
    private $regionId;

    private $conv15SecFlag;

    private $broadcasterCompanyIds;

    /**
     * TopInputData constructor.
     * @param int $regionId
     * @param string $conv15SecFlag
     * @param array $broadcasterCompanyIds
     */
    public function __construct(int $regionId, string $conv15SecFlag, array $broadcasterCompanyIds)
    {
        $this->regionId = $regionId;
        $this->conv15SecFlag = $conv15SecFlag;
        $this->broadcasterCompanyIds = $broadcasterCompanyIds;
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
    public function conv15SecFlag(): string
    {
        return $this->conv15SecFlag;
    }

    public function broadcasterCompanyIds()
    {
        return $this->broadcasterCompanyIds;
    }
}
