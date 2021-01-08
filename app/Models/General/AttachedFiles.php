<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttachedFiles extends Model
{
    use SoftDeletes;

    protected $fillable = ['model_type', 'model_id', 'file', 'created_by', 'updated_by'];
}
