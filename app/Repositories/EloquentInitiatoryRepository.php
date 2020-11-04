<?php

namespace App\Repositories;

use App\Events\InitiatoryHappened;
use App\Models\Customer\CustomerInitiatoryCredit;
use App\Models\InitiatoryEvent;
use App\Models\Product\ProductCredits;
use App\Models\Product\ProductCreditWarehouses;
use App\Models\Requirements\InitiatoryType;
use App\Models\Supplier\SupplierInitiatoryCredit;
use App\Repositories\Contracts\InitiatoryRepository;
use App\Events\ActionHappened;
use Auth;
use DB;

class EloquentInitiatoryRepository implements InitiatoryRepository {

    private function getAuthUserId() {
        return Auth::user()->id;
    }

    public function productCreditsIndex () {
        return ProductCredits::withProductAndImages()->orderedID()->whereNull('import_invoice_id')->paginate(30);
    }

    public function productCreditsSearch ($q) {
        return ProductCredits::withProductAndImages()
           ->whereNull('import_invoice_id')
           ->where(function ($query) use ($q) {
                $query->where('quantity', 'LIKE', '%'.$q.'%');
                $query->orWhere('purchase_price', 'LIKE', '%'.$q.'%');
                $query->orWhereHas('product', function($query) use ($q) {
                    $query->where('name', 'LIKE', '%'.$q.'%');
                    $query->orWhere('barcode', 'LIKE', '%'.$q.'%');
                });
            })
           ->paginate(30);
    }

    public function productCreditsStore ($request) {
        return DB::transaction(function () use ($request) {
            $products_credit = $request->products_credit;
            $product_credit = null;
            foreach ($products_credit as $product) {
                $product_credit = ProductCredits::create([
                    "import_invoice_id" => null,
                    "product_id"        => $product['product_id'],
                    "quantity"          => $product['quantity'],
                    "package_size"      => $product['package_size'],
                    "purchase_price"    => $product['purchase_price'],
                    "discount"          => $product['discount'],
                    "created_by"        => $this->getAuthUserId(),
                ]);

                ProductCreditWarehouses::create([
                    "product_credit_id"     => $product_credit->id,
                    "warehouse_id"          => $product['warehouse_id'],
                    "created_by"            => $this->getAuthUserId()
                ]);
            }

            // TRANSACTION EVENT STORE
            $initiatory_type_id = InitiatoryType::where('type', 'اضافة رصيد لمنتج')->first()->id;
            event(new InitiatoryHappened([
                'initiatory_type_id'=> $initiatory_type_id,
                'description'       => 'Added new credit to product id: '.$product_credit->id,
                'model_type'        => 'App\Models\Product\ProductCredits',
                'model_id'          => $product_credit->id,
                'created_by'        => $this->getAuthUserId(),
            ]));
            // STORE ACTION
            event(new ActionHappened('initiatory product credit inserted', $product_credit, $this->getAuthUserId()));
            return $products_credit;
        });
    }

    public function productCreditsDelete($product_credit_id) {
        return DB::transaction(function() use ($product_credit_id) {
            // DELETE THE PRODUCT CREDIT INSERTION EVENT
            $initiatory_event = InitiatoryEvent::where('model_type', 'App\Models\Product\ProductCredits')
               ->where('model_id', $product_credit_id)
               ->first();
            if ($initiatory_event)
                $initiatory_event->delete();

            // DELETE THE PRODUCT CREDIT
            $product_credit = ProductCredits::find($product_credit_id);
            $product_credit->delete();

            // STORE ACTION
            event(new ActionHappened('initiatory product credit deleted', $product_credit, $this->getAuthUserId()));
            return $product_credit;
        });
    }

    /********************************
     * ********* SUPPLIER ***********
     *******************************/
    public function supplierCreditsIndex () {
        return SupplierInitiatoryCredit::withSupplier()->paginate(30);
    }

    public function supplierCreditsSearch ($q) {
        return SupplierInitiatoryCredit::withSupplier()
           ->where('amount', 'LIKE', '%'.$q.'%')
           ->orWhereHas('supplier', function ($query) use ($q) {
                $query->where('name', 'LIKE', '%'.$q.'%');
            })
           ->paginate(30);
    }

    public function supplierCreditsStore ($request) {
        return DB::transaction(function () use ($request) {
            // ADDING THE SUPPLIER INITIATORY VALUE
            $supplier_initiatory_credit_values = array_merge(
                $request->all(),
                ['created_by' => $this->getAuthUserId()]
            );
            $supplier_initiatory_credit = SupplierInitiatoryCredit::create($supplier_initiatory_credit_values);

            // TRANSACTION EVENT STORE
            $initiatory_type_id = InitiatoryType::where('type', 'اضافة رصيد لمورد')->first()->id;
            event(new InitiatoryHappened([
                'initiatory_type_id'=> $initiatory_type_id,
                'description'       => 'Added new credit to supplier id: '.$supplier_initiatory_credit->id,
                'model_type'        => 'App\Models\Supplier\SupplierInitiatoryCredit',
                'model_id'          => $supplier_initiatory_credit->id,
                'created_by'        => $this->getAuthUserId(),
            ]));
            // STORE ACTION
            event(new ActionHappened('initiatory supplier credit inserted', $supplier_initiatory_credit, $this->getAuthUserId()));
            return $supplier_initiatory_credit;
        });
    }

    public function supplierCreditsDelete($supplier_credit_id) {
        return DB::transaction(function() use ($supplier_credit_id) {
            // DELETE THE SUPPLIER CREDIT INSERTION EVENT
            $initiatory_event = InitiatoryEvent::where('model_type', 'App\Models\Supplier\SupplierInitiatoryCredit')
               ->where('model_id', $supplier_credit_id)
               ->first();
            if ($initiatory_event)
                $initiatory_event->delete();

            // DELETE THE SUPPLIER CREDIT
            $supplier_credit = SupplierInitiatoryCredit::find($supplier_credit_id);
            $supplier_credit->delete();

            // STORE ACTION
            event(new ActionHappened('initiatory supplier credit deleted', $supplier_credit, $this->getAuthUserId()));
            return $supplier_credit;
        });
    }

    /*****************************************
     * ************** CUSTOMER ***************
     ****************************************/
    public function customerCreditsIndex () {
        return CustomerInitiatoryCredit::withCustomer()->paginate(30);
    }

    public function customerCreditsSearch ($q) {
        return CustomerInitiatoryCredit::withCustomer()
           ->orWhereHas('customer', function ($query) use ($q) {
                $query->where('name', 'LIKE', '%'.$q.'%');
            })
           ->paginate(30);
    }

    public function customerCreditsStore ($request) {
        return DB::transaction(function () use ($request) {
            // ADDING THE CUSTOMER INITIATORY VALUE
            $customer_initiatory_credit_values = array_merge(
                $request->all(),
                ['created_by' => $this->getAuthUserId()]
            );
            $customer_initiatory_credit = CustomerInitiatoryCredit::create($customer_initiatory_credit_values);

            // TRANSACTION EVENT STORE
            $initiatory_type_id = InitiatoryType::where('type', 'اضافة رصيد لفرع عميل')->first()->id;
            event(new InitiatoryHappened([
                'initiatory_type_id'=> $initiatory_type_id,
                'description'       => 'Added new credit to customer id: '.$customer_initiatory_credit->id,
                'model_type'        => 'App\Models\Customer\CustomerInitiatoryCredit',
                'model_id'          => $customer_initiatory_credit->id,
                'created_by'        => $this->getAuthUserId(),
            ]));
            // STORE ACTION
            event(new ActionHappened('initiatory customer credit inserted', $customer_initiatory_credit, $this->getAuthUserId()));
            return $customer_initiatory_credit;
        });
    }

    public function customerCreditsDelete($customer_credit_id) {
        return DB::transaction(function() use ($customer_credit_id) {
            // DELETE THE SUPPLIER CREDIT INSERTION EVENT
            $initiatory_event = InitiatoryEvent::where('model_type', 'App\Models\Customer\CustomerInitiatoryCredit')
               ->where('model_id', $customer_credit_id)
               ->first();
            if ($initiatory_event)
                $initiatory_event->delete();

            // DELETE THE CUSTOMER CREDIT
            $customer_credit = CustomerInitiatoryCredit::find($customer_credit_id);
            $customer_credit->delete();

            // STORE ACTION
            event(new ActionHappened('initiatory customer credit deleted', $customer_credit, $this->getAuthUserId()));
            return $customer_credit;
        });
    }
}
