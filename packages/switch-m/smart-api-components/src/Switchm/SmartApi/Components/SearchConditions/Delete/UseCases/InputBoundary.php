<?php

namespace Switchm\SmartApi\Components\SearchConditions\Delete\UseCases;

/**
 * Interface InputBoundary.
 */
interface InputBoundary
{
    /**
     * @param InputData $inputData
     */
    public function __invoke(InputData $inputData): void;
}
