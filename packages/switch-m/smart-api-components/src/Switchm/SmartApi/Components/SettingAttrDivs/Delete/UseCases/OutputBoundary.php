<?php

namespace Switchm\SmartApi\Components\SettingAttrDivs\Delete\UseCases;

interface OutputBoundary
{
    /**
     * @param OutputData $output
     * @return mixed
     */
    public function __invoke(OutputData $output);
}
