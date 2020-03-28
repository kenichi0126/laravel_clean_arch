<?php

namespace Smart2\CommandModel\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * Smart2\CommandModel\Eloquent\LoginFailed.
 *
 * @property string $login_id
 * @property string $info
 * @property null|\Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\LoginFailed newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\LoginFailed newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\LoginFailed query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\LoginFailed whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\LoginFailed whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\LoginFailed whereLoginId($value)
 * @mixin \Eloquent
 */
class LoginFailed extends Model
{
    const UPDATED_AT = null;

    public $incrementing = false;

    /**
     * table name.
     *
     * @var string
     */
    protected $table = 'login_failed';

    /**
     * fill.
     *
     * @var array
     */
    protected $fillable = [
        'login_id',
        'info',
    ];
}
