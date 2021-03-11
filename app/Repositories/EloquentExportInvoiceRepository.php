<?php

namespace App\Repositories;

use App\Cache\RedisAdapter;
use App\Events\TransactionHappened;
use App\Models\Customer\Customer;
use App\Models\Customer\CustomerBranch;
use App\Models\Customer\CustomerContract;
use App\Models\Customer\CustomerPriceList;
use App\Models\Product\ProductCredits;
use App\Models\Product\ProductLog;
use App\Traits\Logic\InvoiceCalculations;
use App\Repositories\Contracts\ExportInvoiceRepository;
use App\Models\Invoices\ExportInvoice;
use App\Traits\Data\GetCategoriesList;
use App\Models\Product\SoldProducts;
use App\Events\ActionHappened;
use App\Traits\Observers\InvoiceObserversTrait;
use Auth;
use DB;

class EloquentExportInvoiceRepository implements ExportInvoiceRepository
{
    use GetCategoriesList, InvoiceCalculations, InvoiceObserversTrait;

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
        // ADDING TAX CONDITION
        $tax_conditions = [];
        if(Auth::user()->hasRole(['tax']))
            $tax_conditions = ['tax' => '1'];

        $sorting = $this->setSorting($request['sort_by'], $request['sort_type']);

        return ExportInvoice::withCustomerBranch()
            ->withSeller()
            ->withCreatedByAndUpdatedBy()
            ->where($tax_conditions)
            ->orderBy($sorting['sort_by'], $sorting['sort_type'])
            ->paginate(30);
    }

    public function getExportInvoicesSearchResult($request)
    {
        // ADDING TAX CONDITION
        $tax_conditions = [];
        if(Auth::user()->hasRole(['tax']))
            $tax_conditions = ['tax' => '1'];

        $sorting = $this->setSorting($request['sort_by'], $request['sort_type']);
        $q = $request['query'];

        return ExportInvoice::withCustomerBranch()->withSeller()->withCreatedByAndUpdatedBy()
            ->where($tax_conditions)
            ->where(function ($query) use ($tax_conditions, $q) {
                $query->where('name', 'LIKE', '%' . $q . '%');
                $query->orWhere('number', 'LIKE', '%' . $q . '%');
                $query->orWhere('date', 'LIKE', '%' . $q . '%');
                // SEARCH FOR CUSTOMER BRANCH
                $query->orWhereHas('customerBranch', function ($query) use ($q) {
                    $query->where('address', 'LIKE', '%' . $q . '%');
                });
                // SEARCH BY CUSTOMER NAME
                $query->orWhereHas('customerBranch.customer', function ($query) use ($q) {
                    $query->where('name', 'LIKE', '%' . $q . '%');
                });
            })
            ->orderBy($sorting['sort_by'], $sorting['sort_type'])
            ->paginate(30);
    }

    public function showExportInvoiceDetails($export_invoice_id)
    {
        // ADDING TAX CONDITION
        $tax_conditions = [];
        if(Auth::user()->hasRole(['tax']))
            $tax_conditions = ['tax' => '1'];

        $user = Auth::user();
        if ($user->hasRole(['super_admin', 'accountant', 'tax'])) {
            $export_invoice = ExportInvoice::withCustomerBranch()
                ->withSeller()
                ->withSoldProductsImages()
                ->withAttachedFiles()
                ->where($tax_conditions)
                ->find($export_invoice_id);
        } else {
            $export_invoice = ExportInvoice::withCustomerBranch()
                ->withSoldProductsImages()
                ->withCustomerBranch()
                ->withAttachedFiles()
                ->where('seller_id', $this->getAuthUserId())
                ->where($tax_conditions)
                ->find($export_invoice_id);
        }

        // EDIT NAME AND BARCODE FROM CUSTOMER LIST IF EXISTS
        $this->customerListEditData($export_invoice, 'selling');

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
            $user = Auth::user();
            if ($user->hasRole(['super_admin', 'accountant', 'tax'])) {
                $invoice = ExportInvoice::notApproved()->find($request->invoice_id);
            } else {
                $invoice = ExportInvoice::notApproved()->where('seller_id', $this->getAuthUserId())->find($request->invoice_id);
            }

            if($invoice)
                // ADDING SOLD PRODUCT
                SoldProducts::create([
                    "sequence_number" => $request->sequence_number,
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
            })
            ->orderBy($sorting['sort_by'], $sorting['sort_type'])
            ->paginate(30);
    }

    public function updateProductSellingPriceInInvoice($product_row_id, $new_price) {
        return DB::transaction(function() use ($product_row_id, $new_price) {
            $product = SoldProducts::where('id', $product_row_id)->first();
            $invoice = ExportInvoice::notApproved()->where('id', $product->export_invoice_id)->first();
            if ($invoice) {
                $product->sold_price = $new_price;
                $product->save();
                // ADD PRODUCT NET PRICE TO INVOICE
                $this->calculateSingleExportInvoice($invoice->id);

                // NEW INVOICE AFTER OBSERVERS
                return $invoice;
            }
        });
    }

    // REPORTS
    public function reportProfit($item_id)
    {
        $invoice = ExportInvoice::withSoldProductsImages()->find($item_id);
        $invoice_profit_total = 0;
        foreach($invoice->soldProducts as $product) {
            // INVOICE DATA
            $invoice_product_id = $product->product_id;
            $invoice_product_quantity = $product->quantity;
            $invoice_product_net_price = $product->sold_price;

            // LOGGED PRODUCT DATA
            $product_log = ProductCredits::where('product_id', $invoice_product_id)
                ->where('purchase_price', '>', 0)
                ->whereHas('importInvoice', function($query) {
                    $query->where('approve', 1);
                })
                ->orderBy('id', 'DESC')->first();
            if(!$product_log) {
                $product_log = ProductLog::where('product_id', $invoice_product_id)->where('average_purchase_price', '>', 0)->first();
                $product_log_purchase_price = $product_log->average_purchase_price;
            } else {
                $product_log_purchase_price = $product_log->purchase_price;
            }
            
            if($product_log->discount > 0)
                $product_log_purchase_price = $product_log_purchase_price - ($product_log_purchase_price * ($product_log->discount/100));

            // PROFIT
            $invoice_profit_total += ($invoice_product_net_price - $product_log_purchase_price) * $invoice_product_quantity;
        }
        // INVOICE DISCOUNT IF EXISTS
        if($invoice->discount > 0)
            $invoice_profit_total = $invoice_profit_total - ($invoice_profit_total * ($invoice->discount/100));

        // CUSTOMER CONTRACT DISCOUNT
        $customer_id = $invoice->customerBranch->customer->id;
        $customer_contract = CustomerContract::where('customer_id', $customer_id)
            ->where('year', date('Y', strtotime($invoice->date)))
            ->orderBy('id',  'DESC')
            ->first();

        $invoice_net_profit = null;
        $contract_year = null;
        $discount_percentage = null;
        if ($customer_contract) {
            $discount_percentage = $customer_contract->discount;
            $invoice_net_profit = $invoice_profit_total * ((100 - $discount_percentage) / 100);
            $contract_year = $customer_contract->year;
        }
        return [
            'invoice_profit' => round($invoice_profit_total,2),
            'contract_discount' => [
                'contract_year' => $contract_year,
                'discount_percentage' => $discount_percentage,
                'invoice_net_profit' => round($invoice_net_profit, 2),
            ],
        ];
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
