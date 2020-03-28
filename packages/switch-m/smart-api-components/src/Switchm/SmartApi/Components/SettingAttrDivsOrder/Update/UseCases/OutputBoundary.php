<?php

namespace Switchm\SmartApi\Components\SettingAttrDivsOrder\Update\UseCases;

interface OutputBoundary
{
    /**
     * @param OutputData $output
     * @return mixed
     */
    public function __invoke(OutputData $output);
}
