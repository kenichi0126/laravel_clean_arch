<?php

namespace Switchm\SmartApi\Queries\Services;

use Switchm\SmartApi\Queries\Dao\Rdb\DivisionDao;

class DivisionService
{
    /**
     * @var DivisionDao
     */
    private $divisionDao;

    /**
     * DivisionService constructor.
     * @param DivisionDao $divisionDao
     */
    public function __construct(DivisionDao $divisionDao)
    {
        $this->divisionDao = $divisionDao;
    }

    /**
     * @param string $division
     * @param int $regionId
     * @param int $memberId
     * @param array $baseDivision
     * @return array
     */
    public function getCodeList(string $division, int $regionId, int $memberId, array $baseDivision): array
    {
        if ($division === 'condition_cross') {
            return [];
        }

        if (in_array($division, $baseDivision, true)) {
            return $this->divisionDao->find([
                $division,
            ]);
        }

        return $this->divisionDao->findOriginalDiv(
            [
                $division,
            ],
            $memberId,
            $regionId
        );
    }
}
