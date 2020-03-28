<?php

namespace Switchm\SmartApi\Components\SettingAggregate\Get\UseCases;

interface InputBoundary
{
    /**
     * @param InputData $inputData
     * @return mixed
     */
    public function __invoke(InputData $inputData);
}
