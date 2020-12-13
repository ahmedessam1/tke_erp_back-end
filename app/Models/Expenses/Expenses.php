<?php

namespace App\Models\Expenses;

use App\Traits\Eloquent\Status;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Eloquent\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expenses extends Model
{
    use SoftDeletes, User, Status;

    // FILLABLE
    protected $fillable = [
        'customer_id', 'user_id', 'expense_type_id', 'payment_type_id', 'title', 'date', 'amount', 'national_id',
        'check_number', 'check_date', 'notes', 'approve', 'created_by', 'updated_by'
    ];

    // DATES
    protected $dates = ['deleted_at'];
}
