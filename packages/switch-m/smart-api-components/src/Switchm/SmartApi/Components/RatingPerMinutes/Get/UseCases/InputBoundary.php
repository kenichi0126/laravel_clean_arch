<?php

namespace Switchm\SmartApi\Components\RatingPerMinutes\Get\UseCases;

interface InputBoundary
{
    /**
     * @param InputData $inputData
     * @return mixed
     */
    public function __invoke(InputData $inputData);
}
