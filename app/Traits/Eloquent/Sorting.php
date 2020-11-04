<?php

namespace App\Traits\Eloquent;

use Illuminate\Database\Eloquent\Builder;

trait Sorting {
    public function scopeOrderedName (Builder $builder) {
        return $builder -> orderBy('name', 'ASC');
    }

    public function scopeOrderedId (Builder $builder) {
        return $builder -> orderBy('id', 'DESC');
    }
}