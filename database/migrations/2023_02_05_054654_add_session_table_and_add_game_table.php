<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->uuid('id');
            $table->foreignUuid('battletag_id');
            $table->string('name');
            $table->integer('total_wins');
            $table->integer('wins');
            $table->integer('losses');
            $table->integer('draws');
            $table->integer('total_games');
            $table->timestamps();

        });

        Schema::create('games', function (Blueprint $table) {
            $table->uuid('id');
            $table->foreignUuid('battletag_id');
            $table->foreignUuid('session_id');
            $table->boolean('victory');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('games');
    }
};
