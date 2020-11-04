<?php

namespace App\Repositories;

use App\Repositories\Contracts\CategoryRepository;
use App\Models\Category\Subcategory;
use App\Models\Category\Category;
use App\Events\ActionHappened;
use App\Cache\RedisAdapter;
use Auth;
use DB;

class EloquentCategoryRepository implements CategoryRepository {
    protected $cache;
    public function __construct()
    {
        $this->cache = new RedisAdapter();
    }

    private function getAuthUserId() {
        return Auth::user() -> id;
    }

    public function getAllActiveCategories () {
        // RETURN DATA IF IN CACHE AND IF NOT THEN RE-CACHE IT
        $categories = $this->cache->remember('categories_and_subcategories:'.$_REQUEST['page'], function () {
            return json_encode(Category::withCreatedByAndUpdatedBy() -> withSubcategories() -> orderedName() -> paginate(30));
        });
        return json_decode($categories);
    }

    public function addCategory ($request) {
        return DB::transaction(function () use ($request) {
            // ADDING CATEGORY
            $category_fillable_values = array_merge(
                $request -> all(),
                ['created_by' => $this->getAuthUserId()]
            );
            $added_category = Category::create($category_fillable_values);

            // ADDING SUB-CATEGORY
            $this -> insertSubcategories($request -> subcategories, $added_category);

            // STORE ACTION
            event(new ActionHappened('category add', $added_category, $this -> getAuthUserId()));
            // EMPTY CACHE
            $this->cache->forget('categories_and_subcategories');
            return $added_category;
        });
    }

    public function addSubcategory ($request) {
        $category = Category::withSubcategories() -> find($request -> category_id);
        // ADDING SUB-CATEGORY
        $this -> insertSubcategories($request -> subcategories, $category);

        $new_category = Category::withSubcategories() -> find($request -> category_id);
        // STORE ACTION
        event(new ActionHappened('Subcategory add with category id: ', $category -> id, $this -> getAuthUserId()));
        // EMPTY CACHE
        $this->cache->forgetByPattern('categories_and_subcategories:*');
        return $new_category;
    }

    public function getCategoriesSearchResult ($q) {
        $categories = Category::withCreatedByAndUpdatedBy() -> orderedName() -> withSubcategories()
            -> where('name', 'LIKE', '%'.$q.'%')
            -> orWhere('description', 'LIKE', '%'.$q.'%')
            -> paginate(30);
        return $categories;
    }

    public function editCategory($category_id) {
        $edited_category = Category::withSubcategories() -> withCreatedByAndUpdatedBy() -> find($category_id);
        return $edited_category;
    }

    public function updateCategory($request, $category_id)
    {
        // UPDATE MAIN CATEGORY
        $category = Category::withCreatedByAndUpdatedBy() -> find($category_id);
        $category_fillable_values = array_merge(
            $request -> all(),
            ['updated_by'    => $this -> getAuthUserId()]
        );
        $category -> update($category_fillable_values);

        // STORE ACTION
        event(new ActionHappened('category updated', $category, $this -> getAuthUserId()));
        // EMPTY CACHE
        $this->cache->forgetByPattern('categories_and_subcategories:*');
        return $category;
    }

    public function deleteCategory ($category_id) {
        // DELETING THE CATEGORY
        $category = Category::find($category_id);
        $category -> delete();
        // STORE ACTION
        event(new ActionHappened('category deleted', $category, $this -> getAuthUserId()));
        // EMPTY CACHE
        $this->cache->forgetByPattern('categories_and_subcategories:*');
        return $category;
    }

    public function restoreCategory ($category_id) {
        // RESTORING THE CATEGORY
        $category = Category::withTrashed() -> find($category_id);
        $category -> restore();
        // STORE ACTION
        event(new ActionHappened('category restored', $category, $this -> getAuthUserId()));
        // EMPTY CACHE
        $this->cache->forgetByPattern('categories_and_subcategories:*');
        return $category;
    }

    public function getSubcategories ($category_id) {
        return Category::withSubcategories() -> find($category_id) -> subcategories;
    }

    public function deleteSubcategory($subcategory_id) {
        $subcategory = Subcategory::find($subcategory_id);
        $subcategory -> delete();
        // EMPTY CACHE
        $this->cache->forgetByPattern('categories_and_subcategories:*');
        return $subcategory;
    }

    /*
     * **************************************************
     * ********** PRIVATE HELPERS FUNCTIONS *************
     * **************************************************
     */

    private function insertSubcategories ($subcategories, $category) {
        foreach ($subcategories as $subcategory)
            $category -> subcategories() -> create([
                'name'          => $subcategory['name'],
                'description'   => $subcategory['description'],
                'created_by'    => $this -> getAuthUserId(),
            ]);
    }

    private function updateSubcategories ($subcategories, $updated_category) {
        $updated_category -> subcategories() -> delete();

        // ADDING THE NEW SUBCATEGORIES
        $this -> insertSubcategories($subcategories, $updated_category);
    }
}
