<?php

namespace Switchm\SmartApi\Components\ProgramPeriodAverage\Get\UseCases;

interface InputBoundary
{
    /**
     * @param InputData $inputData
     * @return mixed
     */
    public function __invoke(InputData $inputData);
}
