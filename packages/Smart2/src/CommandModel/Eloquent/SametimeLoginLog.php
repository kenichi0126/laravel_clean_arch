<?php

namespace Smart2\CommandModel\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * Smart2\CommandModel\Eloquent\SametimeLoginLog.
 *
 * @property int $id
 * @property int $member_id
 * @property null|string $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\SametimeLoginLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\SametimeLoginLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\SametimeLoginLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\SametimeLoginLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\SametimeLoginLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\SametimeLoginLog whereMemberId($value)
 * @mixin \Eloquent
 */
class SametimeLoginLog extends Model
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
    protected $table = 'sametime_login_log';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'member_id',
        'created_at',
    ];
}
