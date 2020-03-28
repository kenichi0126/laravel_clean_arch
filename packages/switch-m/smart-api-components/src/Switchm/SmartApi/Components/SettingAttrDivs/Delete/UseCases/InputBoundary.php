<?php

namespace Switchm\SmartApi\Components\SettingAttrDivs\Delete\UseCases;

interface InputBoundary
{
    /**
     * @param InputData $inputData
     * @return mixed
     */
    public function __invoke(InputData $inputData);
}
