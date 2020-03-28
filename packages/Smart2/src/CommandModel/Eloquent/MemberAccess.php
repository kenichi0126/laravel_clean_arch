<?php

namespace Smart2\CommandModel\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * Smart2\CommandModel\Eloquent\MemberAccess.
 *
 * @property int $member_id
 * @property int $login_count
 * @property null|string $last_login_at
 * @property string $login_token
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MemberAccess newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MemberAccess newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MemberAccess query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MemberAccess whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MemberAccess whereLoginCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MemberAccess whereLoginToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MemberAccess whereMemberId($value)
 * @mixin \Eloquent
 */
class MemberAccess extends Model
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
    protected $table = 'member_accesses';

    protected $primaryKey = 'member_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'member_id',
        'login_count',
        'last_login_at',
        'login_token',
    ];
}
