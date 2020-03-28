<?php

namespace Smart2\CommandModel\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * Smart2\CommandModel\Eloquent\Programs.
 *
 * @property string $prog_id
 * @property int $time_box_id
 * @property null|string $date
 * @property null|string $started_at
 * @property null|string $ended_at
 * @property null|string $real_started_at
 * @property null|string $real_ended_at
 * @property null|int $channel_id
 * @property null|string $genre_id
 * @property null|string $title
 * @property null|string $ts_update
 * @property null|int $unknown
 * @property null|string $created_at
 * @property null|string $updated_at
 * @property null|int $prepared
 * @property null|int $finalized
 * @property null|string $calculated_at
 * @property null|int $personal_viewing_seconds
 * @property null|float $personal_viewing_rate
 * @property null|int $household_viewing_seconds
 * @property null|float $household_viewing_rate
 * @property null|float $household_viewing_share
 * @property null|float $household_end_viewing_rate
 * @property null|string $ts_calculated_at TS視聴率計算日時
 * @property null|int $ts_personal_viewing_seconds TS個人視聴秒数
 * @property null|float $ts_personal_viewing_rate TS個人視聴率％
 * @property null|int $ts_personal_total_viewing_seconds TS延べ個人視聴秒数
 * @property null|float $ts_personal_total_viewing_rate TS延べ個人視聴率％
 * @property null|int $ts_personal_gross_viewing_seconds TS総合個人視聴秒数
 * @property null|float $ts_personal_gross_viewing_rate TS総合個人視聴率％
 * @property null|int $ts_household_viewing_seconds TS世帯視聴秒数
 * @property null|float $ts_household_viewing_rate TS世帯視聴率％
 * @property null|int $ts_household_total_viewing_seconds TS延べ世帯視聴秒数
 * @property null|float $ts_household_total_viewing_rate TS延べ世帯視聴率％
 * @property null|int $ts_household_gross_viewing_seconds TS総合世帯視聴秒数
 * @property null|float $ts_household_gross_viewing_rate TS総合世帯視聴率％
 * @property null|int $ts_personal_rt_total_viewing_seconds
 * @property null|float $ts_personal_rt_total_viewing_rate
 * @property null|int $ts_samples_personal_viewing_seconds
 * @property null|float $ts_samples_personal_viewing_rate
 * @property null|int $ts_samples_household_viewing_seconds
 * @property null|float $ts_samples_household_viewing_rate
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereCalculatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereEndedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereFinalized($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereGenreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereHouseholdEndViewingRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereHouseholdViewingRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereHouseholdViewingSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereHouseholdViewingShare($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs wherePersonalViewingRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs wherePersonalViewingSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs wherePrepared($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereProgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereRealEndedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereRealStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereTimeBoxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereTsCalculatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereTsHouseholdGrossViewingRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereTsHouseholdGrossViewingSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereTsHouseholdTotalViewingRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereTsHouseholdTotalViewingSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereTsHouseholdViewingRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereTsHouseholdViewingSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereTsPersonalGrossViewingRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereTsPersonalGrossViewingSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereTsPersonalRtTotalViewingRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereTsPersonalRtTotalViewingSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereTsPersonalTotalViewingRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereTsPersonalTotalViewingSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereTsPersonalViewingRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereTsPersonalViewingSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereTsSamplesHouseholdViewingRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereTsSamplesHouseholdViewingSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereTsSamplesPersonalViewingRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereTsSamplesPersonalViewingSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereTsUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereUnknown($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Programs whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Programs extends Model
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
    protected $table = 'programs';

    /**
     * fill.
     *
     * @var array
     */
    protected $fillable = [
        'prog_id',
        'time_box_id',
        'real_started_at',
        'real_ended_at',
    ];
}
