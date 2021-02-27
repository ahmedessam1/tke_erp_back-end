<?php

namespace App;

use App\Tenant\Models\Tenant;
use App\Traits\Eloquent\ActiveStatus;
use App\Traits\Eloquent\Status;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;
use App\Traits\Eloquent\Sorting;
use Auth;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles, Sorting, Status, ActiveStatus;
    protected $connection = 'landlord';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'active', 'tenant_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    // RELATIONSHIPS
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    /*
     * SCOPES
     */
    public function scopeWhereNotLoggedUser ($builder) {
        return $builder -> where('id', '!=', Auth::user() -> id);
    }
}
