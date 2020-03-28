<?php

namespace Smart2\CommandModel\Eloquent;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Smart2\CommandModel\Eloquent\MemberSystemSetting.
 *
 * @property int $member_id
 * @property int $conv_15_sec_flag
 * @property string $aggregate_setting
 * @property null|string $aggregate_setting_code
 * @property null|int $aggregate_setting_region_id
 * @property-read \Illuminate\Notifications\DatabaseNotification[]|\Illuminate\Notifications\DatabaseNotificationCollection $notifications
 * @property-read null|int $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MemberSystemSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MemberSystemSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MemberSystemSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MemberSystemSetting whereAggregateSetting($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MemberSystemSetting whereAggregateSettingCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MemberSystemSetting whereAggregateSettingRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MemberSystemSetting whereConv15SecFlag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MemberSystemSetting whereMemberId($value)
 * @mixin \Eloquent
 */
class MemberSystemSetting extends Authenticatable
{
    use Notifiable;

    public $incrementing = false;

    /**
     * to Disable updated_at.
     * @var string
     */
    public $timestamps = false;

    public $primaryKey = 'member_id';

    /**
     * table name.
     *
     * @var string
     */
    protected $table = 'member_system_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'member_id', 'conv_15_sec_flag', 'aggregate_setting',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];
}
