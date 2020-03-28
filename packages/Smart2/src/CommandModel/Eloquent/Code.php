<?php

namespace Smart2\CommandModel\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * Smart2\CommandModel\Eloquent\Code.
 *
 * @property string $division
 * @property string $code
 * @property string $name
 * @property int $display_order
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Code newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Code newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Code query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Code whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Code whereDisplayOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Code whereDivision($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Code whereName($value)
 * @mixin \Eloquent
 */
class Code extends Model
{
    public $incrementing = false;

    /**
     * to Disable updated_at.
     * @var string
     */
    public $timestamps = false;

    /**
     * table name.
     *
     * @var string
     */
    protected $table = 'codes';

    /**
     * fill.
     *
     * @var array
     */
    protected $fillable = [
        'division',
        'code',
        'name',
        'display_order',
    ];
}
