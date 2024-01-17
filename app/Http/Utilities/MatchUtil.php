<?php

namespace App\Http\Utilities;

use App\Models\TblMatch;

class MatchUtil
{
    /**
     * @param TblMatch $match
     * @return array
     */
    public function playMatch(TblMatch $match): array
    {
        $homeAttack = (float)$match->homeTeam->attack_score * 1.03;
        $awayAttack = (float)$match->awayTeam->attack_score;
        $homeDefence = (float)$match->homeTeam->defence_score * 1.03;
        $awayDefence = (float)$match->awayTeam->defence_score;

        if (0 > $homeAttack - $awayDefence) {
            $homeScore = mt_rand(0, 2);
        } else {
            $homeScore = mt_rand(0, (round($homeAttack - $awayDefence) <= 8) ?? 8);
        }

        if (0 > $awayAttack - $homeDefence) {
            $awayScore = mt_rand(0, 2);
        } else {
            $awayScore = mt_rand(0, (round($homeAttack - $awayDefence) <= 8) ?? 8);
        }

        return array(
            "home_score" => $homeScore,
            "away_score" => $awayScore
        );
    }
}
