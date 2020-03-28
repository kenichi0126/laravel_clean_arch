<?php

namespace Smart2\CommandModel\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * Smart2\CommandModel\Eloquent\Commercial.
 *
 * @property string $cm_id
 * @property string $prog_id
 * @property string $started_at
 * @property string $ended_at
 * @property string $date
 * @property int $region_id
 * @property int $time_box_id
 * @property int $channel_id
 * @property int $company_id
 * @property int $product_id
 * @property null|string $scene_id
 * @property int $duration
 * @property string $program_title
 * @property null|string $genre_id
 * @property null|string $setting
 * @property null|string $talent
 * @property null|string $remarks
 * @property null|string $bgm
 * @property null|string $memo
 * @property null|string $first_date
 * @property string $ts_update
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 * @property null|string $calculated_at
 * @property null|int $personal_viewing_number
 * @property null|float $personal_viewing_rate
 * @property null|int $household_viewing_number
 * @property null|float $household_viewing_rate
 * @property int $cm_type
 * @property null|string $cm_type_updated_at
 * @property null|string $ts_calculated_at TS視聴率計算日時
 * @property null|int $ts_personal_viewing_number TS個人視聴者数
 * @property null|float $ts_personal_viewing_rate TS個人視聴率％
 * @property null|int $ts_personal_total_viewing_number TS延べ個人視聴者数
 * @property null|float $ts_personal_total_viewing_rate TS延べ個人視聴率％
 * @property null|int $ts_personal_gross_viewing_number TS総合個人視聴者数
 * @property null|float $ts_personal_gross_viewing_rate TS総合個人視聴率％
 * @property null|int $ts_household_viewing_number TS世帯視聴者数
 * @property null|float $ts_household_viewing_rate TS世帯視聴率％
 * @property null|int $ts_household_total_viewing_number TS延べ世帯視聴者数
 * @property null|float $ts_household_total_viewing_rate TS延べ世帯視聴率％
 * @property null|int $ts_household_gross_viewing_number TS総合世帯視聴者数
 * @property null|float $ts_household_gross_viewing_rate TS総合世帯視聴率％
 * @property null|int $ts_personal_rt_total_viewing_number
 * @property null|float $ts_personal_rt_total_viewing_rate
 * @property null|int $ts_samples_personal_viewing_number
 * @property null|float $ts_samples_personal_viewing_rate
 * @property null|int $ts_samples_household_viewing_number
 * @property null|float $ts_samples_household_viewing_rate
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereBgm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereCalculatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereCmId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereCmType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereCmTypeUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereEndedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereFirstDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereGenreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereHouseholdViewingNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereHouseholdViewingRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereMemo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial wherePersonalViewingNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial wherePersonalViewingRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereProgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereProgramTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereSceneId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereSetting($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereTalent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereTimeBoxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereTsCalculatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereTsHouseholdGrossViewingNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereTsHouseholdGrossViewingRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereTsHouseholdTotalViewingNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereTsHouseholdTotalViewingRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereTsHouseholdViewingNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereTsHouseholdViewingRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereTsPersonalGrossViewingNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereTsPersonalGrossViewingRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereTsPersonalRtTotalViewingNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereTsPersonalRtTotalViewingRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereTsPersonalTotalViewingNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereTsPersonalTotalViewingRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereTsPersonalViewingNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereTsPersonalViewingRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereTsSamplesHouseholdViewingNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereTsSamplesHouseholdViewingRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereTsSamplesPersonalViewingNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereTsSamplesPersonalViewingRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereTsUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Commercial whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Commercial extends Model
{
    public $incrementing = false;

    /**
     * to Disable updated_at.
     * @var string
     */
    public $timestamps = true;

    /**
     * table name.
     *
     * @var string
     */
    protected $table = 'commercials';

    /**
     * fill.
     *
     * @var array
     */
    protected $fillable = [
        'cm_id',
        'prog_id',
        'started_at',
        'ended_at',
        'date',
        'region_id',
        'time_box_id',
        'channel_id',
        'company_id',
        'product_id',
        'scene_id',
        'duration',
        'program_title',
        'genre_id',
        'setting',
        'talent',
        'remarks',
        'bgm',
        'memo',
        'first_date',
        'ts_update',
        'created_at',
        'updated_at',
        'calculated_at',
        'personal_viewing_number',
        'personal_viewing_rate',
        'household_viewing_number',
        'household_viewing_rate',
        'cm_type',
        'cm_type_updated_at',
    ];
}
