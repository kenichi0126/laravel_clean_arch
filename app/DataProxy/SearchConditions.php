<?php

namespace App\DataProxy;

use App\Eloquent\SearchConditions as Model;

/**
 * Class SearchConditions.
 */
class SearchConditions extends BaseEloquent implements SearchConditionsInterface
{
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
}
