<?php

namespace Smart2\CommandModel\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * Smart2\CommandModel\Eloquent\MemberLoginLog.
 *
 * @property int $member_id
 * @property string $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MemberLoginLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MemberLoginLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MemberLoginLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MemberLoginLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MemberLoginLog whereMemberId($value)
 * @mixin \Eloquent
 */
class MemberLoginLog extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'member_login_logs';

    protected $primaryKey = 'member_id';

    protected $fillable = [
        'member_id',
        'created_at',
    ];
}
