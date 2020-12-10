<?php

namespace App\Repositories;

use App\Cache\RedisAdapter;
use App\Events\ActionHappened;
use App\Models\Refund\RefundProduct;
use App\Repositories\Contracts\RefundRepository;
use App\Models\Refund\Refund;
use App\Traits\Logic\InvoiceCalculations;
use Auth;
use DB;

class EloquentRefundRepository implements RefundRepository {
    use InvoiceCalculations;

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
        $type = $request['type'];

        // ADDING TAX CONDITION
        $tax_conditions = [];
        if(Auth::user()->hasRole(['tax']))
            $tax_conditions = ['tax' => '1'];

        if ($type === 'in')
            $result = Refund::withCustomer()
                ->withAssignedUser()
                ->where('type', $type)
                ->where($tax_conditions)
                ->withCustomerSellers()
                ->orderBy($sorting['sort_by'], $sorting['sort_type'])
                ->paginate(30);
        else
            $result = Refund::withSupplier()
                ->where('type', $type)
                ->where($tax_conditions)
                ->orderBy($sorting['sort_by'], $sorting['sort_type'])
                ->paginate(30);

        return $result;
    }

    public function search ($request) {
        // ADDING TAX CONDITION
        $tax_conditions = [];
        if(Auth::user()->hasRole(['tax']))
            $tax_conditions = ['tax' => '1'];

        $sorting = $this->setSorting($request['sort_by'], $request['sort_type']);
        $type = $request['type'];
        $q = $request['query'];

        if ($type === 'in')
            return Refund::withCustomer()
                ->withAssignedUser()
                ->where('type', $type)
                ->where($tax_conditions)
                ->where(function ($query) use ($q) {
                    $query->where('title', 'LIKE', '%' . $q . '%');
                    $query->orWhere('number', 'LIKE', '%' . $q . '%');
                })
                ->withCustomerSellers()
                ->orderBy($sorting['sort_by'], $sorting['sort_type'])
                ->paginate(30);
        else
            return Refund::withSupplier()
                ->where('type', $type)
                ->where($tax_conditions)
                ->where(function ($query) use ($q) {
                    $query->where('title', 'LIKE', '%' . $q . '%');
                    $query->orWhere('number', 'LIKE', '%' . $q . '%');
                })
                ->orderBy($sorting['sort_by'], $sorting['sort_type'])
                ->paginate(30);
    }

    public function show ($refund_id) {
        // ADDING TAX CONDITION
        $tax_conditions = [];
        if(Auth::user()->hasRole(['tax']))
            $tax_conditions = ['tax' => '1'];

        $refund = Refund::where($tax_conditions)->find($refund_id);
        if ($refund->type === 'in') {
            $invoice = Refund::withRefundedProductsImages()->withCustomer()->find($refund->id);
            $this->customerListEditData($invoice, 'refund_in');
        } else
            $invoice = Refund::withRefundedProductsImages()->withSupplier()->find($refund->id);

        return $invoice;
    }

    public function storeRefundOrder ($request) {
        return DB::transaction(function () use ($request) {
            // STORING THE REFUND ORDER
            $refund_order = Refund::create([
                'title' => $request->title,
                'assigned_user_id' => $request->assigned_user_id,
                'number' => $request->number,
                'model_id' => $request->model_id,
                'notes' => $request->notes,
                'tax' => $request->tax,
                'discount' => $request->discount,
                'date' => $request->date,
                'type' => $request->type,
                'created_by' => $this->getAuthUserId(),
            ]);

            // STORE ACTION
            event(new ActionHappened('refund order add:', $refund_order->id, $this->getAuthUserId()));
            return $refund_order;
        });
    }

    public function storeRefundOrderProducts ($request) {
        return DB::transaction(function () use ($request) {
            $refund_order = Refund::notApproved()->find($request->refund_id);

            // ADDING SOLD PRODUCT
            RefundProduct::create([
                'refund_id' => $refund_order->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'price' => $request->price,
                'discount' => $request->discount,
                'valid' => $request->valid,
            ]);

            return $refund_order;
        });
    }

    public function edit ($refund_id) {
        return Refund::notApproved()->find($refund_id);
    }

    public function update ($request, $refund_id) {
        return DB::transaction(function () use ($request, $refund_id) {
            // UPDATING THE INVOICE MAIN DATA
            $refund = Refund::notApproved()->find($refund_id);
            $refund_fillable_values = array_merge(
                $request->all(),
                [ 'updated_by'    => $this->getAuthUserId() ]
            );
            $refund->update($refund_fillable_values);

            // UPDATE ACTION
            event(new ActionHappened('refund order updated: ', $refund->id, $this->getAuthUserId()));
            return $refund;
        });
    }

    public function removeProductFromRefundOrder($refund_id, $product_id) {
        $refunded_product = RefundProduct::where('id', $product_id)
           ->whereHas('refundOrder', function ($query) use ($refund_id) {
                $query->where('approve', 0);
            })
            ->first();
        $refunded_product->delete();
        return Refund::withCustomerBranch()->withRefundedProducts()->find($refund_id);
    }

    public function approve($refund_id) {
        $this->cache->forgetByPattern('refund_invoices:*');
        // APPROVE REFUND
        $refund = Refund::withCustomerBranch()->find($refund_id);
        $refund->approve = 1;
        $refund->save();
        event(new ActionHappened('refund order restored:', $refund->id, $this->getAuthUserId()));
        return $refund;
    }

    public function delete ($refund_id) {
        $this->cache->forgetByPattern('refund_invoices:*');
        $refund = Refund::notApproved()->find($refund_id);
        $refund->delete();
        event(new ActionHappened('refund order deleted:', $refund->id, $this->getAuthUserId()));
        return $refund;
    }

    /*
     * **************************************************
     * ********** PRIVATE HELPERS FUNCTIONS *************
     * **************************************************
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
