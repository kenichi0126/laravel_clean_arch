<?php

namespace Switchm\SmartApi\Components\SettingAggregate\Get\UseCases;

/**
 * Class Interactor.
 */
class Interactor implements InputBoundary
{
    private $outputBoundary;

    /**
     * Interactor constructor.
     * @param OutputBoundary $outputBoundary
     */
    public function __construct(OutputBoundary $outputBoundary)
    {
        $this->outputBoundary = $outputBoundary;
    }

    /**
     * @param InputData $inputData
     */
    public function __invoke(InputData $inputData): void
    {
        $result = [];

        if (isset($inputData->userInfo()->conv_15_sec_flag)) {
            $result = [
                'conv15SecFlag' => $inputData->userInfo()->conv_15_sec_flag,
            ];
        }

        $output = new OutputData($result);
        ($this->outputBoundary)($output);
    }
}
