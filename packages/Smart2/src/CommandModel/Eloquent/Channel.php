<?php

namespace Smart2\CommandModel\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * Smart2\CommandModel\Eloquent\Channel.
 *
 * @property int $id
 * @property int $region_id
 * @property string $type
 * @property null|int $button_number
 * @property string $code_name
 * @property string $display_name
 * @property null|string $created_at
 * @property null|string $updated_at
 * @property null|int $position
 * @property null|string $mdata_service_id
 * @property null|int $with_commercials
 * @property null|string $hdy_channel_code
 * @property null|string $hdy_channel_name
 * @property null|string $hdy_type_code
 * @property int $hdy_report_targeted
 * @property int $report_targeted
 * @property null|string $network
 * @property null|string $division
 * @property int $ts_flag タイムシフトフラグ
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Channel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Channel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Channel query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Channel whereButtonNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Channel whereCodeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Channel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Channel whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Channel whereDivision($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Channel whereHdyChannelCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Channel whereHdyChannelName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Channel whereHdyReportTargeted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Channel whereHdyTypeCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Channel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Channel whereMdataServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Channel whereNetwork($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Channel wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Channel whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Channel whereReportTargeted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Channel whereTsFlag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Channel whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Channel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Channel whereWithCommercials($value)
 * @mixin \Eloquent
 */
class Channel extends Model
{
    public $incrementing = true;

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
    protected $table = 'channels';

    /**
     * fill.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'region_id',
        'type',
        'button_number',
        'code_name',
        'display_name',
        'created_at',
        'updated_at',
        'position',
        'mdata_service_id',
        'with_commercials',
        'hdy_channel_code',
        'hdy_channel_name',
        'hdy_type_code',
        'hdy_report_targeted',
        'report_targeted',
        'network',
        'division',
    ];
}
