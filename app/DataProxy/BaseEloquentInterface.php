<?php

namespace App\DataProxy;

use Illuminate\Database\Eloquent\Model;

interface BaseEloquentInterface
{
    public function create(array $attributes): Model;

    public function updateById(int $id, array $attributes): Model;

    public function deleteById(int $id): Model;
}
