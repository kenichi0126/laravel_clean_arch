<?php

namespace Switchm\SmartApi\Components\ProgramListAverage\Get\UseCases;

use Switchm\SmartApi\Queries\Dao\Dwh\ProgramDao;

class Interactor implements InputBoundary
{
    private $programDao;

    private $outputBoundary;

    /**
     * Interactor constructor.
     * @param ProgramDao $programDao
     * @param OutputBoundary $outputBoundary
     */
    public function __construct(ProgramDao $programDao, OutputBoundary $outputBoundary)
    {
        $this->programDao = $programDao;
        $this->outputBoundary = $outputBoundary;
    }

    /**
     * @param InputData $inputData
     */
    public function __invoke(InputData $inputData): void
    {
        $bsFlg = false;

        if (strpos($inputData->digitalAndBs(), 'bs') !== false) {
            $bsFlg = true;
        }

        $params = [
            $inputData->averageType(),
            $inputData->progIds(),
            $inputData->timeBoxIds(),
            $inputData->division(),
            $inputData->conditionCross(),
            $inputData->codes(),
            $bsFlg,
            $inputData->regionId(),
            $inputData->dataTypeFlags(),
            $inputData->dataType(),
            $inputData->dataTypeConst(),
        ];

        if (in_array($inputData->division(), $inputData->baseDivision())) {
            $list = $this->programDao->average(...$params);
        } else {
            $list = $this->programDao->averageOriginal(...array_merge($params, [$inputData->prefixes(), $inputData->selectedPersonalName(), $inputData->codeNumber()]));
        }

        $output = new OutputData($list);

        ($this->outputBoundary)($output);
    }
}
