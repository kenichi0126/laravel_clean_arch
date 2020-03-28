<?php

namespace Smart2\CommandModel\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * Smart2\CommandModel\Eloquent\SystemInformation.
 *
 * @property string $name
 * @property int $is_maintenance
 * @property string $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\SystemInformation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\SystemInformation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\SystemInformation query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\SystemInformation whereIsMaintenance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\SystemInformation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\SystemInformation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SystemInformation extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    /**
     * table name.
     *
     * @var string
     */
    protected $table = 'system_informations';

    /**
     * fill.
     *
     * @var array
     */
    protected $fillable = [
    ];
}
