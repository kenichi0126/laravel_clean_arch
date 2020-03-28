<?php

namespace Switchm\SmartApi\Components\CommercialAdvertising\Get\UseCases;

interface OutputBoundary
{
    /**
     * @param OutputData $output
     */
    public function __invoke(OutputData $output): void;
}
