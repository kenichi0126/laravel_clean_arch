<?php

namespace App\DataProxy;

use App\Eloquent\AttrDivs as AttrDivsEloquent;

/**
 * Class AttrDivs.
 */
class AttrDivs extends BaseEloquent implements AttrDivsInterface
{
    public function __construct(AttrDivsEloquent $model)
    {
        $this->model = $model;
    }

    public function updateByDivisionAndCode(string $division, string $code, array $attributes): int
    {
        return $this->model->where('division', $division)->where('code', $code)->update($attributes);
    }

    public function deleteByDivisionAndCode(string $division, string $code): void
    {
        $this->model->where('division', $division)->where('code', $code)->delete();
    }
}
