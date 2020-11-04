<?php

namespace App\Models\Supplier;

use App\Traits\Eloquent\Sorting;
use App\Traits\Eloquent\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierInitiatoryCredit extends Model
{
    use Sorting, User, SoftDeletes;

    // DATES
    protected $dates = ['deleted_at'];

    // FILLABLE
    protected $fillable = [
        'supplier_id', 'amount', 'date', 'created_by', 'updated_by'
    ];

    // RELATIONSHIPS
    /*
     * Users.php trait contain:
     * created_by and Updated_by relationships
     */
    public function supplier () {
        return $this -> belongsTo('App\Models\Supplier\Supplier') -> withTrashed();
    }

    // SCOPES
    public function scopeWithSupplier ($builder) {
        return $builder -> with('supplier');
    }
}
