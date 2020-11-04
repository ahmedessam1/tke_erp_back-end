<?php

namespace App\Traits\Eloquent;

use Illuminate\Database\Eloquent\Builder;

trait User {
    // RELATIONSHIPS
    public function created_by () {
        return $this -> belongsTo('App\User', 'created_by');
    }

    public function updated_by () {
        return $this -> belongsTo('App\User', 'updated_by');
    }


    // SCOPES
    public function scopeWithCreatedBy (Builder $builder) {
        return $builder -> with('created_by');
    }

    public function scopeWithUpdatedBy (Builder $builder) {
        return $builder -> with('updated_by');
    }

    public function scopeWithCreatedByAndUpdatedBy (Builder $builder) {
        return $builder -> with(['created_by', 'updated_by']);
    }
}