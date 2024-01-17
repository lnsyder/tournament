<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_leagues', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->tinyInteger('points')->default(0);
            $table->tinyInteger('played')->default(0);
            $table->tinyInteger('win')->default(0);
            $table->tinyInteger('draw')->default(0);
            $table->tinyInteger('lose')->default(0);
            $table->tinyInteger('goal_difference')->default(0);
            $table->tinyInteger('order_number')->default(0);
            $table->string('win_probability')->default('%0');
            $table->timestamps();

            $table->foreign('team_id')->references('id')->on('lkp_team');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_leagues');
    }
};
