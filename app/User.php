<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\Eloquent\ActiveStatus;
use Laravel\Passport\HasApiTokens;
use App\Traits\Eloquent\Sorting;
use Auth;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles, ActiveStatus, Sorting;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    /*
     * SCOPES
     */

    public function scopeWhereNotLoggedUser ($builder) {
        return $builder -> where('id', '!=', Auth::user() -> id);
    }
}
