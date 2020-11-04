<?php

namespace App\Traits\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Auth;

trait FieldsPermission {
    public function fieldHidePermission ($permissions_and_fields) {
        $user = Auth::user();

        $hidden = [];
        $counter = count($permissions_and_fields);

        for ($i = 0; $i < $counter; $i++) {
            for ($x = 0; $x < count($permissions_and_fields[$i]['roles']); $x++) {
                if (!$user -> hasRole($permissions_and_fields[$i]['roles'][$x]))
                    array_push($hidden, $permissions_and_fields[$i]['field']);
            }
        }

        return $hidden;
    }

    public function hasRole ($roles) {
        $user = Auth::user();
        return $user -> hasRole($roles);
    }
}
