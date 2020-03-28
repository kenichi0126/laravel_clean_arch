<?php

namespace Smart2\CommandModel\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * Smart2\CommandModel\Eloquent\RecurringTask.
 *
 * @property int $region_id
 * @property string $name
 * @property string $status
 * @property null|string $started_at
 * @property null|string $ended_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\RecurringTask newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\RecurringTask newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\RecurringTask query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\RecurringTask whereEndedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\RecurringTask whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\RecurringTask whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\RecurringTask whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\RecurringTask whereStatus($value)
 * @mixin \Eloquent
 */
class RecurringTask extends Model
{
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
    protected $table = 'recurring_tasks';

    /**
     * fill.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'status',
    ];
}
