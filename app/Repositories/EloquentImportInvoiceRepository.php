<?php

namespace App\Repositories;

use App\Cache\RedisAdapter;
use App\Events\ActionHappened;
use App\Models\Product\ProductCredits;
use App\Models\Product\ProductCreditWarehouses;
use App\Repositories\Contracts\ImportInvoiceRepository;
use App\Models\Invoices\ImportInvoice;
use App\Traits\Data\GetCategoriesList;
use App\Traits\Data\GetSuppliersList;
use App\Traits\Logic\InvoiceCalculations;
use Auth;
use DB;

class EloquentImportInvoiceRepository implements ImportInvoiceRepository {
    use GetSuppliersList, GetCategoriesList, InvoiceCalculations;
    protected $cache;
    public function __construct()
    {
        $this->cache = new RedisAdapter();
    }

    private function getAuthUserId() {
        return Auth::user() -> id;
    }

    public function getAllActiveImportInvoices ($request) {
        $sorting = $this->setSorting($request['sort_by'], $request['sort_type']);

        $redis_key = 'import_invoices:'.$request['page'].':sort_by:'.$request['sort_by'].':sort_type:'.$request['sort_type'];

        // RETURN DATA IF IN CACHE AND IF NOT THEN RE-CACHE IT
        $invoices = $this->cache->remember($redis_key, function () use ($sorting) {
            $result = ImportInvoice::withSupplier() -> withCreatedByAndUpdatedBy()
                ->orderBy($sorting['sort_by'], $sorting['sort_type'])
                ->paginate(30);

            return json_encode($result);
        }, config('constants.cache_expiry_minutes_small'));
        return json_decode($invoices);

    }

    public function getImportInvoicesSearchResult ($request) {
        $sorting = $this->setSorting($request['sort_by'], $request['sort_type']);
        $q = $request['query'];

        return ImportInvoice::withSupplier()->withCreatedByAndUpdatedBy()
            ->where('name', 'LIKE', '%'.$q.'%')
            ->orWhere('number', 'LIKE', '%'.$q.'%')
            ->orWhere('date', 'LIKE', '%'.$q.'%')
            // SEARCH FOR SUPPLIER NAME
            ->orWhereHas('supplier', function ($query) use ($q) {
                $query->where('name', 'LIKE', '%'.$q.'%');
            })
            ->orderBy($sorting['sort_by'], $sorting['sort_type'])
            ->paginate(30);
    }

    public function showImportInvoiceDetails ($import_invoice_id) {
        return ImportInvoice::withProductCredits() -> withSupplier() -> find($import_invoice_id);
    }

    public function storeInvoice ($request) {
        // DELETE CACHED INVOICES
        $this->cache->forgetByPattern('import_invoices:*');
        return DB::transaction(function () use ($request) {
            // STORING THE INVOICE MAIN DATA
            $import_invoice_fillable_values = array_merge(
                $request -> all(),
                ['created_by' => $this->getAuthUserId()]
            );
            $stored_import_invoice = ImportInvoice::create($import_invoice_fillable_values);

            // STORE ACTION
            event(new ActionHappened('import invoice add: ', $stored_import_invoice -> id, $this -> getAuthUserId()));
            return $stored_import_invoice;
        });
    }

    public function storeInvoiceProducts ($request) {
        return DB::transaction(function () use ($request) {
            $invoice = ImportInvoice::notApproved() -> find($request -> invoice_id);

            // ADDING SOLD PRODUCT
            $product_credit = ProductCredits::create([
                "import_invoice_id" => $invoice -> id,
                "product_id"        => $request -> product_id,
                "quantity"          => $request -> quantity,
                "package_size"      => $request -> package_size,
                "purchase_price"    => $request -> purchase_price,
                "discount"          => $request -> discount,
                "created_by"        => $this -> getAuthUserId(),
            ]);

            // ADD PRODUCT TO WAREHOUSES
            ProductCreditWarehouses::create([
                "product_credit_id"     => $product_credit -> id,
                "warehouse_id"          => $request -> warehouse_id,
                "created_by"            => $this -> getAuthUserId()
            ]);

            // NEW INVOICE AFTER OBSERVERS
            $new_invoice = ImportInvoice::withProductCredits()->find($invoice -> id);
            return $new_invoice;
        });
    }

    public function removeProductFromImportInvoice($invoice_id, $purchase_product_id) {
        $product = ProductCredits::where('id', $purchase_product_id)
            -> whereHas('importInvoice', function ($query) use ($invoice_id) {
                $query -> where('approve', 0);
            })->first();
        if ($product)
            $product -> delete();

        // NEW INVOICE AFTER OBSERVERS
        $new_invoice = ImportInvoice::withProductCredits()->find($invoice_id);
        return $new_invoice;
    }

    public function editImportInvoice ($import_invoice_id) {
        return ImportInvoice::where('id', $import_invoice_id)
            -> where('approve', 0)
            -> with('supplier')
            -> withProductCredits()
            -> first();
    }

    public function updateImportInvoice($request, $import_invoice_id) {
        // DELETE CACHED INVOICES
        $this->cache->forgetByPattern('import_invoices:*');
        return DB::transaction(function () use ($request, $import_invoice_id) {
            // UPDATING THE INVOICE MAIN DATA
            $invoice = ImportInvoice::where('id', $import_invoice_id) -> where('approve', 0) -> first();
            $import_invoice_fillable_values = array_merge(
                $request -> invoice_data,
                [ 'updated_by'    => $this->getAuthUserId() ]
            );
            $invoice -> update($import_invoice_fillable_values);

            // UPDATE ACTION
            event(new ActionHappened('import invoice updated: ', $invoice -> id, $this -> getAuthUserId()));
            return $invoice;
        });
    }

    public function deleteImportInvoice ($import_invoice_id) {
        // DELETE CACHED INVOICES
        $this->cache->forgetByPattern('import_invoices:*');
        return DB::transaction(function () use ($import_invoice_id) {
            $import_invoice = ImportInvoice::where('approve', 0) -> find($import_invoice_id);
            if ($import_invoice) {
                // DELETING IMPORT INVOICE
                $import_invoice -> delete();

                // DELETING THE QUANTITIES
                $import_invoice -> productCredits() -> delete();

                // STORE ACTION
                event(new ActionHappened('import invoice deleted: ', $import_invoice -> id, $this -> getAuthUserId()));
                return $import_invoice;
            } else return;
        });
    }

    public function restoreImportInvoice ($import_invoice_id) {
        // DELETE CACHED INVOICES
        $this->cache->forgetByPattern('import_invoices:*');
        return DB::transaction(function () use ($import_invoice_id) {
            // RESTORING IMPORT INVOICE
            $import_invoice = ImportInvoice::withTrashed() -> find($import_invoice_id);
            $import_invoice -> restore();

            // RESTORING QUANTITIES
            $import_invoice -> productCredits() -> restore();

            // STORE ACTION
            event(new ActionHappened('import invoice restored: ', $import_invoice -> id, $this -> getAuthUserId()));
            return $import_invoice;
        });
    }

    public function approveImportInvoice($import_invoice_id) {
        // DELETE CACHED INVOICES
        $this->cache->forgetByPattern('import_invoices:*');
        $import_invoice = ImportInvoice::withProductCredits() -> withSupplier() -> find($import_invoice_id);
        $import_invoice -> approve = 1;
        $import_invoice -> save();
        event(new ActionHappened('import invoice approve: ', $import_invoice -> id, $this -> getAuthUserId()));
        return $import_invoice;
    }

    public function getImportInvoiceRequirements() {
        // GETTING SUPPLIERS
        return [
            "suppliers" => $this -> getSuppliersListOrderedByName(),
            "product_categories" => $this -> getCategoriesListOrderedByName()
        ];
    }

    public function updateProductPurchasePriceInInvoice($request, $invoice_id, $purchase_product_id) {
        $product = ProductCredits::where('id', $purchase_product_id)
            -> whereHas('importInvoice', function ($query) use ($invoice_id) {
                $query -> where('approve', 0);
            })->first();
        if ($product) {
            $product->purchase_price = $request->data['purchase_price'];
            $product->save();
        }

        // NEW INVOICE AFTER OBSERVERS
        $new_invoice = ImportInvoice::withProductCredits()->find($invoice_id);
        return $new_invoice;
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
