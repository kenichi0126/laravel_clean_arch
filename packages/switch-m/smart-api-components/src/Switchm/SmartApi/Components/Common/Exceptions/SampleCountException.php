<?php

namespace Switchm\SmartApi\Components\Common\Exceptions;

class SampleCountException extends BusinessException
{
    public function __construct(int $maxNumber)
    {
        $this->message = "指定条件では、該当サンプル数が${maxNumber}に達していません。条件を見直してください。";
    }
}
