<?php

namespace Switchm\SmartApi\Components\SettingAttrDivs\Update\UseCases;

interface InputBoundary
{
    /**
     * @param InputData $inputData
     * @return mixed
     */
    public function __invoke(InputData $inputData);
}
