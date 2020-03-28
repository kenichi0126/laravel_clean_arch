<?php

namespace Switchm\SmartApi\Components\CmMaterials\Get\UseCases;

interface OutputBoundary
{
    /**
     * @param OutputData $output
     * @return mixed
     */
    public function __invoke(OutputData $output);
}
