<?php

namespace Smart2\CommandModel\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * Smart2\CommandModel\Eloquent\TimeBox.
 *
 * @property int $id
 * @property int $region_id
 * @property string $start_date
 * @property int $duration
 * @property int $version
 * @property string $started_at
 * @property string $ended_at
 * @property int $panelers_number
 * @property int $households_number
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\TimeBox newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\TimeBox newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\TimeBox query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\TimeBox whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\TimeBox whereEndedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\TimeBox whereHouseholdsNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\TimeBox whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\TimeBox wherePanelersNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\TimeBox whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\TimeBox whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\TimeBox whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\TimeBox whereVersion($value)
 * @mixin \Eloquent
 */
class TimeBox extends Model
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
    protected $table = 'time_boxes';

    /**
     * fill.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'region_id',
        'start_date',
        'duration',
        'version',
        'started_at',
        'ended_at',
        'panelers_number',
        'households_number',
    ];
}
