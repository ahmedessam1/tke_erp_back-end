<?php

namespace App\Models\Supplier;

use App\Traits\Eloquent\FieldsPermission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Eloquent\Sorting;
use App\Traits\Eloquent\User;

class Supplier extends Model
{
    use SoftDeletes, User, Sorting, FieldsPermission;
    // FILLABLE
    protected $fillable = [
        'name', 'description', 'created_by', 'updated_by'
    ];

    // DATES
    protected $dates = ['deleted_at'];

    // PERMISSIONS
    public function toArray()
    {
        $hidden = $this -> fieldHidePermission([
            ['roles' => ['super_admin'], 'field' => 'addresses'],
        ]);
        $this -> hidden = $hidden;

        return parent::toArray();
    }

    // RELATIONSHIPS
    /*
     * Users.php trait contain:
     * created_by and Updated_by relationships
     */
    public function addresses () {
        return $this -> hasMany('App\Models\Supplier\SupplierAddress');
    }

    public function invoices () {
        return $this -> belongsTo('App\Models\Invoices\ImportInvoice', 'supplier_id');
    }


    // SCOPES
    public function scopeGetAddresses (Builder $builder) {
        return $builder -> with('addresses');
    }

    public function scopeGetAddressesContacts (Builder $builder) {
        return $builder -> with('addresses.contacts.position');
    }

    public function scopeGetAddressesAndContacts (Builder $builder) {
        return $builder -> with('addresses')
            -> with('addresses.contacts')
            -> with('addresses.contacts.position');
    }
}
