<?php

namespace Smart2\CommandModel\Eloquent;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Smart2\CommandModel\Eloquent\MemberOriginalDiv.
 *
 * @property int $member_id
 * @property string $menu
 * @property string $division
 * @property string $target_date_from
 * @property string $target_date_to
 * @property int $display_order
 * @property int $original_div_edit_flag
 * @property int $region_id
 * @property-read \Illuminate\Notifications\DatabaseNotification[]|\Illuminate\Notifications\DatabaseNotificationCollection $notifications
 * @property-read null|int $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MemberOriginalDiv newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MemberOriginalDiv newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MemberOriginalDiv query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MemberOriginalDiv whereDisplayOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MemberOriginalDiv whereDivision($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MemberOriginalDiv whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MemberOriginalDiv whereMenu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MemberOriginalDiv whereOriginalDivEditFlag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MemberOriginalDiv whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MemberOriginalDiv whereTargetDateFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MemberOriginalDiv whereTargetDateTo($value)
 * @mixin \Eloquent
 */
class MemberOriginalDiv extends Authenticatable
{
    use Notifiable;

    public $incrementing = false;

    /**
     * to Disable updated_at.
     *
     * @var string
     */
    public $timestamps = false;

    public $primaryKey = 'member_id';

    /**
     * table name.
     *
     * @var string
     */
    protected $table = 'member_original_divs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'member_id',
        'menu',
        'division',
        'target_date_from',
        'target_date_to',
        'display_order',
        'original_div_edit_flag',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];
}
