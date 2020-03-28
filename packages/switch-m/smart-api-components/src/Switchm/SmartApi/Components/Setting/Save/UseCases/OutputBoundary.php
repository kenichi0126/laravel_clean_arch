<?php

namespace Switchm\SmartApi\Components\Setting\Save\UseCases;

interface OutputBoundary
{
    /**
     * @param OutputData $output
     * @return mixed
     */
    public function __invoke(OutputData $output);
}
