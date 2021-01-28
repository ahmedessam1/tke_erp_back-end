<?php

namespace App\Exports;

use App\Models\Invoices\ImportInvoice;
use App\Models\Product\Product;
use App\Models\Product\ProductCredits;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SupplierProductCreditExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $supplier_id;
    public function __construct($supplier_id) {
        $this->supplier_id = $supplier_id;
    }

//    public function collection()
//    {
//        $supplier_id = $this->supplier_id;
//        $product_credits = ProductCredits::whereHas('importInvoice', function($q) use ($supplier_id) {
//            $q->where('supplier_id', $supplier_id);
//        })->get();
//        $product_unique_ids = [];
//        foreach($product_credits as $p_c) {
//            if (!in_array($p_c->product_id, $product_unique_ids))
//                array_push($product_unique_ids, $p_c->product_id);
//        }
//
//        $products = Product::whereIn('id', $product_unique_ids)
//            ->whereHas('productLog', function ($q) {
//                $q->where('available_quantity', '>', 0);
//            })
//            ->get();
//
//        return $products;
//    }
    public function collection()
    {
        $supplier_id = $this->supplier_id;
        $invoices_ids = ImportInvoice::where('supplier_id', $supplier_id)->pluck('id');
        $products = ProductCredits::withProductAndImages()->withImportInvoice()->whereIn('import_invoice_id', $invoices_ids)->orderBy('product_id', 'DESC')->get();

        return $products;
    }

    public function map($products): array {
        return [
            $products->product->name,
            $products->product->code,
            // ADDING WHITE SPACE AFTER BARCODE TO TREAT IT AS STRING IN EXCEL
            $products->product->barcode." ",
            $products->quantity,
            $products->item_net_price,
            // INVOICE DATA
            $products->importInvoice->number,
            $products->importInvoice->date,
        ];
    }

    public function headings(): array {
        return [
            trans('reports.CREDITS.EXCEL_COLUMNS.NAME'),
            trans('reports.CREDITS.EXCEL_COLUMNS.CODE'),
            trans('reports.CREDITS.EXCEL_COLUMNS.BARCODE'),
            trans('reports.CREDITS.EXCEL_COLUMNS.QUANTITY'),
            trans('reports.CREDITS.EXCEL_COLUMNS.PURCHASE_PRICE'),
            // INVOICE DATA
            trans('reports.CREDITS.EXCEL_COLUMNS.INVOICE_NUMBER'),
            trans('reports.CREDITS.EXCEL_COLUMNS.INVOICE_DATE'),
        ];
    }
}
