<?php

namespace Switchm\SmartApi\Components\SettingAttrDivsOrder\Update\UseCases;

interface InputBoundary
{
    /**
     * @param InputData $inputData
     * @return mixed
     */
    public function __invoke(InputData $inputData);
}
