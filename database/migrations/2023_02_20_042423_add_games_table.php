<?php

use App\Models\Battletag;
use App\Models\Session;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id('id');
            $table->integer('result');
            $table->string('location');
            $table->foreignIdFor(Battletag::class);
            $table->foreignIdFor(Session::class);
            
            $table->foreign('battletag_id')
            ->references('id')
            ->on('battletags')
            ->onDelete('cascade');
            
            $table->foreign('session_id')
            ->references('id')
            ->on('sessions')
            ->onDelete('cascade');
            
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
        Schema::dropIfExists('games');
    }
};