<?php

namespace Switchm\SmartApi\Components\RatingPerMinutes\Get\UseCases;

interface OutputBoundary
{
    /**
     * @param OutputData $output
     */
    public function __invoke(OutputData $output): void;
}
