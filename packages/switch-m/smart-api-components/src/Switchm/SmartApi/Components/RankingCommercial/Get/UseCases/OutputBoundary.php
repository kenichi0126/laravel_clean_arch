<?php

namespace Switchm\SmartApi\Components\RankingCommercial\Get\UseCases;

interface OutputBoundary
{
    /**
     * @param OutputData $output
     * @return mixed
     */
    public function __invoke(OutputData $output);
}
