<?php

namespace App\Repositories;

use App\Cache\RedisAdapter;
use App\Events\TransactionHappened;
use App\Models\Customer\CustomerBranch;
use App\Traits\Logic\InvoiceCalculations;
use App\Repositories\Contracts\ExportInvoiceRepository;
use App\Models\Invoices\ExportInvoice;
use App\Traits\Data\GetCategoriesList;
use App\Models\Product\SoldProducts;
use App\Events\ActionHappened;
use Auth;
use DB;

class EloquentExportInvoiceRepository implements ExportInvoiceRepository
{
    use GetCategoriesList, InvoiceCalculations;

    protected $cache;

    public function __construct()
    {
        $this->cache = new RedisAdapter();
    }

    private function getAuthUserId()
    {
        return Auth::user()->id;
    }

    public function getAllActiveExportInvoices($request)
    {
        $sorting = $this->setSorting($request['sort_by'], $request['sort_type']);
        $redis_key = 'export_invoices:' . $request['page'] . ':sort_by:' . $request['sort_by'] . ':sort_type:' . $request['sort_type'];

        // RETURN DATA IF IN CACHE AND IF NOT THEN RE-CACHE IT
        $invoices = $this->cache->remember($redis_key, function () use ($sorting) {
            return json_encode(ExportInvoice::withCustomerBranch()
                ->withSeller()
                ->withCreatedByAndUpdatedBy()
                ->orderBy($sorting['sort_by'], $sorting['sort_type'])
                ->paginate(30));
        }, config('constants.cache_expiry_minutes_small'));
        return json_decode($invoices);
    }

    public function getExportInvoicesSearchResult($request)
    {
        $sorting = $this->setSorting($request['sort_by'], $request['sort_type']);
        $q = $request['query'];

        return ExportInvoice::withCustomerBranch()->withSeller()->withCreatedByAndUpdatedBy()->orderedName()
            ->where('name', 'LIKE', '%' . $q . '%')
            ->orWhere('number', 'LIKE', '%' . $q . '%')
            ->orWhere('date', 'LIKE', '%' . $q . '%')
            // SEARCH FOR CUSTOMER BRANCH
            ->orWhereHas('customerBranch', function ($query) use ($q) {
                $query->where('address', 'LIKE', '%' . $q . '%');
            })
            // SEARCH BY CUSTOMER NAME
            ->orWhereHas('customerBranch.customer', function ($query) use ($q) {
                $query->where('name', 'LIKE', '%' . $q . '%');
            })
            // SEARCH BY SELLER NAME
            ->orWhereHas('seller', function ($query) use ($q) {
                $query->where('name', 'LIKE', '%' . $q . '%');
            })
            ->orderBy($sorting['sort_by'], $sorting['sort_type'])
            ->paginate(30);
    }

    public function showExportInvoiceDetails($export_invoice_id)
    {
        $user = Auth::user();
        if ($user->hasRole(['super_admin'])) {
            $export_invoice = ExportInvoice::withCustomerBranch()
                ->withSeller()
                ->withSoldProductsImages()
                ->find($export_invoice_id);
        } else {
            $export_invoice = ExportInvoice::withCustomerBranch()
                ->withSoldProductsImages()
                ->withCustomerBranch()
                ->whereHas('customerBranch.sellers', function ($query) {
                    $query->where('seller_id', $this->getAuthUserId());
                })
                ->find($export_invoice_id);
        }

        return $export_invoice;
    }

    public function storeInvoice($request)
    {
        // DELETE CACHED INVOICES
        $this->cache->forgetByPattern('export_invoices:*');
        return DB::transaction(function () use ($request) {
            // STORING THE INVOICE MAIN DATA
            $export_invoice_fillable_values = array_merge(
                $request->all(),
                ['created_by' => $this->getAuthUserId()]
            );
            $stored_export_invoice = ExportInvoice::create($export_invoice_fillable_values);

            // STORE ACTION
            event(new ActionHappened('export invoice add: ', $stored_export_invoice->id, $this->getAuthUserId()));
            return $stored_export_invoice;
        });
    }

    public function storeInvoiceProducts($request)
    {
        return DB::transaction(function () use ($request) {
            $invoice = ExportInvoice::notApproved()->find($request->invoice_id);

            // ADDING SOLD PRODUCT
            SoldProducts::create([
                "export_invoice_id" => $invoice->id,
                "product_id" => $request->product_id,
                "quantity" => $request->quantity,
                "sold_price" => $request->sold_price,
                "discount" => $request->discount,
                "created_by" => $this->getAuthUserId(),
            ]);

            return $invoice;
        });
    }

    public function removeProductFromInvoice($invoice_id, $sold_product_id)
    {
        $product = SoldProducts::where('id', $sold_product_id)
            ->whereHas('exportInvoice', function ($query) use ($invoice_id) {
                $query->where('approve', 0);
            })
            ->first();
        if ($product)
            $product->delete();
        $invoice = ExportInvoice::withCustomerBranch()
            ->withSoldProductsImages()
            ->withCreatedByAndUpdatedBy()
            ->find($invoice_id);

        return $invoice;
    }

    public function editExportInvoice($export_invoice_id)
    {
        return ExportInvoice::where('id', $export_invoice_id)
            ->where('approve', 0)
            ->withCreatedByAndUpdatedBy()
            ->first();
    }

    public function updateExportInvoice($request, $export_invoice_id)
    {
        // DELETE CACHED INVOICES
        $this->cache->forgetByPattern('export_invoices:*');
        return DB::transaction(function () use ($request, $export_invoice_id) {
            // UPDATING THE INVOICE MAIN DATA
            $invoice = ExportInvoice::where('id', $export_invoice_id)->where('approve', 0)->first();
            $export_invoice_fillable_values = array_merge(
                $request->invoice_data,
                ['updated_by' => $this->getAuthUserId()]
            );
            $invoice->update($export_invoice_fillable_values);

            // UPDATE ACTION
            event(new ActionHappened('export invoice updated: ', $invoice->id, $this->getAuthUserId()));
            return $invoice;
        });
    }

    public function deleteExportInvoice($export_invoice_id)
    {
        // DELETE CACHED INVOICES
        $this->cache->forgetByPattern('export_invoices:*');
        return DB::transaction(function () use ($export_invoice_id) {
            // DELETING EXPORT INVOICE
            $export_invoice = ExportInvoice::where('approve', 0)->find($export_invoice_id);
            if ($export_invoice) {
                $export_invoice->delete();

                // DELETING SOLD QUANTITIES
                $export_invoice->soldProducts()->delete();

                // STORE ACTION
                event(new ActionHappened('export invoice deleted: ', $export_invoice->id, $this->getAuthUserId()));
                return $export_invoice;
            } else return;
        });
    }

    public function restoreExportInvoice($export_invoice_id)
    {
        // DELETE CACHED INVOICES
        $this->cache->forgetByPattern('export_invoices:*');
        return DB::transaction(function () use ($export_invoice_id) {
            // RESTORING EXPORT INVOICE
            $export_invoice = ExportInvoice::withTrashed()->find($export_invoice_id);
            $export_invoice->restore();

            // RESTORING SOLD QUANTITIES
            $export_invoice->soldProducts()->restore();

            // STORE ACTION
            event(new ActionHappened('export invoice restored: ', $export_invoice->id, $this->getAuthUserId()));
            return $export_invoice;
        });
    }

    public function approveExportInvoice($export_invoice_id)
    {
        // DELETE CACHED INVOICES
        $this->cache->forgetByPattern('export_invoices:*');
        // CHANGE THE EXPORT INVOICE TO APPROVED
        $export_invoice = ExportInvoice::withSoldProductsImages()->withCustomerBranch()->withSeller()->find($export_invoice_id);
        $export_invoice->approve = 1;
        $export_invoice->save();
        event(new ActionHappened('export invoice approve: ', $export_invoice->id, $this->getAuthUserId()));
        return $export_invoice;
    }

    public function invoicesPerUser($request)
    {
        $sorting = $this->setSorting($request['sort_by'], $request['sort_type']);
        return ExportInvoice::where('seller_id', $this->getAuthUserId())
            ->withCustomerBranch()
            ->withSeller()
            ->withCreatedByAndUpdatedBy()
            ->orderBy($sorting['sort_by'], $sorting['sort_type'])
            ->paginate(30);
    }

    public function invoicesPerUserSearch($request)
    {
        $sorting = $this->setSorting($request['sort_by'], $request['sort_type']);
        $q = $request['query'];

        return ExportInvoice::withCustomerBranch()->withSeller()->withCreatedByAndUpdatedBy()->orderedName()
            ->where('seller_id', $this->getAuthUserId())
            ->where(function ($w_query) use ($q) {
                $w_query->where('name', 'LIKE', '%' . $q . '%');
                $w_query->orWhere('number', 'LIKE', '%' . $q . '%');
                $w_query->orWhere('date', 'LIKE', '%' . $q . '%');
                // SEARCH FOR CUSTOMER BRANCH
                $w_query->orWhereHas('customerBranch', function ($query) use ($q) {
                    $query->where('address', 'LIKE', '%' . $q . '%');
                });
                // SEARCH BY CUSTOMER NAME
                $w_query->orWhereHas('customerBranch.customer', function ($query) use ($q) {
                    $query->where('name', 'LIKE', '%' . $q . '%');
                });
                // SEARCH BY SELLER NAME
                $w_query->orWhereHas('seller', function ($query) use ($q) {
                    $query->where('name', 'LIKE', '%' . $q . '%');
                });
            })
            ->orderBy($sorting['sort_by'], $sorting['sort_type'])
            ->paginate(30);
    }


    /*
     * **************************************************
     * ********** PRIVATE HELPERS FUNCTIONS *************
     * **************************************************
     */
    private function setSorting($sort_by, $sort_type)
    {
        $sorting = ['sort_by' => 'id', 'sort_type' => 'DESC'];
        if ($sort_by !== null)
            $sorting['sort_by'] = $sort_by;
        if ($sort_type !== null)
            $sorting['sort_type'] = $sort_type;
        return $sorting;
    }
}
