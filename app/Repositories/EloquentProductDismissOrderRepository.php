<?php

namespace App\Repositories;

use App\Models\ProductDismissOrder\ProductDismissOrderProducts;
use App\Repositories\Contracts\ProductDismissOrderRepository;
use App\Models\ProductDismissOrder\ProductDismissOrder;
use App\Cache\RedisAdapter;
use App\Events\ActionHappened;
use Auth;
use DB;

class EloquentProductDismissOrderRepository implements ProductDismissOrderRepository {
    protected $cache;
    public function __construct()
    {
        $this->cache = new RedisAdapter();
    }

    private function getAuthUserId() {
        return Auth::user()->id;
    }

    public function index ($request) {
        $sorting = $this->setSorting($request['sort_by'], $request['sort_type']);
        $redis_key = 'dismissal_order:'.$request['page'].':sort_by:'.$request['sort_by'].':sort_type:'.$request['sort_type'];

        // RETURN DATA IF IN CACHE AND IF NOT THEN RE-CACHE IT
        $dismiss_order = $this->cache->remember($redis_key, function () use ($sorting) {
            return json_encode(ProductDismissOrder::orderBy($sorting['sort_by'], $sorting['sort_type'])->paginate(30));
        }, config('constants.cache_expiry_minutes_small'));
        return json_decode($dismiss_order);
    }

    public function search ($request) {
        $sorting = $this->setSorting($request['sort_by'], $request['sort_type']);
        $q = $request['query'];
        return ProductDismissOrder::orderBy($sorting['sort_by'], $sorting['sort_type'])
            ->where('title', 'LIKE', '%'.$q.'%')
            ->paginate(30);
    }

    public function show ($product_dismiss_order_id) {
        return ProductDismissOrder::withProductDismissOrderProducts()->find($product_dismiss_order_id);
    }

    public function store ($request) {
        // DELETE CACHED PRODUCTS DISMISSAL ORDERS
        $this->cache->forgetByPattern('dismissal_order:*');
        return DB::transaction(function () use ($request) {
            // STORE THE PRODUCT DISMISS ORDER
            $stored_product_dismiss_order = ProductDismissOrder::create([
                "title" => $request->title,
                "notes" => $request->notes,
                "created_by" => $this->getAuthUserId(),
            ]);

            // STORE THE DISMISSED PRODUCTS
            $this->storeDismissedProducts($stored_product_dismiss_order->id, $request->products);

            // STORE ACTION
            event(new ActionHappened('product dismiss order add', $stored_product_dismiss_order, $this->getAuthUserId()));
            return $stored_product_dismiss_order;
        });
    }

    public function delete ($product_dismiss_order_id) {
        // DELETE CACHED PRODUCTS DISMISSAL ORDERS
        $this->cache->forgetByPattern('dismissal_order:*');
        // GET PRODUCT DISMISS ORDER (NOT APPROVED)
        $product_dismiss_order = ProductDismissOrder::where('approve', 0)->find($product_dismiss_order_id);

        // DELETE THE ORDER
        $product_dismiss_order->delete();

        // DELETE THE DISMISSED PRODUCTS
        $product_dismiss_order->productDismissOrderProducts()->delete();

        // SAVING THE EVENT
        event(new ActionHappened('product dismiss order deleted', $product_dismiss_order, $this->getAuthUserId()));
        return $product_dismiss_order;
    }

    public function approve ($product_dismiss_order_id) {
        // DELETE CACHED PRODUCTS DISMISSAL ORDERS
        $this->cache->forgetByPattern('dismissal_order:*');
        $product_dismiss_order = ProductDismissOrder::where('approve', 0)->find($product_dismiss_order_id);
        $product_dismiss_order->approve = 1;
        $product_dismiss_order->save();

        event(new ActionHappened('product dismiss order approve', $product_dismiss_order, $this->getAuthUserId()));
        return $product_dismiss_order;
    }

    /*
     * **************************************************
     * ********** PRIVATE HELPERS FUNCTIONS *************
     * **************************************************
     */
    private function storeDismissedProducts ($dismiss_order_id, $products) {
        $counter = count($products);
        for ($i = 0; $i < $counter; $i++) {
            ProductDismissOrderProducts::create([
                "product_dismiss_order_id" => $dismiss_order_id,
                "product_id" => $products[$i]['product_id'],
                "reason_id" => $products[$i]['reason_id'],
                "quantity" => $products[$i]['quantity'],
            ]);
        }
        return;
    }

    /**
     * @param $sort_by
     * @param $sort_type
     * @return array
     */
    private function setSorting ($sort_by, $sort_type) {
        $sorting = ['sort_by' => 'id', 'sort_type' => 'DESC'];
        if ($sort_by !== null)
            $sorting['sort_by'] = $sort_by;
        if ($sort_type !== null)
            $sorting['sort_type'] = $sort_type;
        return $sorting;
    }
}