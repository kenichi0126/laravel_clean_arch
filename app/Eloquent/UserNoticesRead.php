<?php

namespace App\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * Smart2\CommandModel\Eloquent\UserNoticesRead.
 *
 * @property null|int $notice_id
 * @property null|int $member_id
 * @property null|\Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\UserNoticesRead newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\UserNoticesRead newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\UserNoticesRead query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\UserNoticesRead whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\UserNoticesRead whereNoticeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\UserNoticesRead whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class UserNoticesRead extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * table name.
     *
     * @var string
     */
    protected $table = 'user_notices_read';

    /**
     * fill.
     *
     * @var array
     */
    protected $fillable = ['notice_id', 'member_id', 'updated_at'];

    /**
     * @var null
     */
    protected $primaryKey;
}
