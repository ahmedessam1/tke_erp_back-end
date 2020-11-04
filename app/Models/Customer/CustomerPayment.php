<?php

namespace App\Models\Customer;

use App\Traits\Eloquent\Sorting;
use App\Traits\Eloquent\Status;
use App\Traits\Eloquent\User;
use App\Traits\Logic\NumberToWords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerPayment extends Model
{
    use SoftDeletes, User, Sorting, Status, NumberToWords;

    // FILLABLE
    protected $fillable = [
        'date',
        'customer_id',
        'money_courier_id',
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
    protected $appends = ['amount_in_words'];

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

    public function customer () {
        return $this -> belongsTo('App\Models\Customer\Customer', 'customer_id');
    }

    public function moneyCourier () {
        return $this -> belongsTo('App\User', 'money_courier_id');
    }

    // SCOPES
    public function scopeWithCustomer (Builder $builder) {
        return $builder -> with('customer');
    }

    public function scopeWithMoneyCourier (Builder $builder) {
        return $builder -> with('moneyCourier');
    }
}
