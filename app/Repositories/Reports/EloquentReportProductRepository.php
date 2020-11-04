<?php

namespace App\Repositories\Reports;

use App\Cache\RedisAdapter;
use App\Events\ActionHappened;
use App\Exports\ProductCreditExport;
use App\Exports\SupplierProductCreditExport;
use App\Models\Invoices\ExportInvoice;
use App\Models\Product\Product;
use App\Models\Product\ProductCredits;
use App\Models\Product\ProductLog;
use App\Models\Product\SoldProducts;
use App\Models\ProductDismissOrder\ProductDismissOrderProducts;
use App\Models\Refund\RefundProduct;
use App\Repositories\Reports\Contracts\ReportProductRepository;
use Excel;
use Auth;
use DB;

class EloquentReportProductRepository implements ReportProductRepository {
    protected $cache;
    public function __construct()
    {
        $this->cache = new RedisAdapter();
    }

    private function getAuthUserId() {
        return Auth::user()->id;
    }

    public function exportProductsCredit ($request) {
        $categories_id = $request->categories_id;

        // STORE ACTION
        event(new ActionHappened('report generate', 'product credits excel export', $this->getAuthUserId()));
        return Excel::download(new ProductCreditExport($categories_id), date('Y-mm-dd').'.xlsx');
    }

    public function productHistory ($request) {
        return DB::transaction(function () use ($request) {
            /*
            * The product history is retrieved from these tables:
            * ['product_credits', 'product_dismiss_order_products', 'refund_products', 'sold_products']
            */
            $product_id = $request->product_id;
            $from = $request->from_date;
            $to = $request->to_date;

            // GET PRODUCT HISTORY FROM `product_credits`  AND INITIATORY CREDIT
            $product_credits = ProductCredits::where('product_id', $product_id)
               ->where(function ($q) use ($from, $to) {
                    $q->whereHas('importInvoice', function ($query) use ($from, $to) {
                        $query->whereBetween('date', [$from, $to]);
                    });
                    $q->orWhere(function ($qq) use ($from, $to) {
                        $qq->whereNull('import_invoice_id');
                        $qq->whereBetween('created_at', [$from, $to]);
                    });
                })
               ->get();


            // GET PRODUCT HISTORY FROM `sold_products` TABLE
            $sold_products = SoldProducts::where('product_id', $product_id)
               ->whereHas('exportInvoice', function ($query) use ($from, $to) {
                    $query->whereBetween('date', [$from, $to]);
                })
               ->get();

            // GET PRODUCT HISTORY FROM `refund_products` TABLE
            $refund_products = RefundProduct::where('product_id', $product_id)
               ->whereHas('refundOrder', function ($query) use ($from, $to) {
                    $query->whereBetween('date', [$from, $to]);
                })
               ->get();

            // GET PRODUCT HISTORY FROM `product_dismiss_order_products` TABLE
            $product_dismiss_order_products = ProductDismissOrderProducts::withProductDismissOrder()
               ->where('product_id', $product_id)
               ->whereBetween('created_at', [$from, $to])
               ->get();

            // SORTING AND ARRANGING DATA
            $new_data =  $this->sortingAndArrangingProductHistory(
                $product_credits,
                $sold_products,
                $refund_products,
                $product_dismiss_order_products
            );

            // GET PRODUCT DATA
            $product = Product::find($product_id);

            return [
                "product" => $product,
                "data" => $new_data
            ];
        });
    }

    public function topSoldByAmount($year) {
        // RETURN REPORT FROM CACHE IF IN CACHE AND IF NOT THEN RE-CACHE IT
        $result = $this->cache->remember('top_sold_products_by_amount_report:'.$year, function () use ($year) {
            $columns = ['sold_products.deleted_at', 'export_invoices.deleted_at'];

            $report_data = ExportInvoice::join('sold_products', 'sold_products.export_invoice_id', '=', 'export_invoices.id')
                ->join('products', 'sold_products.product_id', '=', 'products.id')
                ->select(
                    'sold_products.product_id',
                    'products.name',
                    DB::raw('
                        round(round(sum(round(sold_products.sold_price * sold_products.quantity * (1 - sold_products.discount / 100), 2))
                        * (1 - export_invoices.discount / 100), 2) * (1 + IF(export_invoices.tax, 14, 0) / 100), 2)
                        as total
                    ')
                )
                ->whereYear('export_invoices.date', $year)
                ->where(function($q) use ($columns) {
                    foreach ($columns as $column) {
                        $q->whereNull($column);
                    }
                })
                ->groupBy('sold_products.product_id', 'products.name', 'export_invoices.discount', 'export_invoices.tax')
                ->orderBy('total', 'DESC')
                ->limit(20)
                ->get();
            return json_encode($report_data);
        }, config('constants.cache_expiry_minutes'));
        return json_decode($result);
    }

    public function topSoldRepeatedly($year) {
        // RETURN REPORT FROM CACHE IF IN CACHE AND IF NOT THEN RE-CACHE IT
        $result = $this->cache->remember('top_repeated_products_report:'.$year, function () use ($year) {
            $columns = ['sold_products.deleted_at', 'export_invoices.deleted_at'];

            $report_data = ExportInvoice::join('sold_products', 'sold_products.export_invoice_id', '=', 'export_invoices.id')
                ->join('products', 'sold_products.product_id', '=', 'products.id')
                ->select(
                    'sold_products.product_id',
                    'products.name',
                    DB::raw('
                        COUNT(DISTINCT(sold_products.export_invoice_id))
                        as requested
                    ')
                )
                ->whereYear('export_invoices.date', $year)
                ->where(function($q) use ($columns) {
                    foreach ($columns as $column) {
                        $q->whereNull($column);
                    }
                })
                ->groupBy('sold_products.product_id', 'products.name')
                ->orderBy('requested', 'DESC')
                ->limit(20)
                ->get();
            return json_encode($report_data);
        }, config('constants.cache_expiry_minutes'));
        return json_decode($result);
    }

    /**
     * @param $type
     * @param $year
     * @return
     */
    public function topSoldProfit ($type) {
        $this->cache->forget('top_profit_products_report:'.$type);
        $result = $this->cache->remember('top_profit_products_report:'.$type, function () use ($type) {
            $report_data = ProductLog::join('products', 'product_logs.product_id', '=', 'products.id')
            ->select(
                'product_id',
                'products.name',
                DB::raw('ROUND(((average_sell_price - average_purchase_price) / average_purchase_price) * 100, 2) as result_in_percentage'),
                DB::raw('ROUND((average_sell_price - average_purchase_price), 2) as result_in_number')
            )
            ->where('average_purchase_price', '>', 0)
            ->where('average_sell_price', '>', 0)
            ->orderBy('result_in_percentage', $type)
            ->limit(10)
            ->get();
            return json_encode($report_data);
        }, config('constants.cache_expiry_minutes'));
        return json_decode($result);
    }

    /**
     * @param $request
     * @return mixed
     */
    public function exportSupplierProductsCredit ($request) {
        $supplier_id = $request->supplier_id;

        // STORE ACTION
        event(new ActionHappened('report generate', 'supplier products credit excel export', $this->getAuthUserId()));
        return Excel::download(new SupplierProductCreditExport($supplier_id), date('Y-mm-dd').'.xlsx');
    }


    /*
     * **************************************************
     * ********** PRIVATE HELPERS FUNCTIONS *************
     * **************************************************
     */
    private function sortingAndArrangingProductHistory ($credit, $sold, $refund, $dismiss) {
        $data = [];

        // CREDIT
        foreach($credit as $c) {
            $date = null;
            $status = null;
            $concerned = '';
            if ($c->importInvoice) {
                $date = $c->importInvoice->date;
                $status = $c->importInvoice->approve;
                $concerned = $c->importInvoice->supplier->name;
            } else
                $date = $c->created_at->format('Y-m-d');

            array_push($data, [
                'type' => 'purchase',
                'price' => $c->item_net_price,
                'quantity' => $c->quantity,
                'model_id' => $c->import_invoice_id,
                'status' => $status,
                'date' => $date,
                'concerned' => $concerned,
            ]);
        }

        // SOLD
        foreach($sold as $s)
            array_push($data, [
                'type' => 'sold',
                'price' => $s->item_net_price,
                'quantity' => $s->quantity,
                'model_id' => $s->export_invoice_id,
                'status' => $s->exportInvoice->approve,
                'date' => $s->exportInvoice->date,
                'concerned' => $s->exportInvoice->customerBranch->customer_and_branch,
            ]);

        // REFUND
        foreach($refund as $r) {
            $concerned = '';
            $refund_type = 'refund from customer';
            if ($r->refundOrder->type === 'out') {
                $refund_type = 'refund to supplier';
                $concerned = $r->refundOrder->supplier->name;
            } else
                $concerned = $r->refundOrder->customerBranch->customer_and_branch;

            array_push($data, [
                'type' => $refund_type,
                'price' => $r->item_net_price,
                'quantity' => $r->quantity,
                'model_id' => $r->refund_id,
                'status' => $r->refundOrder->approve,
                'date' => $r->refundOrder->date,
                'concerned' => $concerned,
            ]);
        }

        // DISMISSAL
        foreach($dismiss as $d)
            array_push($data, [
                'type' => 'dismiss',
                'price' => null,
                'quantity' => $d->quantity,
                'model_id' => $d->product_dismiss_order_id,
                'status' => $d->productDismissOrder->approve,
                'date' => $d->created_at->format('Y-m-d'),
                'concerned' => '',
            ]);


        // SORT DATA
        $this->sortDataByDate($data, 'date');

        return $data;
    }

    private function sortDataByDate(&$data, $column) {
        $reference_array = [];

        foreach($data as $key => $row) {
            $reference_array[$key] = $row[$column];
        }

        array_multisort($reference_array, SORT_ASC, $data);
    }

}
