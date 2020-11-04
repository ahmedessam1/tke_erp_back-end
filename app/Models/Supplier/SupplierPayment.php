<?php

namespace App\Models\Supplier;

use App\Traits\Eloquent\Status;
use App\Traits\Logic\NumberToWords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Eloquent\Sorting;
use App\Traits\Eloquent\User;

class SupplierPayment extends Model
{
    use SoftDeletes, User, Sorting, Status, NumberToWords;

    // FILLABLE
    protected $fillable = [
        'date',
        'supplier_id',
        'supplier_address_id',
        'supplier_contact_id',
        'national_id',
        'amount',
        'payment_type_id',
        'notes',
        'check_number',
        'check_date',
        'created_by',
        'updated_by'
    ];

    // APPENDS TO COLLECTION
    protected $appends = [ 'amount_in_words' ];

    // DATES
    protected $dates = ['deleted_at'];

    // MUTATOR
    public function getAmountInWordsAttribute() {
        $amount_in_numbers = $this -> amount;
        $amount_in_words = $this -> numberToWords($amount_in_numbers, 'male');
        return $this->attributes['amount_in_words'] = $amount_in_words;
    }

    // RELATIONSHIPS
    public function paymentType () {
        return $this -> belongsTo('App\Models\Requirements\PaymentType', 'payment_type_id');
    }

    public function supplier () {
        return $this -> belongsTo('App\Models\Supplier\Supplier', 'supplier_id');
    }

    // THE PERSON THAT RECEIVED THE MONEY
    public function supplierAddress () {
        return $this -> belongsTo('App\Models\Supplier\SupplierAddress', 'supplier_address_id');
    }

    // THE PERSON THAT RECEIVED THE MONEY
    public function supplierContact () {
        return $this -> belongsTo('App\Models\Supplier\SupplierAddressContact', 'supplier_contact_id');
    }
}
