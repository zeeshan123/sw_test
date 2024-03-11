<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FilmCharacter extends Model
{
    use HasFactory;
     public $timestamps = false;
protected $table = 'character_film';

protected $fillable = ['character_id', 'film_id'];
}
