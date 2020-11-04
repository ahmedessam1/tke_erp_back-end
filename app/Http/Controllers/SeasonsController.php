<?php

namespace App\Http\Controllers;

use App\Http\Requests\TableSearchRequest;
use App\Repositories\Contracts\SeasonRepository;
use App\Http\Requests\SeasonsRequest;
use Illuminate\Http\Request;
use App\Models\Season;
use Response;

class SeasonsController extends Controller
{
    protected $model;
    public function __construct(SeasonRepository $seasons) {
        $this -> model = $seasons;
    }

    public function index () {
        // TESTED....
        $getSeasons = $this -> model -> getAllActiveSeasons();
        return Response::json($getSeasons);
    }

    public function store (SeasonsRequest $request) {
        // TESTED....
        $added_season = $this -> model -> addSeason($request);
        return Response::json($added_season);
    }

    public function edit ($season_id) {
        // TESTED....
        $edited_season = $this -> model -> editSeason($season_id);
        return Response::json($edited_season);
    }

    public function update (SeasonsRequest $request, $season_id) {
        // TESTED....
        $updated_season = $this -> model -> updateSeason($request, $season_id);
        return Response::json($updated_season);
    }

    public function search (TableSearchRequest $request) {
        // TESTED....
        $getSeasons = $this -> model -> getSeasonsSearchResult($request -> input('query'));
        return Response::json($getSeasons);
    }

    public function delete ($season_id) {
        // TESTED....
        $delete_season = $this -> model -> deleteSeason($season_id);
        return Response::json($delete_season);
    }

    public function restore ($season_id) {
        // TESTED....
        $restore_season = $this -> model -> restoreSeason($season_id);
        return Response::json($restore_season);
    }
}
