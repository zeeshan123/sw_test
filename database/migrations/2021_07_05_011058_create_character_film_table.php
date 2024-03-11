<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCharacterFilmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_film', function (Blueprint $table) {
            $table->foreignId('character_id') ->constrained() ->onDelete('cascade');
            $table->foreignId('film_id') ->constrained() ->onDelete('cascade');
            $table->unique(['character_id', 'film_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('character_film');
    }
}
