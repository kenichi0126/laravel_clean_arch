<?php

namespace Smart2\CommandModel\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * Smart2\CommandModel\Eloquent\MdataProgGenre.
 *
 * @property string $genre_id
 * @property string $name
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MdataProgGenre newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MdataProgGenre newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MdataProgGenre query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MdataProgGenre whereGenreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MdataProgGenre whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\MdataProgGenre whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MdataProgGenre extends Model
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
    protected $table = 'mdata_prog_genres';

    /**
     * fill.
     *
     * @var array
     */
    protected $fillable = [
        'genre_id',
        'name',
        'updated_at',
    ];

    // 作成日は無いため除外
    public function setCreatedAt($value)
    {
        return $this;
    }

    public function getCreatedAtColumn(): void
    {
        //Do-nothing
    }
}
