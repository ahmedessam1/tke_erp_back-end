<?php

namespace App\Models\Custody;

use App\Traits\Eloquent\Status;
use App\Traits\Eloquent\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Custody extends Model
{
    use SoftDeletes, User, Status;

    protected $table = 'custody';

    // FILLABLE
    protected $fillable = [
        'user_id', 'payment_type_id', 'title', 'date', 'amount', 'national_id',
        'check_number', 'check_date', 'notes', 'approve', 'spent_amount', 'created_by', 'updated_by'
    ];

    // DATES
    protected $dates = ['deleted_at'];

    // RELATIONSHIPS
    public function paymentType()
    {
        return $this->belongsTo('App\Models\Requirements\PaymentType', 'payment_type_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    // SCOPES
    public function scopeWithPaymentType($builder) {
        return $builder->with('paymentType');
    }

    public function scopeWithUser($builder) {
        return $builder->with('user');
    }
}
