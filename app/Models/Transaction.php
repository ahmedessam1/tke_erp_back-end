<?php

namespace App\Models;

use App\Traits\Eloquent\Sorting;
use App\Traits\Eloquent\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes, Sorting, User;

    // FILLABLE
    protected $fillable = [
        'model_type', 'model_id', 'case', 'payment_type_id', 'amount', 'created_by', 'updated_by'
    ];
}
