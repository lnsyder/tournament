<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TblLeague extends Model
{
    use HasFactory;

    protected $table = 'tbl_leagues';
    protected $fillable = ['team_id','points', 'win', 'played', 'draw', 'lose', 'goal_difference', 'win_probability', 'order_number', 'created_at', 'updated_at'];

    public function team(): BelongsTo
    {
        return $this->belongsTo(LkpTeam::class, 'team_id');
    }

}
