<?php

namespace App\DataAccess\SearchConditions\Update;

use App\DataProxy\SearchConditionsInterface as SearchConditions;
use Switchm\SmartApi\Components\SearchConditions\Update\UseCases\DataAccessInterface;

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
     * @param int $id
     * @param string $name
     * @param string $condition
     */
    public function __invoke(int $id, string $name, string $condition): void
    {
        $this->searchConditions->updateById($id, ['name' => $name, 'condition' => $condition]);
    }
}
