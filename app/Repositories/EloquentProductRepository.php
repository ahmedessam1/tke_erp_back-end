<?php

namespace App\Repositories;

use App\Cache\RedisAdapter;
use App\Models\Product\LocalCode;
use App\Models\Product\ProductImages;
use App\Models\Product\ProductLog;
use App\Repositories\Contracts\ProductRepository;
use App\Events\ActionHappened;
use App\Models\Product\Product;
use App\Traits\Data\FilterProducts;
use App\Traits\Data\GetCategoriesList;
use App\Traits\Data\GetSeasonsList;
use App\Traits\Logic\GenerateLocalCode;
use Storage;
use Auth;
use DB;

class EloquentProductRepository implements ProductRepository
{
    use GenerateLocalCode,
        GetCategoriesList,
        GetSeasonsList,
        FilterProducts;

    protected $cache;

    public function __construct()
    {
        $this->cache = new RedisAdapter();
    }

    private function getAuthUserId()
    {
        return Auth::user()->id;
    }

    public function getAllActiveProducts()
    {
            $counter = Product::count();
            $products_counter = collect(['products_counter' => $counter]);
            $products = $this->getProductsFullDetails()->paginate(30);
            return $products_counter->merge($products);
    }

    public function getProductsSearchResult($q, $category_id)
    {
        $query = Product::search($q);
        if ($category_id !== null)
            $query->where('category_id', (int) $category_id);
        $products = $query->paginate(30);
        $products->load('images', 'category');
        return $products;
    }

    public function categorySubcategoryFiltering($filtering)
    {
        $category_id = $filtering->category_id;
        $subcategories_id = $filtering->subcategories_id;
        $q = $filtering->q;
        $type = $filtering->type;
        $invoice_id = $filtering->invoice_id;
        return $this->categorySubcategoryProductsFiltering($category_id, $subcategories_id, $q, $type, $invoice_id);
    }

    public function showProductDetails($product_id)
    {
        return $this->getProductsFullDetails()
            ->withSold()
            ->withCreditsAndWarehouses()
            ->withSupplier()
            ->find($product_id);
    }

    public function getProductRequirements()
    {
        // GETTING CATEGORIES
        $categories = $this->getCategoriesListOrderedByName();
        // GETTING SEASONS
        $seasons = $this->getSeasonsListOrderedByName();
        // MERGING
        $product_requirements = ["categories" => $categories, "seasons" => $seasons];

        return $product_requirements;
    }

    public function storeProduct($request)
    {
        return DB::transaction(function () use ($request) {
            // GENERATE NEW LOCAL CODE AND STORING IT IN THE DATABASE
            $local_code_gen = $this->getLocalCode();
            $local_code = LocalCode::create(['local_code' => $local_code_gen]);

            // ADD THE PRODUCT
            $product_fillable_values = array_merge(
                $request->all(),
                [
                    'local_code_id' => $local_code->id,
                    'created_by' => $this->getAuthUserId()
                ]
            );
            $added_product = Product::create($product_fillable_values);
            $product_id = $added_product->id;

            // UPLOADING IMAGE, CREATING THUMBNAIL AND RETURNING BOTH IMAGES NAMES.
            $images = $this->addProductImages($request->image);

            // STORING THE IMAGES TO DATABASE
            $this->storeImagesToDatabase($images, $product_id, 1);

            // ADDING THE PRODUCT SUBCATEGORIES
            $added_product->subcategories()->attach($request->subcategories_id);

            // ADDING THE PRODUCT SEASONS
            $added_product->seasons()->attach($request->seasons_id);

            // ADDING PRODUCT TO PRODUCTS LOG WITH EMPTY DATA
            $product_log = ProductLog::create(['product_id' => $added_product->id]);

            // STORE ACTION
            event(new ActionHappened('Product add', $added_product, $this->getAuthUserId()));
            return $added_product;
        });
    }

    public function storeNewImageToExistingProduct($request)
    {
        $product_id = $request->product_id;
        $image = $request->file;
        // UPLOADING IMAGE, CREATING THUMBNAIL AND RETURNING BOTH IMAGES NAMES.
        $images = $this->addProductImages($image);

        // STORING THE IMAGES TO DATABASE
        return $this->storeImagesToDatabase($images, $product_id, 0);
    }

    public function removeImageFromExistingProduct($product_id, $image_id)
    {
        return DB::transaction(function () use ($product_id, $image_id) {
            $image = ProductImages::where('id', $image_id)->where('product_id', $product_id)->where('active', 0)->first();
            // DELETE THE IMAGE FROM STORAGE
            Storage::delete([
                'public/uploads/products/main/' . $image->large_image,
                'public/uploads/products/thumbnail/' . $image->thumbnail_image,
            ]);

            // DELETING THE IMAGE FROM THE DATABASE
            $image->delete();

            return $image;
        });
    }


    public function editProduct($product_id)
    {
        $edited_product = $this->getProductsFullDetails()->find($product_id);
        return $edited_product;
    }

    public function updateProduct($request, $product_id)
    {
        return DB::transaction(function () use ($request, $product_id) {
            // FIND THE PRODUCT
            $product = Product::withCreatedByAndUpdatedBy()->find($product_id);

            // CHECK IF HAS IMAGE TO UPDATE
            if ($request->hasFile('image')) {
                // DELETE THE OLD IMAGE FROM STORAGE
                Storage::delete([
                    'public/uploads/products/main/' . $product->activeImage()->first()->large_image,
                    'public/uploads/products/thumbnail/' . $product->activeImage()->first()->thumbnail_image,
                ]);

                // DELETING THE IMAGE FROM THE DATABASE
                $product->activeImage()->delete();

                // UPLOADING IMAGE, CREATING THUMBNAIL AND RETURNING BOTH IMAGES NAMES.
                $images = $this->addProductImages($request->image);

                // STORING THE IMAGES TO DATABASE
                $this->storeImagesToDatabase($images, $product_id, 1);
            }

            // UPDATING PRODUCT SUBCATEGORIES
            $product->subcategories()->detach();
            $product->subcategories()->attach($request->subcategories_id);

            // UPDATING PRODUCT SEASONS
            $product->seasons()->detach();
            $product->seasons()->attach($request->seasons_id);

            // UPDATING THE PRODUCT
            $product_fillable_values = array_merge(
                $request->all(),
                ['updated_by' => $this->getAuthUserId()]
            );
            $product->update($product_fillable_values);

            // STORE ACTION
            event(new ActionHappened('product updated', $product, $this->getAuthUserId()));
            return $product;
        });
    }

    public function deleteProduct($product_id)
    {
        $product = Product::find($product_id);
        if ($product->deletable) {
            $product->delete();
            // DELETING PRODUCT LOG
            $product->productLog->delete();
            // STORE ACTION
            event(new ActionHappened('product deleted', $product, $this->getAuthUserId()));
            return $product;
        }
    }

    public function restoreProduct($product_id)
    {
        // RESTORING THE PRODUCT
        $product = Product::withTrashed()->find($product_id);
        $product->restore();
        // STORE ACTION
        event(new ActionHappened('category restored', $product, $this->getAuthUserId()));
        return $product;
    }

    /* ------------------------------------------------------
     * ----------------- HELPERS FUNCTIONS ------------------
     * ------------------------------------------------------
     */
    private function getProductsFullDetails()
    {
        return Product::withCreatedByAndUpdatedBy()
            ->orderedName()
            ->withCategory()
            ->withSubcategories()
            ->withSeasons()
            ->withImages()
            ->withLocalCode();
    }

    private function addProductImages($image)
    {
        // IMAGE UPLOADING AND GETTING LARGE IMAGE AND THUMBNAIL IMAGE NAMES
        $location = 'products';
        $large_height = trans('validation_standards.images.products.large.height');
        $thumbnail_height = trans('validation_standards.images.products.thumbnail.height');
        return uploadImageWithThumbnail($image, $location, $large_height, $thumbnail_height);
    }

    private function storeImagesToDatabase($images, $product_id, $status)
    {
        $product_images_fillable_values = array_merge(
            $images,
            [
                'product_id' => $product_id,
                'active' => $status
            ]
        );
        return ProductImages::create($product_images_fillable_values);
    }
}
