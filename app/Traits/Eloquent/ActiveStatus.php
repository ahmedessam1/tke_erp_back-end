<?php

namespace App\Traits\Eloquent;

use Illuminate\Database\Eloquent\Builder;

trait ActiveStatus {
    public function scopeIsActive (Builder $builder) {
        return $builder -> where('active', 1);
    }

    public function scopeNotActive (Builder $builder) {
        return $builder -> where('active', 0);
    }
}