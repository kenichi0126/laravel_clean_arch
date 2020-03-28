<?php

namespace Switchm\SmartApi\Components\SettingAttrDivs\Get\UseCases;

interface InputBoundary
{
    /**
     * @param InputData $inputData
     * @return mixed
     */
    public function __invoke(InputData $inputData);
}
