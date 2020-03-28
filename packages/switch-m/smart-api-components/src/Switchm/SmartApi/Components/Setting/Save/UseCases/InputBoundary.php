<?php

namespace Switchm\SmartApi\Components\Setting\Save\UseCases;

interface InputBoundary
{
    /**
     * @param InputData $inputData
     * @return mixed
     */
    public function __invoke(InputData $inputData);
}
