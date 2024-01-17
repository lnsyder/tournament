<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblMatchTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_match', function (Blueprint $table) {
            $table->id();
            $table->integer('week');
            $table->unsignedBigInteger('home_team_id');
            $table->unsignedBigInteger('away_team_id');
            $table->boolean('status')->default(false);
            $table->string('home_score')->nullable();
            $table->string('away_score')->nullable();
            $table->timestamps();

            $table->foreign('home_team_id')->references('id')->on('lkp_team');
            $table->foreign('away_team_id')->references('id')->on('lkp_team');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_match');
    }
}

