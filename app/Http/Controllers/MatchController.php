<?php

namespace App\Http\Controllers;

use App\Http\Utilities\MatchUtil;
use App\Models\TblLeague;
use App\Models\TblMatch;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Artisan;

class MatchController extends BaseController
{

    /**
     * @param Request $request
     * @return mixed
     */
    public function getMatches(Request $request): mixed
    {
        $matches = null;
        $week = $request->input('week');
        if ("next" == $week) {
            $firstWeekMatch = TblMatch::orderBy('week')->where('status', false)->first();
            if (isset($firstWeekMatch)) {
                $currentWeek = $firstWeekMatch->week;
                $matches = TblMatch::where('week', $currentWeek)->get();
            }
        } else if ("all" == $week) {
            $matches = TblMatch::where('status', false)->get();
        }
        return $matches;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function playMatches(Request $request): JsonResponse
    {
        $matchUtil = new MatchUtil;
        $matches = $this->getMatches($request);
        if (isset($matches)) {
            foreach ($matches as $match) {
                $matchScore = $matchUtil->playMatch($match);
                $this->updateMatches($match, $matchScore);
            }
        }
        $this->calculateLeagueTable();
        return response()->json(['message' => 'Matches successfully played!'], 200);
    }

    /**
     * @param TblMatch $match
     * @param array $matchScore
     * @return void
     */
    public function updateMatches(TblMatch $match, array $matchScore): void
    {
        $match->update([
            "home_score" => $matchScore['home_score'],
            "away_score" => $matchScore['away_score'],
            "status" => true
        ]);
    }

    /**
     * @return void
     */
    public function calculateLeagueTable(): void
    {
        $leagueTeams = TblLeague::all();
        $playedMatches = TblMatch::where('status', true)->get();
        if (!is_null($playedMatches)) {
            $calculatedLeagueTeams = $this->calculateMatches($playedMatches);
            foreach ($leagueTeams as $team) {
                $team->update($calculatedLeagueTeams[$team->team_id]);
            }
        }
    }

    /**
     * @param Collection $playedMatches
     * @return array
     */
    public function calculateMatches(Collection $playedMatches): array
    {
        $leagueCalculatedTeams = [];
        foreach ($playedMatches as $match) {
            $homeMatchPoints = 0;
            $awayMatchPoints = 0;
            $homeWin = 0;
            $homeDraw = 0;
            $homeLose = 0;
            $awayWin = 0;
            $awayDraw = 0;
            $awayLose = 0;

            if ($match->home_score > $match->away_score) {
                $homeMatchPoints = 3;
                $homeWin = 1;
                $awayLose = 1;
            } else if ($match->home_score < $match->away_score) {
                $awayMatchPoints = 3;
                $homeLose = 1;
                $awayWin = 1;
            } else {
                $homeMatchPoints = 1;
                $awayMatchPoints = 1;
                $homeDraw = 1;
                $awayDraw = 1;
            }

            $homeTotalGoalDifference = (isset($leagueCalculatedTeams[$match->home_team_id]["goal_difference"]))
                ? $leagueCalculatedTeams[$match->home_team_id]["goal_difference"] + $match->home_score - $match->away_score
                : $match->home_score - $match->away_score;
            $awayTotalGoalDifference = (isset($leagueCalculatedTeams[$match->away_team_id]["goal_difference"]))
                ? $leagueCalculatedTeams[$match->away_team_id]["goal_difference"] + $match->away_score - $match->home_score
                : $match->away_score - $match->home_score;

            $homeTotalPoints = (isset($leagueCalculatedTeams[$match->home_team_id]["points"]))
                ? $leagueCalculatedTeams[$match->home_team_id]["points"] + $homeMatchPoints
                : $homeMatchPoints;
            $awayTotalPoints = (isset($leagueCalculatedTeams[$match->away_team_id]["points"]))
                ? $leagueCalculatedTeams[$match->away_team_id]["points"] + $awayMatchPoints
                : $awayMatchPoints;

            $homeTotalWins = (isset($leagueCalculatedTeams[$match->home_team_id]["win"]))
                ? $leagueCalculatedTeams[$match->home_team_id]["win"] + $homeWin
                : $homeWin;
            $awayTotalWins = (isset($leagueCalculatedTeams[$match->away_team_id]["win"]))
                ? $leagueCalculatedTeams[$match->away_team_id]["win"] + $awayWin
                : $awayWin;

            $homeTotalDraws = (isset($leagueCalculatedTeams[$match->home_team_id]["draw"]))
                ? $leagueCalculatedTeams[$match->home_team_id]["draw"] + $homeDraw
                : $homeDraw;
            $awayTotalDraws = (isset($leagueCalculatedTeams[$match->away_team_id]["draw"]))
                ? $leagueCalculatedTeams[$match->away_team_id]["draw"] + $awayDraw
                : $awayDraw;

            $homeTotalLoses = (isset($leagueCalculatedTeams[$match->home_team_id]["lose"]))
                ? $leagueCalculatedTeams[$match->home_team_id]["lose"] + $homeLose
                : $homeLose;
            $awayTotalLoses = (isset($leagueCalculatedTeams[$match->away_team_id]["lose"]))
                ? $leagueCalculatedTeams[$match->away_team_id]["lose"] + $awayLose
                : $awayLose;

            $homeTotalPlayed = (isset($leagueCalculatedTeams[$match->home_team_id]["played"]))
                ? $leagueCalculatedTeams[$match->home_team_id]["played"] + 1
                : 1;
            $awayTotalPlayed = (isset($leagueCalculatedTeams[$match->away_team_id]["played"]))
                ? $leagueCalculatedTeams[$match->away_team_id]["played"] + 1
                : 1;


            $leagueCalculatedTeams[$match->home_team_id] = array(
                "team_id" => $match->home_team_id,
                "goal_difference" => $homeTotalGoalDifference,
                "points" => $homeTotalPoints,
                "win" => $homeTotalWins,
                "draw" => $homeTotalDraws,
                "lose" => $homeTotalLoses,
                "played" => $homeTotalPlayed
            );

            $leagueCalculatedTeams[$match->away_team_id] = array(
                "team_id" => $match->away_team_id,
                "goal_difference" => $awayTotalGoalDifference,
                "points" => $awayTotalPoints,
                "win" => $awayTotalWins,
                "draw" => $awayTotalDraws,
                "lose" => $awayTotalLoses,
                "played" => $awayTotalPlayed
            );
        }
        $leagueCalculatedTeams = $this->sortTable($leagueCalculatedTeams);
        $calculateProbability = $this->calculateChampionProbability($leagueCalculatedTeams);
        return $calculateProbability;
    }

    /**
     * @param $leagueCalculatedTeams
     * @return array
     */
    public function sortTable($leagueCalculatedTeams): array
    {
        uasort($leagueCalculatedTeams, function ($a, $b) {
            return $b['points'] - $a['points'];
        });

        $oldTeam = null;
        $orderNumber = 1;
        foreach ($leagueCalculatedTeams as &$team) {
            if (!is_null($oldTeam) && $team['points'] == $oldTeam['points']) {
                if ($team['goal_difference'] > $oldTeam['goal_difference']) {
                    $team['order_number'] = $oldTeam['order_number'];
                    $leagueCalculatedTeams[$oldTeam['team_id']]['order_number'] = $orderNumber;
                } else {
                    $team['order_number'] = $orderNumber;
                }
            } else {
                $team['order_number'] = $orderNumber;
            }
            $orderNumber++;
            $oldTeam = $team;
        }

        return $leagueCalculatedTeams;
    }

    /**
     * @param array $teams
     * @return array
     */
    function calculateChampionProbability(array $teams): array
    {
        $totalPoints = 0;
        foreach ($teams as $team) {
            $totalPoints += $team['points'];
        }
        foreach ($teams as &$team) {
            $team['win_probability'] = (int)(($team['points'] / $totalPoints) * 100);
        }
        return $teams;
    }

    /**
     * @return mixed
     */
    public function getWeekResults(): mixed
    {
        $currentWeek = $this->getLastWeek();
        return TblMatch::where('week', $currentWeek)->get();
    }

    /**
     * @return mixed
     */
    public function getLastWeek(): mixed
    {
        $lastWeekMatch = TblMatch::orderBy('week', 'desc')->where('status', true)->first();
        if (!isset($lastWeekMatch)) {
            $lastWeekMatch = TblMatch::orderBy('week', 'asc')->where('status', false)->first();
        }
        return $lastWeekMatch->week;
    }

    /**
     * @return JsonResponse
     */
    public function reset(): JsonResponse
    {
        Artisan::call('migrate:fresh --seed');
        Artisan::call('fixture:generate ');
        return response()->json(['message' => 'Fixture reset is successful!'], 200);
    }

}
