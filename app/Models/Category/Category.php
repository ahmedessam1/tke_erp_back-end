<?php

namespace App\Models\Category;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Eloquent\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Eloquent\Sorting;

class Category extends Model
{
    use SoftDeletes, Sorting, User;

    // FILLABLE
    protected $fillable = [
        'name', 'description', 'created_by', 'updated_by'
    ];

    // DATES
    protected $dates = ['deleted_at'];

    // RELATIONSHIPS
    /*
     * Users.php trait contain:
     * created_by and Updated_by relationships
     */
    public function subcategories() {
        return $this->hasMany('App\Models\Category\Subcategory');
    }

    // SCOPES
    public function scopeWithSubcategories ($builder) {
        return $builder -> with('subcategories');
    }
}
