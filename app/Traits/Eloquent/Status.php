<?php

namespace App\Traits\Eloquent;

trait Status {
    public function scopeApproved ($builder) {
        return $builder -> where('approve', 1);
    }

    public function scopeNotApproved ($builder) {
        return $builder -> where('approve', 0);
    }
}