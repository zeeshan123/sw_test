<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Character extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'films' => AsCollection::class,
        'species' => AsCollection::class,
        'starships' => AsCollection::class,
        'vehicles' => AsCollection::class,
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The films that character has been in.
     */
    public function movies() : BelongsToMany
    {
        return $this->belongsToMany(Film::class);
    }

    /**
     * Get all genders assigned to characters.
     *
     * @return array
     */
    public static function genders() : array
    {
        return self::distinct()->pluck('gender')->toArray();
    }
}
