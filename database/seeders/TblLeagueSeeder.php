<?php

namespace Database\Seeders;

use App\Models\LkpTeam;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TblLeagueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = LkpTeam::all();
        foreach ($teams as $team) {
            $data[] = array(
                'team_id' => $team->id,
                'points' => 0,
                'played' => 0,
                'win' => 0,
                'draw' => 0,
                'lose' => 0,
                'goal_difference' => 0,
                'win_probability' => '%' . 100 / $teams->count(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            );
        }
        DB::table('tbl_leagues')->insert($data);
    }
}
