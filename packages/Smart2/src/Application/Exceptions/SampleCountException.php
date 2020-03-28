<?php

namespace Smart2\Application\Exceptions;

// TODO: takata/移行が完了したら削除する
class SampleCountException extends BusinessException
{
    public function __construct(int $maxNumber)
    {
        $this->message = "指定条件では、該当サンプル数が${maxNumber}に達していません。条件を見直してください。";
    }
}
