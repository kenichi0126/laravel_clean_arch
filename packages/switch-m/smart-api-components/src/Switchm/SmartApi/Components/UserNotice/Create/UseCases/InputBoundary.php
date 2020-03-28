<?php

namespace Switchm\SmartApi\Components\UserNotice\Create\UseCases;

interface InputBoundary
{
    /**
     * @param InputData $inputData
     * @return mixed
     */
    public function __invoke(InputData $inputData): void;
}
