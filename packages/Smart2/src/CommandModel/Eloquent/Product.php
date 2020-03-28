<?php

namespace Smart2\CommandModel\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * Smart2\CommandModel\Eloquent\Product.
 *
 * @property int $id
 * @property int $company_id
 * @property null|string $name
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Product whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Product whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Product extends Model
{
    public $incrementing = true;

    /**
     * to Disable updated_at.
     * @var string
     */
    public $timestamps = true;

    /**
     * table name.
     *
     * @var string
     */
    protected $table = 'products';

    /**
     * fill.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'company_id',
        'name',
        'created_at',
        'updated_at',
    ];
}
