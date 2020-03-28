<?php

namespace Switchm\SmartApi\Components\SettingAttrDivsOrder\Update\UseCases;

/**
 * Class Interactor.
 */
class Interactor implements InputBoundary
{
    private $outputBoundary;

    private $dataAccess;

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
        ($this->dataAccess)($inputData->divisions());

        $output = new OutputData();
        ($this->outputBoundary)($output);
    }
}
