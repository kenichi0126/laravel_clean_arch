<?php

namespace Smart2\Application\Exceptions;

// TODO: takata/移行が完了したら削除する
class AttrDivUpdateFailureException extends BusinessException
{
    public function __construct()
    {
        $this->message = '条件を更新できませんでした。他のユーザーによって削除された可能性があります。';
    }
}
