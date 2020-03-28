<?php

namespace Smart2\Application\Exceptions;

// TODO: takata/移行が完了したら削除する
class DateRangeException extends BusinessException
{
    public function __construct(int $maxNumber)
    {
        $this->message = "期間は${maxNumber}日以内で指定してください。";
    }
}
