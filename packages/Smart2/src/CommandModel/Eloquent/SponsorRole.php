<?php

namespace Smart2\CommandModel\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * Smart2\CommandModel\Eloquent\SponsorRole.
 *
 * @property int $sponsor_id
 * @property array $permissions
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\SponsorRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\SponsorRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\SponsorRole query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\SponsorRole wherePermissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\SponsorRole whereSponsorId($value)
 * @mixin \Eloquent
 */
class SponsorRole extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    /**
     * table name.
     *
     * @var string
     */
    protected $table = 'sponsor_roles';

    protected $primaryKey = 'sponsor_id';

    /**
     * fill.
     *
     * @var array
     */
    protected $fillable = [
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'json',
    ];
}
