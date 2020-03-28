<?php

namespace Smart2\CommandModel\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * Smart2\CommandModel\Eloquent\SystemNoticesRead.
 *
 * @property int $notice_id
 * @property int $member_id
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\SystemNoticesRead newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\SystemNoticesRead newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\SystemNoticesRead query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\SystemNoticesRead whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\SystemNoticesRead whereNoticeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\SystemNoticesRead whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SystemNoticesRead extends Model
{
    /**
     * table name.
     *
     * @var string
     */
    protected $table = 'system_notices_read';

    /**
     * fill.
     *
     * @var array
     */
    protected $fillable = ['notice_id', 'member_id', 'updated_at'];
}
