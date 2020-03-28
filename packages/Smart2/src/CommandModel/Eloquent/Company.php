<?php

namespace Smart2\CommandModel\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * Smart2\CommandModel\Eloquent\Company.
 *
 * @property int $id
 * @property string $name
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Company newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Company newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Company query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Company whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Company whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Company whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Company whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Company extends Model
{
    public $incrementing = true;

    /**
     * table name.
     *
     * @var string
     */
    protected $table = 'companies';

    /**
     * fill.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'created_at',
        'updated_at',
    ];
}
