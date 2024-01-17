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

        return array(
            "home_score" => $this->randomizeCalculator($homeAttack, $awayDefence),
            "away_score" => $this->randomizeCalculator($awayAttack, $homeDefence)
        );
    }

    /**
     * @param $firstTeamScore
     * @param $secondTeamDefence
     * @return int
     */
    protected function randomizeCalculator($firstTeamScore, $secondTeamDefence): int
    {
        if (0 > $firstTeamScore - $secondTeamDefence) {
            $teamScore = mt_rand(0, 2);
        } else {
            if ($firstTeamScore - $secondTeamDefence >= 8) {
                $rand = 8;
            } else {
                $rand = $firstTeamScore - $secondTeamDefence;
            }
            $teamScore = mt_rand(0, round($rand));
        }
        return $teamScore;
    }
}
