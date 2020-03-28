<?php

namespace Switchm\SmartApi\Components\ProgramMultiChannelProfile\Get\UseCases;

interface OutputBoundary
{
    /**
     * @param OutputData $output
     * @return mixed
     */
    public function __invoke(OutputData $output);
}
