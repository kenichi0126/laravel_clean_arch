<?php

namespace App\DataProxy;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface SearchConditionsInterface.
 */
interface SearchConditionsInterface
{
    /**
     * @param array $attributes
     * @return Model
     */
    public function create(array $attributes): Model;

    /**
     * @param int $id
     * @param array $attributes
     * @return Model
     */
    public function updateById(int $id, array $attributes): Model;

    /**
     * @param int $id
     * @return Model
     */
    public function deleteById(int $id): Model;
}
