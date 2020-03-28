<?php

namespace Switchm\SmartApi\Components\CommercialAdvertising\Get\UseCases;

interface InputBoundary
{
    /**
     * @param InputData $inputData
     */
    public function __invoke(InputData $inputData): void;
}
