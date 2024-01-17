<?php

namespace App\Console\Commands;

use App\Models\LkpTeam;
use App\Models\TblMatch;
use Carbon\Carbon;
use Carbon\Traits\Date;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixtureGenerator extends Command
{
    protected $signature = 'fixture:generate';
    protected $description = 'Generate fixtures for teams';

    public function handle()
    {
        $this->info('Fixtures generating...');
        $this->generateFixtures();
        $this->info('Fixtures generated successfully!');
    }

    public function generateFixtures()
    {
        $teams = LkpTeam::all();
        foreach ($teams as $team) {
            for ($i = 1; $i <= (count($teams) * 2) - 2; $i++) {
                $availabeWeeks[$team->id][$i] = $i;
            }
        }
        foreach ($teams as $home) {
            foreach ($teams as $away) {
                if ($home !== $away) {
                    $availabeMatchWeeks = array_intersect($availabeWeeks[$home->id], $availabeWeeks[$away->id]);
                    $week = reset($availabeMatchWeeks);
                    $matches[$week][] = array(
                        "home_team_id" => $home->id,
                        "away_team_id" => $away->id,
                        "created_at" => Carbon::now(),
                        "updated_at" => Carbon::now(),
                    );
                    unset($availabeWeeks[$home->id][$week]);
                    unset($availabeWeeks[$away->id][$week]);
                }
            }
        }
        foreach ($matches as $key => $value) {
            foreach ($value as &$val){
                $val['week'] = $key;
                $allMatches[] = $val;
            }
        }
        TblMatch::insert($allMatches);
    }
}
