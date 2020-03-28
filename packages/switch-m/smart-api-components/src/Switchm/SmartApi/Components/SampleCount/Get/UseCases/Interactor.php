<?php

namespace Switchm\SmartApi\Components\SampleCount\Get\UseCases;

use Switchm\SmartApi\Queries\Dao\Rdb\EnqDao;

class Interactor implements InputBoundary
{
    private $enqDao;

    private $outputBoundary;

    /**
     * Interactor constructor.
     * @param EnqDao $enqDao
     * @param OutputBoundary $outputBoundary
     */
    public function __construct(EnqDao $enqDao, OutputBoundary $outputBoundary)
    {
        $this->enqDao = $enqDao;
        $this->outputBoundary = $outputBoundary;
    }

    /**
     * @param InputData $inputData
     */
    public function __invoke(InputData $inputData): void
    {
        $params = [
            $inputData->info(),
            $inputData->conditionCross(),
            $inputData->regionId(),
        ];

        $cnt = $this->enqDao->getSampleCount(...$params);

        $editFlg = false;

        if ($inputData->editFlg() != null) {
            $editFlg = $inputData->editFlg();
        }

        $output = $this->produceOutputData($cnt, $editFlg);

        ($this->outputBoundary)($output);
    }

    /**
     * @param $cnt
     * @param $editFlg
     * @return OutputData
     */
    protected function produceOutputData($cnt, $editFlg): OutputData
    {
        return new OutputData($cnt, $editFlg);
    }
}
