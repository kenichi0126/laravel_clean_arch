<?php

namespace Smart2\CommandModel\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * Smart2\CommandModel\Eloquent\ApiLog.
 *
 * @property string $api
 * @property mixed $parameter
 * @property int $member_id
 * @property float $exec_time
 * @property string $date
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\ApiLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\ApiLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\ApiLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\ApiLog whereApi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\ApiLog whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\ApiLog whereExecTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\ApiLog whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\ApiLog whereParameter($value)
 * @mixin \Eloquent
 */
class ApiLog extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    /**
     * table name.
     *
     * @var string
     */
    protected $table = 'api_log';

    /**
     * fill.
     *
     * @var array
     */
    protected $fillable = [
        'api',
        'parameter',
        'member_id',
        'exec_time',
        'date',
    ];
}
