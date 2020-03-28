<?php

namespace Switchm\SmartApi\Components\SettingAttrDivs\Create\UseCases;

interface InputBoundary
{
    /**
     * @param InputData $inputData
     * @return mixed
     */
    public function __invoke(InputData $inputData);
}
