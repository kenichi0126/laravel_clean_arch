<?php

namespace App\Eloquent;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class SearchConditions.
 *
 * @property int $id
 * @property int $member_id
 * @property int $region_id
 * @property string $name
 * @property string $route_name
 * @property string $condition
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property null|Carbon $deleted_at
 * @mixin Eloquent
 */
class SearchConditions extends Model
{
    use SoftDeletes;

    public $incrementing = true;

    /**
     * @var string
     */
    protected $table = 'search_conditions';

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'member_id',
        'region_id',
        'name',
        'route_name',
        'condition',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
