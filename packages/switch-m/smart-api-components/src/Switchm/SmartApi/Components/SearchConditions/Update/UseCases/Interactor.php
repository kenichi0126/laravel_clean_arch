<?php

namespace Switchm\SmartApi\Components\SearchConditions\Update\UseCases;

/**
 * Class Interactor.
 */
final class Interactor implements InputBoundary
{
    private $dataAccess;

    private $outputBoundary;

    /**
     * Interactor constructor.
     * @param DataAccessInterface $dataAccess
     * @param OutputBoundary $outputBoundary
     */
    public function __construct(DataAccessInterface $dataAccess, OutputBoundary $outputBoundary)
    {
        $this->dataAccess = $dataAccess;
        $this->outputBoundary = $outputBoundary;
    }

    /**
     * @param InputData $inputData
     */
    public function __invoke(InputData $inputData): void
    {
        ($this->dataAccess)($inputData->id(), $inputData->name(), $inputData->condition());
        ($this->outputBoundary)(new OutputData());
    }
}
