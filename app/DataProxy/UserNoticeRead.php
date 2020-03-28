<?php

namespace App\DataProxy;

use App\Eloquent\UserNoticesRead;

class UserNoticeRead extends BaseEloquent implements UserNoticeReadInterface
{
    public function __construct(UserNoticesRead $model)
    {
        $this->model = $model;
    }
}
