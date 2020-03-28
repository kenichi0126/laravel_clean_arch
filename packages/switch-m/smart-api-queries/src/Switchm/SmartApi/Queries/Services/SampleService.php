<?php

namespace Switchm\SmartApi\Queries\Services;

use Switchm\SmartApi\Queries\Dao\Rdb\SampleDao;

class SampleService
{
    /**
     * @var SampleDao
     */
    private $sampleDao;

    /**
     * SampleService constructor.
     * @param SampleDao $sampleDao
     */
    public function __construct(SampleDao $sampleDao)
    {
        $this->sampleDao = $sampleDao;
    }

    /**
     * @param array $conditionCross
     * @param string $startDate
     * @param string $endDate
     * @param int $regionId
     * @param bool $isRt
     * @return int
     */
    public function getConditionCrossCount(array $conditionCross, string $startDate, string $endDate, int $regionId, bool $isRt = true): int
    {
        $result = $this->sampleDao->getCrossConditionCount($conditionCross, $startDate, $endDate, $regionId, $isRt);

        return $result->cnt;
    }
}
