<?php

namespace Switchm\SmartApi\Components\Setting\Save\UseCases;

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
        ($this->dataAccess)(
            $inputData->id(),
            [
                'conv_15_sec_flag' => $inputData->secFlag(),
                'aggregate_setting' => $inputData->division(),
                'aggregate_setting_code' => json_encode($inputData->codes()),
                'aggregate_setting_region_id' => $inputData->regionId(),
            ]
        );

        $output = new OutputData();
        ($this->outputBoundary)($output);
    }
}
