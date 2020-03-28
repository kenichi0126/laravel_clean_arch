<?php

namespace App\DataProxy;

use Illuminate\Database\Eloquent\Model;

class BaseEloquent implements BaseEloquentInterface
{
    protected $model;

    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    /**
     * @param int $id
     * @param array $attributes
     * @return Model
     */
    public function updateById(int $id, array $attributes): Model
    {
        $model = $this->model->findOrFail($id);
        $model->update($attributes);

        return $model;
    }

    /**
     * @param int $id
     * @return Model
     */
    public function deleteById(int $id): Model
    {
        $model = $this->model->findOrFail($id);
        $model->delete();

        return $model;
    }
}
