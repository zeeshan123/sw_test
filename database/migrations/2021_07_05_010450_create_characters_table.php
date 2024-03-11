<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCharactersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('characters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('api_id')->nullable()->comment('ID from SWAPI');
            $table->string('name')->unique();
            $table->unsignedSmallInteger('height')->nullable();
            $table->unsignedSmallInteger('mass')->nullable();
            $table->string('hair_color');
            $table->string('skin_color');
            $table->string('eye_color');
            $table->string('gender');
            $table->string('homeworld');
            $table->string('culture')->nullable();
            $table->float('born', 8, 1)->nullable();
            $table->float('died', 8, 1)->nullable();
            $table->json('films');
            $table->json('species');
            $table->json('starships');
            $table->json('vehicles');
            $table->timestamp('created')->nullable();
            $table->timestamp('edited')->nullable();
            $table->string('url')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('characters');
    }
}
