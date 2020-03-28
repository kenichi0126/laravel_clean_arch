<?php

namespace App\DataAccess\SearchConditions\Create;

use App\DataProxy\SearchConditionsInterface as SearchConditions;
use Switchm\SmartApi\Components\SearchConditions\Create\UseCases\DataAccessInterface;

/**
 * Class DataAccess.
 */
final class DataAccess implements DataAccessInterface
{
    private $searchConditions;

    /**
     * DataAccess constructor.
     * @param SearchConditions $searchConditions
     */
    public function __construct(SearchConditions $searchConditions)
    {
        $this->searchConditions = $searchConditions;
    }

    /**
     * @param int $memberId
     * @param int $regionId
     * @param string $name
     * @param string $routeName
     * @param string $condition
     */
    public function __invoke(int $memberId, int $regionId, string $name, string $routeName, string $condition): void
    {
        $this->searchConditions->create(['member_id' => $memberId, 'region_id' => $regionId, 'name' => $name, 'route_name' => $routeName, 'condition' => $condition]);
    }
}
