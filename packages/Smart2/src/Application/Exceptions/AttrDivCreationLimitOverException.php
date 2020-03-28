<?php

namespace Smart2\Application\Exceptions;

// TODO: takata/移行が完了したら削除する
class AttrDivCreationLimitOverException extends BusinessException
{
    public function __construct()
    {
        $this->message = '条件を保存できる数の限界を超えています。他のユーザーによって追加された可能性があります。';
    }
}
