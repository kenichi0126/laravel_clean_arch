<?php

namespace Switchm\SmartApi\Components\SearchConditions\Delete\UseCases;

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
