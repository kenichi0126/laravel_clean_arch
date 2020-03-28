<?php

namespace Switchm\SmartApi\Components\SearchConditions\Create\UseCases;

/**
 * Interface OutputBoundary.
 */
interface OutputBoundary
{
    /**
     * @param OutputData $outputData
     */
    public function __invoke(OutputData $outputData): void;
}
