<?php

namespace App\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * Smart2\CommandModel\Eloquent\AttrDiv.
 *
 * @property string $division
 * @property string $code
 * @property string $name
 * @property int $display_order
 * @property null|string $definition
 * @property null|string $color
 * @property null|float $population
 * @property null|float $weight
 * @property null|string $restore_info
 * @property null|string $restore_info_text
 * @property null|int $base_samples
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\AttrDiv newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\AttrDiv newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\AttrDiv query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\AttrDiv whereBaseSamples($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\AttrDiv whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\AttrDiv whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\AttrDiv whereDefinition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\AttrDiv whereDisplayOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\AttrDiv whereDivision($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\AttrDiv whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\AttrDiv wherePopulation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\AttrDiv whereRestoreInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\AttrDiv whereRestoreInfoText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\AttrDiv whereWeight($value)
 * @mixin \Eloquent
 */
class AttrDivs extends Model
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
    protected $table = 'attr_divs';

    /**
     * fill.
     *
     * @var array
     */
    protected $fillable = [
        'division',
        'code',
        'name',
        'display_order',
        'definition',
        'color',
        'population',
        'weight',
        'restore_info',
        'restore_info_text',
    ];
}
