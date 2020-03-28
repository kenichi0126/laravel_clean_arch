<?php

namespace Smart2\CommandModel\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * Smart2\CommandModel\Eloquent\Sponsor.
 *
 * @property int $id
 * @property string $name
 * @property null|string $deleted_at
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 * @property string $status
 * @property string $started_at
 * @property null|string $ended_at
 * @property string $disp_name
 * @property null|string $sales
 * @property null|string $contract_period
 * @property null|string $renewal_date
 * @property int $auto_renewal
 * @property-read \Smart2\CommandModel\Eloquent\SponsorRole $sponsorRole
 * @property-read \Smart2\CommandModel\Eloquent\SponsorTrial $sponsorTrial
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Sponsor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Sponsor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Sponsor query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Sponsor whereAutoRenewal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Sponsor whereContractPeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Sponsor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Sponsor whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Sponsor whereDispName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Sponsor whereEndedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Sponsor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Sponsor whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Sponsor whereRenewalDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Sponsor whereSales($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Sponsor whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Sponsor whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Sponsor whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Sponsor extends Model
{
    /**
     * table name.
     *
     * @var string
     */
    protected $table = 'sponsors';

    /**
     * fill.
     *
     * @var array
     */
    protected $fillable = [
    ];

    public function sponsorRole()
    {
        return $this->hasOne(SponsorRole::class);
    }

    public function sponsorTrial()
    {
        return $this->hasOne(SponsorTrial::class)->withDefault();
    }
}
