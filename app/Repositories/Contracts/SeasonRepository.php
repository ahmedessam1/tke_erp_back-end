<?php

namespace App\Repositories\Contracts;

interface SeasonRepository {
    // RETURN ALL THE ACTIVE SEASONS ONLY..
    public function getAllActiveSeasons();

    // ADD NEW SEASON
    public function addSeason($request);

    // EDIT SEASON
    public function editSeason($season_id);

    // UPDATE SEASON
    public function updateSeason($request, $season_id);

    // SEARCH SEASONS
    public function getSeasonsSearchResult($q);

    // DELETE SEASON
    public function deleteSeason($season);

    // RESTORE SEASON
    public function restoreSeason($season_id);

}