<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LkpTeam extends Model
{
    protected $table = 'lkp_team';
    protected $fillable = ['name', 'attack_score', 'defence_score'];
}

