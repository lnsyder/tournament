<?php

namespace App\Http\Controllers;

use App\Models\TblLeague;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     */
    public function index(): \Illuminate\Foundation\Application|View|Factory|Application
    {
        $league = TblLeague::orderBy('order_number', 'asc')->get();
        $weekResults = (new MatchController)->getWeekResults();
        return view('index', compact('league', 'weekResults'));
    }
}
