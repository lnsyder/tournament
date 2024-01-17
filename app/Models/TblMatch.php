<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TblMatch extends Model
{
    use HasFactory;

    protected $table = 'tbl_match';
    protected $fillable = ['week', 'home_team_id', 'away_team_id', 'status', 'home_score', 'away_score'];

    /**
     * @return BelongsTo
     */
    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(LkpTeam::class, 'home_team_id');
    }

    /**
     * @return BelongsTo
     */
    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(LkpTeam::class, 'away_team_id');
    }
}
