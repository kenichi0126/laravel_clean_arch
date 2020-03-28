<?php

namespace Switchm\SmartApi\Components\SearchConditions\Update\UseCases;

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
