<?php

namespace Switchm\SmartApi\Components\Common\Exceptions;

class DateRangeException extends BusinessException
{
    public function __construct(int $maxNumber)
    {
        $this->message = "期間は${maxNumber}日以内で指定してください。";
    }
}
