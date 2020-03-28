<?php

namespace Smart2\CommandModel\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * Smart2\CommandModel\Eloquent\ResetPasswordHistory.
 *
 * @property int $member_id
 * @property string $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\ResetPasswordHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\ResetPasswordHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\ResetPasswordHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\ResetPasswordHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\ResetPasswordHistory whereMemberId($value)
 * @mixin \Eloquent
 */
class ResetPasswordHistory extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'reset_password_histories';
}
