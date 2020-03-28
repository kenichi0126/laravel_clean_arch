<?php

namespace App\DataAccess\SearchConditions\Delete;

use App\DataProxy\SearchConditions;
use Switchm\SmartApi\Components\SearchConditions\Delete\UseCases\DataAccessInterface;

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
     */
    public function __invoke(int $id): void
    {
        $this->searchConditions->deleteById($id);
    }
}
