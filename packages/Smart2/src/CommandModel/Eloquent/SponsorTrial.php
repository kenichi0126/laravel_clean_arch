<?php

namespace Smart2\CommandModel\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * Smart2\CommandModel\Eloquent\SponsorTrial.
 *
 * @property int $sponsor_id
 * @property array $settings
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\SponsorTrial newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\SponsorTrial newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\SponsorTrial query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\SponsorTrial whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\SponsorTrial whereSponsorId($value)
 * @mixin \Eloquent
 */
class SponsorTrial extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    /**
     * table name.
     *
     * @var string
     */
    protected $table = 'sponsor_trials';

    protected $primaryKey = 'sponsor_id';

    /**
     * fill.
     *
     * @var array
     */
    protected $fillable = [
    ];

    protected $casts = [
        'settings' => 'json',
    ];
}
