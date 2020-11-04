<?php

namespace App\Repositories;

use App\Cache\RedisAdapter;
use App\Repositories\Contracts\SeasonRepository;
use App\Events\ActionHappened;
use App\Models\Season;
use Auth;

class EloquentSeasonRepository implements SeasonRepository {
    protected $cache;
    public function __construct()
    {
        $this->cache = new RedisAdapter();
    }

    private function getAuthUserId() {
        return Auth::user() -> id;
    }

    public function getAllActiveSeasons () {
        // RETURN DATA IF IN CACHE AND IF NOT THEN RE-CACHE IT
        $seasons = $this->cache->remember('seasons:'.$_REQUEST['page'], function () {
            return json_encode(Season::withCreatedByAndUpdatedBy() -> orderedName() -> paginate(30));
        });
        return json_decode($seasons);
    }

    public function addSeason($request) {
        $seasons_fillable_values = array_merge(
            $request -> all(),
            ['created_by' => $this->getAuthUserId()]
        );
        $added_season = Season::create($seasons_fillable_values);

        // STORE ACTION
        event(new ActionHappened('season add', $added_season, $this -> getAuthUserId()));
        // EMPTY CACHE
        $this->cache->forgetByPattern('seasons:*');
        return $added_season;
    }

    public function editSeason($season_id) {
        $edited_season = Season::withCreatedByAndUpdatedBy() -> find($season_id);
        return $edited_season;
    }

    public function updateSeason($request, $season_id)
    {
        $season = Season::withCreatedByAndUpdatedBy() -> find($season_id);
        $season_fillable_values = array_merge(
            $request -> all(),
            ['updated_by'    => $this -> getAuthUserId()]
        );
        $season -> update($season_fillable_values);
        // STORE ACTION
        event(new ActionHappened('season updated', $season, $this -> getAuthUserId()));
        // EMPTY CACHE
        $this->cache->forgetByPattern('seasons:*');
        return $season;
    }

    public function getSeasonsSearchResult ($q) {
        $seasons = Season::withCreatedByAndUpdatedBy() -> orderedName()
            -> where('name', 'LIKE', '%'.$q.'%')
            -> paginate(30);
        return $seasons;
    }

    public function deleteSeason ($season_id) {
        // DELETING THE SEASON
        $season = Season::find($season_id);
        $season -> delete();
        // STORE ACTION
        event(new ActionHappened('season deleted', $season, $this -> getAuthUserId()));
        // EMPTY CACHE
        $this->cache->forgetByPattern('seasons:*');
        return $season;
    }

    public function restoreSeason ($season_id) {
        // RESTORING THE SEASON
        $season = Season::withTrashed() -> find($season_id);
        $season -> restore();
        // STORE ACTION
        event(new ActionHappened('season restored', $season, $this -> getAuthUserId()));
        // EMPTY CACHE
        $this->cache->forgetByPattern('seasons:*');
        return $season;
    }
}
