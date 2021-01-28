<?php

namespace App\Exports;

use App\Models\Invoices\ImportInvoice;
use App\Models\Product\Product;
use App\Models\Product\ProductCredits;
use App\Models\Product\ProductLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use DB;

class SupplierProductCreditExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $supplier_id;
    public function __construct($supplier_id) {
        $this->supplier_id = $supplier_id;
    }

    public function collection()
    {
        $supplier_id = $this->supplier_id;
        $invoices_ids = ImportInvoice::where('supplier_id', $supplier_id)->pluck('id');
        $products_ids = ProductCredits::whereIn('import_invoice_id', $invoices_ids)->pluck('product_id');
        $products = Product::withProductLog()->whereIn('id', $products_ids)->get();
        return $products;
    }

    public function map($products): array {
        return [
            $products->name,
            $products->code,
            // ADDING WHITE SPACE AFTER BARCODE TO TREAT IT AS STRING IN EXCEL
            $products->barcode." ",
            $products->productLog->available_quantity,
            round($products->productLog->average_purchase_price, 3),
        ];
    }

    public function headings(): array {
        return [
            trans('reports.CREDITS.EXCEL_COLUMNS.NAME'),
            trans('reports.CREDITS.EXCEL_COLUMNS.CODE'),
            trans('reports.CREDITS.EXCEL_COLUMNS.BARCODE'),
            trans('reports.CREDITS.EXCEL_COLUMNS.QUANTITY'),
            trans('reports.CREDITS.EXCEL_COLUMNS.PURCHASE_PRICE'),
        ];
    }
}
