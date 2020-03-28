<?php

namespace Switchm\SmartApi\Components\SearchConditions\Create\UseCases;

use Switchm\SmartApi\Queries\Dao\Rdb\SearchConditionDao;

/**
 * Class Interactor.
 */
final class Interactor implements InputBoundary
{
    const REGISTER_UPPER_LIMIT = 30;

    private $searchConditionDao;

    private $dataAccess;

    private $outputBoundary;

    /**
     * Interactor constructor.
     * @param SearchConditionDao $searchConditionDao
     * @param DataAccessInterface $dataAccess
     * @param OutputBoundary $outputBoundary
     */
    public function __construct(SearchConditionDao $searchConditionDao, DataAccessInterface $dataAccess, OutputBoundary $outputBoundary)
    {
        $this->searchConditionDao = $searchConditionDao;
        $this->dataAccess = $dataAccess;
        $this->outputBoundary = $outputBoundary;
    }

    /**
     * @param InputData $inputData
     */
    public function __invoke(InputData $inputData): void
    {
        if (self::REGISTER_UPPER_LIMIT <= $this->searchConditionDao->countByMemberId($inputData->regionId(), $inputData->memberId())) {
            ($this->outputBoundary)(new OutputData(false));
            return;
        }
        ($this->dataAccess)($inputData->memberId(), $inputData->regionId(), $inputData->name(), $inputData->routeName(), $inputData->condition());
        ($this->outputBoundary)(new OutputData(true));
    }
}
