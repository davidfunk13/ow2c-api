<?php

use App\Models\Battletag;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('battletags', function (Blueprint $table) {
            $table->id('id');
            $table->string('battletag');
            $table->integer('blizz_id');
            $table->string('sub');
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->id('id');
            $table->string('name');
            $table->timestamps();
            $table->foreignIdFor(Battletag::class);
        
            $table->foreign('battletag_id')
                ->references('id')
                ->on('battletags')
                ->onDelete('cascade');

        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('battletags');
        Schema::dropIfExists('sessions');

    }
};